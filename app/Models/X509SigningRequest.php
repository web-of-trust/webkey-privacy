<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Factories\HasFactory,
    Relations\BelongsTo,
    Relations\HasMany,
    Model,
};

/**
 * X509 signing request model
 *
 * @package  App
 * @category Models
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class X509SigningRequest extends Model
{
    use HasFactory;

    protected $table = 'x509_signing_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'cn',
        'country',
        'state',
        'locality',
        'organization',
        'organization_unit',
        'key_algorithm',
        'key_strength',
        'with_password',
        'key_data',
        'csr_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'with_password' => 'boolean',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id')->withDefault();
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(X509Certificate::class, 'signing_request_id');
    }
}
