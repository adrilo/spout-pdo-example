<?php

namespace SpoutExample\ReportWriter;

use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\WriterFactory;

/**
 * Class SpoutWriter
 *
 * @package SpoutExample\ReportWriter
 */
class SpoutWriter extends AbstractWriter
{
    /** @var \Box\Spout\Writer\XLSX\Writer The Spout writer */
    private $reportWriter;

    /**
     * @inheritDoc
     */
    public function __construct($outputFilePath)
    {
        $this->reportWriter = WriterFactory::create(Type::XLSX);
        $this->reportWriter->openToFile($outputFilePath);
    }

    /**
     * @inheritDoc
     */
    public function writeRow($row)
    {
        $this->reportWriter->addRow($row);
    }

    /**
     * @inheritDoc
     */
    public function writeHeaderRow($headerRow)
    {
        $headerStyle = (new StyleBuilder())->setFontBold()->build();
        $this->reportWriter->addRowWithStyle($headerRow, $headerStyle);
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        $this->reportWriter->close();
    }
}
