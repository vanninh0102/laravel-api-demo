<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

interface ProductInterface extends RepositoryInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<array-key, \Illuminate\Database\Eloquent\Builder|\App\Models\Product>
     */
    public function getAllByUser(User $user);

    /**
     * @return \Illuminate\Database\Eloquent\Model|\App\Models\Product>|null
     */
    public function findByUser(string|int $id, User $user);

    public function getBaseQueryIndex(User $user = null): Builder;

    public function getSearchColumns(): array;

    public function searchFilter(array $filters, User $user): Builder;
}
