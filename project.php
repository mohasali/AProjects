<?php
include("db_connection.php");

if(!isset($_GET['id'])){
    header('Location: error.php');
    exit();
}
$projectId = $_GET['id'];

$query = $conn->prepare("
    SELECT projects.*, users.email 
    FROM projects 
    INNER JOIN users ON projects.uid = users.uid 
    WHERE projects.pid = ?
");
$query->bind_param("i", $projectId);

$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
        $title = $row['title'];
        $startDate = $row['start_date'];
        $end_date = $row['end_date'];
        $phase = $row['phase'];
        $description = $row['description'];
        $email = $row['email'];

} else {
    header('Location: error.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Project - AProjects</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include('header.php')?>

    <div class="container">
        <div class="project-info">
            <h2>Project Details</h2>
            <p><strong>Title:</strong> <?php echo $title; ?></p>
            <p><strong>User Email:</strong> <?php echo $email; ?></p>
            <p><strong>Start Date:</strong> <?php echo $startDate; ?></p>
            <p><strong>End Date:</strong> <?php echo $end_date; ?></p>
            <p><strong>Phase:</strong> <?php echo $phase; ?></p>
            <p><strong>Description:</strong> <?php echo $description; ?></p>
        </div>
    </div>

    <?php include('footer.php')?>
</body>
</html>
