<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Models;

use App\Support\Helper;
use Illuminate\Database\Eloquent\{
    Factories\HasFactory,
    Relations\BelongsTo,
    Relations\HasOne,
    Model,
};
use ParagonIE\ConstantTime\Base32;

/**
 * OpenPGP certificate model
 *
 * @package  App
 * @category Models
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class OpenPGPCertificate extends Model
{
    use HasFactory;

    protected $table = 'openpgp_certificates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'fingerprint',
        'key_id',
        'wkd_hash',
        'key_algorithm',
        'key_strength',
        'key_version',
        'is_revoked',
        'primary_user',
        'key_data',
        'creation_time',
        'expiration_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_revoked' => 'boolean',
    ];

    public array $subkeys = [];

    /**
     * The "boot" method of the model.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(static function (self $model) {
            $parts = explode(
                '@', Helper::extractEmail($model->primary_user)
            );
            if (empty($model->wkd_hash) && !empty($parts[0])) {
                $model->wkd_hash = Base32::encode(
                    hash('sha1', $parts[0], true)
                );
            }
        });

        static::updating(static function (self $model) {
            unset($model->subkeys);
        });
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id')->withDefault();
    }

    public function revocation(): HasOne
    {
        return $this->hasOne(OpenPGPRevocation::class, 'certificate_id', 'id');
    }
}
