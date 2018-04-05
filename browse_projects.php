<?php

session_start();
require 'connect.php';

if (isset($_GET['search_key'])) {
    $search_key = mysqli_real_escape_string($conn, $_GET['search_key']);
} else if (isset($_GET['category'])) {
    $category = mysqli_real_escape_string($conn, $_GET['category']);
} else if (isset($_GET['keyword'])) {
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
    p.end_datetime > CURDATE()
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
    COALESCE(f.fundings, 0) >= p.funding_goal
ORDER BY
    end_datetime DESC
";

$sql_select_unfunded_projects = "
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
    COALESCE(f.fundings, 0) < p.funding_goal
ORDER BY
    end_datetime DESC
";

if (isset($search_key)) {
    $sql_select_ongoing_projects = "
    SELECT
        *
    FROM
        ($sql_select_ongoing_projects) AS uf_p
    WHERE
        title LIKE '%$search_key%'
    ";

    $sql_select_funded_projects = "
    SELECT
        *
    FROM
        ($sql_select_funded_projects) AS f_p
    WHERE
        title LIKE '%$search_key%'
    ";

    $sql_select_unfunded_projects = "
    SELECT
        *
    FROM
        ($sql_select_unfunded_projects) AS f_p
    WHERE
        title LIKE '%$search_key%'
    "; 
} else if (isset($category)) {
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

    $sql_select_unfunded_projects = "
    SELECT
        *
    FROM
        ($sql_select_unfunded_projects) AS f_p
    WHERE
        category = '$category'
    ";
} else if (isset($keyword)) {
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

    $sql_select_unfunded_projects = "
    SELECT
        *
    FROM
        ($sql_select_unfunded_projects) AS f_p NATURAL JOIN project_keywords
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

if ($result = $conn->query($sql_select_unfunded_projects)) {
    $unfunded_projects = array();
    while ($row = $result->fetch_assoc()) {
        array_push($unfunded_projects, $row);
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
<body>
    <form method="get" action=?>
	<br> &nbsp; <!--&nbsp; is for indentation -->
    Search: <input style="width: 200px; height: 35px" type="text" name="search_key">
    </form>

    <br>

    <fieldset>
	    <div class="col-lg-12 text-center">
        <h2 class="section-heading text-uppercase">Ongoing Projects</h2>
        <table align="center" border=1>
            <tr>
                <th>&nbsp; Title &nbsp;</th>
                <th>&nbsp; Category &nbsp;</th>
                <th>&nbsp; Keywords &nbsp;</th>
                <th>&nbsp; Fundings &nbsp;</th>
                <th>&nbsp; Goal &nbsp;</th>
                <th>&nbsp; Deadline &nbsp;</th>
                <th>&nbsp; Fund &nbsp;</th>
                <th>&nbsp; Delete &nbsp;</th>
            </tr>
            <?php

            foreach ($ongoing_projects as $ongoing_project) {
                echo '<tr align="center">';
                echo '<td> &nbsp;' . $ongoing_project['title'] . '&nbsp;</td>';
                echo '<td> &nbsp;<a href=?category=' . urlencode($ongoing_project['category']) . '>' . $ongoing_project['category'] . '&nbsp;</a></td>';
                echo '<td> &nbsp;' . $ongoing_project['keyword_links'] . '&nbsp;</td>';
                echo '<td> &nbsp;' . $ongoing_project['funding_amount'] . '&nbsp;</td>';
                echo '<td> &nbsp;' . $ongoing_project['funding_goal'] . '&nbsp;</td>';
                echo '<td> &nbsp;' . $ongoing_project['end_datetime'] . '&nbsp;</td>';
                echo '<td> &nbsp;<a href="fund_project.php?project_id=' . $ongoing_project['project_id'] . '">+&nbsp;</a></td>';
                echo '<td> &nbsp;<a href="delete_project.php?project_id=' . $ongoing_project['project_id'] . '&redirect=browse_projects.php">X&nbsp;</a></td>';
				echo '<td> &nbsp;<a href="view_project.php?project_id=' . $ongoing_project['project_id'] . '">VIEW&nbsp;</a></td>';
                echo '</tr>';
            }

            ?>
        </table>
		</div>
    </fieldset>

    <br>

    <fieldset>
		<div class="col-lg-12 text-center">
		<h2 class="section-heading text-uppercase">Funded Projects</h2>
        <table align="center" border=1>
            <tr>
                <th>&nbsp; Title &nbsp;</th>
                <th>&nbsp; Category &nbsp;</th>
                <th>&nbsp; Keywords &nbsp;</th>
                <th>&nbsp; Fundings &nbsp;</th>
                <th>&nbsp; Goal &nbsp;</th>
                <th>&nbsp; Deadline &nbsp;</th>
                <th>&nbsp; Fund &nbsp;</th>
                <th>&nbsp; Delete &nbsp;</th>
            </tr>

            <?php
            foreach ($funded_projects as $funded_project) {
                echo '<tr align="center">';
                echo '<td> &nbsp;' . $funded_project['title'] . '&nbsp;</td>';
                echo '<td> &nbsp;<a href=?category=' . urlencode($funded_project['category']) . '>' . $funded_project['category'] . '&nbsp;</a></td>';
                echo '<td> &nbsp;' . $funded_project['keyword_links'] . '&nbsp;</td>';
                echo '<td> &nbsp;' . $funded_project['funding_amount'] . '&nbsp;</td>';
                echo '<td> &nbsp;' . $funded_project['funding_goal'] . '&nbsp;</td>';
                echo '<td> &nbsp;' . $funded_project['end_datetime'] . '&nbsp;</td>';
                echo '<td> &nbsp;<a href="fund_project.php?project_id=' . $funded_project['project_id'] . '">+&nbsp;</a></td>';
                echo '<td> &nbsp;<a href="delete_project.php?project_id=' . $funded_project['project_id'] . '&redirect=browse_projects.php">X&nbsp;</a></td>';
				echo '<td> &nbsp;<a href="view_project.php?project_id=' . $funded_project['project_id'] . '">VIEW&nbsp;</a></td>';
                echo '</tr>';
            }
            ?>
        </table>
		</div>
    </fieldset>

    <br>

    <fieldset>
		<div class="col-lg-12 text-center">
		<h2 class="section-heading text-uppercase">Unfunded Projects</h2>
        <table align="center" border=1>
            <tr>
                <th>&nbsp; Title &nbsp;</th>
                <th>&nbsp; Category &nbsp;</th>
                <th>&nbsp; Keywords &nbsp;</th>
                <th>&nbsp; Fundings &nbsp;</th>
                <th>&nbsp; Goal &nbsp;</th>
                <th>&nbsp; Deadline &nbsp;</th>
                <th>&nbsp; Fund &nbsp;</th>
                <th>&nbsp; Delete &nbsp;</th>
            </tr>

            <?php
            foreach ($unfunded_projects as $unfunded_project) {
                echo '<tr align="center">';
                echo '<td> &nbsp;' . $unfunded_project['title'] . '&nbsp;</td>';
                echo '<td> &nbsp;<a href=?category=' . urlencode($unfunded_project['category']) . '>' . $unfunded_project['category'] . '&nbsp;</a></td>';
                echo '<td> &nbsp;' . $unfunded_project['keyword_links'] . '&nbsp;</td>';
                echo '<td> &nbsp;' . $unfunded_project['funding_amount'] . '&nbsp;</td>';
                echo '<td> &nbsp;' . $unfunded_project['funding_goal'] . '&nbsp;</td>';
                echo '<td> &nbsp;' . $unfunded_project['end_datetime'] . '&nbsp;</td>';
                echo '<td> &nbsp;<a href="fund_project.php?project_id=' . $unfunded_project['project_id'] . '">+&nbsp;</a></td>';
                echo '<td> &nbsp;<a href="delete_project.php?project_id=' . $unfunded_project['project_id'] . '&redirect=browse_projects.php">X&nbsp;</a></td>';
				echo '<td> &nbsp;<a href="view_project.php?project_id=' . $unfunded_project['project_id'] . '">VIEW&nbsp;</a></td>';
                echo '</tr>';
            }
            ?>
        </table>
		</div>
    </fieldset>
	
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
