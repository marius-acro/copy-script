<?php

use RowCopier\DatabaseManipulator;
use RowCopier\RowCopier;

require_once "RowCopier.php";
require_once "DatabaseManipulator.php";

// initializeDatabase();
// refreshDatabase();

$rowCopier = new RowCopier();

$namedParameters = [];

foreach ($argv as $key => $argument) {
    if ($key === 0) {
        continue;
    }

    if (strpos($argument, '--') !== false) {
        $parameterName = substr($argument, 2, strpos($argument, '=') - 2);
        $parameterValue = substr($argument, strpos($argument, '=') + 1);
        $namedParameters[$parameterName] = $parameterValue;
    } else {
        $namedParameters['id'] = $argument;
    }
}

$rowCopier->copyRow(
    $namedParameters['id'],
    $namedParameters['only'] ?? null,
    $namedParameters['include-posts'] ?? null,
);

function initializeDatabase() {
    DatabaseManipulator::createTables();
    DatabaseManipulator::seedDummyData();
}

function refreshDatabase() {
    DatabaseManipulator::truncateTables();
    DatabaseManipulator::seedDummyData();
}