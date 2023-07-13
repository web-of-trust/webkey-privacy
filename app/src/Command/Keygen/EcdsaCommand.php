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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Ecdsa keygen command class
 * 
 * @package  App
 * @category Command
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
#[AsCommand(
    name: 'keygen:ecdsa',
    description: 'Generate a new ecdsa key.'
)]
final class EcdsaCommand extends KeygenCommand
{
    private const P_256_CURVE = 'P-256';
    private const P_384_CURVE = 'P-384';
    private const P_521_CURVE = 'P-521';

    private const CURVES = [
        self::P_256_CURVE,
        self::P_384_CURVE,
        self::P_521_CURVE,
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setHelp('This command allows you to generate an ecdsa key.')
             ->addArgument(
                'curve', InputArgument::OPTIONAL, 'The curve of the key.', self::P_256_CURVE
             );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $curve = $input->getArgument('curve');
        if (!in_array($curve, self::CURVES)) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Please select the curve (defaults to P-256)',
                self::CURVES,
                0,
            );
            $question->setErrorMessage('The curve %s is invalid.');
            $curve = $helper->ask($input, $output, $question);
        }

        if (!empty($this->signKeyFile) && !empty($this->verifyKeyFile)) {
            $curveName = match ($curve) {
                self::P_384_CURVE => 'secp384r1',
                self::P_521_CURVE => 'secp521r1',
                default => 'secp256r1',
            };
            $ecKey = EC::createKey($curveName);

            file_put_contents(
                $this->signKeyFile,
                $ecKey->toString('PKCS8')
            );
            file_put_contents(
                $this->verifyKeyFile,
                $ecKey->getPublicKey()->toString('PKCS8')
            );
        }
        else {
            return $this->missingParameter($output);
        }

        $output->writeln('Ecdsa key successfully generated!');
        return 0;
    }
}
