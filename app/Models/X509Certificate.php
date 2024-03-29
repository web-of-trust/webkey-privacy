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
    Model,
};

/**
 * X509 certificate model
 *
 * @package  App
 * @category Models
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class X509Certificate extends Model
{
    use HasFactory;

    protected $table = 'x509_certificates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'signing_request_id',
        'serial_number',
        'subject_dn',
        'issuer_dn',
        'not_before',
        'not_after',
        'certificate_data',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id')->withDefault();
    }

    public function csr(): BelongsTo
    {
        return $this->belongsTo(X509SigningRequest::class, 'signing_request_id')->withDefault();
    }
}
