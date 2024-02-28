<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class PrivateKey extends Model
{
    use HasFactory;

    protected $table = 'private_keys';

    protected $fillable = [
        'user_id',
        'certificate_id',
        'private_key',
    ];

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class, 'certificate_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
