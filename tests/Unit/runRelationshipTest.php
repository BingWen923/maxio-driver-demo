<?php

require 'vendor/autoload.php';

use PHPUnit\TextUI\Command;

$className = 'tests\Unit\RelationshipTest';
$testfile = "{$className}.php";


$reflection = new \ReflectionClass($className);
$methods = $reflection->getMethods();

echo "Available Test Methods in {$className}:\n";
echo "[0] Exit\n";// Add the Exit option
$testMethods = [];
$index = 1;

// list all methods with 'test'
foreach ($methods as $method) {
    if (strpos($method->name, 'test') === 0) {
        echo "[{$index}] {$method->name}\n";
        $testMethods[$index] = $method->name;
        $index++;
    }
}

// choose which method to run
echo "\nEnter the number of the test method you want to run: ";
$choice = trim(fgets(STDIN));

// Handle user input
if ($choice == 0) {
    echo "Exiting...\n";
    exit(0);
}

if (isset($testMethods[$choice])) {
    $methodName = $testMethods[$choice];
    echo "*************** Running {$methodName}..............\n";

    // execute the command
    $command = "php vendor\\bin\\phpunit --filter {$methodName} {$testfile}";
    passthru($command);
} else {
    echo "Invalid choice. Exiting...\n";
}
