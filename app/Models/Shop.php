<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/**
 * @property int $id_shop
 * @property int $server_id
 * @property-read Server $server
 * @property-read Collection|Product[] $products
 * @method static Shop create(array $attributes)
 */
class Shop extends Model
{
    protected $table = 'shops';
    protected $primaryKey = 'id_shop';

    protected $fillable = ['server_id'];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id', 'id_server');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'shop_id', 'id_shop');
    }
}
