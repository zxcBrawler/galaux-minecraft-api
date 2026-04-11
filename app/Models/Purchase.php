<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id_purchase
 * @property int $user_id
 * @property int $product_id
 * @property int $quantity
 * @property float $price_paid
 * @property Carbon $purchased_at
 * * @property-read User $user
 * @property-read Product $product
 * * @method static Builder|Purchase query()
 */
class Purchase extends Model
{
    protected $table = 'purchases';
    protected $primaryKey = 'id_purchase';

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'price_paid',
        'purchased_at'
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'purchased_at' => 'datetime',
        'quantity' => 'integer',
        'user_id' => 'integer',
        'product_id' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id_product');
    }
}
