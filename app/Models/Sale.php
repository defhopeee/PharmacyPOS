<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'createdat';
    const UPDATED_AT = 'updatedat';
    const DELETED_AT = 'deletedat';

    protected $fillable = [
        'reference',
        'userid',
        'total',
        'paid',
        'balance',
        'method',
        'mpesareceipt',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'paid' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        // Keep the seller's name on historical sales even after the staff
        // account is archived (soft-deleted).
        return $this->belongsTo(User::class, 'userid')->withTrashed();
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'saleid');
    }
}
