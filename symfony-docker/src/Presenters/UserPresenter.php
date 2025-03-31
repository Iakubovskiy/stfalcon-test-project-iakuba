<?php
declare(strict_types=1);


namespace App\Presenters;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UserPresenter
{

    public function __construct(private readonly EntityManagerInterface $entityManager){}
    public function present(User $user): array
    {
        return [
          'id' => $user->getId(),
          'email' => $user->getEmail(),
          'roles' => $user->getRoles(),
          'phone' => $user->getPhone(),
          'name' => $user->getName(),
        ];
    }

    public function presentPaginatedList(array $paginationData): array
    {
        return [
           'result' => array_map(fn (User $user) => $this->present($user), $paginationData['result']),
           'total' => $paginationData['total'],
           'offset' => $paginationData['offset'],
           'limit' => $paginationData['limit'],
        ];
    }

    public function presentCustomer(Customer $user): array
    {
        $propertyTypePresenter = new PropertyTypePresenter();
        $currencyPresenter = new CurrencyPresenter($this->entityManager);
        $pricePresenter = new PricePresenter($currencyPresenter);
        $locationPresenter = new LocationPresenter();
        $sizePresenter = new SizePresenter();
        $statusPresenter = new PropertyStatusPresenter();
        $propertyPresenter = new PropertyPresenter(
            propertyTypePresenter: $propertyTypePresenter,
            pricePresenter: $pricePresenter,
            locationPresenter: $locationPresenter,
            sizePresenter: $sizePresenter,
            statusPresenter: $statusPresenter,
            userPresenter: $this,
        );
        $result = $this->present($user);
        $result['favorites'] = $propertyPresenter->presentProperties($user->getFavoriteProperties());
        return $result;
    }
}
