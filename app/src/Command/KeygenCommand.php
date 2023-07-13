<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Abstract keygen command class
 * 
 * @package  App
 * @category Command
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
abstract class KeygenCommand extends Command
{
    protected const SIGN_KEY_FILE_OPTION   = 'sign-key-file';
    protected const VERIFY_KEY_FILE_OPTION = 'verify-key-file';

    protected ?string $signKeyFile;
    protected ?string $verifyKeyFile;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->addOption(
            self::SIGN_KEY_FILE_OPTION, null, InputOption::VALUE_REQUIRED, 'The sign key file.'
        )->addOption(
            self::VERIFY_KEY_FILE_OPTION, null, InputOption::VALUE_REQUIRED, 'The verify key file.'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->signKeyFile = $input->getOption(self::SIGN_KEY_FILE_OPTION);
        $this->verifyKeyFile = $input->getOption(self::VERIFY_KEY_FILE_OPTION);

        $helper = $this->getHelper('question');
        if (empty($this->signKeyFile)) {
            $this->signKeyFile = $helper->ask(
                $input,
                $output,
                new Question('Please enter the sign key file: '),
            );
        }
        if (empty($this->verifyKeyFile)) {
            $this->verifyKeyFile = $helper->ask(
                $input,
                $output,
                new Question('Please enter the verify key file: '),
            );
        }
    }

    /**
     * Output missing parameter.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function missingParameter(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $style->error(
            self::SIGN_KEY_FILE_OPTION
            . ' or '
            . self::VERIFY_KEY_FILE_OPTION
            . ' parameter is missing!'
        );
        return 1;
    }
}
