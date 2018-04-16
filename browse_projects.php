<?php

session_start();
require 'connect.php';

if (isset($_GET['search_key'])) {
    $search_key = mysqli_real_escape_string($conn, $_GET['search_key']);
} else {
    $search_key = "";
}

if (isset($_GET['category'])) {
    $category = mysqli_real_escape_string($conn, $_GET['category']);
}

if (isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
}

$sql_select_ongoing_projects = "
SELECT
    p.project_id,
    title,
    category,
    kl.keyword_links AS keyword_links,
    COALESCE(f.fundings, 0) AS funding_amount,
    funding_goal,
    end_datetime
FROM
    projects p LEFT JOIN
    (SELECT
        project_id,
        GROUP_CONCAT(CONCAT('<a href=?keyword=', keyword, '>', keyword, '</a>') SEPARATOR ', ') AS keyword_links
    FROM
        project_keywords
    GROUP BY
        project_id) AS kl ON p.project_id = kl.project_id LEFT JOIN
    (SELECT
        project_id,
        SUM(amount) AS fundings
    FROM
        fundings
    GROUP BY
        project_id) AS f ON p.project_id = f.project_id
WHERE
    p.end_datetime > CURDATE() AND
    (title LIKE '%$search_key%' OR category LIKE '%$search_key%' OR '$search_key' IN (SELECT keyword FROM project_keywords WHERE project_id = p.project_id))
ORDER BY
    COALESCE(f.fundings, 0) * 1.0 / funding_goal DESC
";

$sql_select_funded_projects = "
SELECT
    p.project_id,
    title,
    category,
    kl.keyword_links AS keyword_links,
    COALESCE(f.fundings, 0) AS funding_amount,
    funding_goal,
    end_datetime
FROM
    projects p LEFT JOIN
    (SELECT
        project_id,
        GROUP_CONCAT(CONCAT('<a href=?keyword=', keyword, '>', keyword, '</a>') SEPARATOR ', ') AS keyword_links
    FROM
        project_keywords
    GROUP BY
        project_id) AS kl ON p.project_id = kl.project_id LEFT JOIN
    (SELECT
        project_id,
        SUM(amount) AS fundings
    FROM
        fundings
    GROUP BY
        project_id) AS f ON p.project_id = f.project_id
WHERE
    p.end_datetime <= CURDATE() AND
    COALESCE(f.fundings, 0) >= p.funding_goal AND
    (title LIKE '%$search_key%' OR category LIKE '%$search_key%' OR '$search_key' IN (SELECT keyword FROM project_keywords WHERE project_id = p.project_id))
ORDER BY
    end_datetime DESC
";

if (isset($category)) {
    $sql_select_ongoing_projects = "
    SELECT
        *
    FROM
        ($sql_select_ongoing_projects) AS uf_p
    WHERE
        category = '$category'
    ";

    $sql_select_funded_projects = "
    SELECT
        *
    FROM
        ($sql_select_funded_projects) AS f_p
    WHERE
        category = '$category'
    ";
}

if (isset($keyword)) {
    $sql_select_ongoing_projects = "
    SELECT
        *
    FROM
        ($sql_select_ongoing_projects) AS uf_p NATURAL JOIN project_keywords
    WHERE
        keyword = '$keyword'
    ";

    $sql_select_funded_projects = "
    SELECT
        *
    FROM
        ($sql_select_funded_projects) AS f_p NATURAL JOIN project_keywords
    WHERE
        keyword = '$keyword'
    ";
}

if ($result = $conn->query($sql_select_ongoing_projects)) {
    $ongoing_projects = array();
    while ($row = $result->fetch_assoc()) {
        array_push($ongoing_projects, $row);
    }
}

if ($result = $conn->query($sql_select_funded_projects)) {
    $funded_projects = array();
    while ($row = $result->fetch_assoc()) {
        array_push($funded_projects, $row);
    }
}

?>

<!DOCTYPE html>
<html lang="en-US">
<head>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
    <script src="bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>

    <meta charset="UTF-8">
    <title>Search projects</title>

	<!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700' rel='stylesheet' type='text/css'>

    <!-- Custom styles for this template -->
    <link href="css/crowdfunding.min.css" rel="stylesheet">

</head>

<body id="page-top">
    <?php
        include 'header.php';
    ?>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color:Black;" id="mainNav">
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="#page-top">DreamFactory</a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
              Menu
                <i class="fa fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav text-uppercase ml-auto">
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="homepage.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="#ongoingProjects">Ongoing Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="#fundedProjects">Funded Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="create_project.php">Create Project</a>
                    </li>

                    <li class="nav-item">
                        <?php
                        if (isset($_SESSION['email'])) {
                          echo '<a class="nav-link js-scroll-trigger" href="logout.php">Logout</a>';
                        } else {
                          echo '<a class="nav-link js-scroll-trigger" href="login.php?redirect=browse_projects.php">Login</a>';
                        }
                        ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="ongoingProjects">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <form method="get" action=?>
                        Search: <input style="width: 200px; height: 35px" type="text" name="search_key">
                    </form>

                    <br>
                    <br>

                    <?php

                    if (isset($search_key) && !empty($search_key)) {
                        echo "
                            <h5 class='section-heading'>Search results on '$search_key'</h5>
                            <br>
                        ";
                    }
                    ?>

                    <h2 class="section-heading text-uppercase">Ongoing Projects</h2>
                    <table align="center" border=1 cellpadding=5>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Keywords</th>
                            <th>Fundings</th>
                            <th>Goal</th>
                            <th>Deadline</th>
                            <th>Fund</th>
                            <th>Delete</th>
                        </tr>
                        <?php

                        foreach ($ongoing_projects as $ongoing_project) {
                        echo '<tr align="center">';
                        echo '<td><a href="view_project.php?project_id=' . $ongoing_project['project_id'] . '">' . $ongoing_project['title'] . '</a></td>';
                        echo '<td><a href=?category=' . urlencode($ongoing_project['category']) . '>' . $ongoing_project['category'] . '</a></td>';
                        echo '<td>' . $ongoing_project['keyword_links'] . '</td>';
                        echo '<td>$ ' . $ongoing_project['funding_amount'] . '</td>';
                        echo '<td>$ ' . $ongoing_project['funding_goal'] . '</td>';
                        echo '<td>' . $ongoing_project['end_datetime'] . '</td>';
                        echo '<td><a href="fund_project.php?project_id=' . $ongoing_project['project_id'] . '">+</a></td>';
                        echo '<td><a href="delete_project.php?project_id=' . $ongoing_project['project_id'] . '&redirect=browse_projects.php">X</a></td>';
                        echo '</tr>';
                        }

                        ?>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section id="fundedProjects">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading text-uppercase">Funded Projects</h2>
                    <table align="center" border=1 cellpadding=5>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Keywords</th>
                            <th>Fundings</th>
                            <th>Goal</th>
                            <th>Deadline</th>
                            <th>Fund</th>
                            <th>Delete</th>
                        </tr>

                        <?php
                        foreach ($funded_projects as $funded_project) {
                        echo '<tr align="center">';
                        echo '<td><a href="view_project.php?project_id=' . $funded_project['project_id'] . '">' . $funded_project['title'] . '</a></td>';
                        echo '<td><a href=?category=' . urlencode($funded_project['category']) . '>' . $funded_project['category'] . '</a></td>';
                        echo '<td>' . $funded_project['keyword_links'] . '</td>';
                        echo '<td>$ ' . $funded_project['funding_amount'] . '</td>';
                        echo '<td>$ ' . $funded_project['funding_goal'] . '</td>';
                        echo '<td>' . $funded_project['end_datetime'] . '</td>';
                        echo '<td><a href="fund_project.php?project_id=' . $funded_project['project_id'] . '">+</a></td>';
                        echo '<td><a href="delete_project.php?project_id=' . $funded_project['project_id'] . '&redirect=browse_projects.php">X</a></td>';
                        echo '</tr>';
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </section>

	<!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Contact form JavaScript -->
    <script src="js/jqBootstrapValidation.js"></script>
    <script src="js/contact_me.js"></script>

    <!-- Custom scripts for this template -->
    <script src="js/crowdfunding.min.js"></script>

</body>
</html>
