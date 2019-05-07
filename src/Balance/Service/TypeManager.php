<?php

namespace App\Balance\Service;

use App\Application\Model\User;
use App\Application\Service\UserManager;
use App\Balance\Hydrator\BalanceHydrator;
use App\Balance\Hydrator\TypeHydratorStrategy;
use App\Balance\Model\IncomeType;
use App\Balance\Repository\IncomeTypeRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class TypeManager
{
    private $hydrator;

    private $hydrationStrategy;

    private $typeRepository;

    private $userManager;

    public function __construct(
        BalanceHydrator $hydrator,
        TypeHydratorStrategy $strategy,
        IncomeTypeRepository $typeRepository,
        UserManager $userManager
    ) {
        $this->hydrator = $hydrator;
        $this->hydrationStrategy = $strategy;
        $this->typeRepository = $typeRepository;
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

    public function update(IncomeType $type, array $updateValues): void
    {
        if (isset($updateValues['name'])) {
            $type->changeName($updateValues['name']);
        }

        if (isset($updateValues['description'])) {
            $type->changeDescription($updateValues['description']);
        }

        $this->typeRepository->save($type);
    }

    public function getFiltered(array $params): array
    {
        /** @var User $author */
        $author = $this->getTypeAuthor();

        if (!$author) {
            return [];
        }

        $types = $this->typeRepository->findByAuthorAndFilters($author->getId(), $params);

        return $this->hydrator->extractSeveral($types, $this->hydrationStrategy);
    }

    public function countFiltered(array $params): int
    {
        /** @var User $author */
        $author = $this->getTypeAuthor();

        if (!$author) {
            return 0;
        }

        return $this->typeRepository->findByAuthorAndFilters($author->getId(), $params, true);
    }

    public function getTypeAuthor(): ?UserInterface
    {
        return $this->userManager->getCurrentUser();
    }
}
