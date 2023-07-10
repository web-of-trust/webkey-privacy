<?php declare(strict_types=1);

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
