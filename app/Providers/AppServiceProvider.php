<?php

namespace App\Providers;

use App\Interfaces\ServerInterface;
use App\Models\Server;
use App\Observers\ServerObserver;
use App\Services\Cache\CachingServerService;
use App\Services\ServerService;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ServerInterface::class, function () {
            $baseService = new ServerService();
            return new CachingServerService($baseService);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });
        Server::observe(ServerObserver::class);
    }
}
