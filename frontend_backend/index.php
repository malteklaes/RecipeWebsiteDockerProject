<?php

session_start();
$_SESSION["migrationToMongoStarted"] = false;

//* MySQL-Part
if (!$_SESSION["migrationToMongoStarted"]) {
    //* main controller
    require_once('02_SERVICE/MainController.php');


    //* login if button is pressed and session "selectedUser" will be started
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["login"])) {
            $selectedUser = $_POST["user-dropdown"];
            $_SESSION["selectedUser"] = $selectedUser;
            header("Location: /01_UI/UI_Main.php");
            exit;
        }
    }


    $mainController = new MainController();
    $userArray = $mainController->retrieveDataFromSQLDataBase();
    $userArray = array();
    $userArrayUserNames = array();
    if (isset($_POST['eraseAllSQLButton'])) {
        $mainController->eraseAllData();
    } elseif (isset($_POST['createDataButton'])) {
        $mainController->initDataBase(20);
        $output_database_pdo = $mainController->showAllCreatedData();
        $userArray = $mainController->retrieveDataFromSQLDataBase();
        $userArrayUserNames = $mainController->retrieveUserNameFromSQLDataBase();
        $combinedArray = array_map(null, $userArray, $userArrayUserNames);
        $_SESSION["users"] = $userArray;
        $_SESSION["dataCreated"] = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecipeWebsite</title>
    <link rel="stylesheet" href="LayoutHTML/style.css">
    <style>
        .green-button {
            background-color: #24b55c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px 36px;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .green-button:hover {
            background-color: #1a9745;
        }

        .login-button {
            background-color: white;
            color: black;
            border: 3px solid black;
            padding: 7px 15px;
            font-size: 20px 40px;
            font-weight: bold;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background-color: #1a9745;
        }

        .red-button {
            background-color: #E84E46;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px 32px;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .red-button:hover {
            background-color: #7d2914;
        }

        .centered-image {
            display: flex;
            justify-content: center;
            margin-top: 0px;
        }

        #container {
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .centered-object {
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="container">
        <div class="centered-image">
            <img src="LayoutHTML/Logo/logo.png" alt="Logo">
        </div>
        <form class="centered-object" method="post">
            <input type="submit" name="createDataButton" value="Create MySQL Data" class="green-button">
        </form>
        <br>
        <form class="centered-object" method="post">
            <input type="submit" name="eraseAllSQLButton" value="Erase all MySQL Data" class="red-button">
        </form>



        <div>
            <p><?php echo $outputSQLInserter; ?></p>
        </div>

        <div>
            <?php
            if (isset($output_database_pdo)) {
                echo "<p>" . $output_database_pdo . "</p>";
            }
            ?>
        </div>



        <h1 class="centered-object">Choose user</h1>

        <form class="centered-object" method="POST" action="<?php echo  $_SERVER["PHP_SELF"]; ?>">
            <label for="user-dropdown">username:</label>
            <select id="user-dropdown" name="user-dropdown">
                <?php foreach ($combinedArray as $user) : ?>
                    <option value="<?php echo $user[0]; ?>"><?php echo $user[1]; ?></option>
                <?php endforeach; ?>
            </select>

            <br>
            <br>

            <input class="login-button" type="submit" name="login" value="Login">
        </form>


    </div>
</body>

</html>