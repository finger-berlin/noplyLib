<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 2012-10-05
 * Time: 10:29 AM
 * To change this template use File | Settings | File Templates.
 */
namespace Noply {

    interface InterfaceDB
    {
        /**
         * @param array $param
         */
        public function __construct($param = array());

        /**
         *
         */
        public function __destruct();

        /**
         * @static
         * @abstract
         *
         * @param string $dbh_host
         * @param int    $dbh_port
         * @param string $dbh_dbname
         * @param string $dbh_user
         * @param string $dbh_pass
         *
         * @return mixed
         */
        public function _newdbh($dbh_host = '', $dbh_port = 3306, $dbh_dbname = '', $dbh_user = '', $dbh_pass = '');

        /**
         * @abstract
         *
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function select($statement = '', $args = null);

        /**
         * @abstract
         *
         * @param string $tablename
         *
         * @return mixed
         */
        public function count($tablename = '');

        /**
         * @abstract
         *
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function delete($statement = '', $args = null);

        /**
         * @abstract
         *
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function execute($statement = '', $args = null);

        /**
         * @abstract
         *
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function insert($statement = '', $args = null);

        /**
         * @abstract
         *
         * @param string $tableName
         * @param array  $namedArray
         *
         * @return mixed
         */
        public function insertArray($tableName = '', $namedArray = array());

        /**
         * @abstract
         *
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function update($statement = '', $args = null);

        /**
         * @abstract
         *
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function create($statement = '', $args = null);

        /**
         * @abstract
         *
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function drop($statement = '', $args = null);

        /**
         * @abstract
         * @return mixed
         */
        public function beginTransaction();

        /**
         * @abstract
         * @return mixed
         */
        public function commit();

        /**
         * @abstract
         * @return mixed
         */
        public function rollBack();
    }
}
//EOF