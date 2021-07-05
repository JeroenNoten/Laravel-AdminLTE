<?php

namespace JeroenNoten\LaravelAdminLte\Events;

use JeroenNoten\LaravelAdminLte\Http\Controllers\DarkModeController;

class DarkModeWasToggled
{
    /**
     * An instance of the dark mode controller.
     *
     * @var DarkModeController
     */
    public $darkMode;

    /**
     * Create a new event instance.
     *
     * @param DarkModeController $ctrl
     */
    public function __construct(DarkModeController $ctrl)
    {
        $this->darkMode = $ctrl;
    }
}
