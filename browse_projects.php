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
	echo '<a href="login.php">Login</a>';
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
</body>
</html>
