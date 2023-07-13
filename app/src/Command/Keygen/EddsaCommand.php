<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command\Keygen;

use App\Command\KeygenCommand;
use phpseclib3\Crypt\EC;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Eddsa keygen command class
 * 
 * @package  App
 * @category Command
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
#[AsCommand(
    name: 'keygen:eddsa',
    description: 'Generate a new eddsa key.'
)]
final class EddsaCommand extends KeygenCommand
{
    private const CURVE_NAME = 'Ed25519';

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setHelp('This command allows you to generate an eddsa key.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!empty($this->signKeyFile) && !empty($this->verifyKeyFile)) {
            $edKey = EC::createKey(self::CURVE_NAME);
            file_put_contents(
                $this->signKeyFile,
                $edKey->toString('libsodium')
            );
            file_put_contents(
                $this->verifyKeyFile,
                $edKey->getPublicKey()->toString('libsodium')
            );
        }
        else {
            return $this->missingParameter($output);
        }

        $output->writeln('Eddsa key successfully generated!');
        return 0;
    }
}
