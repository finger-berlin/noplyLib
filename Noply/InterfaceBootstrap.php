<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 2012-10-05
 * Time: 10:26 AM
 * To change this template use File | Settings | File Templates.
 */
namespace Noply {

    interface InterfaceBootstrap
    {
        public function init();

        public static function getInstance();
    }
}
