<?php

namespace JeroenNoten\LaravelAdminLte\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CommandHelper
{
    /**
     * Ensure a directory exists by creating it when needed.
     *
     * @param string $dir The path of the directory
     * @param int $mode The directory access mode
     * @param bool $recursive Allow creating nested directories present in path
     * @return void
     */
    public static function ensureDirectoryExists($dir, $mode = 0755, $recursive = true)
    {
        File::ensureDirectoryExists($dir, $mode, $recursive);
    }

    /**
     * Copy an entire directory to a destination.
     *
     * @param string $dir The path of the source folder
     * @param string $dest The path of the destination folder
     * @param bool $force Whether to force the overwrite of existing files
     * @param bool $recursive Whether to copy subfolders recursively
     * @param array $ignores Array of files to be ignored
     * @return void
     */
    public static function copyDirectory($dir, $dest, $force = false, $recursive = false, $ignores = [])
    {
        // Open the source folder. Return if fails to open.

        if (! is_resource($dirHandler = @opendir($dir))) {
            return;
        }

        // Ensure the destination folder exists.

        self::ensureDirectoryExists($dest);

        // Copy the source files to destination.

        while (($file = readdir($dirHandler)) !== false) {

            // Check if this file should be ignored.

            $filesToIgnore = array_merge($ignores, ['.', '..']);

            if (self::isIgnoredFile($file, $filesToIgnore)) {
                continue;
            }

            // Now, copy the file/folder. If the resource is a folder, proceed
            // recursively. Otherwise, copy the file to destination.

            $source = $dir.DIRECTORY_SEPARATOR.$file;
            $target = $dest.DIRECTORY_SEPARATOR.$file;

            if (is_dir($source) && $recursive) {
                self::copyDirectory($source, $target, $force, $recursive, $ignores);
            } elseif (is_file($source) && ($force || ! file_exists($target))) {
                copy($source, $target);
            }
        }

        // Close the source folder.

        closedir($dirHandler);
    }

    /**
     * Compare two directories file by file.
     *
     * @param string $dir1 The path of the first folder
     * @param string $dir2 The path of the second folder
     * @param bool $recursive Whether to compare subfolders recursively
     * @param array $ignores Array of files to be ignored
     * @return bool|null Result of comparison or null if a folder not exists
     */
    public static function compareDirectories($dir1, $dir2, $recursive = false, $ignores = [])
    {
        // Open the first folder. Return if fails to open.

        if (! is_resource($dirHandler = @opendir($dir1))) {
            return null;
        }

        // Check if the second folder exists.

        if (! is_dir($dir2)) {
            return null;
        }

        // Now, compare the folders.

        while (($file = readdir($dirHandler)) !== false) {

            // Check if this file should be ignored.

            $filesToIgnore = array_merge($ignores, ['.', '..']);

            if (self::isIgnoredFile($file, $filesToIgnore)) {
                continue;
            }

            // Get paths of the resources to compare.

            $source = $dir1.DIRECTORY_SEPARATOR.$file;
            $target = $dir2.DIRECTORY_SEPARATOR.$file;

            // If the resources to compare are files, check that both files are
            // equals.

            if (is_file($source) && ! self::compareFiles($source, $target)) {
                return false;
            }

            // If the resources to compare are folders, recursively compare the
            // folders.

            $isDir = is_dir($source) && $recursive;

            if ($isDir && ! (bool) self::compareDirectories($source, $target, $recursive, $ignores)) {
                return false;
            }
        }

        // Close the opened folder.

        closedir($dirHandler);

        // At this point all the resources compared are equals.

        return true;
    }

    /**
     * Check if two files are equals by comparing sha1 hash values.
     *
     * @param string $file1 The first file
     * @param string $file2 The second file
     * @return bool
     */
    public static function compareFiles($file1, $file2)
    {
        if (! is_file($file1) || ! is_file($file2)) {
            return false;
        }

        return sha1_file($file1) === sha1_file($file2);
    }

    /**
     * Recursively delete a directory.
     *
     * @param string $dir The directory to remove
     * @return bool
     */
    public static function removeDirectory($dir)
    {
        return File::deleteDirectory($dir);
    }

    /**
     * Check if a file is included in a set of ignored file patterns.
     *
     * @param string $file The file to check
     * @param array $ignores Array of file patterns to be ignored
     * @return bool
     */
    protected static function isIgnoredFile($file, $ignores)
    {
        foreach ($ignores as $pattern) {
            $match = Str::startsWith($pattern, 'regex:') ?
                     preg_match(Str::substr($pattern, 6), $file) :
                     Str::is($pattern, $file);

            if ($match) {
                return true;
            }
        }

        return false;
    }
}
