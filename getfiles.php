<?php
$dir = '.';
if (array_key_exists('dir', $_GET)) {
    $dir = $_GET['dir'];
    // print(json_encode($_GET));
    // return;
}
if (0 == 1) {
    echo 'Got here 1234';
    echo (1 / 5) . "\n";
    echo 'Got here 4567';
    try {
        echo (1 / 5) . "\n";
        echo (1 / 0) . "\n";
        if (0 == 1) {
            $list = scandir($dir);
            echo len($list);
            for ($i = 0; $i < len($list); $i++) {
                echo $list[$i];
            }
        }
    } catch (Exception $e) {
        echo 'Exception';
        //    echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    echo 'Done' . "\n";
}
function getfiles($dir)
{
    //if(!is_dir($dir)) {
    //return;
    //}
    $dir = realpath($dir);
    $here = realpath('.');
    $dir='.'.substr($dir,strlen($here));
    // $data[$here]=array('type'=>'directory');
    // $data[$hhhh]=array('type'=>'directory');
    // $dir=substr($dir, len($here));
    $a = scandir($dir);
    // echo count($a);
    for ($i = 0; $i < count($a); $i++) {
        $name = $dir . '/' . $a[$i];
        // print($name);
        // print("\n");
        if (is_dir($name)) {
            //print('Directory');
            //print("\n");
            //getfiles($name);
            $data[$name] = array('type'=>'directory');
        } else {
            $data[$name] = array('type'=>'file', 'length'=>filesize($name));
        }
    }
    // print_r($a);
    return $data;
}
$data = array();
$data = getfiles($dir);
// print_r($data);
print(json_encode($data));
