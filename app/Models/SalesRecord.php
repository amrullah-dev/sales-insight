<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'sale_date',
        'quantity',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'quantity' => 'integer',
        ];
    }

    /**
     * Get the product that owns the sales record.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope query to apply optional analytical filters.
     *
     * @param Builder $query
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int|null $productId
     * @return Builder
     */
    public function scopeFilterRange(Builder $query, ?string $startDate = null, ?string $endDate = null, ?int $productId = null): Builder
    {
        if ($startDate !== null) {
            $query->where('sale_date', '>=', $startDate);
        }

        if ($endDate !== null) {
            $query->where('sale_date', '<=', $endDate);
        }

        if ($productId !== null) {
            $query->where('product_id', $productId);
        }

        return $query;
    }
}
