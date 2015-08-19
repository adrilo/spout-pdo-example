<?php

namespace SpoutExample\ReportWriter;

/**
 * Class PHPExcelWriter
 *
 * @package SpoutExample\ReportWriter
 */
class PHPExcelWriter extends AbstractWriter
{
    /** @var \PHPExcel PHPExcel instance */
    private $phpExcel;

    /** @var string Path to where the report will be written to */
    private $outputFilePath;

    /** @var int To keep track of the last written row's index */
    private $currentRowIndex = 0;

    /**
     * @inheritDoc
     */
    public function __construct($outputFilePath)
    {
        $this->outputFilePath = $outputFilePath;
        $this->phpExcel = new \PHPExcel();
    }

    /**
     * @inheritDoc
     */
    public function writeRow($row)
    {
        $this->currentRowIndex++;
        $this->phpExcel->getActiveSheet()->fromArray($row, null, "A{$this->currentRowIndex}");
    }

    /**
     * @inheritDoc
     */
    public function writeHeaderRow($headerRow)
    {
        $this->writeRow($headerRow);

        $lastColumnLetter = \PHPExcel_Cell::stringFromColumnIndex(count($headerRow)-1);
        $headerRange = "A{$this->currentRowIndex}:{$lastColumnLetter}{$this->currentRowIndex}";
        $this->phpExcel->getActiveSheet()->getStyle($headerRange)->getFont()->setBold(true);
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        $reportWriter = \PHPExcel_IOFactory::createWriter($this->phpExcel, 'Excel2007');
        $reportWriter->save($this->outputFilePath);
    }
}
