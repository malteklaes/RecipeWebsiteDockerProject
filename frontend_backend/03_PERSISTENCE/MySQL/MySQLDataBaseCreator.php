<?php

    require_once('MySQLDataBaseConnector.php');
    require_once('MySQLDataBaseInserter.php');


    class MySQLDataBaseCreator {
        private $sqlConnector;
        private $sqlConn;
        private $sqlInserter;

        public function __construct() {
            //* connect to database
        	$this->sqlConnector = new MySQLDataBaseConnector();
            $this->sqlConn = $this->sqlConnector->getConnection();
            $this->sqlInserter = new MySQLDataBaseInserter();
            //* set procedure
            $this->createUserSchema();
            $this->createRecipeSchema();
            $this->createCommentSchema();
            $this->createListRecipeSchema();
            $this->createUserRatesRecipeSchema();
            $this->createUserLikesListRecipeSchema();
            $this->createUserFollowSchema();
            $this->createListContainsOfRecipeSchema();
        }

        //* USER SCHEMA -------------------------------------------------------------
        private function createUserSchema(){
            //* erase USER table
            $this->sqlConn->exec("DROP TABLE IF EXISTS user");

            //* create USER table
			$sqlCreation = "CREATE TABLE user (
                userID VARCHAR(255) PRIMARY KEY NOT NULL,
                username VARCHAR(255) NOT NULL,
                email VARCHAR(255),
                pwd VARCHAR(255) NOT NULL,
                registrationDate DATETIME NOT NULL
                )";
            $this->sqlConn->exec($sqlCreation);
        }

        public function eraseUserSchema(){
            $this->sqlConn->exec("DROP TABLE IF EXISTS user");
        }

        //* RECIPE SCHEMA -------------------------------------------------------------
        private function createRecipeSchema(){
            //* erase RECIPE table
            $this->sqlConn->exec("DROP TABLE IF EXISTS recipe");

            //* create RECIPE table
			$sqlCreation = "CREATE TABLE recipe (
                postID VARCHAR(255) PRIMARY KEY NOT NULL,
                recipeName VARCHAR(255) NOT NULL,
                recipeDescription TEXT NOT NULL,
                category VARCHAR(255),
                rating FLOAT,
                ownerUserID VARCHAR(255) NOT NULL,
                creationDate DATETIME NOT NULL,
                editedDate DATETIME
                )";
            $this->sqlConn->exec($sqlCreation);
        }

        public function eraseRecipeSchema(){
            $this->sqlConn->exec("DROP TABLE IF EXISTS recipe");
        }

        //* COMMENT SCHEMA -------------------------------------------------------------
        private function createCommentSchema(){
            //* erase COMMENT table
            $this->sqlConn->exec("DROP TABLE IF EXISTS comment");

            //* create COMMENT table
			$sqlCreation = "CREATE TABLE comment (
                postID VARCHAR(255) PRIMARY KEY NOT NULL,
                commentContent TEXT,
                ownerUserID VARCHAR(255) NOT NULL,
                recipeIDReference VARCHAR(255),
                creationDate DATETIME NOT NULL,
                editedDate DATETIME
                )";
            $this->sqlConn->exec($sqlCreation);
        }

        public function eraseCommentSchema(){
            $this->sqlConn->exec("DROP TABLE IF EXISTS comment");
        }


         //* LISTRECIPE SCHEMA -------------------------------------------------------------
         private function createListRecipeSchema(){
            //* erase LISTRECIPE table
            $this->sqlConn->exec("DROP TABLE IF EXISTS listRecipe");

            //* create LISTRECIPE table
			$sqlCreation = "CREATE TABLE listRecipe (
                listID VARCHAR(255) PRIMARY KEY NOT NULL,
                ownerID VARCHAR(255) NOT NULL,
                listName VARCHAR(255) NOT NULL,
                likesAmount INT,
                listDescription TEXT,
                privateStatus BOOLEAN NOT NULL,
                creationDate DATETIME NOT NULL,
                editedDate DATETIME
                )";
            $this->sqlConn->exec($sqlCreation);
        }

        public function eraseListRecipeSchema(){
            $this->sqlConn->exec("DROP TABLE IF EXISTS listRecipe");
        }

        //* USERRATESRECIPE SCHEMA -------------------------------------------------------------
        private function createUserRatesRecipeSchema(){
            //* erase USERRATESRECIPE table
            $this->sqlConn->exec("DROP TABLE IF EXISTS userRatesRecipe");

            //* create USERRATESRECIPE table
			$sqlCreation = "CREATE TABLE userRatesRecipe (
                userID VARCHAR(255) NOT NULL,
                postID VARCHAR(255) NOT NULL,
                rating INT NOT NULL
                )";
            $this->sqlConn->exec($sqlCreation);
        }

        public function eraseUserRatesRecipeSchema(){
            $this->sqlConn->exec("DROP TABLE IF EXISTS userRatesRecipe");
        }

        //* USERLIKESLISTRECIPE SCHEMA -------------------------------------------------------------
        private function createUserLikesListRecipeSchema(){
            //* erase USERLIKESRECIPE table
            $this->sqlConn->exec("DROP TABLE IF EXISTS userLikesListRecipe");

            //* create USERLIKESRECIPE table
			$sqlCreation = "CREATE TABLE userLikesListRecipe (
                userID VARCHAR(255) NOT NULL,
                listID VARCHAR(255) NOT NULL
                )";
            $this->sqlConn->exec($sqlCreation);
        }

        public function eraseUserLikesListRecipeSchema(){
            $this->sqlConn->exec("DROP TABLE IF EXISTS userLikesListRecipe");
        }

        //* USERFOLLOW SCHEMA -------------------------------------------------------------
        private function createUserFollowSchema(){
            //* erase USERLIKESRECIPE table
            $this->sqlConn->exec("DROP TABLE IF EXISTS userFollow");

            //* create USERLIKESRECIPE table
			$sqlCreation = "CREATE TABLE userFollow (
                user1ID VARCHAR(255) NOT NULL,
                user2ID VARCHAR(255) NOT NULL
                )";
            $this->sqlConn->exec($sqlCreation);
        }

        public function eraseUserFollowSchema(){
            $this->sqlConn->exec("DROP TABLE IF EXISTS userFollow");
        }

        //* LISTCONTAINSOFRECIPE SCHEMA -------------------------------------------------------------
        private function createListContainsOfRecipeSchema(){
            //* erase USERLIKESRECIPE table
            $this->sqlConn->exec("DROP TABLE IF EXISTS listContainsOfRecipe");

            //* create USERLIKESRECIPE table
			$sqlCreation = "CREATE TABLE listContainsOfRecipe (
                listID VARCHAR(255) NOT NULL,
                recipeID VARCHAR(255) NOT NULL
                )";
            $this->sqlConn->exec($sqlCreation);
        }

        public function eraseListContainsOfRecipeSchema(){
            $this->sqlConn->exec("DROP TABLE IF EXISTS listContainsOfRecipe");
        }


        public function getSQLConnector(){
            return $this->sqlConnector;
        }
        public function getSQLConn(){
            return $this->sqlConn;
        }

        public function closeThisDataBaseConnection(){
            $this->sqlConnector = null;
        }

    }



?>