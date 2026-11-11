<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title', 'author', 'isbn', 'category',
        'total_copies', 'available_copies', 'shelf', 'published_year',
    ];

    protected function casts(): array
    {
        return [
            'total_copies'     => 'integer',
            'available_copies' => 'integer',
        ];
    }

    public function issues()
    {
        return $this->hasMany(BookIssue::class);
    }

    public function activeIssues()
    {
        return $this->hasMany(BookIssue::class)->where('status', 'issued');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('author', 'like', "%{$term}%")
                ->orWhere('isbn', 'like', "%{$term}%")
                ->orWhere('category', 'like', "%{$term}%");
        });
    }
}
