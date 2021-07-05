<?php

use JeroenNoten\LaravelAdminLte\Components;

class LayoutComponentsTest extends TestCase
{
    /**
     * Return array with the available blade components.
     */
    protected function getComponents()
    {
        $base = 'adminlte::components.layout';

        return [
            "{$base}.navbar-notification" => new Components\Layout\NavbarNotification('id', 'icon'),
            "{$base}.navbar-darkmode-widget" => new Components\Layout\NavbarDarkmodeWidget(),
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

    public function testNavbarNotificationClasses()
    {
        // Test basic component.

        $component = new Components\Layout\NavbarNotification('id', 'icon');

        $iClass = $component->makeIconClass();
        $bClass = $component->makeBadgeClass();
        $liClass = $component->makeListItemClass();
        $aAttrs = $component->makeAnchorDefaultAttrs();

        $this->assertStringContainsString('icon', $iClass);
        $this->assertStringContainsString('badge', $bClass);
        $this->assertStringContainsString('navbar-badge', $bClass);
        $this->assertStringContainsString('nav-item', $liClass);
        $this->assertStringContainsString('nav-link', $aAttrs['class']);

        // Test advanced component.
        // $id, $icon, $iconColor, $badgeLabel, $badgeColor, $updateCfg,
        // $enableDropdownMode, $dropdownFooterLabel

        $component = new Components\Layout\NavbarNotification(
            'id', 'icon', 'danger', null, 'primary', null, true, null
        );

        $iClass = $component->makeIconClass();
        $bClass = $component->makeBadgeClass();
        $liClass = $component->makeListItemClass();
        $aAttrs = $component->makeAnchorDefaultAttrs();
        $uUrl = $component->makeUpdateUrl();

        $this->assertStringContainsString('text-danger', $iClass);
        $this->assertStringContainsString('badge-primary', $bClass);
        $this->assertStringContainsString('nav-item', $liClass);
        $this->assertStringContainsString('dropdown', $liClass);
        $this->assertStringContainsString('nav-link', $aAttrs['class']);
        $this->assertStringContainsString('dropdown', $aAttrs['data-toggle']);
        $this->assertEquals(null, $uUrl);
    }

    public function testNavbarNotificationUrls()
    {
        // Register a test route.

        Route::any('test/url')->name('test.url');

        // Test using basic update cfg url.
        // $id, $icon, $iconColor, $badgeLabel, $badgeColor, $updateCfg

        $updateCfg = ['url' => 'test/url', 'period' => 10];
        $component = new Components\Layout\NavbarNotification(
            'id', 'icon', null, null, null, $updateCfg
        );

        $uPeriod = $component->makeUpdatePeriod();
        $uUrl = $component->makeUpdateUrl();

        $this->assertEquals(10000, $uPeriod);
        $this->assertStringContainsString('test/url', $uUrl);

        // Test using update url with params.

        $updateCfg = ['url' => ['test/url', ['p1', 'p2']]];
        $component = new Components\Layout\NavbarNotification(
            'id', 'icon', null, null, null, $updateCfg
        );

        $uPeriod = $component->makeUpdatePeriod();
        $uUrl = $component->makeUpdateUrl();

        $this->assertEquals(0, $uPeriod);
        $this->assertStringContainsString('test/url/p1/p2', $uUrl);

        // Test using basic update route.

        $updateCfg = ['route' => 'test.url'];
        $component = new Components\Layout\NavbarNotification(
            'id', 'icon', null, null, null, $updateCfg
        );

        $uUrl = $component->makeUpdateUrl();
        $this->assertStringContainsString('test/url', $uUrl);

        // Test using update route with params.

        $updateCfg = ['route' => ['test.url', ['param1' => 'p1']]];
        $component = new Components\Layout\NavbarNotification(
            'id', 'icon', null, null, null, $updateCfg
        );

        $uUrl = $component->makeUpdateUrl();
        $this->assertStringContainsString('test/url?param1=p1', $uUrl);

        // Test using update route with invalid params.

        $updateCfg = ['route' => ['test.url', 'invalid_param']];
        $component = new Components\Layout\NavbarNotification(
            'id', 'icon', null, null, null, $updateCfg
        );

        $uUrl = $component->makeUpdateUrl();
        $this->assertStringContainsString('test/url', $uUrl);

        // Test using update route with invalid config.

        $updateCfg = ['route' => 66];
        $component = new Components\Layout\NavbarNotification(
            'id', 'icon', null, null, null, $updateCfg
        );

        $uUrl = $component->makeUpdateUrl();
        $this->assertEquals(null, $uUrl);
    }

    public function testNavbarDarkmodeWidgetClasses()
    {
        // Test basic component with darkmode config disabled.

        config(['adminlte.layout_dark_mode' => null]);

        $component = new Components\Layout\NavbarDarkmodeWidget(
            null, null, 'color-off', 'color-on'
        );

        $iClass = $component->makeIconClass();

        $this->assertStringContainsString('far fa-moon', $iClass);
        $this->assertStringContainsString('text-color-off', $iClass);

        // Test advanced component with darkmode config disabled.

        $component = new Components\Layout\NavbarDarkmodeWidget(
            'icon-off', 'icon-on', 'color-off', 'color-on'
        );

        $iClass = $component->makeIconClass();

        $this->assertStringContainsString('icon-off', $iClass);
        $this->assertStringContainsString('text-color-off', $iClass);

        // Test basic component with darkmode config enabled.

        config(['adminlte.layout_dark_mode' => true]);

        $component = new Components\Layout\NavbarDarkmodeWidget(
            null, null, 'color-off', 'color-on'
        );

        $iClass = $component->makeIconClass();

        $this->assertStringContainsString('fas fa-moon', $iClass);
        $this->assertStringContainsString('text-color-on', $iClass);

        // Test advanced component with darkmode config enabled.

        $component = new Components\Layout\NavbarDarkmodeWidget(
            'icon-off', 'icon-on', 'color-off', 'color-on'
        );

        $iClass = $component->makeIconClass();

        $this->assertStringContainsString('icon-on', $iClass);
        $this->assertStringContainsString('text-color-on', $iClass);
    }
}
