<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'symbol', 'description', 'conversion_factor'];


    public static function Convert(float $quantity, Unit $fromUnit, Unit $toUnit): float
    {
        if ($fromUnit->id === $toUnit->id) {
            return $quantity;
        }

        $fromRate = (float) $fromUnit->conversion_factor ?? 1;
        $toRate = (float) $toUnit->conversion_factor ?? 1;

        return ($quantity * $fromRate) / $toRate;
    }



}
