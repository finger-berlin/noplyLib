# noplyLib

Small helper for fast DB access via PHP.

Can used to connect to local and remote MySQL databases via PDO and even thru SSH (Tunnel).

Have a look at "Noply\DB" and "Noply\DB\sTunnel".

## example

    <?php
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

## usage

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.