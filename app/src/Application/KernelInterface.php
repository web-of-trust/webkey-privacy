<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Application;

/**
 * Kernel interface
 *
 * @package  App
 * @category Application
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
interface KernelInterface
{
    const DEVELOPMENT_MODE = 'development';
    const PRODUCTION_MODE  = 'production';

	static function serve(): void;
}
