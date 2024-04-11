<?php 
include_once("db_connection.php");
session_start();

if(!isset($_SESSION["user"])){
    header("error.php");
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];


$userId = $_SESSION['user'];
$sql = "SELECT projects.* FROM projects WHERE uid = $userId";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
} else {
    $projects = array();
}
$result->close();
$conn->close();
session_write_close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Projects - AProjects</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
<?php include("header.php");?> 
<div class="content">
<div class="banner <?php echo isset($_SESSION["error"]) ? 'error' : (isset($_SESSION["success"]) ? 'success' : ''); ?>">
    <?php
    if (isset($_SESSION["error"])) {
        echo $_SESSION["error"];
        unset($_SESSION["error"]);
    } else if (isset($_SESSION["success"])) {
        echo $_SESSION['success'];
        unset($_SESSION['success']);
    }
    ?>
</div>
<div class="project-container">
    <div class="project-tabs">
        <div class="tabs">
            <a href="#made" class="active">My Projects</a>
            <a href="#edit">Edit Project</a>
            <a href="#add">New Project</a>
        </div>
    </div>

    <div class="project-card">   
        <div id="made" class="project-tab active">
            <h2>My Projects</h2>
            <div class="results-container">
                <?php
                    if (count($projects)> 0) {
                        foreach ($projects as $project) {
                            echo "<div class='project-item'  onclick=\"document.location='project.php?id={$project['pid']}'\">";
                            echo "<h3>{$project['title']}</h3>";
                            echo "<p><strong>Start Date:</strong> {$project['start_date']}</p>";
                            echo "<p><strong>End Date:</strong> {$project['end_date']}</p>";
                            echo "<p><strong>Short Description:</strong> {$project['description']}</p>";
                            echo '</div>';
                        }
                    } else {
                        echo '<p>You have no projects.</p>';
                    }
                ?>
            </div>
        </div>

        <div id="edit" class="project-tab">
            <h2>Edit Project</h2>
            <div class="edit-container">
                <select onchange="updateProjectDetails(this.value)">;
                	<option value="" disabled selected>Select a project</option>;
                	<?php
                    	if (count($projects) > 0) {
                        	$titles = array();
                        	foreach ($projects as $project) {
                            	echo "<option value='{$project['pid']}'>{$project['title']}</option>";

                        }}?>
                        
                </select>   
                <br>
                <form id="editForm" method="post" action="edit_project.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" id="projectId" name="projectId">
                    <input type="text" id="title" placeholder="Title" name="title" required>
                    <br>
                    <label for="start_date">Start Date:</label >
                    <input type="date" id="start_date" name="startDate" required>
                    <br>
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="endDate" required>
                    <br>
                    <select id="phase" name="phase" required>
                    <option value="" disabled selected>Select a phase</option>
                    <option value="design">Design</option>
                    <option value="development">Development</option>
                    <option value="testing">Testing</option>
                    <option value="deployment">Deployment</option>
                    <option value="complete">Complete</option>
                    </select>
                    <br>
                    <textarea id="description" name="description" placeholder="Enter a brief description (max 200 characters)" maxlength="200" required></textarea>
                    <br>
                    <button type="submit">Save Project</button>
                </form>
                <form method="post" action="delete_project.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken); ?>">
                    <input type="number" id="projectId2" name="projectId" value="" style="display:none" required>
                    <button class="delete-button" type="submit">Delete Project</button>

                </form>
            </div>
        </div>
        <div id="add" class="project-tab">
                <h2>Add Project</h2>
                <div class="edit-container">
                <form id="addForm" method="post" action="add_project.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken); ?>">
                    <input type="text" name="title" placeholder="Title" required>
                    <br>
                    <label for="start_date">Start Date:</label >
                    <input type="date" name="startDate" required>
                    <br>
                    <label for="end_date">End Date:</label>
                    <input type="date" name="endDate" required>
                    <br>
                    <select name="phase" required>
                    <option value="" disabled selected>Select a phase</option>
                    <option value="design">Design</option>
                    <option value="development">Development</option>
                    <option value="testing">Testing</option>
                    <option value="deployment">Deployment</option>
                    <option value="complete">Complete</option>
                    </select>
                    <br>
                    <textarea name="description" placeholder="Enter a brief description (max 200 characters)" maxlength="200" required></textarea>
                    <br>
                    <button type="submit">Add Project</button>
                        </form>
                </div>

        </div>
    </div>
</div>
</div>
</body>
<script>
    document.querySelectorAll('.tabs a').forEach(tabLink => {
        tabLink.addEventListener("click", function (event) {
            event.preventDefault();
            swapTab(tabLink);
        });
    });

    function swapTab(clickedTab) {
        var currentHref = clickedTab.getAttribute('href').substring(1);
        var targetTab =document.getElementById(currentHref);

        document.querySelectorAll('.tabs a, .project-tab').forEach(element => {
            element.classList.remove('active');
        });

        if (targetTab) {
            targetTab.classList.add('active');
            clickedTab.classList.add('active');
        }
    }

    function updateProjectDetails(projectId) {
        var projectDetails = <?php echo json_encode($projects); ?>;
        var selectedProject = projectDetails.find(project => project.pid == projectId);
        document.getElementById('projectId').value = projectId;
        document.getElementById('projectId2').value = projectId;
        document.getElementById('title').value = selectedProject.title;
        document.getElementById('start_date').value = selectedProject.start_date;
        document.getElementById('end_date').value = selectedProject.end_date;
        document.getElementById('phase').value = selectedProject.phase;
        document.getElementById('description').value = selectedProject.description;
    }
</script>

</html>