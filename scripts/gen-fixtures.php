<?php

require_once 'FixturesGenerator.php';

$outputPath = 'sql/fixtures.sql';
$numProductsToGenerate = 50000;

$fixturesGenerator = new FixturesGenerator();
$fixturesGenerator->generate($outputPath, $numProductsToGenerate);
