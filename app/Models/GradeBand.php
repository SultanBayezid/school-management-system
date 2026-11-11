<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeBand extends Model
{
    protected $fillable = ['grade', 'min_percent', 'max_percent', 'grade_point', 'description'];

    protected function casts(): array
    {
        return [
            'min_percent' => 'decimal:2',
            'max_percent' => 'decimal:2',
            'grade_point' => 'decimal:2',
        ];
    }

    public function scopeForPercentage($query, float $percentage)
    {
        return $query->where('min_percent', '<=', $percentage)
            ->where('max_percent', '>=', $percentage);
    }
}
