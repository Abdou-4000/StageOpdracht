<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class UmamiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::directive('umami', function () {
            return '<?php if(env("UMAMI_ENABLED", true) && 
                    !(auth()->check() && 
                        (auth()->user()->hasRole("teacher") ||
                        (auth()->user()->hasRole("admin") || 
                        auth()->user()->hasRole("super_admin")))): ?>
            <script defer src="<?php echo env("UMAMI_SCRIPT_URL", "http://localhost:3000/script.js"); ?>" data-website-id="<?php echo env("UMAMI_WEBSITE_ID", "721df71c-618e-4ff2-a1ea-7fb94380b054"); ?>"></script>
            <?php endif; ?>';
        });
    }
}
