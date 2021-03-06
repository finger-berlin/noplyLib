<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 22.03.12
 * Time: 10:41
 * To _change this template use File | Settings | File Templates.
 */
namespace Noply {
    class DB implements InterfaceDB
    {
        public $runtime = 0;
        public $dbh = null;
        public $debug = FALSE;

        protected $dbh_driver = 'mysql';
        protected $dbh_host = null;
        protected $dbh_port = null;
        protected $dbh_dbname = null;
        protected $dbh_user = null;
        protected $dbh_pass = null;

        /**
         * @param array $param
         */
        public function __construct($param = array())
        {
            $this->runtime = microtime(true);

            $this->dbh_host   = isset($param['db_host']) ? $param['db_host'] : null;
            $this->dbh_port   = isset($param['db_port']) ? $param['db_port'] : 3306;
            $this->dbh_dbname = isset($param['db_name']) ? $param['db_name'] : null;
            $this->dbh_user   = isset($param['db_username']) ? $param['db_username'] : null;
            $this->dbh_pass   = isset($param['db_password']) ? $param['db_password'] : null;

            if (isset($this->dbh_host, $this->dbh_dbname, $this->dbh_user, $this->dbh_pass)) {
                $this->dbh = $this->_newdbh(
                    $this->dbh_host,
                    $this->dbh_port,
                    $this->dbh_dbname,
                    $this->dbh_user,
                    $this->dbh_pass
                );

                return true;
            }

            return false;
        }

        /**
         *
         */
        public function __destruct()
        {
            $this->dbh = null;
        }

        /**
         * @param string $dbh_host
         * @param int    $dbh_port
         * @param string $dbh_dbname
         * @param string $dbh_user
         * @param string $dbh_pass
         *
         * @return null|\PDO
         */
        public function _newdbh($dbh_host = '', $dbh_port = 3306, $dbh_dbname = '', $dbh_user = '', $dbh_pass = '')
        {
            $this->runtime = microtime(true);

            if ($this->debug == 1) error_log("_newdbh ($dbh_host, $dbh_dbname, $dbh_user, \$dbh_pass)");

            $dbh = null;
            $dsn = $this->dbh_driver . ':dbname=' . $dbh_dbname . ';host=' . $dbh_host;

            if ($dbh_port != 3306) {
                $dsn .= ';port=' . $dbh_port;
            }

            try {
                $dbh = new \PDO(
                    $dsn,
                    $dbh_user,
                    $dbh_pass,
                    array(
                         \PDO::ATTR_PERSISTENT               => true,
                         \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    ));

                $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
                $dbh->exec('SET CHARACTER SET utf8');
            } catch (\PDOException $e) {
                error_log("Error!: " . $e->getMessage());
                exit(1);
            }

            return $dbh;
        }

        /**
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function select($statement = '', $args = null)
        {
            $this->runtime = microtime(true);

            if (preg_match('/^SELECT/i', $statement) == 0) {
                $statement = 'SELECT ' . $statement;
            }

            if (!is_array($args)) {
                $args = array($args);
            }

            if ($this->debug == 1) error_log("select ($statement," . implode(', ', $args) . ")");

            $sth = $this->dbh->prepare($statement);
            $sth->setFetchMode(\PDO::FETCH_ASSOC);

            $sth->execute($args);

            return $sth->fetchAll();
        }

        /**
         * @param string $tablename
         *
         * @return int
         */
        public function count($tablename = '')
        {
            $this->runtime = microtime(true);

            $sth = $this->dbh->prepare('SELECT COUNT(*) FROM ' . $tablename);
            $sth->execute();

            return (int)$sth->fetchColumn();
        }

        /**
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function delete($statement = '', $args = null)
        {
            $this->runtime = microtime(true);

            if ($this->debug == 1) error_log("delete ($statement,$args)");

            if (preg_match('/^DELETE/i', $statement) == 0) {
                $statement = 'DELETE ' . $statement;
            }

            if (!is_array($args)) {
                $args = array($args);
            }

            $sth = $this->dbh->prepare($statement);
            $sth->execute($args);

            return $sth->rowCount();
        }

        /**
         * @param $statement
         * @param $args
         *
         * @return array
         */
        private function _change($statement, $args)
        {
            $sth = $this->dbh->prepare($statement);
            $sth->execute($args);

            return $sth->rowCount();
        }

        /**
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function execute($statement = '', $args = null)
        {
            if (!is_array($args)) {
                $args = array($args);
            }

            $sth = $this->dbh->prepare($statement);
            $sth->setFetchMode(\PDO::FETCH_ASSOC);
            $sth->execute($args);

            return $sth->fetchAll();
        }

        /**
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function insert($statement = '', $args = null)
        {
            $this->runtime = microtime(true);

            if (preg_match('/^INSERT/i', $statement) == 0) {
                $statement = 'INSERT ' . $statement;
            }

            return array(
                $this->_change($statement, $args),
                $this->dbh->lastInsertId()
            );
        }

        /**
         * @param string $tableName
         * @param array  $namedArray
         *
         * @return array
         */
        public function insertArray($tableName = '', $namedArray = array())
        {
            $this->runtime = microtime(true);

            if (!is_array($namedArray) || !count($namedArray)) return array(false, false);

            $bindNames = ':' . implode(',:', array_keys($namedArray));

            $statement = 'INSERT INTO ' . $tableName . '(' . implode(',', array_keys($namedArray)) . ') ' .
                'VALUES (' . $bindNames . ')';

            return array(
                $this->_change($statement, array_combine(explode(',', $bindNames), array_values($namedArray))),
                $this->dbh->lastInsertId()
            );
        }

        /**
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function update($statement = '', $args = null)
        {
            $this->runtime = microtime(true);

            if (preg_match('/^UPDATE/i', $statement) == 0) {
                $statement = 'UPDATE ' . $statement;
            }

            return $this->_change($statement, $args);
        }

        /**
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function create($statement = '', $args = null)
        {
            $this->runtime = microtime(true);

            if (preg_match('/^CREATE/i', $statement) == 0) {
                $statement = 'CREATE ' . $statement;
            }

            return $this->_change($statement, $args);
        }

        /**
         * @param string $statement
         * @param null   $args
         *
         * @return mixed
         */
        public function drop($statement = '', $args = null)
        {
            $this->runtime = microtime(true);

            if (preg_match('/^DROP/i', $statement) == 0) {
                $statement = 'DROP ' . $statement;
            }

            return $this->_change($statement, $args);
        }

        /**
         * @return mixed
         */
        public function beginTransaction()
        {
            return $this->dbh->beginTransaction();
        }

        /**
         * @return mixed
         */
        public function commit()
        {
            return $this->dbh->commit();
        }

        /**
         * @return mixed
         */
        public function rollBack()
        {
            return $this->dbh->rollBack();
        }

        /**
         * @return int
         */
        public function getRuntime()
        {
            return microtime(true) - $this->runtime;
        }
    }
}