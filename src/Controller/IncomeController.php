<?php

namespace App\Controller;

use App\Entity\Income;
use App\Service\IncomeManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class IncomeController extends AbstractController
{
    private $incomeManager;

    private $serializer;

    private $entityManager;

    public function __construct(
        IncomeManager $incomeManager,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $this->incomeManager = $incomeManager;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /** @Route("api/incomes", methods={"GET"}) */
    public function getAllIncomes(): JsonResponse
    {
        $incomes = $this->entityManager->getRepository(Income::class)->findAll();

        return new JsonResponse([
            'incomes' => $this->incomeManager->prepareIncomes($incomes)
        ]);
    }

    /** @Route("api/incomes", methods={"POST"}) */
    public function addIncome(Request $request): JsonResponse
    {
        $income = $this->serializer->deserialize($request->getContent(), Income::class, 'json');

        $this->entityManager->persist($income);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'The income has been added successfully!'
        ]);
    }

    /** @Route("api/incomes/{id}", methods={"GET"}) */
    public function getExpense(int $id): JsonResponse
    {
        $income = $this->entityManager->find(Income::class, $id);

        return new JsonResponse([$this->incomeManager->extractOneIncome($income)]);
    }

    /** @Route("api/incomes/{id}", methods={"DELETE"}) */
    public function deleteExpense(int $id): JsonResponse
    {
        $income = $this->entityManager->find(Income::class, $id);

        $this->entityManager->remove($income);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'The income has been deleted successfully!'
        ]);
    }

    /** @Route("api/incomes/{id}", methods={"PUT", "PATCH"}) */
    public function updateExpense(int $id, Request $request): JsonResponse
    {
        $updateValues = json_decode($request->getContent(), true);
        $income = $this->entityManager->find(Income::class, $id);

        $this->incomeManager->updateIncome($income, $updateValues);

        return new JsonResponse([
            'message' => 'The income has been updated successfully!'
        ]);
    }
}