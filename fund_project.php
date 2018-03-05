<?php

session_start();
require 'connect.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php?redirect=fund_project.php?project_id=" . $_GET['project_id']);
}

$sql_check_for_history = "SELECT amount FROM fundings WHERE user_id = '" . $_SESSION['user_id'] . "' AND project_id = '" . $_GET['project_id'] . "'";
$result = $conn->query($sql_check_for_history);
if ($result->num_rows != 0) {
    // Previous funding found
    $row = $result->fetch_assoc();
    $prev_amount = $row['amount'];
}

if (isset($_POST['fund'])) {
    $fund_amount = mysqli_real_escape_string($conn, $_POST['fund_amount']);

    if (isset($prev_amount)) {
        $sql_update_funding = "UPDATE fundings SET amount = '$fund_amount' WHERE user_id = '" . $_SESSION['user_id'] . "' AND project_id = '" . $_GET['project_id'] . "'";
        if ($result = $conn->query($sql_update_funding)) {
            echo 'Your funding has been recorded.';
            $prev_amount = $fund_amount;
        }
    } else {
        $sql_insert_funding = "INSERT INTO fundings (user_id, project_id, amount) VALUES ('" . $_SESSION['user_id'] . "', '" . $_GET['project_id'] . "', '$fund_amount')";
        if ($result = $conn->query($sql_insert_funding)) {
            echo 'Your funding has been recorded.';
            $prev_amount = $fund_amount;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>Fund Project</title>
    </head>
    <body>
        <form method="post" action="?project_id=<?php echo $_GET['project_id']; ?>">
            <table align="center">
                <tr>
                    <td>Fund ($):</td>
                    <td><input type="number" name="fund_amount" value="<?php echo (isset($prev_amount) ? $prev_amount : ""); ?>"></td>
                </tr>
                <tr>
                    <td colspan=2 align="right"><input type="submit" name="fund" value="Confirm"></td>
                </tr>
            </table>
        </form>
    </body>
</html>
