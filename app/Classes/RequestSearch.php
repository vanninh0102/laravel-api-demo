<?php

namespace App\Classes;

use App\Enums\RequestSearchType;

class RequestSearch
{
    public string $name;
    public string $column;
    public string $type;

    public function __construct(string $name, string $column, RequestSearchType $type)
    {
        $this->name = $name;
        $this->column = $column;
        $this->type = $type->name;
    }
}
