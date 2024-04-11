
<header>
<h2><a href="index.php">AProjects</a></h2>
<nav>
<ul>
<?php
session_start();
if(isset($_SESSION["user"])){
        echo'<li><a href="my_projects.php">My Projects</a></li> <li><a href="sign_out.php">Sign Out</a></li>';
}
else{
        echo'<li><a href="login.php">Login</a></li> <li><a href="register.php">Register</a></li>';
}
?>
</ul>
</nav>
</header>