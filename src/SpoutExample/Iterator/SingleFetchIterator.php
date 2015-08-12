<?php

namespace SpoutExample\Iterator;

/**
 * Class SingleFetchIterator
 *
 * @package SpoutExample\Iterator
 */
class SingleFetchIterator implements \Iterator
{
    /** @var \PDOStatement $statement PDO Statement to execute */
    private $statement;

    /** @var int $cursorOffset Cursor offset */
    private $cursorOffset;

    /** @var array|bool Last fetched row. FALSE on failure */
    private $currentRow;

    /**
     * @param \PDO $pdo
     * @param string $query
     */
    public function __construct($pdo, $query)
    {
        // This setting allows to keep the memory consumption really low (there is performance tradeoff though)
        // @see http://php.net/manual/en/mysqlinfo.concepts.buffering.php
        $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

        // These options tell PDO to use a scrollable cursor, so that
        // we can fetch rows in batch and rewind if needed.
        $driverOptions = [\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL];

        $this->statement = $pdo->prepare($query, $driverOptions);
        $this->statement->execute();
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->cursorOffset = 0;
        unset($this->currentRow);

        $this->next();
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return ($this->currentRow !== false);
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->cursorOffset++;

        // Fetching one row, located at the given offset in the result set
        // (This is what PDO::FETCH_ORI_ABS is for).
        $this->currentRow = $this->statement->fetch(
            \PDO::FETCH_ASSOC,
            \PDO::FETCH_ORI_ABS,
            $this->cursorOffset
        );
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return $this->currentRow;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->cursorOffset;
    }
}
