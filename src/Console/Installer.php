<?php
/**
 * plugin-template (https://github.com/PotatoPowered/plugin-template)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Blake Sutton <blake@potatopowered.net>
 * @copyright   Copyright (c) Potato Powered Software
 * @link        http://potatopowered.net
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 * @codingStandardsIgnoreStart
 */
namespace PluginTemplate\Console;

use Composer\Script\Event;
use Exception;

/**
 * Provides installation hooks for when this application is installed via
 * composer. Customize this class to suit your needs.
 */
class Installer
{
    /**
     * Gets the root directory of your install.
     *
     * @return string The root of your application install.
     */
    public static function getInstallRoot()
    {
        return dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
    }

    /**
     * Does some routine installation tasks so people don't have to.
     *
     * @param \Composer\Script\Event $event The composer event object.
     * @throws \Exception Exception raised by validator.
     * @return void
     */
    public static function postInstall(Event $event)
    {
        $pluginName = basename(realpath("."));
        $rootDirectory = static::getInstallRoot();
        $installAssetsDirectory = $rootDirectory . 'install' . DIRECTORY_SEPARATOR;
        $io = $event->getIO();

        $io->write('Generating a new Potato Powered Software plugin: ' . $pluginName);

        $replacements = [
            "{{plugin-name}}" => $pluginName,
            "{{developer-name}}" => $io->isInteractive() ? $io->ask('What is the lead developers name? ') : '',
            "{{developer-email}}" => $io->isInteractive() ? $io->ask('What is the lead developers email? ') : '',
        ];

        $installAssetFiles = [
            $installAssetsDirectory . 'composer.json',
            $installAssetsDirectory . 'README.md',
            $installAssetsDirectory . 'phpunit.xml',
        ];

        $templateFiles = [
            $rootDirectory . 'tests' . DIRECTORY_SEPARATOR . 'bootstrap.php',
            $rootDirectory . 'config' . DIRECTORY_SEPARATOR . 'routes.php',
        ];


        $io->write('Replacing values in installation assets.');
        static::replaceValues(array_merge($installAssetFiles, $templateFiles), $replacements);
        $io->write('Moving install assets to project root.');
        static::installAssets($installAssetFiles);
        $io->write('Removing asset folder.');
        static::removeInstallFolder($installAssetsDirectory);
        $io->write('Installation complete!');
    }

    /**
     * Takes in a list of assets files that have been setup for this plugin and then copies over the files
     * in the plugins root directory.
     *
     * @param $installAssets array A list of files to install into the new plugin root
     */
    public static function installAssets($installAssets)
    {
        $rootDirectory = static::getInstallRoot();

        foreach ($installAssets as $file) {
            if (file_exists($file)) {
                rename($file, $rootDirectory . basename($file));
            }
        }
    }


    /**
     * Takes in an array of files and an array of replacement pairs and iterates through each replacing them.
     *
     * @param $files array An array containing all files that could contain a value in need of replacing.
     * @param $replacements array An array of replacement pairs to be replaced in all files.
     */
    public static function replaceValues($files, $replacements)
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                $updatedFile = strtr(file_get_contents($file), $replacements);
                file_put_contents($file, $updatedFile);
            }
        }
    }

    /**
     * Takes in an array of files and an array of replacement pairs and iterates through each replacing them.
     *
     * @param $installAssetsDirectory string A list of files to install into the new plugin root
     * @return bool Returns true when delete was successful
     */
    public static function removeInstallFolder($installAssetsDirectory)
    {
        $retry = 0;
        while (!file_exists($installAssetsDirectory) || $retry < 5) {
            if (!rmdir($installAssetsDirectory)) {
                sleep(3);
            } else {
                return true;
            }
        }
        return false;
    }
}
/**
 * @codingStandardsIgnoreEnd
 */
