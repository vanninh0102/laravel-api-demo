<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'amount', 'price', 'store_id'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function userOwned()
    {
        return $this->hasOneThrough(User::class, Store::class, 'store_id', 'user_id');
    }
}
