<?php

namespace App\Balance\Controller\Api;

use App\Balance\Service\ExpenseManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseController extends AbstractController
{
    private $expenseManager;

    private $entityManager;

    public function __construct(ExpenseManager $expenseManager, EntityManagerInterface $entityManager)
    {
        $this->expenseManager = $expenseManager;
        $this->entityManager = $entityManager;
    }

    /** @Route("api/expenses", methods={"GET"}) */
    public function getAllExpenses(): JsonResponse
    {
        return new JsonResponse([
            'expenses' => $this->expenseManager->getAllExpensesAsArray()
        ]);
    }

    /** @Route("api/expenses", methods={"POST"}) */
    public function addExpense(Request $request): JsonResponse
    {
        $this->expenseManager->addExpense(json_decode($request->getContent(), true));

        return new JsonResponse([
            'message' => 'The expense has been added successfully!'
        ]);
    }

    /** @Route("api/expenses/{id}", methods={"GET"}) */
    public function getExpense(int $id): JsonResponse
    {
        return new JsonResponse([
            $this->expenseManager->getExpenseAsArray($id)
        ]);
    }

    /** @Route("api/expenses/{id}", methods={"DELETE"}) */
    public function deleteExpense(int $id): JsonResponse
    {
        $this->expenseManager->deleteExpense($id);

        return new JsonResponse([
            'message' => 'The expense has been deleted successfully!'
        ]);
    }

    /** @Route("api/expenses/{id}", methods={"PUT", "PATCH"}) */
    public function updateExpense(int $id, Request $request): JsonResponse
    {
        $this->expenseManager->updateExpense($id, json_decode($request->getContent(), true));

        return new JsonResponse([
            'message' => 'The expense has been updated successfully!'
        ]);
    }
}