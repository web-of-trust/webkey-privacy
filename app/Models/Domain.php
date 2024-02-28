<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $table = 'domains';

    protected $fillable = [
        'name',
        'description',
        'public_key',
        'private_key',
        'dane_record',
    ];
}
