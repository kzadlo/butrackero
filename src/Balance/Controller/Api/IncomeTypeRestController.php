<?php

namespace App\Balance\Controller\Api;

use App\Application\Filter\Filter;
use App\Application\Service\PaginatorInterface;
use App\Balance\Controller\Api\Traits\LinkCreatorTrait;
use App\Balance\Model\IncomeType;
use App\Balance\Service\TypeManager;
use App\Balance\Validator\TypeValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IncomeTypeRestController extends AbstractController
{
    use LinkCreatorTrait;

    private $entityManager;

    private $typeManager;

    private $typeValidator;

    public function __construct(
        EntityManagerInterface $entityManager,
        TypeManager $typeManager,
        TypeValidator $validator
    ) {
        $this->entityManager = $entityManager;
        $this->typeManager = $typeManager;
        $this->typeValidator = $validator;
    }

    /** @Route("api/income-types", methods={"GET"}, name="api_income_types_get_all") */
    public function getAll(Request $request, PaginatorInterface $paginator, Filter $filter): JsonResponse
    {
        $page = (int) $request->get('page', 1);

        $filter->prepare($request->query->all());

        $filteredTypesQuantity = $this->typeManager->countFiltered($filter->getAll());
        $lastPage = $paginator->calculateLastPage($filteredTypesQuantity);

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
        $types = $this->typeManager->getFiltered($filters);

        if (empty($types)) {
            return new JsonResponse([
                'errors' => [
                    'types' => 'Not found'
                ]
            ], 400);
        }

        $route = 'api_income_types_get_all';

        return new JsonResponse([
            'types' => $types,
            '_metadata' => [
                'page' => $paginator->getPage(),
                'per_page' => $paginator->getLimit(),
                'page_count' => count($types),
                'total_count' => $filteredTypesQuantity,
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

    /** @Route("api/income-types", methods={"POST"}, name="api_income_types_add") */
    public function add(Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'json') {
            return new JsonResponse([
                'errors' => sprintf('%s is not acceptable content type', $request->getContentType())
            ], 415);
        }

        $typeData = json_decode($request->getContent(), true);
        $this->typeValidator->validate($typeData);

        if (!$this->typeValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->typeValidator->getErrors()
            ], 400);
        }

        $typeData['author'] = $this->typeManager->getTypeAuthor();

        $this->typeManager->save($this->typeManager->createFromArray($typeData));

        return new JsonResponse([
            'message' => 'The type has been added successfully!'
        ], 201);
    }

    /** @Route("api/income-types/{id}", methods={"GET"}, name="api_income_types_get") */
    public function getBy(int $id): JsonResponse
    {
        $type = $this->entityManager->find(IncomeType::class, $id);

        $this->typeValidator->validateTypeExists($type);

        if (!$this->typeValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->typeValidator->getErrors()
            ], 400);
        }

        return new JsonResponse([
            $this->typeManager->getAsArray($type)
        ]);
    }

    /** @Route("api/income-types/{id}", methods={"DELETE"}, name="api_income_types_delete") */
    public function delete(int $id): JsonResponse
    {
        $type = $this->entityManager->find(IncomeType::class, $id);

        $this->typeValidator->validateTypeExists($type);
        $this->typeValidator->validateTypeHasIncomes($type);

        if (!$this->typeValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->typeValidator->getErrors()
            ], 400);
        }

        $this->typeManager->delete($type);

        return new JsonResponse([
            'message' => 'The type has been deleted successfully!'
        ]);
    }

    /** @Route("api/income-types/{id}", methods={"PATCH"}, name="api_income_types_update") */
    public function update(int $id, Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'json') {
            return new JsonResponse([
                'errors' => sprintf('%s is not acceptable content type', $request->getContentType())
            ], 415);
        }

        $typeData = json_decode($request->getContent(), true);

        $type = $this->entityManager->find(IncomeType::class, $id);
        $this->typeValidator->validateTypeExists($type);

        if ($this->typeValidator->hasArrayKey('name', $typeData)) {
            $this->typeValidator->validateName($typeData);
        }

        if ($this->typeValidator->hasArrayKey('description', $typeData)) {
            $this->typeValidator->validateDescription($typeData);
        }

        if (!$this->typeValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->typeValidator->getErrors()
            ], 400);
        }

        $this->typeManager->update($type, $typeData);

        return new JsonResponse([
            'message' => 'The type has been updated successfully!'
        ]);
    }
}