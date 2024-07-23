<?php

namespace Simasten\Platform;

use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Monoland\Modular\Console\Commands\PlatformInstall;
use Monoland\Modular\Console\Commands\PlatformMakeJob;
use Monoland\Modular\Console\Commands\PlatformMakeSeed;
use Monoland\Modular\Console\Commands\PlatformMakeEvent;
use Monoland\Modular\Console\Commands\PlatformMakeModel;
use Monoland\Modular\Console\Commands\PlatformMakeExport;
use Monoland\Modular\Console\Commands\PlatformMakeImport;
use Monoland\Modular\Console\Commands\PlatformMakeModule;
use Monoland\Modular\Console\Commands\PlatformMakePolicy;
use Monoland\Modular\Console\Commands\PlatformModuleList;
use Monoland\Modular\Console\Commands\PlatformModuleSeed;
use Monoland\Modular\Console\Commands\PlatformMakeCommand;
use Monoland\Modular\Console\Commands\PlatformMakeReplica;
use Monoland\Modular\Console\Commands\PlatformModuleClone;
use Monoland\Modular\Console\Commands\PlatformMakeListener;
use Monoland\Modular\Console\Commands\PlatformMakeResource;
use Monoland\Modular\Console\Commands\PlatformModuleDelete;
use Monoland\Modular\Console\Commands\PlatformMakeMigration;
use Monoland\Modular\Console\Commands\PlatformModuleInstall;
use Monoland\Modular\Console\Commands\PlatformModuleMigrate;
use Monoland\Modular\Console\Commands\PlatformMakeController;
use Monoland\Modular\Console\Commands\PlatformMakeNotification;

class ModularServiceProvider extends ServiceProvider
{
    /**
     * boot function
     *
     * @return void
     */
    public function boot(): void
    {
        /** Disable wrapping of the outer-most resource array. */
        JsonResource::withoutWrapping();

        /** Prevent model relationships from being lazy loaded. */
        Model::preventLazyLoading();

        /** Prevent non-fillable attributes from being silently discarded. */
        Model::preventSilentlyDiscardingAttributes();

        /** Register Artisan Commands */
        $this->registerArtisanCommands();

        /** Boot and Register Modules */
        $this->bootAndRegisterModules();

        /** Publish asset, config and frontend-components */
        $this->publishes([
            __DIR__.'/../.eslintrc.js' => base_path('.eslintrc.js'),
            __DIR__.'/../config/database.php' => config_path('database.php'),
            __DIR__.'/../config/cors.php' => config_path('cors.php'),
            __DIR__.'/../modules' => base_path('modules'),
            __DIR__.'/../package.json' => base_path('package.json'),
            __DIR__.'/../routes' => base_path('routes'),
            __DIR__.'/../seeders' => database_path('seeders'),
            __DIR__.'/../vite.config.mjs' => base_path('vite.config.mjs'),
        ], 'monoland-config');

        $this->publishes([
            __DIR__.'/../frontend' => resource_path(),
        ], 'monoland-frontend');

        $this->publishes([
            __DIR__.'/../assets' => resource_path('assets'),
            __DIR__.'/../avatars' => resource_path('avatars'),
        ], 'monoland-assets');
    }

    /**
     * register function
     *
     * @return void
     */
    public function register(): void
    {
        URL::forceScheme('https');

        Fortify::ignoreRoutes();
    }

    /**
     * registerArtisanCommands function
     *
     * @return void
     */
    protected function registerArtisanCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PlatformInstall::class,
                PlatformMakeCommand::class,
                PlatformMakeController::class,
                PlatformMakeEvent::class,
                PlatformMakeExport::class,
                PlatformMakeImport::class,
                PlatformMakeJob::class,
                PlatformMakeListener::class,
                PlatformMakeMigration::class,
                PlatformMakeModel::class,
                PlatformMakeModule::class,
                PlatformMakeNotification::class,
                PlatformMakePolicy::class,
                PlatformMakeReplica::class,
                PlatformMakeResource::class,
                PlatformMakeSeed::class,
                PlatformModuleClone::class,
                PlatformModuleDelete::class,
                PlatformModuleInstall::class,
                PlatformModuleList::class,
                PlatformModuleMigrate::class,
                PlatformModuleSeed::class
            ]);
        }
    }

    /**
     * bootAndRegisterModules function
     *
     * @return void
     */
    protected function bootAndRegisterModules(): void
    {
        $modules = Cache::has('modules') && count(Cache::get('modules')) > 0 ?
            Cache::get('modules') :
            $this->scanModulesFolder();

        foreach ($modules as $module) {
            if (!File::exists(base_path('modules' . DIRECTORY_SEPARATOR . str($module->name)->lower()))) {
                continue;
            }

            if ($module->providers && is_array($module->providers)) {
                foreach ($module->providers as $provider) {
                    if (class_exists($provider)) {
                        with(new $provider($this->app))->boot();
                        with(new $provider($this->app))->register();
                    }
                }
            } else {
                if (class_exists($module->providers)) {
                    with(new $module->providers($this->app))->boot();
                    with(new $module->providers($this->app))->register();
                }
            }
        }
    }

    /**
     * scanModulesFolder function
     *
     * @return array
     */
    protected function scanModulesFolder(): array
    {
        Cache::forget('modules');

        return Cache::rememberForever('modules', function () {
            $modules = [];

            /** Scan Module System */
            $folder = base_path('modules' . DIRECTORY_SEPARATOR . 'system');
            $json_path = $folder . DIRECTORY_SEPARATOR . 'module.json';

            if (File::exists($json_path)) {
                $content                = file_get_contents($json_path);
                $json_data              = json_decode($content, true);
                $module_name            = $json_data['name'];
                $json_data['path']      = $folder;
                $modules[$module_name]  = json_decode(json_encode($json_data), false);
            }

            /** Scan All-Module Except System */
            $folders = glob(base_path('modules') . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);

            foreach ($folders as $folder) {
                $json_path = $folder . DIRECTORY_SEPARATOR . 'module.json';

                if (!File::exists($json_path)) {
                    continue;
                }

                $content                = file_get_contents($json_path);
                $json_data              = json_decode($content, true);
                $module_name            = $json_data['name'];

                if ($module_name === 'System') {
                    continue;
                }

                $json_data['path']      = $folder;
                $modules[$module_name]  = json_decode(json_encode($json_data), false);
            }

            return count($modules) > 0 ? $modules : [];
        });
    }
}
