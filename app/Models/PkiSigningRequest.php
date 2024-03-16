<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany,
};
use Illuminate\Database\Eloquent\Model;

/**
 * Pki signing request model
 *
 * @package  App
 * @category Models
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class PkiSigningRequest extends Model
{
    use HasFactory;

    protected $table = 'pki_signing_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'common_name',
        'country_name',
        'province_name',
        'locality_name',
        'organization_name',
        'organization_unit_name',
        'key_algorithm',
        'key_strength',
        'key_data',
        'csr_data',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id')->withDefault();
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(PkiCertificate::class);
    }
}
