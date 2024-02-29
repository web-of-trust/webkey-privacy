<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Export ceritificates to command
 *
 * @package  App
 * @category Console
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ExportToWkd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webkey:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to export ceritificates to webkey directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
