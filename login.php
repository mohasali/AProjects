<?php
include_once("db_connection.php");
session_start();

if(isset($_SESSION["user"])){
    header("error.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize user inputs
    $username = htmlspecialchars(trim($_POST["username"]));
    $password = $_POST['password'];

    // Validate the inputs
    if (empty($username)) {
        echo'<script> alert("All fields are required.")</script>';

    }
    else{
    
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row["password"];

        if (password_verify($password, $hashed_password)) {
            session_regenerate_id(true);
            $_SESSION["user"] = $row["uid"];
            header("location:index.php");
            session_write_close();
            exit();
        } 
        else {
           echo '<script>alert("Incorrect password.")</script>';

        }
    } 
    else {
        echo '<script>alert("User not found.")</script>';
    }
}
}
session_write_close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - AProjects</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body>
<?php include("header.php");?>
<div class="container">
    <form class="account-form" method="POST">
        <h2>Login</h2>
        <input type="text" placeholder="Username" name="username" required>
        <input type="password" placeholder="Password" id="password" name="password"required>
        <button type="submit" >Login</button>
    </form>
</div>
<?php include("footer.php");?>
</body>
</html>