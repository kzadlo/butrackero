<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Service\ExpenseManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ExpenseController extends AbstractController
{
    private $expenseManager;

    private $serializer;

    private $entityManager;

    public function __construct(
        ExpenseManager $expenseManager,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $this->expenseManager = $expenseManager;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /** @Route("api/expenses", methods={"GET"}) */
    public function getAllExpenses(): JsonResponse
    {
        $expenses = $this->entityManager->getRepository(Expense::class)->findAll();

        return new JsonResponse([
            'expenses' => $this->expenseManager->prepareExpenses($expenses)
        ]);
    }

    /** @Route("api/expenses", methods={"POST"}) */
    public function addExpense(Request $request): JsonResponse
    {
        $expense = $this->serializer->deserialize($request->getContent(), Expense::class, 'json');

        $this->entityManager->persist($expense);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'The expense has been added successfully!'
        ]);
    }

    /** @Route("api/expenses/{id}", methods={"GET"}) */
    public function getExpense(int $id): JsonResponse
    {
        $expense = $this->entityManager->find(Expense::class, $id);

        return new JsonResponse([$this->expenseManager->extractOneExpense($expense)]);
    }

    /** @Route("api/expenses/{id}", methods={"DELETE"}) */
    public function deleteExpense(int $id): JsonResponse
    {
        $expense = $this->entityManager->find(Expense::class, $id);

        $this->entityManager->remove($expense);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'The expense has been deleted successfully!'
        ]);
    }

    /** @Route("api/expenses/{id}", methods={"PUT", "PATCH"}) */
    public function updateExpense(int $id, Request $request): JsonResponse
    {
        $updateValues = json_decode($request->getContent(), true);
        $expense = $this->entityManager->find(Expense::class, $id);

        $this->expenseManager->updateExpense($expense, $updateValues);

        return new JsonResponse([
            'message' => 'The expense has been updated successfully!'
        ]);
    }
}