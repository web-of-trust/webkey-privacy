<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenPGP\OpenPGP;

/**
 * Domain model
 *
 * @package  App
 * @category Models
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class Domain extends Model
{
    use HasFactory;

    protected $table = 'domains';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'organization',
        'description',
        'key_data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'has_key',
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

        static::retrieved(static function (self $model) {
            $model->has_key = !empty($model->key_data);
        });

        static::created(static function (self $model) {
            self::createCertificate($model);
        });

        static::updating(static function (self $model) {
            if ($model->isDirty('key_data')) {
                self::createCertificate($model);
            }
            unset($model->has_key);
        });
    }

    private static function createCertificate(self $model): void
    {
        if (!empty($model->key_data)) {
            $publicKey = OpenPGP::readPrivateKey(
                $model->key_data
            )->toPublic();

            Certificate::create([
                'domain_id' => $model->id,
                'fingerprint' => $publicKey->getFingerprint(true),
                'key_id' => $publicKey->getKeyID(true),
                'key_algorithm' => $publicKey->getKeyAlgorithm()->value,
                'key_strength' => $publicKey->getKeyStrength(),
                'key_version' => $publicKey->getVersion(),
                'key_data' => $publicKey->armor(),
                'primary_user' => $publicKey->getPrimaryUser()?->getUserID(),
                'creation_time' => $publicKey->getCreationTime(),
                'expiration_time' => $publicKey->getExpirationTime(),
            ]);
        }
    }
}
