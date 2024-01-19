<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Application;

use Symfony\Component\Console\Application;

/**
 * Console application runner class
 * Run the console application.
 *
 * @package  App
 * @category Application
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
final class ConsoleApplicationRunner extends ApplicationRunner
{
    /**
     * {@inheritdoc}
     */
    public function run(): void
    {
        exit(
            $this->getContainer()->get(Application::class)->run()
        );
    }
}
