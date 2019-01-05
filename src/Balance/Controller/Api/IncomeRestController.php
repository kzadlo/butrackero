<?php

namespace App\Balance\Controller\Api;

use App\Application\Filter\Filter;
use App\Balance\Controller\Api\Traits\LinkCreatorTrait;
use App\Balance\Model\Income;
use App\Balance\Model\IncomeType;
use App\Balance\Service\IncomeManager;
use App\Balance\Validator\IncomeValidator;
use App\Application\Service\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IncomeRestController extends AbstractController
{
    use LinkCreatorTrait;

    private $entityManager;

    private $incomeManager;

    private $incomeValidator;

    public function __construct(
        EntityManagerInterface $entityManager,
        IncomeManager $incomeManager,
        IncomeValidator $incomeValidator
    ) {
        $this->entityManager = $entityManager;
        $this->incomeManager = $incomeManager;
        $this->incomeValidator = $incomeValidator;
    }

    /** @Route("api/incomes", methods={"GET"}, name="api_incomes_get_all") */
    public function getAllIncomes(Request $request, PaginatorInterface $paginator, Filter $filter): JsonResponse
    {
        $page = (int) $request->get('page', 1);

        $filter->prepare($request->query->all());

        $filteredIncomesQuantity = $this->incomeManager->countFiltered($filter->getAll());
        $lastPage = $paginator->calculateLastPage($filteredIncomesQuantity);

        if ($paginator->isPageOutOfRange($page, $lastPage)) {
            return new JsonResponse([
                'errors' => [
                    'page' => sprintf('This value should be greater than 0 and less than %d', $lastPage+1)
                ]
            ], 400);
        }

        $paginator->setPage($page);

        $filter->add('offset', $paginator->getOffset());
        $filter->add('limit', $paginator->getLimit());

        $filters = $filter->getAll();
        $incomes = $this->incomeManager->getFiltered($filters);

        if (empty($incomes)) {
            return new JsonResponse([
                'errors' => [
                    'incomes' => 'Not found'
                ]
            ], 400);
        }

        $route = 'api_incomes_get_all';

        return new JsonResponse([
            'incomes' => $incomes,
            '_metadata' => [
                'page' => $paginator->getPage(),
                'per_page' => $paginator->getLimit(),
                'page_count' => count($incomes),
                'total_count' => $filteredIncomesQuantity,
                'Links' => [
                    'self' => $this->generateLink($route, $filters),
                    'first' => $this->generateLink($route, $filters, 1),
                    'previous' => !$paginator->isFirstPage()
                        ? $this->generateLink($route, $filters, $paginator->previousPage())
                        : '',
                    'next' => !$paginator->isLastPage($lastPage)
                        ? $this->generateLink($route, $filters, $paginator->nextPage())
                        : '',
                    'last' => $this->generateLink($route, $filters, $lastPage)
                ]
            ]
        ]);
    }

    /** @Route("api/incomes", methods={"POST"}, name="api_incomes_add") */
    public function addIncome(Request $request): JsonResponse
    {
        $incomeData = json_decode($request->getContent(), true);

        $this->incomeValidator->validate($incomeData);

        if ($this->incomeValidator->isValid()) {
            $incomeData['type'] = $this->entityManager->find(IncomeType::class, $incomeData['type']);
            $this->incomeValidator->validateTypeExists($incomeData['type']);
        }

        if (!$this->incomeValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->incomeValidator->getErrors()
            ], 400);
        }

        $incomeData['author'] = $this->incomeManager->getIncomeAuthor();

        $this->incomeManager->save($this->incomeManager->createFromArray($incomeData));

        return new JsonResponse([
            'message' => 'The income has been added successfully!'
        ], 201);
    }

    /** @Route("api/incomes/{id}", methods={"GET"}, name="api_incomes_get") */
    public function getIncome(int $id): JsonResponse
    {
        $income = $this->entityManager->find(Income::class, $id);

        $this->incomeValidator->validateIncomeExists($income);

        if (!$this->incomeValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->incomeValidator->getErrors()
            ], 400);
        }

        return new JsonResponse([
            $this->incomeManager->getAsArray($income)
        ]);
    }

    /** @Route("api/incomes/{id}", methods={"DELETE"}, name="api_incomes_delete") */
    public function deleteIncome(int $id): JsonResponse
    {
        $income = $this->entityManager->find(Income::class, $id);

        $this->incomeValidator->validateIncomeExists($income);

        if (!$this->incomeValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->incomeValidator->getErrors()
            ], 400);
        }

        $this->incomeManager->delete($income);

        return new JsonResponse([
            'message' => 'The income has been deleted successfully!'
        ]);
    }

    /** @Route("api/incomes/{id}", methods={"PUT", "PATCH"}, name="api_incomes_update") */
    public function updateIncome(int $id, Request $request): JsonResponse
    {
        $incomeData = json_decode($request->getContent(), true);

        $income = $this->entityManager->find(Income::class, $id);
        $this->incomeValidator->validateIncomeExists($income);

        if ($this->incomeValidator->hasArrayKey('amount', $incomeData)) {
            $this->incomeValidator->validateAmount($incomeData);
        }

        if ($this->incomeValidator->hasArrayKey('type', $incomeData)) {
            if ($this->incomeValidator->validateType($incomeData)) {
                $incomeData['type'] = $this->entityManager->find(IncomeType::class, $incomeData['type']);
                $this->incomeValidator->validateTypeExists($incomeData['type']);
            }
        }

        if (!$this->incomeValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->incomeValidator->getErrors()
            ], 400);
        }

        $this->incomeManager->update($income, $incomeData);

        return new JsonResponse([
            'message' => 'The income has been updated successfully!'
        ]);
    }
}