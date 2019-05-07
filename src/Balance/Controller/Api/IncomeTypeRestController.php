<?php

namespace App\Balance\Controller\Api;

use App\Application\Service\Filter;
use App\Application\Service\PaginatorInterface;
use App\Balance\Repository\IncomeTypeRepository;
use App\Balance\Service\TypeManager;
use App\Balance\Validator\TypeValidator;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IncomeTypeRestController extends AbstractController
{
    use LinkCreatorTrait;

    private $typeManager;

    private $typeValidator;

    private $incomeTypeRepository;

    public function __construct(
        TypeManager $typeManager,
        TypeValidator $validator,
        IncomeTypeRepository $incomeTypeRepository
    ) {
        $this->typeManager = $typeManager;
        $this->typeValidator = $validator;
        $this->incomeTypeRepository = $incomeTypeRepository;
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
                'perPage' => $paginator->getLimit(),
                'pageCount' => count($types),
                'totalCount' => $filteredTypesQuantity,
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

        $this->incomeTypeRepository->save($this->typeManager->createFromArray($typeData));

        return new JsonResponse([
            'message' => 'The type has been added successfully!'
        ], 201);
    }

    /** @Route("api/income-types/{id}", methods={"GET"}, name="api_income_types_get") */
    public function getBy(string $id): JsonResponse
    {
        $type = $this->incomeTypeRepository->findOneById(Uuid::fromString($id));

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
    public function delete(string $id): JsonResponse
    {
        $type = $this->incomeTypeRepository->findOneById(Uuid::fromString($id));

        $this->typeValidator->validateTypeExists($type);

        if (!$this->typeValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->typeValidator->getErrors()
            ], 400);
        }

        $this->incomeTypeRepository->delete($type);

        return new JsonResponse([
            'message' => 'The type has been deleted successfully!'
        ]);
    }

    /** @Route("api/income-types/{id}", methods={"PATCH"}, name="api_income_types_update") */
    public function update(string $id, Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'json') {
            return new JsonResponse([
                'errors' => sprintf('%s is not acceptable content type', $request->getContentType())
            ], 415);
        }

        $typeData = json_decode($request->getContent(), true);

        $type = $this->incomeTypeRepository->findOneById(Uuid::fromString($id));
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
