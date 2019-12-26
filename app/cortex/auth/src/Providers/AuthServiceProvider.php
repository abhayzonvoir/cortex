<?php

declare(strict_types=1);

namespace Cortex\Auth\Providers;

use Bouncer;
use Cortex\Auth\Models\Role;
use Illuminate\Http\Request;
use Cortex\Auth\Models\Admin;
use Cortex\Auth\Models\Member;
use Illuminate\Routing\Router;
use Cortex\Auth\Models\Ability;
use Cortex\Auth\Models\Manager;
use Cortex\Auth\Models\Session;
use Cortex\Auth\Models\Guardian;
use Cortex\Auth\Models\Socialite;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;
use Illuminate\Contracts\Events\Dispatcher;
use Cortex\Auth\Console\Commands\SeedCommand;
use Cortex\Auth\Http\Middleware\Reauthenticate;
use Cortex\Auth\Console\Commands\InstallCommand;
use Cortex\Auth\Console\Commands\MigrateCommand;
use Cortex\Auth\Console\Commands\PublishCommand;
use Cortex\Auth\Console\Commands\RollbackCommand;
use Cortex\Auth\Http\Middleware\UpdateLastActivity;
use Cortex\Auth\Http\Middleware\AuthenticateSession;
use Illuminate\Database\Eloquent\Relations\Relation;
use Cortex\Auth\Http\Middleware\RedirectIfAuthenticated;

class AuthServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        SeedCommand::class => 'command.cortex.auth.seed',
        InstallCommand::class => 'command.cortex.auth.install',
        MigrateCommand::class => 'command.cortex.auth.migrate',
        PublishCommand::class => 'command.cortex.auth.publish',
        RollbackCommand::class => 'command.cortex.auth.rollback',
    ];

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register(): void
    {
        // Merge config
        $this->app['config']->set('auth.model', config('cortex.auth.models.member'));
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'cortex.auth');

        // Register console commands
        ! $this->app->runningInConsole() || $this->registerCommands();

        // Bind eloquent models to IoC container
        $this->app->singleton('cortex.auth.session', $sessionModel = $this->app['config']['cortex.auth.models.session']);
        $sessionModel === Session::class || $this->app->alias('cortex.auth.session', Session::class);

        $this->app->singleton('cortex.auth.socialite', $socialiteModel = $this->app['config']['cortex.auth.models.socialite']);
        $socialiteModel === Socialite::class || $this->app->alias('cortex.auth.socialite', Socialite::class);

        $this->app->singleton('cortex.auth.admin', $adminModel = $this->app['config']['cortex.auth.models.admin']);
        $adminModel === Admin::class || $this->app->alias('cortex.auth.admin', Admin::class);

        $this->app->singleton('cortex.auth.member', $memberModel = $this->app['config']['cortex.auth.models.member']);
        $memberModel === Member::class || $this->app->alias('cortex.auth.member', Member::class);

        $this->app->singleton('cortex.auth.manager', $managerModel = $this->app['config']['cortex.auth.models.manager']);
        $managerModel === Manager::class || $this->app->alias('cortex.auth.manager', Manager::class);

        $this->app->singleton('cortex.auth.guardian', $guardianModel = $this->app['config']['cortex.auth.models.guardian']);
        $guardianModel === Guardian::class || $this->app->alias('cortex.auth.guardian', Guardian::class);

        $this->app->singleton('cortex.auth.role', $roleModel = $this->app['config']['cortex.auth.models.role']);
        $roleModel === Role::class || $this->app->alias('cortex.auth.role', Role::class);

        $this->app->singleton('cortex.auth.ability', $abilityModel = $this->app['config']['cortex.auth.models.ability']);
        $abilityModel === Ability::class || $this->app->alias('cortex.auth.ability', Ability::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @return void
     */
    public function boot(Router $router, Dispatcher $dispatcher): void
    {
        // Attach request macro
        $this->attachRequestMacro();

        // Map bouncer models
        Bouncer::useRoleModel(config('cortex.auth.models.role'));
        Bouncer::useAbilityModel(config('cortex.auth.models.ability'));

        // Map bouncer tables (users, roles, abilities tables are set through their models)
        Bouncer::tables([
            'permissions' => config('cortex.auth.tables.permissions'),
            'assigned_roles' => config('cortex.auth.tables.assigned_roles'),
        ]);

        // Bind route models and constrains
        $router->pattern('role', '[a-zA-Z0-9-_]+');
        $router->pattern('ability', '[a-zA-Z0-9-_]+');
        $router->pattern('session', '[a-zA-Z0-9-_]+');
        $router->pattern('admin', '[a-zA-Z0-9-_]+');
        $router->pattern('member', '[a-zA-Z0-9-_]+');
        $router->pattern('manager', '[a-zA-Z0-9-_]+');
        $router->model('role', config('cortex.auth.models.role'));
        $router->model('admin', config('cortex.auth.models.admin'));
        $router->model('member', config('cortex.auth.models.member'));
        $router->model('manager', config('cortex.auth.models.manager'));
        $router->model('guardian', config('cortex.auth.models.guardian'));
        $router->model('ability', config('cortex.auth.models.ability'));
        $router->model('session', config('cortex.auth.models.session'));

        // Map relations
        Relation::morphMap([
            'role' => config('cortex.auth.models.role'),
            'admin' => config('cortex.auth.models.admin'),
            'member' => config('cortex.auth.models.member'),
            'manager' => config('cortex.auth.models.manager'),
            'guardian' => config('cortex.auth.models.guardian'),
            'ability' => config('cortex.auth.models.ability'),
        ]);

        // Load resources
        $this->loadRoutesFrom(__DIR__.'/../../routes/web/adminarea.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web/frontarea.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web/managerarea.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web/tenantarea.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'cortex/auth');
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'cortex/auth');

        $this->app->runningInConsole() || $dispatcher->listen('accessarea.ready', function ($accessarea) {
            ! file_exists($menus = __DIR__."/../../routes/menus/{$accessarea}.php") || require $menus;
            ! file_exists($breadcrumbs = __DIR__."/../../routes/breadcrumbs/{$accessarea}.php") || require $breadcrumbs;
        });

        // Publish Resources
        ! $this->app->runningInConsole() || $this->publishesLang('cortex/auth', true);
        ! $this->app->runningInConsole() || $this->publishesViews('cortex/auth', true);
        ! $this->app->runningInConsole() || $this->publishesConfig('cortex/auth', true);
        ! $this->app->runningInConsole() || $this->publishesMigrations('cortex/auth', true);

        // Register attributes entities
        ! app()->bound('rinvex.attributes.entities') || app('rinvex.attributes.entities')->push('admin');
        ! app()->bound('rinvex.attributes.entities') || app('rinvex.attributes.entities')->push('member');
        ! app()->bound('rinvex.attributes.entities') || app('rinvex.attributes.entities')->push('manager');

        // Override middlware
        $this->overrideMiddleware($router);

        // Register menus
        $this->registerMenus();

        // Share current user instance with all views
        $this->app['view']->composer('*', function ($view) {
            ! config('rinvex.tenants.active') || $view->with('currentTenant', config('rinvex.tenants.active'));
            $view->with('currentUser', auth()->guard(request()->route('guard'))->user());
        });
    }

    /**
     * Register console commands.
     *
     * @return void
     */
    protected function attachRequestMacro(): void
    {
        Request::macro('attemptUser', function (string $guard = null) {
            $twofactor = $this->session()->get('cortex.auth.twofactor');

            return auth()->guard($guard)->getProvider()->retrieveById($twofactor['user_id']);
        });
    }

    /**
     * Register menus.
     *
     * @return void
     */
    protected function registerMenus(): void
    {
        $this->app['rinvex.menus.presenters']->put('account.sidebar', \Cortex\Auth\Presenters\AccountSidebarMenuPresenter::class);
    }

    /**
     * Override middleware.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    protected function overrideMiddleware(Router $router): void
    {
        // Append middleware to the 'web' middlware group
        $router->pushMiddlewareToGroup('web', AuthenticateSession::class);
        $router->pushMiddlewareToGroup('web', UpdateLastActivity::class);

        // Override route middleware on the fly
        $router->aliasMiddleware('reauthenticate', Reauthenticate::class);
        $router->aliasMiddleware('guest', RedirectIfAuthenticated::class);
    }
}
