<?php

use JeroenNoten\LaravelAdminLte\AdminLte;

class ServiceProviderTest extends TestCase
{
    public function testRegisterSingletonInstance()
    {
        // Check the instance of AdminLte resolver.

        $adminlte = $this->app->make(AdminLte::class);
        $this->assertInstanceOf(AdminLte::class, $adminlte);

        // Check that a singleton instance is registered.

        $this->assertSame($adminlte, $this->app->make(AdminLte::class));
    }

    public function testBootLoadViews()
    {
        // Check that the main views are loaded.

        $this->assertTrue(View::exists('adminlte::master'));
        $this->assertTrue(View::exists('adminlte::page'));
        $this->assertTrue(View::exists('adminlte::auth.auth-page'));
        $this->assertTrue(View::exists('adminlte::auth.login'));
        $this->assertTrue(View::exists('adminlte::auth.register'));
        $this->assertTrue(View::exists('adminlte::auth.verify'));
        $this->assertTrue(View::exists('adminlte::auth.passwords.confirm'));
        $this->assertTrue(View::exists('adminlte::auth.passwords.email'));
        $this->assertTrue(View::exists('adminlte::auth.passwords.reset'));
    }

    public function testBootLoadTranslations()
    {
        // Check that the main translations are loaded.

        $this->assertTrue(Lang::has('adminlte::adminlte.sign_in'));
        $this->assertTrue(Lang::has('adminlte::menu.main_navigation'));
    }

    public function testBootLoadConfig()
    {
        // Check that config values are loaded.

        $this->assertTrue(Config::has('adminlte.title'));
        $this->assertEquals('AdminLTE 3', Config::get('adminlte.title'));

        $this->assertTrue(Config::has('adminlte.menu'));
        $this->assertTrue(is_array(Config::get('adminlte.menu')));
    }

    public function testBootRegisterCommands()
    {
        // Check that the artisan commands are registered.

        $commands = Artisan::all();
        $this->assertTrue(Arr::has($commands, 'adminlte:install'));
        $this->assertTrue(Arr::has($commands, 'adminlte:status'));
        $this->assertTrue(Arr::has($commands, 'adminlte:update'));
        $this->assertTrue(Arr::has($commands, 'adminlte:plugins'));
    }

    public function testBootRegisterViewComposers()
    {
        // Check that the AdminLte instance exists on the page blade.

        $view = View::make('adminlte::page');
        View::callComposer($view);
        $viewData = $view->getData();

        $this->assertTrue(Arr::has($viewData, 'adminlte'));
    }

    public function testBootRegisterMenu()
    {
        $adminlte = $this->app->make(AdminLte::class);
        $menu = $adminlte->menu();

        $this->assertCount(12, $menu);
        $this->assertEquals('search', $menu[0]['text']);
    }

    public function testBootLoadComponents()
    {
        // Check that some of the blade component views are loaded.

        $this->assertTrue(View::exists('adminlte::components.form.input'));
        $this->assertTrue(View::exists('adminlte::components.form.select2'));
        $this->assertTrue(View::exists('adminlte::components.widget.card'));
        $this->assertTrue(View::exists('adminlte::components.tool.modal'));

        // Support of x-components is only available for Laravel >= 7.x
        // versions. So, check if we can test for component existence first.

        $canCheckComponents = method_exists(
            'Illuminate\Support\Facade\Blade',
            'getClassComponentAliases'
        );

        if (! $canCheckComponents) {
            return;
        }

        // Now, check that the class components aliases are registered.

        $aliases = Blade::getClassComponentAliases();

        $this->assertTrue(isset($aliases['adminlte-input']));
        $this->assertTrue(isset($aliases['adminlte-select2']));
        $this->assertTrue(isset($aliases['adminlte-card']));
        $this->assertTrue(isset($aliases['adminlte-modal']));
    }
}
