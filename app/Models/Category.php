<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nama',
    'deskripsi',  

])]
class Category extends Model
{
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
