<?php 

use MongoDB\Driver\Manager;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;

class MongoDBConnector {

    private $servernameMongo = "dbSQL";
    private $usernameMongo = "user";
    private $passwordMongo = "pwd";
    private $dbnameMongo = "RecipeWebsiteMongoDB";
    private $connMongo =  null;

    public function __construct() {
        try{
            $connMongo = new MongoDB\Driver\Manager("mongodb://".$this->usernameMongo.":".$this->passwordMongo."@recipeWebsite_MongoDB:27017");
        
            if($connMongo){
                //echo "<br> successful connect to Mongo!!";
            }
        } catch(MongoDB\Driver\Exception\ConnectionException $e){
            die("Exception: ". $e->getMessage(). "\n"."On line:" . $e->getLine(). "\n");
        }
        
    }

    
    
}

?>
