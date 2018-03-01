<?php

require 'connect.php';
session_start();
if(isset($_SESSION['logged_in'])){
	if($_SESSION['logged_in'] ==true){
	 $logged_in = true;
	} else {
		$logged_in = false;
	}
}else{
	$logged_in=false;
}

if($logged_in ==true){
	echo 'Logged in as: ',$_SESSION['fullname'];
}else {
	echo '<a class="btn btn-primary btn-xl text-uppercase js-scroll-trigger" href="login.php">Login</a>';
}

if (isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
}

$sql_select_funded_projects = "
SELECT
    p.project_id,
    title,
    category,
    kl.keyword_links AS keywords,
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
    COALESCE(f.fundings, 0) >= p.funding_goal
";

if (isset($keyword)) {
    $sql_select_funded_projects = "
    SELECT
        *
    FROM
        ($sql_select_funded_projects) AS f_p NATURAL JOIN project_keywords
    WHERE
        keyword = '$keyword'
    ";
}

if ($result = $conn->query($sql_select_funded_projects)) {
    $funded_projects = array();
    while ($row = $result->fetch_assoc()) {
        array_push($funded_projects, $row);
    }
}

$sql_select_unfunded_projects = "
SELECT
    p.project_id,
    title,
    category,
    kl.keyword_links AS keywords,
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
    COALESCE(f.fundings, 0) < p.funding_goal AND
    p.end_datetime > CURDATE()
";

if (isset($keyword)) {
    $sql_select_unfunded_projects = "
    SELECT
        *
    FROM
        ($sql_select_unfunded_projects) AS uf_p NATURAL JOIN project_keywords
    WHERE
        keyword = '$keyword'
    ";
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
    <meta charset="UTF-8">
    <title>Search projects</title>
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
	<script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
    <script src="bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Crowd Funding</title>

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
    <fieldset>
        <legend align="center">Funded Projects</legend>
        <table align="center" border=1>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Keywords</th>
                <th>Fundings</th>
                <th>Goal</th>
                <th>Deadline</th>
            </tr>

            <?php
            foreach ($funded_projects as $funded_projects) {
                echo '<tr align="center">';
                echo '<td>' . $funded_projects['title'] . '</td>';
                echo '<td>' . $funded_projects['category'] . '</td>';
                echo '<td>' . $funded_projects['keywords'] . '</td>';
                echo '<td>' . $funded_projects['funding_amount'] . '</td>';
                echo '<td>' . $funded_projects['funding_goal'] . '</td>';
                echo '<td>' . $funded_projects['end_datetime'] . '</td>';
                echo '</tr>';
            }
            ?>
        </table>
    </fieldset>

    <br>

    <fieldset>
        <legend align="center">Unfunded Projects</legend>
        <table align="center" border=1>
          <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Keywords</th>
            <th>Fundings</th>
            <th>Goal</th>
            <th>Deadline</th>
        </tr>
        <?php
        foreach ($unfunded_projects as $unfunded_project) {
            echo '<tr align="center">';
            echo '<td>' . $unfunded_project['title'] . '</td>';
            echo '<td>' . $unfunded_project['category'] . '</td>';
            echo '<td>' . $unfunded_project['keywords'] . '</td>';
            echo '<td>' . $unfunded_project['funding_amount'] . '</td>';
            echo '<td>' . $unfunded_project['funding_goal'] . '</td>';
            echo '<td>' . $unfunded_project['end_datetime'] . '</td>';
            echo '</tr>';
        }
        ?>
    </table>
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
