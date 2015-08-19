<?php

namespace SpoutExample\ReportWriter;

abstract class AbstractWriter
{
    /**
     * @param string $outputFilePath
     * @return void
     */
    abstract public function __construct($outputFilePath);

    /**
     * Write given data to the output.
     *
     * @param array $row
     * @return void
     */
    abstract public function writeRow($row);

    /**
     * Write given data to the output and make it bold.
     *
     * @param array $headerRow
     * @return void
     */
    abstract public function writeHeaderRow($headerRow);

    /**
     * Closes the writer.
     *
     * @return void
     */
    abstract public function close();
}
