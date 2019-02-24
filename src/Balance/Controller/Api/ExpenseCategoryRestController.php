<?php

namespace App\Balance\Controller\Api;

use App\Application\Filter\Filter;
use App\Application\Service\PaginatorInterface;
use App\Balance\Controller\Api\Traits\LinkCreatorTrait;
use App\Balance\Model\ExpenseCategory;
use App\Balance\Service\CategoryManager;
use App\Balance\Validator\CategoryValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseCategoryRestController extends AbstractController
{
    use LinkCreatorTrait;

    private $entityManager;

    private $categoryManager;

    private $categoryValidator;

    public function __construct(
        EntityManagerInterface $entityManager,
        CategoryManager $categoryManager,
        CategoryValidator $validator
    ) {
        $this->entityManager = $entityManager;
        $this->categoryManager = $categoryManager;
        $this->categoryValidator = $validator;
    }

    /** @Route("api/expense-categories", methods={"GET"}, name="api_expense_categories_get_all") */
    public function getAll(Request $request, PaginatorInterface $paginator, Filter $filter): JsonResponse
    {
        $page = (int) $request->get('page', 1);

        $filter->prepare($request->query->all());

        $filteredCategoriesQuantity = $this->categoryManager->countFiltered($filter->getAll());
        $lastPage = $paginator->calculateLastPage($filteredCategoriesQuantity);

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
        $categories = $this->categoryManager->getFiltered($filters);

        if (empty($categories)) {
            return new JsonResponse([
                'errors' => [
                    'categories' => 'Not found'
                ]
            ], 400);
        }

        $route = 'api_expense_categories_get_all';

        return new JsonResponse([
            'categories' => $categories,
            '_metadata' => [
                'page' => $paginator->getPage(),
                'per_page' => $paginator->getLimit(),
                'page_count' => count($categories),
                'total_count' => $filteredCategoriesQuantity,
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

    /** @Route("api/expense-categories", methods={"POST"}, name="api_expense_categories_add") */
    public function add(Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'json') {
            return new JsonResponse([
                'errors' => sprintf('%s is not acceptable content type', $request->getContentType())
            ], 415);
        }

        $categoryData = json_decode($request->getContent(), true);
        $this->categoryValidator->validate($categoryData);

        if (!$this->categoryValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->categoryValidator->getErrors()
            ], 400);
        }

        $categoryData['author'] = $this->categoryManager->getCategoryAuthor();

        $this->categoryManager->save($this->categoryManager->createFromArray($categoryData));

        return new JsonResponse([
            'message' => 'The category has been added successfully!'
        ], 201);
    }

    /** @Route("api/expense-categories/{id}", methods={"GET"}, name="api_expense_categories_get") */
    public function getBy(int $id): JsonResponse
    {
        $category = $this->entityManager->find(ExpenseCategory::class, $id);

        $this->categoryValidator->validateCategoryExists($category);

        if (!$this->categoryValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->categoryValidator->getErrors()
            ], 400);
        }

        return new JsonResponse([
            $this->categoryManager->getAsArray($category)
        ]);
    }

    /** @Route("api/expense-categories/{id}", methods={"DELETE"}, name="api_expense_categories_delete") */
    public function delete(int $id): JsonResponse
    {
        $category = $this->entityManager->find(ExpenseCategory::class, $id);

        $this->categoryValidator->validateCategoryExists($category);
        $this->categoryValidator->validateCategoryHasExpenses($category);

        if (!$this->categoryValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->categoryValidator->getErrors()
            ], 400);
        }

        $this->categoryManager->delete($category);

        return new JsonResponse([
            'message' => 'The category has been deleted successfully!'
        ]);
    }

    /** @Route("api/expense-categories/{id}", methods={"PATCH"}, name="api_expense_categories_update") */
    public function update(int $id, Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'json') {
            return new JsonResponse([
                'errors' => sprintf('%s is not acceptable content type', $request->getContentType())
            ], 415);
        }

        $categoryData = json_decode($request->getContent(), true);

        $category = $this->entityManager->find(ExpenseCategory::class, $id);
        $this->categoryValidator->validateCategoryExists($category);

        if ($this->categoryValidator->hasArrayKey('name', $categoryData)) {
            $this->categoryValidator->validateName($categoryData);
        }

        if ($this->categoryValidator->hasArrayKey('description', $categoryData)) {
            $this->categoryValidator->validateDescription($categoryData);
        }

        if (!$this->categoryValidator->isValid()) {
            return new JsonResponse([
                'errors' => $this->categoryValidator->getErrors()
            ], 400);
        }

        $this->categoryManager->update($category, $categoryData);

        return new JsonResponse([
            'message' => 'The category has been updated successfully!'
        ]);
    }
}