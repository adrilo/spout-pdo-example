<?php

namespace SpoutExample\Iterator;

/**
 * Class BatchFetchIterator
 *
 * @package SpoutExample\Iterator
 */
class BatchFetchIterator implements \Iterator
{
    /** @var \PDO PDO to connect to the DB */
    private $pdo;

    /** @var string Query to be run */
    private $query;

    /** @var string Name of the ID field */
    private $idFieldName;

    /** @var int Maximum number of rows fetched per iteration */
    private $batchSize;

    /** @var array Last fetched rows */
    private $currentRows;

    /** @var int ID of the last selected row */
    private $lastSelectedRowId = 0;

    /**
     * @param \PDO $pdo
     * @param string $query
     * @param string $idFieldName
     * @param int $batchSize
     */
    public function __construct($pdo, $query, $idFieldName, $batchSize)
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->idFieldName = $idFieldName;
        $this->batchSize = $batchSize;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->lastSelectedRowId = 0;
        unset($this->currentRows);

        $this->next();
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return (!empty($this->currentRows));
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        // Add a WHERE based on the ID and a  LIMIT clauses for an efficient fetching
        // OFFSET must not be used because it does not scale (MySQL may perform a full-table scan)
        // @NOTE: This example is pretty basic and only works with queries that don't already have a WHERE clause
        $fullQuery = $this->query;
        $fullQuery .= " WHERE `{$this->idFieldName}` > {$this->lastSelectedRowId}";
        $fullQuery .= " LIMIT {$this->batchSize}";

        $statement = $this->pdo->prepare($fullQuery);
        $statement->execute();

        // Thanks to the WHERE and LIMIT clauses, fetchAll will only fetch a few results,
        // resulting in a fast and memory friendly operation.
        $this->currentRows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $lastFetchedRow = end($this->currentRows);
        if ($lastFetchedRow !== false) {
            $this->lastSelectedRowId = intval($lastFetchedRow[$this->idFieldName]);
            reset($this->currentRows);
        }
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return $this->currentRows;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->lastSelectedRowId;
    }
}
