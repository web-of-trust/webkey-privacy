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
    HasOne,
};
use Illuminate\Database\Eloquent\Model;
use ParagonIE\ConstantTime\Base32;

/**
 * Certificate model
 *
 * @package  App
 * @category Models
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class Certificate extends Model
{
    use HasFactory;

    const EMAIL_PATTERN = '/[\w\.-]+@[\w\.-]+\.\w{2,4}/';

    protected $table = 'certificates';

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
                '@', self::extractEmail($model->primary_user)
            );
            if (empty($model->wkd_hash) && !empty($parts[0])) {
                $model->wkd_hash = Base32::encode(
                    hash('sha1', $parts[0], true)
                );
            }
        });
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    public function revocation(): HasOne
    {
        return $this->hasOne(Revocation::class, 'certificate_id', 'id');
    }

    private static function extractEmail(string $userId): string
    {
        if (preg_match(self::EMAIL_PATTERN, $userId, $matches)) {
            return $matches[0];
        };
        return '';
    }
}
