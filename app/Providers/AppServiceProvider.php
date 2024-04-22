<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Providers;

use App\Settings\OpenPgpSettings;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use OpenPGP\Common\Config;

/**
 * App service provider
 *
 * @package  App
 * @category Providers
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ((bool) env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
        $settings = app(OpenPgpSettings::class);
        Config::setPreferredHash($settings->preferredHash());
        Config::setPreferredSymmetric($settings->preferredSymmetric());
    }
}
