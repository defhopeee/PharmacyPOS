<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    const CREATED_AT = 'createdat';
    const UPDATED_AT = 'updatedat';

    protected $fillable = [
        'name',
        'categoryid',
        'supplierid',
        'barcode',
        'description',
        'price',
        'cost',
        'quantity',
        'reorder',
        'expiry',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
            'quantity' => 'integer',
            'reorder' => 'integer',
            'expiry' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categoryid');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplierid');
    }

    public function saleitems(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'productid');
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder;
    }

    public function isExpired(): bool
    {
        return $this->expiry !== null && $this->expiry->isPast();
    }
}
