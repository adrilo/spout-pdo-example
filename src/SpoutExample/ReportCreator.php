<?php

namespace SpoutExample;

use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\WriterFactory;
use SpoutExample\Iterator\BatchFetchIterator;
use SpoutExample\Iterator\SingleFetchIterator;

/**
 * Class ReportCreator
 * In this example, we want to generate a report listing all the products available
 * as well as some associated data (quantity available, quantity sold, ...).
 *
 * @package SpoutExample
 */
class ReportCreator
{
    /** Fetching methods */
    const FETCH_ROWS_ONE_BY_ONE = 1;
    const FETCH_ROWS_IN_BATCH = 2;
    const FETCH_ROWS_ALL_AT_ONCE = 3;

    /** @var \PDO PDO instance */
    private $pdo;

    /** @var \Box\Spout\Writer\XLSX\Writer Writer used to create a XLSX report */
    private $reportWriter;

    /** @var int The fetching method to use, used for benchmarks */
    private $fetchingMethod = self::FETCH_ROWS_IN_BATCH;

    /** @var int Number of rows to fetch for each batch (used only when FETCH_ROWS_IN_BATCH is enabled) */
    private $batchSize;

    /**
     * @param DBConf $dbConf
     */
    public function __construct(DBConf $dbConf)
    {
        $this->pdo = new \PDO($dbConf->getDSN(), $dbConf->getUsername(), $dbConf->getPassword());
        $this->reportWriter = WriterFactory::create(Type::XLSX);
    }

    /**
     * @return self
     */
    public function setFetchRowsOneByOne()
    {
        $this->fetchingMethod = self::FETCH_ROWS_ONE_BY_ONE;
        return $this;
    }

    /**
     * @param int $batchSize Number of rows to fetch for each batch
     * @return self
     */
    public function setFetchRowsInBatch($batchSize)
    {
        $this->fetchingMethod = self::FETCH_ROWS_IN_BATCH;
        $this->batchSize = $batchSize;
        return $this;
    }

    /**
     * @return self
     */
    public function setFetchAllRowsAtOnce()
    {
        $this->fetchingMethod = self::FETCH_ROWS_ALL_AT_ONCE;
        return $this;
    }

    /**
     * @return string Name of the fetching method used
     */
    public function getFetchMethodName()
    {
        switch ($this->fetchingMethod) {
            case self::FETCH_ROWS_ONE_BY_ONE: return 'Fetch mode: one by one';
            case self::FETCH_ROWS_IN_BATCH: return 'Fetch mode: batch';
            case self::FETCH_ROWS_ALL_AT_ONCE: return 'Fetch mode: all at once';
        }
    }

    /**
     * Fetches the data from the DB and spits it out in a XLSX file.
     *
     * @param string $outputPath Path where the report will be written to
     * @return void
     */
    public function fetchDataAndCreateReport($outputPath)
    {
        $this->reportWriter->openToFile($outputPath);
        $this->writeReportHeader();

        // Make sure to only select the fields we are interested in
        $query = 'SELECT id, name, price, quantity_available, quantity_sold FROM `product`';

        switch ($this->fetchingMethod) {
            case self::FETCH_ROWS_ONE_BY_ONE:
                $this->fetchRowsOneByOneAndWriteThem($query);
                break;
            case self::FETCH_ROWS_IN_BATCH:
                $this->fetchRowsInBatchAndWriteThem($query);
                break;
            case self::FETCH_ROWS_ALL_AT_ONCE:
                $this->fetchAllRowsAtOnceAndWriteThem($query);
                break;
        }

        $this->reportWriter->close();
    }

    /**
     * @param string $query
     * @param int $maxFetchedRows
     * @return void
     */
    private function fetchRowsOneByOneAndWriteThem($query)
    {
        $dbRowIterator = new SingleFetchIterator($this->pdo, $query);

        foreach ($dbRowIterator as $dbRow) {
            $reportRow = [$dbRow['name'], $dbRow['price'], $dbRow['quantity_available'], $dbRow['quantity_sold']];
            $this->reportWriter->addRow($reportRow);
        }
    }

    /**
     * @param string $query
     * @param int $maxFetchedRows
     * @return void
     */
    private function fetchRowsInBatchAndWriteThem($query)
    {
        $idFieldName = 'id';
        $batchFetchIterator = new BatchFetchIterator($this->pdo, $query, $idFieldName, $this->batchSize);

        foreach ($batchFetchIterator as $dbRows) {
            foreach ($dbRows as $dbRow) {
                $reportRow = [$dbRow['name'], $dbRow['price'], $dbRow['quantity_available'], $dbRow['quantity_sold']];
                $this->reportWriter->addRow($reportRow);
            }
        }
    }

    /**
     * @param string $query
     * @return void
     */
    private function fetchAllRowsAtOnceAndWriteThem($query)
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute();

        $allDBRows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($allDBRows as $dbRow) {
            $reportRow = [$dbRow['name'], $dbRow['price'], $dbRow['quantity_available'], $dbRow['quantity_sold']];
            $this->reportWriter->addRow($reportRow);
        }

        $statement->closeCursor();
    }

    /**
     * @return void
     */
    private function writeReportHeader()
    {
        // The header will be bold
        $headerRow = ['Name', 'Price', 'Available', 'Sold'];
        $headerStyle = (new StyleBuilder())->setFontBold()->build();

        $this->reportWriter->addRowWithStyle($headerRow, $headerStyle);
    }
}
