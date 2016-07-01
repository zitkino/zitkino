<?php
/**
 * Getting data from MySQL database.
 */
namespace lib\Net;
use mysqli;

class Database {
    private $connection;
    
    public function __construct($server,$user,$password,$database) {
        $this->connection = $this->connect($server,$user,$password,$database);
    }
    public function __destruct() {
        $this->connection->close();
    }
    
    /**
     * Connect into database.
     * 
     * @param string $server Address of server, which have desired database
     * @param string $user Username, which can login into database 
     * @param string $password Password for username login
     * @param string $database Database, which we want to connect
     * @return mysqli Connection to desired database
     */
    public function connect($server,$user,$password,$database) {
        $this->connection=new mysqli($server,$user,$password,$database);
        
        if($this->connection->connect_errno) {
            return die("Can't connect into database:".$this->connection->connect_error);
        }

        $this->connection->query("SET NAMES UTF8") or die("Can't set charset UTF-8: ".$this->connection->error);

        return $this->connection;
    }
    
    /**
     * Closing a connection.
     * 
     * @param mysqli $connection Connection to close
     */
    public function closing($connection) {
        $connection->close();
    }

    /**
     * Returns one value from SELECT query.
     * 
     * @param string $query SELECT query, which can be run
     * @return string 
     */
    public function returning($query) {
       //$connection=connect();
       $result = $this->connection->prepare($query) or die("Bad query: ".$this->connection->error);
       $result->execute();
       $result->bind_result($note);
       $result->fetch();
     
       return $note;

	   $result->free_result();
     //$connection->close();
   }
   
   /**
    * Runs INSERT, UPDATE or DELETE query.
    * 
    * @param string $query INSERT, UPDATE or DELETE query, which can be run
    * @return int Number of affected rows by query
    */
    public function querySQL($query) {
      //$connection=connect();
      $result = $this->connection->prepare($query) or die("Bad query: ".$this->connection->error);
      
      $run=$result->execute();
      if($run==TRUE) { $rows=$result->affected_rows; }
      
      $result->free_result();
      //$connection->close();
      return $rows;
    }
}
?>
