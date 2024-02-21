<?php
    session_start();

    //* MySQL libs
    require_once('03_PERSISTENCE/MySQL/MySQLDataBaseInserter.php');
    require_once('03_PERSISTENCE/MySQL/MySQLController.php');
    require_once('03_PERSISTENCE/MySQL/MySQLDataBaseConnector.php');
    //* MongoDB libs
    require_once('03_PERSISTENCE/MongoDB/MongoDBConnector.php');

    


    class MainController {

        public $sqlController;
        public $userID;

        private $mySQLcontroller;
        private $mongoDBConnector;
        private $mySQLQuery;

        function __construct(){
            //* Mongo controller
            $this->mongoDBConnector = new MongoDBConnector();

            //* MySQL controller
            $this->mySQLcontroller = new MySQLController();
        }


        
        //* MySQL related data processing -----------------------------------------------------------------

        public function eraseAllData(){
            $_SESSION["users"] = array();
            $this->mySQLcontroller->eraseAllData();
        }

        public function initDataBase($amount){
            $this->mySQLcontroller->initDataBase($amount);
        }

        public function showAllCreatedData(){
            return $this->mySQLcontroller->showAllCreatedData();
        }

        public function retrieveDataFromSQLDataBase(){
            return $this->mySQLcontroller->retrieveDataFromSQLDataBase();
        }
        public function retrieveUserNameFromSQLDataBase(){
            return $this->mySQLcontroller->retrieveUserNameFromSQLDataBase();
        }

        public function retrieveUserDataByUserID($searchedUserID){
        return $this->mySQLQuery->retrieveUserDataByUserID($searchedUserID);
        }
      



       
    }


?>
