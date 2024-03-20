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
    Relations\HasOne,
    Model,
};

/**
 * OpenPGP revocation model
 *
 * @package  App
 * @category Models
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class OpenPGPRevocation extends Model
{
    use HasFactory;

    protected $table = 'openpgp_revocations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'certificate_id',
        'revoke_by',
        'tag',
        'reason',
    ];

    public function certificate(): HasOne
    {
        return $this->hasOne(OpenPGPCertificate::class, 'certificate_id');
    }
}
