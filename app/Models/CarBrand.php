<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarBrand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'created_by'];

    public function CarsModels(): HasMany
    {
        return $this->hasMany(CarModel::class, 'brand_id', 'id');
    }
}
