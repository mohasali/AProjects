<?php
if($_SERVER['REQUEST_METHOD']='GET'){
    include_once('db_connection.php');
    
    $searchTerm = $_GET['q'];
    $searchTerm = "%$searchTerm%";

    $allowedFields = array("title", "start_date", "end_date");
    $allowedOrders = array("ASC", "DESC");
    $sortField = isset($_GET['sort-by']) && in_array($_GET['sort-by'],$allowedFields) ? $_GET['sort-by'] : 'title';
    $sortOrder = isset($_GET['sort-order']) && in_array($_GET['sort-order'],$allowedOrders) ? $_GET['sort-order'] : 'ASC';


    $sqlQuery = "SELECT * FROM projects WHERE title LIKE ? OR start_date LIKE ? ORDER BY $sortField $sortOrder";

    $query = $conn->prepare($sqlQuery);

    $query->bind_param("ss", $searchTerm, $searchTerm);
    $query->execute();

    $result = $query->get_result();
    $query->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - AProjects</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include('header.php')?>
    <div class="container">
        <div class="search-container">
            <div class="search-options">
                <form class="search-bar-container" method="get">
                    <input type="text" placeholder="Search for Project Name or Project Start Date..." class="search-bar" name="q">
                    <button class="search-button">Search</button>
                </form>
                <div>
                    <select class="sort-by-dropdown" id="sort-by" onchange="updateSort()">
                        <option value="title" <?php echo ($sortField == 'title') ? 'selected' : ''; ?>>Title</option>
                        <option value="start_date" <?php echo ($sortField == 'start_date') ? 'selected' : ''; ?>>Start Date</option>
                        <option value="end_date" <?php echo ($sortField == 'end_date') ? 'selected' : ''; ?>>End Date</option>
                    </select>
                    <select class="sort-type-dropdown" id="sort-order" onchange="updateSort()">
                        <option value="ASC" <?php echo ($sortOrder == 'ASC') ? 'selected' : ''; ?>>Ascending</option>
                        <option value="DESC" <?php echo ($sortOrder == 'DESC') ? 'selected' : ''; ?>>Descending</option>
                    </select>
                </div>
            </div>
            <h1>Search Results</h1>

            <div class="search-results">
                <?php                               
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='project-item'  onclick=\"document.location='project.php?id={$row['pid']}'\">";
                        echo "<h3>{$row['title']}</h3>";
                        echo "<p><strong>Start Date:</strong> {$row['start_date']}</p>";
                        echo "<p><strong>End Date:</strong> {$row['end_date']}</p>";
                        echo "<p><strong>Short Description:</strong> {$row['description']}</p>";
                        echo '</div>';
                    }
                }
                else{
                    echo 'No results found.';
                }
                ?>
            </div>


        </div>
    </div>
    
<?php include('footer.php')?>
</body>

<script>
    function updateSort() {
        var sortBy = document.getElementById('sort-by').value;
        var sortOrder = document.getElementById('sort-order').value;

        // Get the current URL parameters
        var urlParams = new URLSearchParams(window.location.search);
        
        // Update or add the sorting parameters
        urlParams.set('sort-by', sortBy);
        urlParams.set('sort-order', sortOrder);

        // Redirect to the same page with updated URL parameters
        window.location.href = 'search.php?' + urlParams.toString();
    }
</script>
</html>