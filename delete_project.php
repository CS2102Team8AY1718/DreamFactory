<?php

    session_start();
    require 'connect.php';

    $sql_delete_project = "DELETE FROM projects WHERE project_id = " . $_GET['project_id'];
    $conn->query($sql_delete_project);

    header("Location: " . $_GET['redirect']);

?>
