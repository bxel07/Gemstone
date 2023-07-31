<?php

// Read the contents of values.json
$jsonString = file_get_contents('log/values.json');
$data = json_decode($jsonString, true);

// Get the rounded values from values.json
$roundedValuesFromJson = $data['rounded_values'];

// Hash the rounded values from values.json
$hashedRoundedValuesFromJson = array_map('hash_sha256', array_values($roundedValuesFromJson));

// Combine the hashed rounded values with the keys into a single associative array
$hashedRoundedValuesArray = array_combine(array_keys($roundedValuesFromJson), $hashedRoundedValuesFromJson);

// Include the config.php file
include('log/config.php');

// Compare the values in config.php with the hashed rounded values from values.json
if ($config === $hashedRoundedValuesArray) {
    echo 'Hello';
} else {
    echo 'Values do not match';
}

// Output a new line for readability
echo PHP_EOL;
?>

<?php
// Custom hash function using sha256
function hash_sha256($value) {
    return hash('sha256', $value);
}
?>
