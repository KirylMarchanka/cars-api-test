<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CarModel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'brand_id', 'created_by'];

    public function getCarBrand(): HasOne
    {
        return $this->hasOne(CarBrand::class, 'id', 'brand_id');
    }
}
