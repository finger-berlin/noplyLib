<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 11.04.12
 * Time: 17:11
 * To change this template use File | Settings | File Templates.
 */

namespace Noply {
    require_once('InterfaceBootstrap.php');

    class Bootstrap implements InterfaceBootstrap
    {
        /**
         * @param $className
         *
         * @throws \Exception
         */
        private function _autoload($className)
        {
            $rootArray = explode('/', APPLICATION_ROOT);
            $testRoot  = end($rootArray);
            $pathArray = explode('\\', $className);

            if ($pathArray[0] == $testRoot) {
                array_shift($pathArray);
            }

            $classFile = APPLICATION_ROOT . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pathArray) . '.php';

            if (!file_exists($classFile)) {
                if (count(spl_autoload_functions()) == 1 && self::$_standAlone === true) {
                    throw new \Exception("[[$className] = [$classFile]] not found, Bootstrap::_autoload failed!");
                } else {
                    // I hope one of the other autoloaders will find that!!!
                    // error_log("[[$className] = [$classFile]] not found, but there are other autoloaders registered!");
                }
            } else {
                include $classFile;
            }
        }

        /**
         * @var
         */
        private static $_standAlone;

        /**
         *
         */
        public function init($standAlone = true)
        {
            self::$_standAlone = (is_bool($standAlone)) ? $standAlone : true;

            ini_set('display_errors', '1');
            error_reporting(E_ALL | E_STRICT);

            if (!defined('APPLICATION_ROOT')) {
                if (defined('APPLICATION_ROOT') && APPLICATION_ROOT != dirname(__FILE__)) {
                    error_log('const APPLICATION_ROOT is already in use... stake out! (APPLICATION_ROOT = ' . APPLICATION_ROOT . ')');
                    die();
                }
                define('APPLICATION_ROOT', dirname(__FILE__));
            }


            spl_autoload_register(array($this, '_autoLoad'), true, true);
        }

        /**
         * @var
         */
        private static $_instance;

        /**
         * @static
         * @return mixed
         */
        public static function getInstance()
        {
            if (!self::$_instance) {
                self::$_instance = new Bootstrap();
            }

            return self::$_instance;
        }
    }
}
//EOF