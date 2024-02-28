<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $table = 'certificates';

    protected $fillable = [
        'domain_id',
        'fingerprint',
        'key_id',
        'key_algorithm',
        'primary_user',
        'key_strength',
        'key_version',
        'certify_by',
        'public_key',
        'creation_time',
        'expiration_time',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }
}
