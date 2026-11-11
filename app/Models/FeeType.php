<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    protected $fillable = ['name', 'amount', 'frequency', 'is_active'];

    protected function casts(): array
    {
        return [
            'amount'    => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
