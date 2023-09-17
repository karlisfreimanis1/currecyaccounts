<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    public $timestamps = false;

    protected $fillable = [
        'accountIdFrom',
        'accountIdTo',
        'valueFrom',
        'valueTo',
        'status',
        'timeCreated',
        'timeProcessed',
    ];

    public function accountFrom(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'accountIdFrom');
    }

    public function accountTo(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'accountIdTo');
    }
}
