<?php

require_once 'vendor/autoload.php';

set_time_limit(0);

$dbConf = new \SpoutExample\DBConf();
$reportCreator = new \SpoutExample\ReportCreator($dbConf);
$outputPath = 'out/report.xlsx';

// Create output folder if needed
$outputFolder = dirname($outputPath);
if (!file_exists($outputFolder)) {
    mkdir($outputFolder);
}

$startTime = microtime(true);

// Generate the report from the data present in the DB
$reportCreator
    ->setFetchRowsInBatch(500)
//    ->setFetchRowsOneByOne()
//    ->setFetchAllRowsAtOnce()
    ->setWriterType(\SpoutExample\ReportWriter\WriterType::SPOUT)
//    ->setWriterType(\SpoutExample\ReportWriter\WriterType::PHP_EXCEL)
    ->fetchDataAndCreateReport($outputPath);

// Display some stats
$timeElapsed = round(microtime(true) - $startTime, 2);
$memoryPeak = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

echo $reportCreator->getFetchMethodName() . "\n";
echo "Elapsed time: {$timeElapsed}s\n";
echo "Memory peak: {$memoryPeak}MB\n";
