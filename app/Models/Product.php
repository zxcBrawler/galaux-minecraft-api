<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_product
 * @property int $shop_id
 * @property string $name
 * @property string|null $description
 * @property float $cost
 * @property int $bought_count
 * @property bool $is_active
 * @property Shop $shop
 * @method static Product create(array $attributes)
 * @method static Product findOrFail($id)
 */
class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $primaryKey = 'id_product';

    protected $fillable = [
        'shop_id', 'name', 'description', 'cost', 'bought_count',
        'icon_url', 'item_id', 'item_data', 'is_active', 'sort_order'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'item_data' => 'array',
        'is_active' => 'boolean',
        'bought_count' => 'integer'
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id_shop');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'product_id', 'id_product');
    }
}
