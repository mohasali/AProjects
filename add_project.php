<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    include_once("db_connection.php");

    // Validate all variables
    $errors = [];
    $title = htmlspecialchars(trim($_POST["title"]));
    $startDate = htmlspecialchars(trim($_POST["startDate"]));
    $endDate = htmlspecialchars(trim($_POST["endDate"]));
    $phase = htmlspecialchars(trim($_POST["phase"]));
    $description = htmlspecialchars(trim($_POST["description"]));

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

    $query = "INSERT INTO projects (title, start_date, end_date, phase, description,uid) VALUES(?,?,?,?,?,?)";
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bind_param("sssssi", $title, $startDate, $endDate, $phase, $description,$_SESSION["user"]);

    // Execute the query
    $result = $stmt->execute();

    // Check if the update was successful
    if ($result) {
        $_SESSION['success'] = "Project has been added successfully.";
    } else {
        $_SESSION['error'] = "An error occured trying to add the project. Error: ".$stmt->error;
    }
    $stmt->close();
    $conn->close();
    header("location: my_projects.php");
    exit();
}
else{
    header('Location: error.php');
    exit();
}
?>