<?php

namespace JeroenNoten\LaravelAdminLte;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JeroenNoten\LaravelAdminLte\Console\AdminLteInstallCommand;
use JeroenNoten\LaravelAdminLte\Console\AdminLtePluginCommand;
use JeroenNoten\LaravelAdminLte\Console\AdminLteStatusCommand;
use JeroenNoten\LaravelAdminLte\Console\AdminLteUpdateCommand;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use JeroenNoten\LaravelAdminLte\Http\ViewComposers\AdminLteComposer;

class AdminLteServiceProvider extends BaseServiceProvider
{
    /**
     * Array with the available layout components.
     *
     * @var array
     */
    protected $layoutComponents = [
        Components\Layout\NavbarDarkmodeWidget::class,
        Components\Layout\NavbarNotification::class,
    ];

    /**
     * Array with the available form components.
     *
     * @var array
     */
    protected $formComponents = [
        Components\Form\Button::class,
        Components\Form\DateRange::class,
        Components\Form\Input::class,
        Components\Form\InputColor::class,
        Components\Form\InputDate::class,
        Components\Form\InputFile::class,
        Components\Form\InputSlider::class,
        Components\Form\InputSwitch::class,
        Components\Form\Select::class,
        Components\Form\Select2::class,
        Components\Form\SelectBs::class,
        Components\Form\Textarea::class,
        Components\Form\TextEditor::class,
    ];

    /**
     * Array with the available tool components.
     *
     * @var array
     */
    protected $toolComponents = [
        Components\Tool\Datatable::class,
        Components\Tool\Modal::class,
    ];

    /**
     * Array with the available widget components.
     *
     * @var array
     */
    protected $widgetComponents = [
        Components\Widget\Alert::class,
        Components\Widget\Callout::class,
        Components\Widget\Card::class,
        Components\Widget\InfoBox::class,
        Components\Widget\ProfileColItem::class,
        Components\Widget\ProfileRowItem::class,
        Components\Widget\ProfileWidget::class,
        Components\Widget\Progress::class,
        Components\Widget\SmallBox::class,
    ];

    /**
     * Register the package services.
     *
     * @return void
     */
    public function register()
    {
        // Bind a singleton instance of the AdminLte class into the service
        // container.

        $this->app->singleton(AdminLte::class, function (Container $app) {
            return new AdminLte(
                $app['config']['adminlte.filters'],
                $app['events'],
                $app
            );
        });
    }

    /**
     * Bootstrap the package's services.
     *
     * @return void
     */
    public function boot(Factory $view, Dispatcher $events, Repository $config)
    {
        $this->loadViews();
        $this->loadTranslations();
        $this->loadConfig();
        $this->registerCommands();
        $this->registerViewComposers($view);
        $this->registerMenu($events, $config);
        $this->loadComponents();
        $this->loadRoutes();
    }

    /**
     * Load the package views.
     *
     * @return void
     */
    private function loadViews()
    {
        $viewsPath = $this->packagePath('resources/views');
        $this->loadViewsFrom($viewsPath, 'adminlte');
    }

    /**
     * Load the package translations.
     *
     * @return void
     */
    private function loadTranslations()
    {
        $translationsPath = $this->packagePath('resources/lang');
        $this->loadTranslationsFrom($translationsPath, 'adminlte');
    }

    /**
     * Load the package config.
     *
     * @return void
     */
    private function loadConfig()
    {
        $configPath = $this->packagePath('config/adminlte.php');
        $this->mergeConfigFrom($configPath, 'adminlte');
    }

    /**
     * Get the absolute path to some package resource.
     *
     * @param string $path The relative path to the resource
     * @return string
     */
    private function packagePath($path)
    {
        return __DIR__."/../$path";
    }

    /**
     * Register the package's artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->commands([
            AdminLteInstallCommand::class,
            AdminLteStatusCommand::class,
            AdminLteUpdateCommand::class,
            AdminLtePluginCommand::class,
        ]);
    }

    /**
     * Register the package's view composers.
     *
     * @return void
     */
    private function registerViewComposers(Factory $view)
    {
        $view->composer('adminlte::page', AdminLteComposer::class);
    }

    /**
     * Register the menu events handlers.
     *
     * @return void
     */
    private static function registerMenu(Dispatcher $events, Repository $config)
    {
        // Register a handler for the BuildingMenu event, this handler will add
        // the menu defined on the config file to the menu builder instance.

        $events->listen(
            BuildingMenu::class,
            function (BuildingMenu $event) use ($config) {
                $menu = $config->get('adminlte.menu', []);
                $menu = is_array($menu) ? $menu : [];
                $event->menu->add(...$menu);
            }
        );
    }

    /**
     * Load the blade view components.
     *
     * @return void
     */
    private function loadComponents()
    {
        // Support of x-components is only available for Laravel >= 7.x
        // versions. So, we check if we can load components.

        $canLoadComponents = method_exists(
            'Illuminate\Support\ServiceProvider',
            'loadViewComponentsAs'
        );

        if (! $canLoadComponents) {
            return;
        }

        // Load all the blade-x components.

        $components = array_merge(
            $this->layoutComponents,
            $this->formComponents,
            $this->toolComponents,
            $this->widgetComponents
        );

        $this->loadViewComponentsAs('adminlte', $components);
    }

    /**
     * Load the package web routes.
     *
     * @return void
     */
    private function loadRoutes()
    {
        $routesCfg = [
            'as' => 'adminlte.',
            'prefix' => 'adminlte',
            'middleware' => ['web'],
        ];

        Route::group($routesCfg, function () {
            $routesPath = $this->packagePath('routes/web.php');
            $this->loadRoutesFrom($routesPath);
        });
    }
}
