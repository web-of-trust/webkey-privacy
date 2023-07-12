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
use phpseclib3\Crypt\RSA;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Rsa keygen command class
 * 
 * @package  App
 * @category Command
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
#[AsCommand(
    name: 'keygen:rsa',
    description: 'Generate a new rsa key.'
)]
final class RsaCommand extends KeygenCommand
{
    private const MINIMUM_KEY_SIZE = 2048;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setHelp('This command allows you to generate an rsa key.')
             ->addArgument(
                'size', InputArgument::OPTIONAL, 'The size of the key.', self::MINIMUM_KEY_SIZE
             );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $keySize = (int) $input->getArgument('size');
        if ($keySize < self::MINIMUM_KEY_SIZE) {
            throw new \UnexpectedValueException(
                'Rsa key size must be at least ' . self::MINIMUM_KEY_SIZE . ' bits.'
            );
        }

        $rsaKey = RSA::createKey($keySize);
        file_put_contents(
            $input->getOption('sign-key-file'),
            $rsaKey->toString('PKCS8')
        );
        file_put_contents(
            $input->getOption('verify-key-file'),
            $rsaKey->getPublicKey()->toString('PKCS8')
        );

        $output->writeln('Rsa key successfully generated!');
        return Command::SUCCESS;
    }
}
