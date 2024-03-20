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
    Relations\HasOne,
    Model,
};
use OpenPGP\OpenPGP;

/**
 * OpenPGP personal key model
 *
 * @package  App
 * @category Models
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class OpenPGPPersonalKey extends Model
{
    use HasFactory;

    protected $table = 'openpgp_personal_keys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'certificate_id',
        'is_revoked',
        'key_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_revoked' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'key_data',
    ];

    /**
     * The "boot" method of the model.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(static function (self $model) {
            if (!empty($model->key_data)) {
                $publicKey = OpenPGP::readPrivateKey(
                    $model->key_data
                )->toPublic();

                $parts = explode('@', User::find($model->user_id)->email);
                $domain = Domain::firstWhere('name', $parts[1] ?? '');

                $model->certificate_id = OpenPGPCertificate::create([
                    'domain_id' => $domain->id,
                    'fingerprint' => $publicKey->getFingerprint(true),
                    'key_id' => $publicKey->getKeyID(true),
                    'key_algorithm' => $publicKey->getKeyAlgorithm()->value,
                    'key_strength' => $publicKey->getKeyStrength(),
                    'key_version' => $publicKey->getVersion(),
                    'key_data' => $publicKey->armor(),
                    'primary_user' => $publicKey->getPrimaryUser()?->getUserID(),
                    'creation_time' => $publicKey->getCreationTime(),
                    'expiration_time' => $publicKey->getExpirationTime(),
                ])->id;
            }
        });

        static::updating(static function (self $model) {
            if ($model->isDirty('key_data')) {
                $publicKey = OpenPGP::readPrivateKey(
                    $model->key_data
                )->toPublic();

                $model->certificate()->update([
                    'fingerprint' => $publicKey->getFingerprint(true),
                    'key_id' => $publicKey->getKeyID(true),
                    'key_algorithm' => $publicKey->getKeyAlgorithm()->value,
                    'key_strength' => $publicKey->getKeyStrength(),
                    'key_version' => $publicKey->getVersion(),
                    'key_data' => $publicKey->armor(),
                    'is_revoked' => $model->is_revoked,
                    'primary_user' => $publicKey->getPrimaryUser()?->getUserID(),
                    'creation_time' => $publicKey->getCreationTime(),
                    'expiration_time' => $publicKey->getExpirationTime(),
                ]);
            }
        });
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(OpenPGPCertificate::class, 'id', 'certificate_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
