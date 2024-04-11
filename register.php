<?php
session_start();

if(isset($_SESSION["user"])){
    header("error.php");
    exit();
}
function showAlertAndRedirect($message, $redirectUrl) {
    echo '<script> 
              alert("' . $message . '");
              window.location.href = "' . $redirectUrl . '";
          </script>';
}
include_once("db_connection.php");
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize user inputs
    $username = htmlspecialchars(trim($_POST["username"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = $_POST['password'];  // No need to sanitize; it will be hashed
    $confirmPassword = $_POST['confirmPassword'];  // No need to sanitize

    // Validate the inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        showAlertAndRedirect("Invalid charecters used for username or email.","register.php");
        exit();
    }
    if (strlen($password) < 8) {
        showAlertAndRedirect("Password must be at least 8 characters long.","register.php");
        exit();
    }
    // Check if password matches the confirmed password
    if ($password !== $confirmPassword) {
        showAlertAndRedirect("Passwords do not match.","register.php");
        exit();
    }
    if (!preg_match("/[A-Z]+/", $password) || !preg_match("/\d+/", $password) || !preg_match("/[!@#\$%^&*()_+\-=\[\]{};':\"\\|,.<>\/?]+/", $password)) {
        showAlertAndRedirect("Password must contain at least one capital letter, one number, and one special character.", "register.php");
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? OR username=?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        showAlertAndRedirect("Username or Email already exist.","register.php");
        exit();
    }
    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashedPassword, $email);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    echo'<script> alert("Registration successful! You can now sign in.")</script>';
}
session_write_close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - AProjects</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body>
<?php include("header.php");?>
<div class="container">
    <form class="account-form" id ="registerForm" method="POST">
        <h2>Register</h2>
        <input type="text" placeholder="Username" name="username" required>
        <input type="email" placeholder="Email" name="email" required>
        <input type="password" placeholder="Password" id="password" name="password"required>
        <input type="password" placeholder="Confirm Password" id="confirmPassword" name="confirmPassword"required>
        <button type="button" onclick="submitFrom()">Register</button>
    </form>
</div>
<?php include("footer.php");?>
</body>
</html>

<script>
function submitFrom(){
    var form = document.getElementById("registerForm");
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirmPassword').value;

    var hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/.test(password);
    var hasCapitalLetter = /[A-Z]+/.test(password);
    var hasNumber = /\d+/.test(password);

    if (!form.checkValidity()) {
        // If form is invalid, display error message
        alert("Please fill in all required fields.");
    }
    else if(password !== confirmPassword){
        // If passwords don't match, display error message        
        alert("Passwords do not match.");
    }
    else if(password.length < 8){
        // If password is too short, display error message       
        alert("Password must be at least 8 characters long.");
    }
    else if (!hasSpecialChar || !hasCapitalLetter || !hasNumber) {
        // If password lacks special character, capital letter, or number, display error message
        alert("Password must contain at least one special character, one capital letter, and one number.");
    }
    else {
       form.submit();
    }
}

</script>