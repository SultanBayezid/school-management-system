<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_name', 'school_address', 'phone', 'email',
        'website', 'principal_name', 'established_year',
    ];

    public static function current(): self
    {
        return static::query()->firstOrFail();
    }
}
