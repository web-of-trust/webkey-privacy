<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * Pki certificate model
 *
 * @package  App
 * @category Models
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class PkiCertificate extends Model
{
    use HasFactory;

    protected $table = 'pki_certificates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'signing_request_id',
        'subject_common_name',
        'issuer_common_name',
        'not_before',
        'not_after',
        'fingerprint',
        'certificate_data',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id')->withDefault();
    }

    public function csr(): BelongsTo
    {
        return $this->belongsTo(PkiSigningRequest::class, 'signing_request_id')->withDefault();
    }
}
