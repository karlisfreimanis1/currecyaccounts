<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    public $timestamps = false;

    protected $fillable = [
        'userId',
        'currencyId',
        'currentBalance',
    ];

    /**
     * @return BelongsTo
     */
    public function accountCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currencyId');
    }
}
