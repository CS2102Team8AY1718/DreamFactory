<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>Create Project</title>
    </head>
</html>

<?php

session_start();
require 'connect.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php?redirect=create_project.php");
}

if (isset($_POST['create'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $end_date = mysqli_real_escape_string($conn, $_POST["end_date"]);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $goal = $_POST['goal'];
    //$keyword = trim(mysqli_real_escape_string($conn, $_POST['keyword']));
    
    $error_message = "";

    if (empty($title)) {
        $error_message .= "Please enter your project title.<br>";
    }

    if (empty($description)) {
        $error_message .= "Please provide a description of your project.<br>";
    }

    if (empty($end_date)) {
        $error_message .= "Please select a funding deadline for your project.<br>";
    }

    if (empty($category)) {
        $error_message .= "Please select a category for your project.<br>";
    }

    if (empty($goal)) {
        $error_message .= "Please provide a funding goal for your project.<br>";
    }

    if (empty($error_message)) {
        // Get user_id from session info
        $user_id = $_SESSION['user_id'];

        //create project
        $sql_create_project = "INSERT INTO projects (user_id, title, image_url, description, end_datetime, category, funding_goal) VALUES ('$user_id', '$title', '$image_url', '$description', '$end_date', '$category', '$goal')";
        if ($conn->query($sql_create_project)){
            echo "Successfully created project";
            $retry = false;
        } else {
            echo $conn->error;
            echo "A project with the same title already exists. Please provide a different title.";
            $retry = true;
        }
    } else {
        echo $error_message;
        $retry = true;
    }
}

if (!isset($retry) || $retry) {
    $sql_select_categories = "SELECT * FROM categories";
    if ($result = $conn->query($sql_select_categories)) {
        $category_list = '';
        while ($row = $result->fetch_assoc()) {
            $category_list .= '<option value="' . $row['category'] . '">' . $row['category'] . '</option>';
        }
    }

    echo '
        <fieldset>
            <legend align="center">Create Project</legend>
            <form action="?" method="post">
                <table align="center">
                    <tr>
                        <td>Title:</td>
                        <td><input type="text" name="title" value="' . (isset($title) ? $title : '') . '"></td>
                    </tr>
                    <tr>
                        <td>Image URL:</td>
                        <td><input type="text" name="image_url" value="' . (isset($image_url) ? $image_url : '') . '"></td>
                    </tr>
                    <tr>
                        <td>Description:</td>
                        <td><input type="text" name="description" value="' . (isset($description) ? $description : '') . '"></td>
                    </tr>
                    <tr>
                        <td>End Date:</td>
                        <td><input type="date" name="end_date" value="' . (isset($end_date) ? $end_date : '') . '"></td>
                    </tr>
                    <tr>
                        <td>Category:</td>
                        <td>
                            <select name="category">'
                            . $category_list .
                            '</select>
                        </td>
                    </tr>
                    <tr>
                        <td>Funding Goal ($):</td>
                        <td><input type="number" name="goal" value="' . (isset($goal) ? $goal : '') . '"></td>
                    </tr>
                    <!--<tr>
                        <td>Keyword:</td>
                        <td><input type="text" name="keyword"></td>
                    </tr>-->
                    <tr>
                        <td colspan=2 align="right"><input type="submit" name="create" value="Create"></td>
                    </tr>
                </table>
            </form>
        </fieldset>
    ';
}

?>
