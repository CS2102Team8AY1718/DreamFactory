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
        } else {
          echo 'Invalid amount.';
        }
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
        <title>Fund Project</title>

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
        <nav style="background-color:Black;" class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
            <div class="container">
                <a class="navbar-brand js-scroll-trigger" href="#page-top">DreamFactory</a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    Menu
                    <i class="fa fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav text-uppercase ml-auto">

                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="homepage.php">Back to Homepage</a>
                        </li>
                        <li class="nav-item">
                            <?php

                            if (isset($_SESSION['email'])) {
                                echo '<a class="nav-link js-scroll-trigger" href="logout.php">Logout</a>';
                            } else {
                                echo '<a class="nav-link js-scroll-trigger" href="login.php?redirect=homepage.php">Login</a>';
                            }

                            ?>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <section class="bg-light" id="fundProject">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center">
        <form method="post" action="?project_id=<?php echo $_GET['project_id']; ?>">
            <table align="center">
                <tr>
                    <td>Fund ($):</td>
                    <td><input class="form-control" type="number" name="fund_amount" value="<?php echo (isset($prev_amount) ? $prev_amount : ""); ?>"></td>
                </tr>
                <tr>
                    <td colspan=2 align="right"><input type="submit" name="fund" value="Confirm"></td>
                </tr>
            </table>
        </form>
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
