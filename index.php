<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - AProjects</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include('header.php')?>

    <div class="container">
        <div class="homepage">
            <img src="logo.png" alt="Your Logo" id="logo">
            <form class="search-bar-container" method="get" action="search.php">
                <input type="text" name="q" class="search-bar" placeholder="Search for Project Name or Project Start Date...">
                <button class="search-button" type="submit">Search</button>
        </form>
        </div>
    </div>
<?php include('footer.php')?>
</body>
</html>