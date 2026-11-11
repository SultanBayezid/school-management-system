<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public const METHODS = ['cash', 'bank', 'bkash', 'nagad', 'card', 'other'];

    protected $fillable = [
        'invoice_id', 'amount', 'paid_date', 'method',
        'receipt_no', 'received_by', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'   => 'decimal:2',
            'paid_date' => 'date',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
