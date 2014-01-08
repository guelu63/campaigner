<?php
// error_reporting(-1);
/**
 * Processor: Tail a log file and output the contents
 */
$file = $modx->getOption('core_path') . 'cache/logs/campaigner/115.log';
// echo 'FILE ' . $file;
$size = 0;
$handle = fopen($file, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $o[] = $line;
        // process the line read.
    }
} else {
    // error opening the file.
}
// var_dump($o);
return $modx->error->success('', $o);
return $modx->toJSON($o);


// while (true) {
//     clearstatcache();
//     $currentSize = filesize($file);
//     if ($size == $currentSize) {
//         usleep(100);
//         continue;
//     }

//     $fh = fopen($file, "r");
//     fseek($fh, $size);

//     while ($d = fgets($fh)) {
//         echo $d;
//     }

//     fclose($fh);
//     $size = $currentSize;
// }