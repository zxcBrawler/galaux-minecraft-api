<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerImage extends Model
{
    protected $fillable = ['server_id', 'path', 'is_main'];

    public function getPathAttribute($value): string
    {
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        return asset('storage/' . $value);
    }
}
