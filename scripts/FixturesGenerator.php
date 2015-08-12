<?php

/**
 * Class FixturesGenerator
 *
 */
class FixturesGenerator
{
    /**
     * @param string $outputPath
     * @param int $numFixturesToGenerate
     * @return void
     */
    public function generate($outputPath, $numFixturesToGenerate)
    {
        $handle = fopen($outputPath, 'w');

        $fileContents = <<<EOD
DROP USER 'spout-pdo'@'localhost';
CREATE USER 'spout-pdo'@'localhost' IDENTIFIED BY 'v$6SnTBD8!\$EtB';
GRANT ALL ON `spout-pdo`.`product` TO 'spout-pdo'@'localhost';

DROP DATABASE IF EXISTS `spout-pdo`;
CREATE DATABASE `spout-pdo`;
USE `spout-pdo`;

CREATE TABLE `product` (
    id BIGINT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity_available MEDIUMINT DEFAULT 0,
    quantity_sold MEDIUMINT DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


EOD;

        fwrite($handle, $fileContents);

        $insertStatement = 'INSERT INTO `product` VALUES ';

        for ($i = 1; $i <= $numFixturesToGenerate; $i++) {
            $name = "Product $i";
            $description = "This is Product $i";

            // Generate a price between 5.00 and 1000.00
            $price = rand(500, 100000) / 100;

            // Generate some random quantities
            $quantityAvailable = rand(1, 10000);
            $quantitySold = rand(0, $quantityAvailable);

            $values = "(null, '$name', '$description', $price, $quantityAvailable, $quantitySold);\n";
            fwrite($handle, $insertStatement . $values);
        }

        fclose($handle);
    }
}
