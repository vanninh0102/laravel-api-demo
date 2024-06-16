<?php

namespace App\Repositories;

use App\Classes\RequestSearch;
use App\Enums\RequestSearchType;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Interfaces\ProductInterface;
use App\Traits\SearchFilterTrait;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository implements ProductInterface
{
    use SearchFilterTrait;

    public function getAll()
    {
        return Product::query()->get();
    }

    public function find(string|int $id)
    {
        return Product::find($id);
    }

    public function create(array $attributes = [])
    {
        return Product::create($attributes);
    }

    public function update(string|int $id, $attributes = [])
    {
        $product = $this->find($id);
        if ($product) $product->update($attributes);
        return $product;
    }

    public function delete(string|int $id)
    {
        $this->find($id)->delete();
    }

    public function getAllByUser(User $user)
    {
        return $user->products()->get();
    }

    public function findByUser(string|int $id, User $user)
    {
        return $user->products()->find($id);
    }

    public function getBaseQueryIndex(User $user = null): Builder
    {
        $baseQuery = Product::join('stores', 'stores.id', 'products.store_id')
            ->join('users', 'users.id', '=', 'stores.user_id')
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.amount',
                'products.price',
                'products.created_at',
                'products.updated_at',
                'products.store_id',
                'stores.user_id',
                'users.name as user_name',
            )
            ->orderByDesc('products.created_at');

        if ($user) $baseQuery->where('stores.user_id', $user->id);

        return $baseQuery;
    }

    public function getSearchColumns(): array
    {
        return [
            'name' => new RequestSearch('name', 'products.name', RequestSearchType::string),
            'description' => new RequestSearch('description', 'products.description', RequestSearchType::string),
            'amount' => new RequestSearch('amount', 'products.amount', RequestSearchType::number),
            'price' => new RequestSearch('price', 'products.price', RequestSearchType::number),
            'created_at' => new RequestSearch('created_at', 'products.created_at', RequestSearchType::date),
            'updated_at' => new RequestSearch('updated_at', 'products.updated_at', RequestSearchType::date),
            'user_id' => new RequestSearch('user_id', 'stores.user_id', RequestSearchType::number),
            'user_name' => new RequestSearch('user_name', 'users.name', RequestSearchType::string),
        ];
    }

    public function searchFilter(array $filters, User $user): Builder
    {
        $baseQuery = $this->getBaseQueryIndex($user);
        $columnsSearch = $this->getSearchColumns();

        $products = $this->scopeSearchFilter($baseQuery, $filters, $columnsSearch);

        return $products;
    }
}
