<?php

namespace App\Repositories;

use App\Classes\RequestSearch;
use App\Enums\RequestSearchType;
use App\Models\Store;
use App\Models\User;
use App\Repositories\Interfaces\StoreInterface;
use App\Traits\SearchFilterTrait;
use Illuminate\Database\Eloquent\Builder;

class StoreRepository implements StoreInterface
{
    use SearchFilterTrait;

    public function getAll()
    {
        return Store::query()->get();
    }

    public function find(string|int $id)
    {
        return Store::query()->find($id);
    }

    public function create(array $attributes = [])
    {
        return Store::create($attributes);
    }

    public function update(string|int $id, $attributes = [])
    {
        $store = $this->find($id);
        if ($store) $store->update($attributes);
        return $store;
    }

    public function delete(string|int $id)
    {
        $this->find($id)->delete();
    }

    public function getAllByUser(User $user)
    {
        return Store::query()->where('user_id', $user->id)->get();
    }

    public function findByUser(string|int $id, User $user)
    {
        return Store::query()->where('user_id', $user->id)->find($id);
    }

    public function getBaseQueryIndex(User $user = null): Builder
    {
        $baseQuery = Store::join('users', 'users.id', '=', 'stores.user_id')
            ->select(
                'stores.id',
                'stores.name',
                'stores.description',
                'stores.created_at',
                'stores.updated_at',
                'stores.user_id',
                'users.name as user_name',
            )
            ->orderByDesc('stores.created_at');

        if ($user) $baseQuery->where('stores.user_id', $user->id);

        return $baseQuery;
    }

    public function getSearchColumns(): array
    {
        return [
            'name' => new RequestSearch('name', 'stores.name', RequestSearchType::string),
            'description' => new RequestSearch('description', 'stores.description', RequestSearchType::string),
            'created_at' => new RequestSearch('created_at', 'stores.created_at', RequestSearchType::date),
            'updated_at' => new RequestSearch('updated_at', 'stores.updated_at', RequestSearchType::date),
            'user_id' => new RequestSearch('user_id', 'users.id', RequestSearchType::number),
            'user_name' => new RequestSearch('user_name', 'users.name', RequestSearchType::string),
        ];
    }

    public function searchFilter(array $filters, User $user): Builder
    {
        $baseQuery = $this->getBaseQueryIndex($user);
        $columnsSearch = $this->getSearchColumns();

        $stores = $this->scopeSearchFilter($baseQuery, $filters, $columnsSearch);

        return $stores;
    }
}
