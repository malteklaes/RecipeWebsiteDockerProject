<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecipeWebsite</title>
    <style>
        body {
            margin: 0px;
        }
        
        header {
            background-color:#E15A5A;
            padding: 15px;
        }

        header nav ul {
            list-style: none;
            padding: 0;
            background-color:#E15A5A;
            margin: 0;
        }

        header nav ul li {
            display: inline-block;
            margin-right: 20px;
        }

        header nav ul li a {
            text-decoration: none;
            color: #FFFFFF;
        }

        header nav ul li a:hover {
            text-decoration: none;
            color: #737b80;
        }


        .header a:hover,
        .dropdown:hover .dropbtn {
            color: #737b80;
        }

        .dropdown-content {
            background-color: #FFFFFF;
            display: none;
            position: absolute;
            min-width: 100px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            float: none;
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .dropdown-content a:hover {
            color: orangered;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }


    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var dropdownTrigger = document.querySelector(".dropdown-trigger");
            var dropdownMenu = document.querySelector(".dropdown-menu");

            dropdownTrigger.addEventListener("click", function() {
                dropdownMenu.classList.toggle("active");
            });
        });
    </script>


</head>

<body>

    <header>
        <nav>
            <ul>
                <div style="margin-left:40px">          
                    <li><a href='../../01_UI/UI_Main.php'>Profile</a></li>
                    <li><a href='../../01_UI/UI_MyRecipes.php'>My Recipes</a></li>
                    <li><a href='../../01_UI/UI_MyLists.php'>My Lists</a></li>
                    <li class="dropdown">
                        <a class="dropbtn">Reports &#8964
                            <i class="fa fa-caret-down"></i>
                        </a>
                        <div class="dropdown-content">
                        <a href='../../01_UI/UI_Report1.php'>Report 1</a>
                        <a href='../../01_UI/UI_Report2.php'>Report 2</a>
                        </div>
                    </li> 

                    <li class="dropdown">
                        <a class="dropbtn">Check raw MongoDB-Data &#8964
                            <i class="fa fa-caret-down"></i>
                        </a>
                        <div class="dropdown-content">
                        <a href='../../01_UI/UI_CheckMongoData/UI_CheckUserMongoData.php'><b>User</b> MongoDB-Data</a>
                        <a href='../../01_UI/UI_CheckMongoData/UI_CheckRecipeMongoData.php'><b>Recipe</b> MongoDB-Data</a>
                        <a href='../../01_UI/UI_CheckMongoData/UI_CheckCommentMongoData.php'><b>Comments</b> MongoDB-Data</a>
                        <a href='../../01_UI/UI_CheckMongoData/UI_CheckListRecipeMongoData.php'><b>ListRecipe</b> MongoDB-Data</a>
                        </div>
                    </li> 
                </div>   
                </div>   
            </ul>
        </nav>
    </header>





</body>

</html>