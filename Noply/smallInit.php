<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 11.04.12
 * Time: 17:11
 * To change this template use File | Settings | File Templates.
 */
namespace Noply;

class smallInit
{
    private function __autoload($className)
    {
        $rootArray = explode('/', NOPLY_ROOT);
        $testRoot  = end($rootArray);
        $pathArray = explode('\\', $className);

        if ($pathArray[0] == $testRoot) {
            array_shift($pathArray);
        }

        $classFile = NOPLY_ROOT . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pathArray) . '.php';

        if (!file_exists($classFile)) {
            throw new \Exception("[[$className] = [$classFile]] not found, autoload failed!");
        }

        require $classFile;
    }

    public function __construct()
    {
        ini_set('display_errors', '1');
        error_reporting(E_ALL | E_STRICT);

        define('NOPLY_ROOT', dirname(__FILE__));

        spl_autoload_register(__NAMESPACE__ . '\smallInit::__autoload');
    }

    public function __destruct()
    {
    }
}
