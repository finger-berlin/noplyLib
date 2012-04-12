<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 22.03.12
 * Time: 10:41
 * To _change this template use File | Settings | File Templates.
 */
namespace Noply\DB;
class sTunnel extends \Noply\DB
{
    public $ssh_proc = null;
    public $ssh_user = null;
    public $ssh_host = null;

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
        $this->ssh_user   = isset($param['ssh_user']) ? $param['ssh_user'] : null;
        $this->ssh_host   = isset($param['ssh_host']) ? $param['ssh_host'] : null;

        if (isset($this->ssh_user, $this->ssh_host, $this->dbh_host)) {
            $descriptorspec = array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("file", "/dev/null", "a"),
            );

            $localPort = 9999;

            while (true)
            {
                $connection = @fsockopen($this->dbh_host, $localPort, $x, $y, 3);
                if ($connection) {
                    fclose($connection);
                    $localPort++;
                }
                else {
                    break;
                }
            }

            $cwd            = '/tmp';
            $tunnel         = sprintf('%d:%s:%d', $localPort, $this->dbh_host, $this->dbh_port);
            $this->dbh_port = $localPort;
            $this->dbh_host = '127.0.0.1';

            $this->ssh_proc = proc_open(
                'ssh -L ' . $tunnel . ' ' . $this->ssh_user . '@' . $this->ssh_host,
                $descriptorspec, $cwd, null
            );

            if (is_resource($this->ssh_proc)) {
                while (true)
                {
                    $connection = @fsockopen($this->dbh_host, $localPort);
                    if ($connection) {
                        fclose($connection);
                        break;
                    }
                    usleep(10000);
                }
            }
            else {
                die('Error: SSH connect failed!');
            }
        }

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
        if (is_resource($this->ssh_proc)) {
            proc_terminate($this->ssh_proc);
        }
        $this->dbh = null;
    }
}
