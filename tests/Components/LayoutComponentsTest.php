<?php

use JeroenNoten\LaravelAdminLte\Components;

class LayoutComponentsTest extends TestCase
{
    /**
     * Get package providers.
     */
    protected function getPackageProviders($app)
    {
        // Register our service provider into the Laravel's application.

        return ['JeroenNoten\LaravelAdminLte\AdminLteServiceProvider'];
    }

    /**
     * Return array with the available blade components.
     */
    protected function getComponents()
    {
        $base = 'adminlte::components.layout';

        return [
            "{$base}.navbar-notification-link" => new Components\Layout\NavbarNotificationLink('id', 'icon'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | General components tests.
    |--------------------------------------------------------------------------
    */

    public function testAllComponentsRender()
    {
        foreach ($this->getComponents() as $viewName => $component) {
            $view = $component->render();
            $this->assertEquals($view->getName(), $viewName);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Individual layout components tests.
    |--------------------------------------------------------------------------
    */

    public function testNavbarNotificationLinkClasses()
    {
        // Test basic component.

        $component = new Components\Layout\NavbarNotificationLink('id', 'icon');

        $iClass = $component->makeIconClass();
        $bClass = $component->makeBadgeClass();

        $this->assertStringContainsString('icon', $iClass);
        $this->assertStringContainsString('badge', $bClass);
        $this->assertStringContainsString('navbar-badge', $bClass);

        // Test advanced component.
        // $id, $icon, $iconColor, $badgeLabel, $badgeColor

        $component = new Components\Layout\NavbarNotificationLink(
            'id', 'icon', 'danger', null, 'primary'
        );

        $iClass = $component->makeIconClass();
        $bClass = $component->makeBadgeClass();
        $uUrl = $component->makeUpdateUrl();

        $this->assertStringContainsString('text-danger', $iClass);
        $this->assertStringContainsString('badge-primary', $bClass);
        $this->assertEquals(null, $uUrl);
    }

    public function testNavbarNotificationLinkUrls()
    {
        // Register a test route.

        Route::any('test/url')->name('test.url');

        // Test using basic update cfg url.
        // $id, $icon, $iconColor, $badgeLabel, $badgeColor, $updateCfg

        $updateCfg = ['url' => 'test/url', 'period' => 10];
        $component = new Components\Layout\NavbarNotificationLink(
            'id', 'icon', null, null, null, $updateCfg
        );

        $uPeriod = $component->makeUpdatePeriod();
        $uUrl = $component->makeUpdateUrl();

        $this->assertEquals(10000, $uPeriod);
        $this->assertStringContainsString('test/url', $uUrl);

        // Test using update url with params.

        $updateCfg = ['url' => ['test/url', ['p1', 'p2']]];
        $component = new Components\Layout\NavbarNotificationLink(
            'id', 'icon', null, null, null, $updateCfg
        );

        $uPeriod = $component->makeUpdatePeriod();
        $uUrl = $component->makeUpdateUrl();

        $this->assertEquals(0, $uPeriod);
        $this->assertStringContainsString('test/url/p1/p2', $uUrl);

        // Test using basic update route.

        $updateCfg = ['route' => 'test.url'];
        $component = new Components\Layout\NavbarNotificationLink(
            'id', 'icon', null, null, null, $updateCfg
        );

        $uUrl = $component->makeUpdateUrl();
        $this->assertStringContainsString('test/url', $uUrl);

        // Test using update route with params.

        $updateCfg = ['route' => ['test.url', ['param1' => 'p1']]];
        $component = new Components\Layout\NavbarNotificationLink(
            'id', 'icon', null, null, null, $updateCfg
        );

        $uUrl = $component->makeUpdateUrl();
        $this->assertStringContainsString('test/url?param1=p1', $uUrl);

        // Test using update route with invalid params.

        $updateCfg = ['route' => ['test.url', 'invalid_param']];
        $component = new Components\Layout\NavbarNotificationLink(
            'id', 'icon', null, null, null, $updateCfg
        );

        $uUrl = $component->makeUpdateUrl();
        $this->assertStringContainsString('test/url', $uUrl);

        // Test using update route with invalid config.

        $updateCfg = ['route' => 66];
        $component = new Components\Layout\NavbarNotificationLink(
            'id', 'icon', null, null, null, $updateCfg
        );

        $uUrl = $component->makeUpdateUrl();
        $this->assertEquals(null, $uUrl);
    }
}