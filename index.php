<?php

    use \Gemstone\main;
    require_once __DIR__.'/vendor/autoload.php';


$start = microtime(true);
$memoryStart = memory_get_usage();

$instance = new main();

$instance->reGemstone();
$dataArray = array(
    'name' => 'John Doe',
    'age' => 30,
    'email' => 'johndoe@example.com'
);


$encryptedData = $instance->ed('Diamond', $dataArray);
$data = $instance->dd('Diamond', $encryptedData);
print_r($data);

$end = microtime(true);
$memoryEnd = memory_get_usage();

$executionTime = $end - $start;
$memoryUsage = $memoryEnd - $memoryStart;
$executionTimeF = number_format($executionTime, 2);
echo "<pre>";

echo "execution time : {$executionTimeF} sec";
echo "<pre>";
echo "execution time : {$memoryUsage} bytes";