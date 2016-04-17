<?php
/**
 * A simple database wrapper for the PDO class 
 * and PDO statement class allowing multiple 
 * database connections.
 *
 * @author      Michael Duncan
 * @copyright   (c) 2016 
 */
namespace MultiDB;

use \PDO;
use \PDOException;

class DB {

    private $dbh;
    private $stmt;
    private $data;
    private $conn = '';
    private $settings = array();

    public function __construct($settings=array())
    {
        $this->settings = $settings;
        foreach ($this->settings as $key=>$setting) {
            try {
                $this->dbh[$key] = new PDO('mysql:dbname='.$setting['dbname'].';host='.$setting['host'].'', $setting['user'], $setting['password']);
                $this->dbh[$key]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->dbh[$key]->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo $e->getMessage(); 
            }
        }

        $this->setConn();
    }

    /**
     * Set the an active connection. 
     * Defaults to the first connection setting.
     *
     * @param string $conn name of the connection setting
     */
    public function setConn($conn='') 
    {
        if ($conn == '') {
            // Set the first conn setting to active connection
            $conn = array_keys($this->settings)[0];
        }

        if ($this->dbh[$conn]) {
            $this->conn = $conn;
        }
    }

    /**
     * Get the current connection name
     *
     * @return string
     */
    public function getConn() 
    {
        return $this->conn;
    }

    /**
     * Assign SQL to a PDO statement
     *
     * @param string $statement
     */
    public function prepare($statement) 
    {
        $this->stmt = $this->dbh[$this->conn]->prepare($statement);
    }

    /**
     * Execute an PDO statement with params. 
     * And reset to the default connection.
     *
     * @param array $data
     * @return boolean
     */
    public function execute($data=array()) 
    {
        $this->data = $data;
        $result = $this->stmt->execute($this->data);

        // reset connection to default
        $this->setConn();

        return $result;
    }

    /**
     * Wrapper for PDO rowCount()
     *
     * @return int
     */
    public function rowCount() 
    {
        return $this->stmt->rowCount();
    }

    /**
     * Wrapper for PDO fetch()
     */
    public function fetch() 
    {
        return $this->stmt->fetch();
    }

    /**
     * Wrapper for PDO fetchAll()
     */
    public function fetchAll() 
    {
        return $this->stmt->fetchAll();
    }

    /**
     * Wrapper for PDO fetchColumn()
     *
     * @param int $int
     */
    public function fetchColumn($int=0) 
    {
        return $this->stmt->fetch($int);
    }

    /**
     * Wrapper for PDO lastInsertID()
     */
    public function lastInsertID()
    {
        return $this->dbh[$this->conn]->lastInsertID();
    }

    /**
     * Wrapper for PDO setFetchMode()
     */
    public function setFetchMode($mode=PDO::FETCH_ASSOC)
    {
        return $this->stmt->setFetchMode($mode);
    }

    // Add more wrapper methods as needed...
}

?>
