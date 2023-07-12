<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command\Keygen;

use phpseclib3\Crypt\Random;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Hmac keygen command class
 * 
 * @package  App
 * @category Command
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
#[AsCommand(
    name: 'keygen:hmac',
    description: 'Generate a new hmac key.'
)]
final class HmacCommand extends Command
{
    private const MINIMUM_KEY_SIZE = 256;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setHelp('This command allows you to generate an hmac key.')
             ->addArgument(
                'size', InputArgument::OPTIONAL, 'The size of the key.', self::MINIMUM_KEY_SIZE
             )
             ->addOption('key-file', null, InputOption::VALUE_REQUIRED, 'The key file.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $keySize = (int) $input->getArgument('size');
        if ($keySize < self::MINIMUM_KEY_SIZE) {
            throw new \UnexpectedValueException(
                'Hmac key size must be at least ' . self::MINIMUM_KEY_SIZE . ' bits.'
            );
        }
        file_put_contents(
            $input->getOption('key-file'),
            Random::string(($keySize + 7) >> 3)
        );

        $output->writeln('Hmac key successfully generated!');
        return Command::SUCCESS;
    }
}
