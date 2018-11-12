<?php

namespace App\Controller;

use App\Service\IncomeManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IncomeController extends AbstractController
{
    private $incomeManager;

    private $entityManager;

    public function __construct(IncomeManager $incomeManager, EntityManagerInterface $entityManager)
    {
        $this->incomeManager = $incomeManager;
        $this->entityManager = $entityManager;
    }

    /** @Route("api/incomes", methods={"GET"}) */
    public function getAllIncomes(): JsonResponse
    {
        return new JsonResponse([
            'incomes' => $this->incomeManager->getAllIncomesAsArray()
        ]);
    }

    /** @Route("api/incomes", methods={"POST"}) */
    public function addIncome(Request $request): JsonResponse
    {
        $this->incomeManager->addIncome(json_decode($request->getContent(), true));

        return new JsonResponse([
            'message' => 'The income has been added successfully!'
        ]);
    }

    /** @Route("api/incomes/{id}", methods={"GET"}) */
    public function getExpense(int $id): JsonResponse
    {
        return new JsonResponse([
            $this->incomeManager->getIncomeAsArray($id)
        ]);
    }

    /** @Route("api/incomes/{id}", methods={"DELETE"}) */
    public function deleteExpense(int $id): JsonResponse
    {
        $this->incomeManager->deleteIncome($id);

        return new JsonResponse([
            'message' => 'The income has been deleted successfully!'
        ]);
    }

    /** @Route("api/incomes/{id}", methods={"PUT", "PATCH"}) */
    public function updateExpense(int $id, Request $request): JsonResponse
    {
       $this->incomeManager->updateIncome($id, json_decode($request->getContent(), true));

        return new JsonResponse([
            'message' => 'The income has been updated successfully!'
        ]);
    }
}