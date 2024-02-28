<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Revocation extends Model
{
    use HasFactory;

    protected $table = 'revocations';

    protected $fillable = [
        'certificate_id',
        'revoke_by',
        'reason',
        'description',
    ];

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class, 'certificate_id');
    }
}
