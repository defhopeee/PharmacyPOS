<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'createdat';
    const UPDATED_AT = 'updatedat';
    const DELETED_AT = 'deletedat';

    protected $fillable = ['name', 'description'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'categoryid');
    }
}
