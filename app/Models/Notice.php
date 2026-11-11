<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    public const AUDIENCES = ['all', 'teachers', 'students', 'parents'];

    protected $fillable = ['title', 'body', 'audience', 'published_by', 'publish_at', 'is_active'];

    protected function casts(): array
    {
        return [
            'publish_at' => 'datetime',
            'is_active'  => 'boolean',
        ];
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function scopeVisibleTo($query, User $user)
    {
        return $query->where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->where('audience', 'all')
                    ->orWhere('audience', $user->role.'s'); // teachers | students | parents
            })
            ->where(function ($q) {
                $q->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            });
    }
}
