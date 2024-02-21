<?php

    require_once(__DIR__.'/../MongoDBEntity/UserMongo.php');
    require_once(__DIR__.'/../MongoDBEntity/CommentMongo.php');
    require_once(__DIR__.'/../MongoDBEntity/RecipeMongo.php');
    require_once(__DIR__.'/../MongoDBEntity/ListRecipeMongo.php');



    class MongoCommentObjectConverter {

        private $commentsArray;

        function __construct() {
            $this->commentsArray = array();
            $this->collectAllComments();
        }

        /**
         * creates all user-objects like this
         * (1) connect to MySQL-db and retrieves array with all users
         * (2) fills $this->usersArray with all those users  
         * @return void
         */
        private function collectAllComments(){
            $comments = $this->collectAllCommentsFromMySQL();
            foreach($comments as $comment){     
                array_push($this->commentsArray, new CommentMongo($comment[1], $comment[2], $comment[4], $comment[5]));
            }
        }


        
        private function collectAllCommentsFromMySQL(){
            $comments = array();
            $sql = "SELECT * FROM comment";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->execute();

            //* collect recipes
            while ($commentData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $commentInfoBundle = array();
                array_push($commentInfoBundle, $commentData['postID'] );
                array_push($commentInfoBundle, $commentData['commentContent'] );
                array_push($commentInfoBundle, $this->collectAllCommentsFromMySQL_GETownerName($commentData['ownerUserID']) );
                array_push($commentInfoBundle, $commentData['recipeIDReference'] );
                array_push($commentInfoBundle, $commentData['creationDate'] );
                array_push($commentInfoBundle, $commentData['editedDate'] );
                $comments[] = $commentInfoBundle;
            }
            
            return $comments;
        }

        private function collectAllCommentsFromMySQL_GETownerName($userID){
            $sql = "SELECT * FROM user WHERE userID = :userID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();

            if($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $userData['username'];
            }
            return null;
        }


        public function getCommentsArray() {
        	return $this->commentsArray;
        }
    }


?>