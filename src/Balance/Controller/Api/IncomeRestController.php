<?php

namespace App\Balance\Controller\Api;

use App\Balance\Model\Income;
use App\Balance\Model\IncomeType;
use App\Balance\Service\IncomeManager;
use App\Balance\Validator\IncomeValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
    public function getAllIncomes(): JsonResponse
    {
        return new JsonResponse([
            'incomes' => $this->incomeManager->getAllIncomesAsArray()
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
            return (new JsonResponse([
                'errors' => $this->incomeValidator->getErrors()
            ]))->setStatusCode(400);
        }

        $this->incomeManager->addIncome($this->incomeManager->createIncomeFromArray($incomeData));

        return (new JsonResponse([
            'message' => 'The income has been added successfully!'
        ]))->setStatusCode(201);
    }

    /** @Route("api/incomes/{id}", methods={"GET"}, name="api_incomes_get") */
    public function getIncome(int $id): JsonResponse
    {
        $income = $this->entityManager->find(Income::class, $id);

        $this->incomeValidator->validateIncomeExists($income);

        if (!$this->incomeValidator->isValid()) {
            return (new JsonResponse([
                'errors' => $this->incomeValidator->getErrors()
            ]))->setStatusCode(400);
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
            return (new JsonResponse([
                'errors' => $this->incomeValidator->getErrors()
            ]))->setStatusCode(400);
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
            $this->incomeValidator->validateType($incomeData);

            if ($this->incomeValidator->isValid()) {
                $incomeData['type'] = $this->entityManager->find(IncomeType::class, $incomeData['type']);

                $this->incomeValidator->validateTypeExists($incomeData['type']);
            }
        }

        if (!$this->incomeValidator->isValid()) {
            return (new JsonResponse([
                'errors' => $this->incomeValidator->getErrors()
            ]))->setStatusCode(400);
        }

        $this->incomeManager->updateIncome($income, $incomeData);

        return new JsonResponse([
            'message' => 'The income has been updated successfully!'
        ]);
    }
}