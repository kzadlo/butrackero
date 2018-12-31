<?php

namespace App\Balance\Controller\Api;

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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExpenseRestController extends AbstractController
{
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
    public function getExpenses(Request $request, PaginatorInterface $paginator): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $params = $request->query->all();
        $paramsToEdit = $params;

        $filteredExpensesQuantity = $this->expenseManager->countFilteredExpenses($params);

        $lastPage = $paginator->calculateLastPage($filteredExpensesQuantity);

        if ($page < 1 || ($page > $lastPage && $lastPage > 0)) {
            return new JsonResponse([
                'errors' => [
                    'page' => sprintf('This value should be greater than 0 and less than %d', $lastPage)
                ]
            ], 400);
        }

        $paginator->setPage($page);
        $params['offset'] = $paginator->getOffset();
        $params['limit'] = $paginator->getLimit();

        $expenses = $this->expenseManager->getFilteredExpenses($params);

        if (empty($expenses)) {
            return new JsonResponse([
                'errors' => [
                    'expenses' => 'Not found'
                ]
            ], 400);
        }

        $selfLink = $this->generateUrl('api_expenses_get_all', $paramsToEdit, UrlGeneratorInterface::ABSOLUTE_URL);

        $paramsToEdit['page'] = 1;
        $firstLink = $this->generateUrl('api_expenses_get_all', $paramsToEdit, UrlGeneratorInterface::ABSOLUTE_URL);

        $paramsToEdit['page'] = $paginator->previousPage();
        $previousLink = $this->generateUrl('api_expenses_get_all', $paramsToEdit, UrlGeneratorInterface::ABSOLUTE_URL);

        $paramsToEdit['page'] = $paginator->nextPage();
        $nextLink = $this->generateUrl('api_expenses_get_all', $paramsToEdit, UrlGeneratorInterface::ABSOLUTE_URL);

        $paramsToEdit['page'] = $lastPage;
        $lastLink = $this->generateUrl('api_expenses_get_all', $paramsToEdit, UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse([
            'expenses' => $expenses,
            '_metadata' => [
                'page' => $paginator->getPage(),
                'per_page' => $paginator->getLimit(),
                'page_count' => count($expenses),
                'total_count' => $filteredExpensesQuantity,
                'Links' => [
                    'self' => $selfLink,
                    'first' => $firstLink,
                    'previous' => $paginator->isFirstPage() ? '' : $previousLink,
                    'next' => $paginator->isLastPage($lastPage) ? '' : $nextLink,
                    'last' => $lastLink
                ]
            ]
        ]);
    }

    /** @Route("api/expenses", methods={"POST"}, name="api_expenses_add") */
    public function addExpense(Request $request): JsonResponse
    {
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

        $this->expenseManager->addExpense($this->expenseManager->createExpenseFromArray($expenseData));

        return new JsonResponse([
            'message' => 'The expense has been added successfully!'
        ], 201);
    }

    /** @Route("api/expenses/{id}", methods={"GET"}, name="api_expenses_get") */
    public function getExpense(int $id): JsonResponse
    {
        $expense = $this->entityManager->find(Expense::class, $id);

        $this->expenseValidator->validateExpenseExists($expense);

        if (!$this->expenseValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->expenseValidator->getErrors()
            ], 400);
        }

        return new JsonResponse([
            $this->expenseManager->getExpenseAsArray($expense)
        ]);
    }

    /** @Route("api/expenses/{id}", methods={"DELETE"}, name="api_expenses_delete") */
    public function deleteExpense(int $id): JsonResponse
    {
        $expense = $this->entityManager->find(Expense::class, $id);

        $this->expenseValidator->validateExpenseExists($expense);

        if (!$this->expenseValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->expenseValidator->getErrors()
            ], 400);
        }

        $this->expenseManager->deleteExpense($expense);

        return new JsonResponse([
            'message' => 'The expense has been deleted successfully!'
        ]);
    }

    /** @Route("api/expenses/{id}", methods={"PUT", "PATCH"}, name="api_expenses_update") */
    public function updateExpense(int $id, Request $request): JsonResponse
    {
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

        $this->expenseManager->updateExpense($expense, $expenseData);

        return new JsonResponse([
            'message' => 'The expense has been updated successfully!'
        ]);
    }
}