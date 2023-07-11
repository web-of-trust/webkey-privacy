<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use Minicli\Command\CommandController;

/**
 * Abstract keygen command controller class
 * 
 * @package  App
 * @category Command
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
abstract class KeygenController extends CommandController
{
    /**
     * {@inheritdoc}
     */
    public function required(): array
    {
        return [
            'sign-key-file',
            'verify-key-file',
        ];
    }
}
