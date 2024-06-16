<?php

declare(strict_types=1);

namespace App\Traits;

use App\Classes\MyHelper;
use App\Classes\RequestSearch;
use App\Enums\RequestSearchType;
use Illuminate\Database\Eloquent\Builder;

trait SearchFilterTrait
{
    /**
     * @param array<RequestSearch> $columnSearch
     */
    public function scopeSearchFilter(Builder $query, array $filters, array $columnSearch = []): Builder
    {
        foreach ($filters as $field => $value) {
            $parts = explode(':', $field); // Split field by colon (:)
            $column = $parts[0]; // Column name
            $operator = isset($parts[1]) ? $parts[1] : 'eq'; // Operator (default: eq)

            if (!isset($columnSearch[$column])) continue;

            $requestSearch = $columnSearch[$column];

            if ($requestSearch->type == RequestSearchType::string->name) {
                $this->searchByText($query, $requestSearch->column, $value, $operator);
            }
            if ($requestSearch->type == RequestSearchType::date->name) {
                $this->searchByDate($query, $requestSearch->column, $value, $operator);
            }
            if ($requestSearch->type == RequestSearchType::number->name) {
                $this->searchByNumber($query, $requestSearch->column, $value, $operator);
            }
        }

        return $query;
    }

    protected function searchByText(Builder $query, string $column, string $value, string $operator): Builder
    {
        switch ($operator) {
            case 'like':
                return $query->where($column, 'like', "%{$value}%");
            case 'eq': // Equal
                return $query->where($column, '=', $value);
            default:
                return $query;
        }
    }
    protected function searchByNumber(Builder $query, string $column, string $value, string $operator): Builder
    {
        switch ($operator) {
            case 'eq': // Equal
                return $query->where($column, '=', $value);
            case 'gt': // Greater than
                return $query->where($column, '>', $value);
            case 'ge': // Greater than or equal to
                return $query->where($column, '>=', $value);
            case 'lt': // Less than
                return $query->where($column, '<', $value);
            case 'le': // Less than or equal to
                return $query->where($column, '<=', $value);
                // ... Add additional operators as needed
            default:
                return $query;
        }
    }
    protected function searchByDate(Builder $query, string $column, string $value, string $operator): Builder
    {
        switch ($operator) {
            case 'eq': // Equal
                return $query->where($column, '=', $value);
            case 'gt': // Greater than
                return $query->where($column, '>', $value);
            case 'ge': // Greater than or equal to
                return $query->where($column, '>=', $value);
            case 'lt': // Less than
                return $query->where($column, '<', $value);
            case 'le': // Less than or equal to
                return $query->where($column, '<=', $value);
                // ... Add additional operators as needed
            default:
                return $query;
        }
    }
}
