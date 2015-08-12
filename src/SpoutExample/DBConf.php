<?php

namespace SpoutExample;

/**
 * Class DBConf
 *
 * @package SpoutExample
 */
class DBConf
{
    const DB_CONF_FILE_PATH = 'conf/db.conf';
    const KEY_DSN = 'dsn';
    const KEY_USERNAME = 'username';
    const KEY_PASSWORD = 'password';

    /** @var string DSN to connect to the database */
    private $dsn;

    /** @var string Username to access the database */
    private $username;

    /** @var string Password to access the database */
    private $password;

    /**
     *
     */
    public function __construct()
    {
        $dbConfig = parse_ini_file(self::DB_CONF_FILE_PATH);

        $this->dsn = $dbConfig[self::KEY_DSN];
        $this->username = $dbConfig[self::KEY_USERNAME];
        $this->password = $dbConfig[self::KEY_PASSWORD];
    }

    /**
     * @return string
     */
    public function getDSN()
    {
        return $this->dsn;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }


}
