<?php

namespace App\Balance\Controller\Api;

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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class IncomeRestController extends AbstractController
{
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
    public function getAllIncomes(Request $request, PaginatorInterface $paginator): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $params = $request->query->all();
        $paramsToEdit = $params;

        $allIncomesQuantity = $this->incomeManager->countIncomes();

        $lastPage = $paginator->calculateLastPage($allIncomesQuantity);

        if ($page < 1 || $page > $lastPage) {
            return new JsonResponse([
                'errors' => [
                    'page' => sprintf('This value should be greater than 0 and less than %d', $lastPage)
                ]
            ], 400);
        }

        $paginator->setPage($page);
        $incomes = $this->incomeManager->getPortionIncomes($paginator->getOffset(), $paginator->getLimit());

        $paramsToEdit['page'] = 1;
        $firstLink = $this->generateUrl('api_incomes_get_all', $paramsToEdit, UrlGeneratorInterface::ABSOLUTE_URL);

        $paramsToEdit['page'] = $paginator->previousPage();
        $previousLink = $this->generateUrl('api_incomes_get_all', $paramsToEdit, UrlGeneratorInterface::ABSOLUTE_URL);

        $paramsToEdit['page'] = $paginator->nextPage();
        $nextLink = $this->generateUrl('api_incomes_get_all', $paramsToEdit, UrlGeneratorInterface::ABSOLUTE_URL);

        $paramsToEdit['page'] = $lastPage;
        $lastLink = $this->generateUrl('api_incomes_get_all', $paramsToEdit, UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse([
            'incomes' => $incomes,
            '_metadata' => [
                'page' => $paginator->getPage(),
                'per_page' => $paginator->getLimit(),
                'page_count' => count($incomes),
                'total_count' => $allIncomesQuantity,
                'Links' => [
                    'self' => $this->generateUrl('api_incomes_get_all', $params, UrlGeneratorInterface::ABSOLUTE_URL),
                    'first' => $firstLink,
                    'previous' => $paginator->isFirstPage() ? '' : $previousLink,
                    'next' => $paginator->isLastPage($lastPage) ? '' : $nextLink,
                    'last' => $lastLink
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

        $this->incomeManager->addIncome($this->incomeManager->createIncomeFromArray($incomeData));

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
            $this->incomeManager->getIncomeAsArray($income)
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

        $this->incomeManager->deleteIncome($income);

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

        $this->incomeManager->updateIncome($income, $incomeData);

        return new JsonResponse([
            'message' => 'The income has been updated successfully!'
        ]);
    }
}