<?php
/**
 * User: huydnt
 * Date: 06/01/2017
 * Time: 09:20
 */

namespace Apps\yn_backuprestore\Adapter;


class Mysql extends Abstracts
{
    protected $connection;

    /**
     * Connect to database server
     * @param $mysql_host
     * @param $mysql_username
     * @param $mysql_password
     * @param $mysql_database
     * @param string $mysql_port
     * @return bool
     */
    public function connect($mysql_host, $mysql_username, $mysql_password, $mysql_database, $mysql_port = null)
    {
        // Connect to MySQL server
        if ($mysql_port) {
            $this->connection = @mysqli_connect($mysql_host, $mysql_username, $mysql_password, $mysql_database,
                $mysql_port);
        } else {
            $this->connection = @mysqli_connect($mysql_host, $mysql_username, $mysql_password, $mysql_database);
        }
        if (!$this->connection) {
            return false;
        }
        return true;
    }

    public function upload($file_path)
    {
        // Temporary variable, used to store current query
        $templine = '';
        // Read in entire file
        $lines = file($file_path);
        // Loop through each line
        @mysqli_query($this->connection, "SET NAMES utf8");
        foreach ($lines as $line) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            // Add this line to the current segment
            $templine .= $line;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                // Perform the query
                @mysqli_query($this->connection, $templine);
                // Reset temp variable to empty
                $templine = '';
            }
        }
    }
}