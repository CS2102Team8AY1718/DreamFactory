<?php

if (isset($_GET['logged_in'])) {
    echo '
        <div id="modal" class="modal">
            <div class="modal-content">
                <span class="close" align="right">&times;</span>
                <p align="center">You are now logged in.<br>Welcome back, ' . $_SESSION['full_name'] . '.</p>
            </div>
        </div>
    ';
} else if (isset($_GET['logged_out'])) {
    echo '
        <div id="modal" class="modal">
            <div class="modal-content">
                <span class="close" align="right">&times;</span>
                <p align="center">You have been logged out.</p>
            </div>
        </div>
    ';
}

?>

<html>
    <head>
        <!-- Modal CSS -->
        <link href="css/modal.css" rel="stylesheet">

        <!-- Modal JavaScript -->
        <script src="js/modal.js"></script>
    </head>
</html>
