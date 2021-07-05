<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use JeroenNoten\LaravelAdminLte\Events\DarkModeWasToggled;
use JeroenNoten\LaravelAdminLte\Http\Controllers\DarkModeController;

class DarkModeControllerTest extends TestCase
{
    public function testDarkModeFallbackToConfig()
    {
        $darkModeCtrl = new DarkModeController();

        // Test dark mode fallback to config disabled.

        config(['adminlte.layout_dark_mode' => null]);
        Session::forget('adminlte_dark_mode');

        $this->assertNull(session('adminlte_dark_mode', null));
        $this->assertFalse($darkModeCtrl->isEnabled());

        // Test dark mode fallback to config enabled.

        config(['adminlte.layout_dark_mode' => true]);
        Session::forget('adminlte_dark_mode');

        $this->assertNull(session('adminlte_dark_mode', null));
        $this->assertTrue($darkModeCtrl->isEnabled());
    }

    public function testToggleDarkModeFromConfig()
    {
        $darkModeCtrl = new DarkModeController();
        Event::fake();

        // Test dark mode is toggled to enabled state and event is emitted.

        config(['adminlte.layout_dark_mode' => null]);
        Session::forget('adminlte_dark_mode');

        $this->assertNull(session('adminlte_dark_mode', null));

        $darkModeCtrl->toggle();

        $this->assertTrue($darkModeCtrl->isEnabled());
        $this->assertTrue(session('adminlte_dark_mode', null));

        Event::assertDispatched(DarkModeWasToggled::class, function ($event) {
            return $event->darkMode->isEnabled() === true;
        });

        // Test dark mode is toggled to disabled state and event is emitted.

        config(['adminlte.layout_dark_mode' => true]);
        Session::forget('adminlte_dark_mode');

        $this->assertNull(session('adminlte_dark_mode', null));

        $darkModeCtrl->toggle();

        $this->assertFalse($darkModeCtrl->isEnabled());
        $this->assertFalse(session('adminlte_dark_mode', null));

        Event::assertDispatched(DarkModeWasToggled::class, function ($event) {
            return $event->darkMode->isEnabled() === false;
        });
    }

    public function testToggleDarkModeFromSession()
    {
        $darkModeCtrl = new DarkModeController();
        Event::fake();

        // Test dark mode is toggled to enabled state and event is emitted.

        Session::put('adminlte_dark_mode', false);

        $this->assertFalse(session('adminlte_dark_mode', null));

        $darkModeCtrl->toggle();

        $this->assertTrue($darkModeCtrl->isEnabled());
        $this->assertTrue(session('adminlte_dark_mode', null));

        Event::assertDispatched(DarkModeWasToggled::class, function ($event) {
            return $event->darkMode->isEnabled() === true;
        });

        // Test dark mode is toggled to disabled state and event is emitted.

        Session::put('adminlte_dark_mode', true);

        $this->assertTrue(session('adminlte_dark_mode', null));

        $darkModeCtrl->toggle();

        $this->assertFalse($darkModeCtrl->isEnabled());
        $this->assertFalse(session('adminlte_dark_mode', null));

        Event::assertDispatched(DarkModeWasToggled::class, function ($event) {
            return $event->darkMode->isEnabled() === false;
        });
    }

    public function testEnableDisableDarkModeManually()
    {
        $darkModeCtrl = new DarkModeController();

        // Test dark mode will be enabled.

        config(['adminlte.layout_dark_mode' => null]);
        Session::forget('adminlte_dark_mode');

        $this->assertNull(session('adminlte_dark_mode', null));
        $this->assertFalse($darkModeCtrl->isEnabled());

        $darkModeCtrl->enable();

        $this->assertTrue($darkModeCtrl->isEnabled());

        // Test dark mode will be disabled.

        $darkModeCtrl->disable();

        $this->assertFalse($darkModeCtrl->isEnabled());
    }
}
