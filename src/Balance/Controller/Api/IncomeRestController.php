<?php

namespace App\Balance\Controller\Api;

use App\Balance\Service\IncomeManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IncomeRestController extends AbstractController
{
    private $incomeManager;

    private $entityManager;

    public function __construct(IncomeManager $incomeManager, EntityManagerInterface $entityManager)
    {
        $this->incomeManager = $incomeManager;
        $this->entityManager = $entityManager;
    }

    /** @Route("api/incomes", methods={"GET"}, name="api_incomes_get_all") */
    public function getAllIncomes(): JsonResponse
    {
        return new JsonResponse([
            'incomes' => $this->incomeManager->getAllIncomesAsArray()
        ]);
    }

    /** @Route("api/incomes", methods={"POST"}, name="api_incomes_add") */
    public function addIncome(Request $request): JsonResponse
    {
        $this->incomeManager->addIncome(json_decode($request->getContent(), true));

        return new JsonResponse([
            'message' => 'The income has been added successfully!'
        ]);
    }

    /** @Route("api/incomes/{id}", methods={"GET"}, name="api_incomes_get") */
    public function getExpense(int $id): JsonResponse
    {
        return new JsonResponse([
            $this->incomeManager->getIncomeAsArray($id)
        ]);
    }

    /** @Route("api/incomes/{id}", methods={"DELETE"}, name="api_incomes_delete") */
    public function deleteExpense(int $id): JsonResponse
    {
        $this->incomeManager->deleteIncome($id);

        return new JsonResponse([
            'message' => 'The income has been deleted successfully!'
        ]);
    }

    /** @Route("api/incomes/{id}", methods={"PUT", "PATCH"}, name="api_incomes_update") */
    public function updateExpense(int $id, Request $request): JsonResponse
    {
       $this->incomeManager->updateIncome($id, json_decode($request->getContent(), true));

        return new JsonResponse([
            'message' => 'The income has been updated successfully!'
        ]);
    }
}