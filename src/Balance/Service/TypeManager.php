<?php

namespace App\Balance\Service;

use App\Application\Service\UserManager;
use App\Balance\Hydrator\BalanceHydrator;
use App\Balance\Hydrator\TypeHydratorStrategy;
use App\Balance\Model\IncomeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TypeManager
{
    private $hydrator;

    private $hydrationStrategy;

    private $entityManager;

    private $userManager;

    public function __construct(
        BalanceHydrator $hydrator,
        TypeHydratorStrategy $strategy,
        EntityManagerInterface $entityManager,
        UserManager $userManager
    ) {
        $this->hydrator = $hydrator;
        $this->hydrationStrategy = $strategy;
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
    }

    public function getAsArray(IncomeType $type): array
    {
        return $this->hydrator->extract($type, $this->hydrationStrategy);
    }

    public function createFromArray(array $typeValues): IncomeType
    {
        return $this->hydrator->hydrate($typeValues, $this->hydrationStrategy);
    }

    public function save(IncomeType $type): void
    {
        $this->entityManager->persist($type);
        $this->entityManager->flush();
    }

    public function delete(IncomeType $type): void
    {
        $this->entityManager->remove($type);
        $this->entityManager->flush();
    }

    public function update(IncomeType $type, array $updateValues): void
    {
        if (isset($updateValues['name'])) {
            $type->setName($updateValues['name']);
        }

        if (isset($updateValues['description'])) {
            $type->setDescription($updateValues['description']);
        }

        $this->save($type);
    }

    public function getFiltered(array $params): array
    {
        $author = $this->getTypeAuthor();

        if (!$author) {
            return [];
        }

        $types = $this->entityManager->getRepository(IncomeType::class)->findByAuthorAndFilters($author->getId(), $params);

        return $this->hydrator->extractSeveral($types, $this->hydrationStrategy);
    }

    public function countFiltered(array $params): int
    {
        $author = $this->getTypeAuthor();

        if (!$author) {
            return 0;
        }

        return $this->entityManager->getRepository(IncomeType::class)->findByAuthorAndFilters($author->getId(), $params, true);
    }

    public function getTypeAuthor(): ?UserInterface
    {
        return $this->userManager->getCurrentUser();
    }
}