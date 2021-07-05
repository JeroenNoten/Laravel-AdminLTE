<?php

use JeroenNoten\LaravelAdminLte\Components;

class ToolComponentsTest extends TestCase
{
    /**
     * Return array with the available blade components.
     */
    protected function getComponents()
    {
        $base = 'adminlte::components.tool';

        return [
            "{$base}.datatable" => new Components\Tool\Datatable('id', []),
            "{$base}.modal"     => new Components\Tool\Modal('id'),
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
    | Individual tool components tests.
    |--------------------------------------------------------------------------
    */

    public function testDatatableComponent()
    {
        // Test basic component.
        $component = new Components\Tool\Datatable('id', []);

        $tClass = $component->makeTableClass();
        $this->assertStringContainsString('table', $tClass);

        // Test advanced component.
        // $id, $heads, $theme, $headTheme, $bordered, $hoverable, $striped,
        // $compressed, $withFooter, $footerTheme, $beautify, $withButtons,
        // $config
        $component = new Components\Tool\Datatable(
            'id', [], 'primary', null, true, true, true, true, null, null,
            null, true, null
        );

        $tClass = $component->makeTableClass();
        $this->assertStringContainsString('table-bordered', $tClass);
        $this->assertStringContainsString('table-hover', $tClass);
        $this->assertStringContainsString('table-striped', $tClass);
        $this->assertStringContainsString('table-sm', $tClass);
        $this->assertStringContainsString('table-primary', $tClass);
    }

    public function testModalComponent()
    {
        // Test basic component.

        $component = new Components\Tool\Modal('id');

        $mClass = $component->makeModalClass();
        $this->assertStringContainsString('modal', $mClass);
        $this->assertStringContainsString('fade', $mClass);

        $mdClass = $component->makeModalDialogClass();
        $this->assertStringContainsString('modal-dialog', $mdClass);

        $mhClass = $component->makeModalHeaderClass();
        $this->assertStringContainsString('modal-header', $mhClass);

        $cbClass = $component->makeCloseButtonClass();
        $this->assertStringContainsString('bg-secondary', $cbClass);

        // Test with all constructor arguments:
        // $id, $title, $icon, $size, $theme, $vCentered, $scrollable,
        // $staticBackdrop, $disableAnimations.

        $component = new Components\Tool\Modal(
            'id', 'title', null, 'lg', 'info', true, true, true, true
        );

        $mClass = $component->makeModalClass();
        $this->assertStringNotContainsString('fade', $mClass);

        $mdClass = $component->makeModalDialogClass();
        $this->assertStringContainsString('modal-dialog-centered', $mdClass);
        $this->assertStringContainsString('modal-dialog-scrollable', $mdClass);
        $this->assertStringContainsString('modal-lg', $mdClass);

        $mhClass = $component->makeModalHeaderClass();
        $this->assertStringContainsString('bg-info', $mhClass);

        $cbClass = $component->makeCloseButtonClass();
        $this->assertStringContainsString('bg-info', $cbClass);
    }
}
