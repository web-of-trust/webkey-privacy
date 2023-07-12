<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * Licensed under GNU Affero General Public License v3.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Application;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Kernel interface
 *
 * @package  App
 * @category Application
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
interface KernelInterface
{
    /**
     * Get psr container
     *
     * @return ContainerInterface
     */
    function getContainer(): ContainerInterface;

    /**
     * Start application and serve user requests.
     *
     * @return void
     */
	function serve(?ServerRequestInterface $request = null): void;

    /**
     * Run a command from CLI.
     *
     * @return void
     */
    function runCommand(array $argv = []): void;
}
