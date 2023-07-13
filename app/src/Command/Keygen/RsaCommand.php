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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

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
    private const DEFAULT_KEY_SIZE = 2048;
    private const KEY_SIZES = [
        2048,
        2560,
        3072,
        3584,
        4096,
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setHelp('This command allows you to generate an rsa key.')
             ->addArgument(
                'size', InputArgument::OPTIONAL, 'The size of the key.', self::DEFAULT_KEY_SIZE
             );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $keySize = (int) $input->getArgument('size');
        if (!in_array($keySize, self::KEY_SIZES)) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Please select the key size (defaults to 2048)',
                self::KEY_SIZES,
                0,
            );
            $question->setErrorMessage('Key size %s is invalid.');
            $keySize = $helper->ask($input, $output, $question);
        }

        if (!empty($this->signKeyFile) && !empty($this->verifyKeyFile)) {
            $rsaKey = RSA::createKey($keySize);
            file_put_contents(
                $this->signKeyFile,
                $rsaKey->toString('PKCS8')
            );
            file_put_contents(
                $this->verifyKeyFile,
                $rsaKey->getPublicKey()->toString('PKCS8')
            );
        }
        else {
            return $this->missingParameter($output);
        }

        $output->writeln('Rsa key successfully generated!');
        return 0;
    }
}
