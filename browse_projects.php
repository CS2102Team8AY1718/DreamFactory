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
    <meta charset="UTF-8">
    <title>Search projects</title>
</head>
<body>
    <form method="get" action=?>
    Search: <input type="text" name="search_key">
    </form>

    <br>

    <fieldset>
        <legend align="center">Ongoing Projects</legend>
        <table align="center" border=1>
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
                echo '<td>' . $ongoing_project['title'] . '</td>';
                echo '<td><a href=?category=' . urlencode($ongoing_project['category']) . '>' . $ongoing_project['category'] . '</a></td>';
                echo '<td>' . $ongoing_project['keyword_links'] . '</td>';
                echo '<td>' . $ongoing_project['funding_amount'] . '</td>';
                echo '<td>' . $ongoing_project['funding_goal'] . '</td>';
                echo '<td>' . $ongoing_project['end_datetime'] . '</td>';
                echo '<td><a href="fund_project.php?project_id=' . $ongoing_project['project_id'] . '">+</a></td>';
                echo '<td><a href="delete_project.php?project_id=' . $ongoing_project['project_id'] . '&redirect=browse_projects.php">X</a></td>';
                echo '</tr>';
            }

            ?>
        </table>
    </fieldset>

    <br>

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
                <th>Fund</th>
                <th>Delete</th>
            </tr>

            <?php
            foreach ($funded_projects as $funded_project) {
                echo '<tr align="center">';
                echo '<td>' . $funded_project['title'] . '</td>';
                echo '<td><a href=?category=' . urlencode($funded_project['category']) . '>' . $funded_project['category'] . '</a></td>';
                echo '<td>' . $funded_project['keyword_links'] . '</td>';
                echo '<td>' . $funded_project['funding_amount'] . '</td>';
                echo '<td>' . $funded_project['funding_goal'] . '</td>';
                echo '<td>' . $funded_project['end_datetime'] . '</td>';
                echo '<td><a href="fund_project.php?project_id=' . $funded_project['project_id'] . '">+</a></td>';
                echo '<td><a href="delete_project.php?project_id=' . $funded_project['project_id'] . '&redirect=browse_projects.php">X</a></td>';
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
                <th>Fund</th>
                <th>Delete</th>
            </tr>

            <?php
            foreach ($unfunded_projects as $unfunded_project) {
                echo '<tr align="center">';
                echo '<td>' . $unfunded_project['title'] . '</td>';
                echo '<td><a href=?category=' . urlencode($unfunded_project['category']) . '>' . $unfunded_project['category'] . '</a></td>';
                echo '<td>' . $unfunded_project['keyword_links'] . '</td>';
                echo '<td>' . $unfunded_project['funding_amount'] . '</td>';
                echo '<td>' . $unfunded_project['funding_goal'] . '</td>';
                echo '<td>' . $unfunded_project['end_datetime'] . '</td>';
                echo '<td><a href="fund_project.php?project_id=' . $unfunded_project['project_id'] . '">+</a></td>';
                echo '<td><a href="delete_project.php?project_id=' . $unfunded_project['project_id'] . '&redirect=browse_projects.php">X</a></td>';
                echo '</tr>';
            }
            ?>
        </table>
    </fieldset>
</body>
</html>
