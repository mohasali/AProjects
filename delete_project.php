<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    include_once("db_connection.php");
    
    $projectId = htmlspecialchars(trim($_POST["projectId"]));

    $query = "DELETE FROM projects WHERE pid = ? AND uid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $projectId, $_SESSION['user']);
    $result = $stmt->execute();

    // Check if the delete was successful
    if ($result && $stmt->affected_rows > 0) {
        $_SESSION['success'] = "Project has been updated successfully.";
    } else if($result && $stmt->affected_rows == 0) {
        $_SESSION['error'] = "An error occured. You are trying to delete a project that is not yours.";
    }
    else{
        $_SESSION['error'] = "An error occurred while trying to update the project. Error: " . $stmt->error;

    }

    header("location: my_projects.php");
    exit();
}
else{
header('Location: error.php');
exit();
}
?>
