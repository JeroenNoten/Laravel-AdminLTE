<?php

use JeroenNoten\LaravelAdminLte\Console\PackageResources\AssetsResource;
use JeroenNoten\LaravelAdminLte\Console\PackageResources\AuthViewsResource;
use JeroenNoten\LaravelAdminLte\Console\PackageResources\BasicRoutesResource;
use JeroenNoten\LaravelAdminLte\Console\PackageResources\BasicViewsResource;
use JeroenNoten\LaravelAdminLte\Console\PackageResources\ConfigResource;
use JeroenNoten\LaravelAdminLte\Console\PackageResources\MainViewsResource;
use JeroenNoten\LaravelAdminLte\Console\PackageResources\PackageResource;
use JeroenNoten\LaravelAdminLte\Console\PackageResources\TranslationsResource;
use JeroenNoten\LaravelAdminLte\Helpers\CommandHelper;

class CommandTestCase extends TestCase
{
    /**
     * Array to store the set of resources.
     *
     * @var array
     */
    protected $resources;

    /**
     * Get the array of resources.
     *
     * @param string $name Name of the resource.
     * @return array
     */
    protected function getResources($name = null)
    {
        if (! isset($this->resources)) {
            $this->resources = [
                'assets'       => new AssetsResource(),
                'config'       => new ConfigResource(),
                'translations' => new TranslationsResource(),
                'main_views'   => new MainViewsResource(),
                'auth_views'   => new AuthViewsResource(),
                'basic_views'  => new BasicViewsResource(),
                'basic_routes' => new BasicRoutesResource(),
            ];
        }

        return $name ? $this->resources[$name] : $this->resources;
    }

    /**
     * Create dummy files for a particular resource.
     *
     * @param string $resName
     * @param PackageResource $res
     * @return void
     */
    protected function createDummyResource($resName, $res)
    {
        // Uninstall the resource.

        $res->uninstall();

        // Create a dummy resource on the target destination. This will fire
        // an overwrite warning when trying to install the resource later.

        $target = $res->target;

        if ($resName === 'assets') {
            $target = $target.DIRECTORY_SEPARATOR.'adminlte';
            CommandHelper::EnsureDirectoryExists($target);
        } elseif ($resName === 'config') {
            $this->createDummyFile($target);
        } elseif ($resName === 'translations') {
            $target = $target.DIRECTORY_SEPARATOR.'en/adminlte.php';
            $this->createDummyFile($target);
        } elseif ($resName === 'main_views') {
            $target = $target.DIRECTORY_SEPARATOR.'master.blade.php';
            $this->createDummyFile($target);
        } elseif ($resName === 'auth_views') {
            $target = $target.DIRECTORY_SEPARATOR.'login.blade.php';
            $this->createDummyFile($target);
        } elseif ($resName === 'basic_views') {
            $target = $target.DIRECTORY_SEPARATOR.'home.blade.php';
            $this->createDummyFile($target);
        } elseif ($resName === 'basic_routes') {
            $stubFile = CommandHelper::getStubPath('routes.stub');
            $content = file_get_contents($stubFile);
            $this->createDummyFile($target, $content);
        }
    }

    /**
     * Create a dummy file with some content.
     *
     * @param string $filePath
     * @param string $content
     * @return void
     */
    protected function createDummyFile($filePath, $content = null)
    {
        $content = $content ?? 'dummy-content';
        CommandHelper::ensureDirectoryExists(dirname($filePath));
        file_put_contents($filePath, $content);
    }

    /**
     * Install the required vendor asset "vendor/almasaeed2010" into the
     * laravel testing project.
     *
     * @return void
     */
    protected function installVendorAssets()
    {
        $resource = CommandHelper::getPackagePath('vendor/almasaeed2010');
        $target = base_path('vendor/almasaeed2010');

        // Check if vendor assets are already installed.

        if (is_link($target)) {
            return;
        }

        // Check if vendor folder exists on the laravel testing project. If
        // vendor folder do not exists, create it.

        if (! is_dir(base_path('vendor'))) {
            mkdir(base_path('vendor'));
        }

        // Create a symbolic link to the required vendor assets.

        symlink($resource, $target);
    }
}
