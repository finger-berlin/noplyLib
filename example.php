<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 12.04.12
 * Time: 10:42
 * To change this template use File | Settings | File Templates.
 */
require('Noply/smallInit.php');
$noply = new Noply\smallInit(); // get Noply "context" and autoloading...

$DBconnect = array(
    'ssh_host'    => 'ssh.example.com',
    'ssh_user'    => 'sshUser',
    'db_host'     => 'db.example.com',
    'db_port'     => 3306,
    'db_name'     => 'dbUser',
    'db_username' => 'dbName',
    'db_password' => 'dbPass'
);

// connects to db behind ssh host...
$ssh_dbh = new Noply\DB\sTunnel($DBconnect);

// insert...
$success = $ssh_dbh->insert('INSERT INTO tablename (row1, row2, row3) VALUES (?, ?, ?)', array(1,'two',3));

// connects directly to db (ssh params will be ignored)
$std_dbh = new Noply\DB($DBconnect);

// select...
$array = $std_dbh->select('SELECT * FROM tablename WHERE row3 = ?', array(3));

// OR this way...
$array = $std_dbh->select('SELECT * FROM tablename WHERE row3 = ?', 3);

// OR this way...
$array = $std_dbh->select('* FROM tablename WHERE row3 = ?', 3);

// update...
$success = $std_dbh->update('tablename SET row2 = ? WHERE row1 = ?', $array(2, 1));
