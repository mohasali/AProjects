<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    include_once("db_connection.php");

    // Validate all variables
    $errors = [];
    $projectId = htmlspecialchars(trim($_POST["projectId"]));
    $title = htmlspecialchars(trim($_POST["title"]));
    $startDate = htmlspecialchars(trim($_POST["startDate"]));
    $endDate = htmlspecialchars(trim($_POST["endDate"]));
    $phase = htmlspecialchars(trim($_POST["phase"]));
    $description = htmlspecialchars(trim($_POST["description"]));
    echo $projectId;
    if (!isset($projectId)) {
        $errors[] = "Project ID can not be found.";
    }
    if (!isset($title)) {
        $errors[] = "Title is required.";
    }
    if (!isset($startDate)) {
        $errors[] = "Start Date is required.";
    }
    if (!isset($endDate)) {
        $errors[] = "End Date is required.";
    }
    if (!isset($phase)) {
        $errors[] = "Phase is required.";
    }
    if (!isset($description)) {
        $errors[] = "Description is required.";
    }

    // If there are errors, display them
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }

    $query = "UPDATE projects SET title=?, start_date=?, end_date=?, phase=?, description=? WHERE pid=? AND uid=?";
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bind_param("sssssii", $title, $startDate, $endDate, $phase, $description, $projectId,$_SESSION['user']);

    // Execute the query
    $result = $stmt->execute();

    // Check if the update was successful
    if ($result && $stmt->affected_rows > 0) {
        $_SESSION['success'] = "Project has been updated successfully.";
    } else if($result && $stmt->affected_rows == 0) {
        $_SESSION['error'] = "An error occured. You are trying to edit a project that is not yours.";
    }
    else{
        $_SESSION['error'] = "An error occurred while trying to update the project. Error: " . $stmt->error;

    }

    header("location: my_projects.php");
    exit();
}
else{
    http_response_code(403);
    header('Location: error.php');
    exit();
}
?>