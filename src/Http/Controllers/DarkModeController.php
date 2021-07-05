<?php

namespace JeroenNoten\LaravelAdminLte\Http\Controllers;

use JeroenNoten\LaravelAdminLte\Events\DarkModeWasToggled;

class DarkModeController extends Controller
{
    /**
     * The key to use for save dark mode preference on the session.
     *
     * @var string
     */
    protected $sessionKey = 'adminlte_dark_mode';

    /**
     * Toggle the dark mode preference.
     *
     * @return void
     */
    public function toggle()
    {
        // Store the new dark mode preference on the session. This way, we can
        // keep the dark mode preference over multiple requests.

        session([$this->sessionKey => ! $this->isEnabled()]);

        // Dispatch an event to notify this situation. This way, a listener may
        // read the new dark mode preference using the controller, and update
        // that preference on a database or another tool for persist data.

        event(new DarkModeWasToggled($this));
    }

    /**
     * Check if the dark mode is currently enabled or not.
     *
     * @return bool
     */
    public function isEnabled()
    {
        // First, check if dark mode preference is available on the session.

        if (! is_null(session($this->sessionKey, null))) {
            return session($this->sessionKey);
        }

        // Otherwise, fallback to the default package configuration preference.

        return (bool) config('adminlte.layout_dark_mode', false);
    }

    /**
     * Enables the dark mode.
     *
     * @return void
     */
    public function enable()
    {
        session([$this->sessionKey => true]);
    }

    /**
     * Disables the dark mode.
     *
     * @return void
     */
    public function disable()
    {
        session([$this->sessionKey => false]);
    }
}
