<?php

namespace App\Balance\Controller\Api;

use App\Application\Filter\Filter;
use App\Balance\Controller\Api\Traits\LinkCreatorTrait;
use App\Balance\Model\Expense;
use App\Balance\Model\ExpenseCategory;
use App\Balance\Service\ExpenseManager;
use App\Balance\Validator\ExpenseValidator;
use App\Application\Service\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseRestController extends AbstractController
{
    use LinkCreatorTrait;

    private $entityManager;

    private $expenseManager;

    private $expenseValidator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ExpenseManager $expenseManager,
        ExpenseValidator $validator
    ) {
        $this->entityManager = $entityManager;
        $this->expenseManager = $expenseManager;
        $this->expenseValidator = $validator;
    }

    /** @Route("api/expenses", methods={"GET"}, name="api_expenses_get_all") */
    public function getAll(Request $request, PaginatorInterface $paginator, Filter $filter): JsonResponse
    {
        $page = (int) $request->get('page', 1);

        $filter->prepare($request->query->all());

        $filteredExpensesQuantity = $this->expenseManager->countFiltered($filter->getAll());
        $lastPage = $paginator->calculateLastPage($filteredExpensesQuantity);

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
        $expenses = $this->expenseManager->getFiltered($filters);

        if (empty($expenses)) {
            return new JsonResponse([
                'errors' => [
                    'expenses' => 'Not found'
                ]
            ], 400);
        }

        $route = 'api_expenses_get_all';

        return new JsonResponse([
            'expenses' => $expenses,
            '_metadata' => [
                'page' => $paginator->getPage(),
                'perPage' => $paginator->getLimit(),
                'pageCount' => count($expenses),
                'totalCount' => $filteredExpensesQuantity,
                'links' => [
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

    /** @Route("api/expenses", methods={"POST"}, name="api_expenses_add") */
    public function add(Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'json') {
            return new JsonResponse([
                'errors' => sprintf('%s is not acceptable content type', $request->getContentType())
            ], 415);
        }

        $expenseData = json_decode($request->getContent(), true);
        $this->expenseValidator->validate($expenseData);

        if ($this->expenseValidator->isValid()) {
            $expenseData['category'] = $this->entityManager->find(ExpenseCategory::class, $expenseData['category']);
            $this->expenseValidator->validateCategoryExists($expenseData['category']);
        }

        if (!$this->expenseValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->expenseValidator->getErrors()
            ], 400);
        }

        $expenseData['author'] = $this->expenseManager->getExpenseAuthor();

        $this->expenseManager->save($this->expenseManager->createFromArray($expenseData));

        return new JsonResponse([
            'message' => 'The expense has been added successfully!'
        ], 201);
    }

    /** @Route("api/expenses/{id}", methods={"GET"}, name="api_expenses_get") */
    public function getBy(int $id): JsonResponse
    {
        $expense = $this->entityManager->find(Expense::class, $id);

        $this->expenseValidator->validateExpenseExists($expense);

        if (!$this->expenseValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->expenseValidator->getErrors()
            ], 400);
        }

        return new JsonResponse([
            $this->expenseManager->getAsArray($expense)
        ]);
    }

    /** @Route("api/expenses/{id}", methods={"DELETE"}, name="api_expenses_delete") */
    public function delete(int $id): JsonResponse
    {
        $expense = $this->entityManager->find(Expense::class, $id);

        $this->expenseValidator->validateExpenseExists($expense);

        if (!$this->expenseValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->expenseValidator->getErrors()
            ], 400);
        }

        $this->expenseManager->delete($expense);

        return new JsonResponse([
            'message' => 'The expense has been deleted successfully!'
        ]);
    }

    /** @Route("api/expenses/{id}", methods={"PATCH"}, name="api_expenses_update") */
    public function update(int $id, Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'json') {
            return new JsonResponse([
                'errors' => sprintf('%s is not acceptable content type', $request->getContentType())
            ], 415);
        }

        $expenseData = json_decode($request->getContent(), true);

        $expense = $this->entityManager->find(Expense::class, $id);
        $this->expenseValidator->validateExpenseExists($expense);

        if ($this->expenseValidator->hasArrayKey('amount', $expenseData)) {
            $this->expenseValidator->validateAmount($expenseData);
        }

        if ($this->expenseValidator->hasArrayKey('category', $expenseData)) {
            if ($this->expenseValidator->validateCategory($expenseData)) {
                $expenseData['category'] = $this->entityManager->find(ExpenseCategory::class, $expenseData['category']);
                $this->expenseValidator->validateCategoryExists($expenseData['category']);
            }
        }

        if (!$this->expenseValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->expenseValidator->getErrors()
            ], 400);
        }

        $this->expenseManager->update($expense, $expenseData);

        return new JsonResponse([
            'message' => 'The expense has been updated successfully!'
        ]);
    }
}