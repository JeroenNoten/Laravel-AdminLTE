<?php

namespace JeroenNoten\LaravelAdminLte;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\View;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use JeroenNoten\LaravelAdminLte\Menu\Builder;
use JeroenNoten\LaravelAdminLte\Helpers\MenuItemHelper;

class AdminLte
{
    protected $menu;

    protected $filters;

    protected $events;

    protected $container;

    /**
     * Map between a valid menu filter token and his respective filter method.
     *
     * @var array
     */
    protected $menuFilterMap = [
        'sidebar'      => 'sidebarFilter',
        'navbar-left'  => 'navbarLeftFilter',
        'navbar-right' => 'navbarRightFilter',
        'navbar-user'  => 'navbarUserMenuFilter',
    ];

    public function __construct(
        array $filters,
        Dispatcher $events,
        Container $container
    ) {
        $this->filters = $filters;
        $this->events = $events;
        $this->container = $container;
    }

    public function menu($filterToken = null)
    {
        if (! $this->menu) {
            $this->menu = $this->buildMenu();
        }

        // Check for filter token.

        if (isset($this->menuFilterMap[$filterToken])) {
            return array_filter(
                $this->menu,
                [$this, $this->menuFilterMap[$filterToken]]
            );
        }

        // No filter token provided, return the complete menu.

        return $this->menu;
    }

    /**
     * Gets the body classes, in relation to the config options.
     */
    public function getBodyClasses()
    {
        $body_classes = [];
        $screen_sizes = ['xs', 'sm', 'md', 'lg', 'xl'];

        // Add classes related to the "sidebar_mini" configuration.

        if (config('adminlte.sidebar_mini', true) === true) {
            $body_classes[] = 'sidebar-mini';
        } elseif (config('adminlte.sidebar_mini', true) == 'md') {
            $body_classes[] = 'sidebar-mini sidebar-mini-md';
        }

        // Add classes related to the "layout_topnav" configuration.

        if (config('adminlte.layout_topnav') || View::getSection('layout_topnav')) {
            $body_classes[] = 'layout-top-nav';
        }

        // Add classes related to the "layout_boxed" configuration.

        if (config('adminlte.layout_boxed') || View::getSection('layout_boxed')) {
            $body_classes[] = 'layout-boxed';
        }

        // Add classes related to the "sidebar_collapse" configuration.

        if (config('adminlte.sidebar_collapse') || View::getSection('sidebar_collapse')) {
            $body_classes[] = 'sidebar-collapse';
        }

        // Add classes related to the "right_sidebar" configuration.

        if (config('adminlte.right_sidebar') && config('adminlte.right_sidebar_push')) {
            $body_classes[] = 'control-sidebar-push';
        }

        // Add classes related to fixed sidebar, these are not compatible with
        // "layout_topnav".

        if (! config('adminlte.layout_topnav') && ! View::getSection('layout_topnav')) {

            // Check for fixed sidebar configuration.

            if (config('adminlte.layout_fixed_sidebar')) {
                $body_classes[] = 'layout-fixed';
            }
        }

        // Add classes related to fixed footer and navbar, these are not
        // compatible with "layout_boxed".

        if (! config('adminlte.layout_boxed') && ! View::getSection('layout_boxed')) {

            // Check for fixed navbar configuration.

            $fixed_navbar_cfg = config('adminlte.layout_fixed_navbar');

            if ($fixed_navbar_cfg === true) {
                $body_classes[] = 'layout-navbar-fixed';
            } elseif (is_array($fixed_navbar_cfg)) {
                foreach ($fixed_navbar_cfg as $size => $enabled) {
                    if (in_array($size, $screen_sizes)) {
                        $size = $size == 'xs' ? '' : '-'.$size;
                        $body_classes[] = $enabled == true ?
                            'layout'.$size.'-navbar-fixed' :
                            'layout'.$size.'-navbar-not-fixed';
                    }
                }
            }

            // Check for fixed footer configuration.

            $fixed_footer_cfg = config('adminlte.layout_fixed_footer');

            if ($fixed_footer_cfg === true) {
                $body_classes[] = 'layout-footer-fixed';
            } elseif (is_array($fixed_footer_cfg)) {
                foreach ($fixed_footer_cfg as $size => $enabled) {
                    if (in_array($size, $screen_sizes)) {
                        $size = $size == 'xs' ? '' : '-'.$size;
                        $body_classes[] = $enabled == true ?
                            'layout'.$size.'-footer-fixed' :
                            'layout'.$size.'-footer-not-fixed';
                    }
                }
            }
        }

        // Add custom classes, related to the "classes_body" configuration.

        $body_classes[] = config('adminlte.classes_body', '');

        // Return the set of configured classes for the body tag.

        return trim(implode(' ', $body_classes));
    }

    /**
     * Gets the body data attributes, in relation to the config options.
     */
    public function getBodyData()
    {
        $body_data = [];

        // Add data related to the "sidebar_scrollbar_theme" configuration.

        $sb_theme_cfg = config('adminlte.sidebar_scrollbar_theme', 'os-theme-light');

        if ($sb_theme_cfg != 'os-theme-light') {
            $body_data[] = 'data-scrollbar-theme='.$sb_theme_cfg;
        }

        // Add data related to the "sidebar_scrollbar_auto_hide" configuration.

        $sb_auto_hide = config('adminlte.sidebar_scrollbar_auto_hide', 'l');

        if ($sb_auto_hide != 'l') {
            $body_data[] = 'data-scrollbar-auto-hide='.$sb_auto_hide;
        }

        return trim(implode(' ', $body_data));
    }

    protected function buildMenu()
    {
        $builder = new Builder($this->buildFilters());

        $this->events->dispatch(new BuildingMenu($builder));

        return $builder->menu;
    }

    protected function buildFilters()
    {
        return array_map([$this->container, 'make'], $this->filters);
    }

    /**
     * Filter method for sidebar menu items.
     */
    private function sidebarFilter($item)
    {
        return MenuItemHelper::isSidebarItem($item);
    }

    /**
     * Filter method for navbar top left menu items.
     */
    private function navbarLeftFilter($item)
    {
        if (config('adminlte.layout_topnav') && MenuItemHelper::isSidebarItem($item)) {
            return MenuItemHelper::isValidNavbarItem($item);
        }

        return MenuItemHelper::isNavbarLeftItem($item);
    }

    /**
     * Filter method for navbar top right menu items.
     */
    private function navbarRightFilter($item)
    {
        return MenuItemHelper::isNavbarRightItem($item);
    }

    /**
     * Filter method for navbar dropdown user menu items.
     */
    private function navbarUserMenuFilter($item)
    {
        return MenuItemHelper::isNavbarUserItem($item);
    }
}
