<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command\Keygen;

use Minicli\Command\CommandController;
use phpseclib3\Crypt\Random;

/**
 * Hmac keygen command controller class
 * 
 * @package  App
 * @category Command
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class HmacController extends CommandController
{
    private const MINIMUM_KEY_SIZE = 256;

    /**
     * {@inheritdoc}
     */
    public function handle(): void
    {
        $this->display('Hmac key generate');

        $keySize = $this->hasParam('key-size') ? (int) $this->getParam('key-size') : self::MINIMUM_KEY_SIZE;
        if ($keySize < self::MINIMUM_KEY_SIZE) {
            throw new \UnexpectedValueException(
                'Hmac key size must be at least ' . self::MINIMUM_KEY_SIZE . ' bits.'
            );
        }
        file_put_contents(
            $this->getParam('key-file'),
            Random::string(($keySize + 7) >> 3)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function required(): array
    {
        return [
            'key-file',
        ];
    }
}
