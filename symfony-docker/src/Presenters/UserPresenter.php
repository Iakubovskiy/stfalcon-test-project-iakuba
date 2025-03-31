<?php
declare(strict_types=1);


namespace App\Presenters;

use App\Entity\User;

final readonly class UserPresenter
{
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
//        dd($paginationData);
        return [
           'result' => array_map(fn (User $user) => $this->present($user), $paginationData['result']),
           'total' => $paginationData['total'],
           'offset' => $paginationData['offset'],
           'limit' => $paginationData['limit'],
        ];
    }
}
