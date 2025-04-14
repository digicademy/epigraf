<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Console;

use Cake\Utility\Security;
use Composer\Script\Event;
use Exception;

/**
 * Provides installation hooks for when this application is installed via
 * composer. Customize this class to suit your needs.
 */
class Installer
{

    /**
     * Does some routine installation tasks, so people don't have to.
     *
     * @param \Composer\Script\Event $event The composer event object
     *
     * @return void
     * @throws \Exception Exception raised by validator
     */
    public static function postInstall(Event $event)
    {
        $io = $event->getIO();

        $rootDir = dirname(dirname(__DIR__));

        static::createAppConfig($rootDir, $io);
        static::createWritableDirectories($rootDir, $io);
        static::buildWebPack($rootDir, $io);

        //static::copyJsFiles($rootDir, $io);
        static::createSymlinks($rootDir, $io);

        static::setFolderPermissions($rootDir, $io);
        // ask if the permissions should be changed
//        if ($io->isInteractive()) {
//            $validator = function ($arg) {
//                if (in_array($arg, ['Y', 'y', 'N', 'n'])) {
//                    return $arg;
//                }
//                throw new Exception('This is not a valid answer. Please choose Y or n.');
//            };
//            $setFolderPermissions = $io->askAndValidate(
//                '<info>Set Folder Permissions ? (Default to Y)</info> [<comment>Y,n</comment>]? ',
//                $validator,
//                10,
//                'Y'
//            );
//
//            if (in_array($setFolderPermissions, ['Y', 'y'])) {
//                static::setFolderPermissions($rootDir, $io);
//            }
//        }
//        else {
//            static::setFolderPermissions($rootDir, $io);
//        }

        static::setSecuritySalt($rootDir, $io);

        if (class_exists('\Cake\Codeception\Console\Installer')) {
            \Cake\Codeception\Console\Installer::customizeCodeceptionBinary($event);
        }

        static::clearCache($io);
    }

    /**
     * Does some routine update tasks so people don't have to.
     *
     * @param \Composer\Script\Event $event The composer event object
     *
     * @return void
     * @throws \Exception Exception raised by validator
     */
    public static function postUpdate(Event $event)
    {
        $io = $event->getIO();
        $rootDir = dirname(dirname(__DIR__));

        static::createAppConfig($rootDir, $io);
        static::createWritableDirectories($rootDir, $io);

        static::buildWebPack($rootDir, $io);
        static::createSymlinks($rootDir, $io);

        //static::copyJsFiles($rootDir, $io);
        static::clearCache($io);
    }

    /**
     * Copy Javascript files
     *
     * @deprecated Place files in the folder and commit them.
     *
     * @param $rootDir
     * @param $io
     *
     * @return void
     */
    public static function copyJsFiles($rootDir, $io)
    {
        $files = [
//            $rootDir . '/vendor/components/jquery/jquery.min.js' => $rootDir . '/htdocs/js/jquery/jquery.min.js',
//            $rootDir . '/vendor/enyo/dropzone/dist/dropzone.js' => $rootDir . '/plugins/Widgets/webroot/js/dropzone/dropzone.js',
//            $rootDir . '/vendor/enyo/dropzone/dist/dropzone.css' => $rootDir . '/plugins/Widgets/webroot/css/dropzone/dropzone.css',
//            $rootDir . '/node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.ttf' => $rootDir . '/htdocs/webfonts/fa-solid-900.ttf',
//            $rootDir . '/node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.woff2' => $rootDir . '/htdocs/webfonts/fa-solid-900.woff2'
        ];

        foreach ($files as $from => $to) {
            if (!file_exists(dirname($to))) {
                mkdir(dirname($to));
            }
            copy($from, $to);
            $io->write('Copied ' . basename($from) . ' to htdocs');
        }
    }

    /**
     * Compress css and js files
     *
     * Compress css and js files using the AssetCompress plugin
     *
     * @param $rootDir
     * @param $io
     *
     * @return void
     * @deprecated Use webpack instead
     *
     */
    public static function buildAssetCompress($rootDir, $io)
    {
        $command = 'bin/cake asset_compress build';
        exec($command);
        $io->write('Compressed assets.');
    }

    /**
     * Build with webpack
     *
     * @param $path
     * @param $io
     *
     * @return void
     */
    public static function buildWebPack($path, $io)
    {
        //NPM
        $npm = 'cd ' . $path . ' &&';
        $npm .= ' npm install';
        exec($npm);
        $io->write('Executed npm install in folder ' . $path);

        //Webpack
        $webpack = 'cd ' . $path . ' &&';
        $webpack .= ' npm run build';
        exec($webpack);

        $io->write('Executed npm run build in folder ' . $path);
    }

    /**
     * Create configuration data
     *
     * Create the config/app.php file if it does not exist.
     *
     * @param string $dir The application's root directory
     * @param \Composer\IO\IOInterface $io IO interface to write to console
     *
     * @return void
     */
    public static function createAppConfig($dir, $io)
    {
        $appConfig = $dir . '/config/app.php';
        $defaultConfig = $dir . '/config/app.default.php';
        if (!file_exists($appConfig)) {
            copy($defaultConfig, $appConfig);
            $io->write('Created `config/app.php` file');
        }
        else {
            $io->write('Use existing`config/app.php` file');
        }
    }

    /**
     * Create the `logs` and `tmp` directories.
     *
     * @param string $dir The application's root directory
     * @param \Composer\IO\IOInterface $io IO interface to write to console
     *
     * @return void
     */
    public static function createWritableDirectories($dir, $io = null)
    {
        $paths = [
            'logs',
            'tmp',
            'tmp/cache',
            'tmp/cache/models',
            'tmp/cache/persistent',
            'tmp/cache/views',
            'tmp/cache/index',
            'tmp/cache/results',
            'tmp/sessions',
            'tmp/tests',
            'tmp/profiles'
        ];

        foreach ($paths as $path) {
            $path = $dir . '/' . $path;
            if (!file_exists($path)) {
                mkdir($path);
                if (!is_null($io)) {
                    $io->write('Created `' . $path . '` directory');
                }
            }
        }
    }

    /**
     * Create symlinks
     *
     * Creates a symlink to codecept
     *
     * @param $rootDir
     * @param $io
     * @return void
     */
    public static function createSymlinks($rootDir, $io)
    {
        $codeceptFile = $rootDir . '/vendor/bin/codecept';
        if (file_exists($codeceptFile) || is_link($codeceptFile)) {
            exec('rm ' . $codeceptFile);
        }

        exec('ln -s ../codeception/codeception/codecept ' . $codeceptFile);

        $io->write('Symlink to codecept created.');

    }

    /**
     * Set globally writable permissions on the "tmp" and "logs" directory.
     *
     * This is not the most secure default, but it gets people up and running quickly.
     *
     * @param string $dir The application's root directory
     * @param \Composer\IO\IOInterface $io IO interface to write to console
     *
     * @return void
     */
    public static function setFolderPermissions($dir, $io)
    {
        // Change the permissions on a path and output the results.
        $changePerms = function ($path, $perms, $io) {
            // Get permission bits from stat(2) result.
            $currentPerms = fileperms($path) & 0777;
            if (($currentPerms & $perms) == $perms) {
                return;
            }

            $res = chmod($path, $currentPerms | $perms);
            if ($res) {
                $io->write('Permissions set on ' . $path);
            }
            else {
                $io->write('Failed to set permissions on ' . $path);
            }
        };

        $walker = function ($dir, $perms, $io) use (&$walker, $changePerms) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $path = $dir . '/' . $file;

                if (!is_dir($path)) {
                    continue;
                }

                $changePerms($path, $perms, $io);
                $walker($path, $perms, $io);
            }
        };

        $worldWritable = bindec('0000000111');
        $walker($dir . '/tmp', $worldWritable, $io);
        $changePerms($dir . '/tmp', $worldWritable, $io);
        $changePerms($dir . '/logs', $worldWritable, $io);
    }

    /**
     * Set the security.salt value in the application's config file.
     *
     * @param string $dir The application's root directory
     * @param \Composer\IO\IOInterface $io IO interface to write to console
     *
     * @return void
     */
    public static function setSecuritySalt($dir, $io)
    {
        $config = $dir . '/config/app.php';
        $content = file_get_contents($config);

        $newKey = hash('sha256', Security::randomBytes(64));
        $content = str_replace('__SALT__', $newKey, $content, $count);

        if ($count == 0) {
            $io->write('No Security.salt placeholder to replace.');

            return;
        }

        $result = file_put_contents($config, $content);
        if ($result) {
            $io->write('Updated Security.salt value in config/app.php');

            return;
        }
        $io->write('Unable to update Security.salt value.');
    }

    /**
     * Clears the cache for the application.
     *
     * @param \Composer\IO\IOInterface $io IO interface to write to console
     *
     * @return void
     */
    public static function clearCache($io)
    {
        $command = 'bin/cake cache clear_all';
        exec($command);
        $io->write('Cleared cache.');
    }
}
