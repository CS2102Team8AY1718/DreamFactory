<?php

    session_start();
    require 'connect.php';

    $sql_total_users = "SELECT count(*) AS count FROM users";

    $sql_total_projects = "SELECT count(*) AS count FROM projects";

    $sql_total_amount = "SELECT sum(amount) AS amount FROM fundings";

    $sql_select_ongoing_projects = "
    SELECT
        p.project_id,
        title,
        image_url,
        category,
        kl.keyword_links AS keyword_links,
        COALESCE(f.fundings, 0) AS funding_amount,
        funding_goal,
        end_datetime
    FROM
        projects p LEFT JOIN
        (SELECT
            project_id,
            GROUP_CONCAT(CONCAT('<a href=browse_projects.php?keyword=', keyword, '>', keyword, '</a>') SEPARATOR ', ') AS keyword_links
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
    ORDER BY
        COALESCE(f.fundings, 0) * 1.0 / funding_goal DESC
    LIMIT 3
    ";

    $sql_select_funded_projects = "
    SELECT
        p.project_id,
        title,
        image_url,
        category,
        kl.keyword_links AS keyword_links,
        COALESCE(f.fundings, 0) AS funding_amount,
        funding_goal,
        end_datetime
    FROM
        projects p LEFT JOIN
        (SELECT
            project_id,
            GROUP_CONCAT(CONCAT('<a href=browse_projects.php?keyword=', keyword, '>', keyword, '</a>') SEPARATOR ', ') AS keyword_links
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
    ORDER BY
        end_datetime DESC
    LIMIT 3
    ";

    if ($result = $conn->query($sql_total_users)) {
        $total_users = array();
        while ($row = $result->fetch_assoc()) {
            $total_users[0]=$row['count'];
        }
    }

    if ($result = $conn->query($sql_total_projects)) {
        $total_projects = array();
        while ($row = $result->fetch_assoc()) {
            $total_projects[0]=$row['count'];
        }
    }

    if ($result = $conn->query($sql_total_amount)) {
        $total_amount = array();
        while ($row = $result->fetch_assoc()) {
            $total_amount[0]=$row['amount'];
        }
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
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
    <script src="bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>DreamFactory: Where Ideas Become Reality</title>

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
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="#page-top">DreamFactory</a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fa fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav text-uppercase ml-auto">

                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="#ongoingProjects">Ongoing Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="#accomplishedProjects">Accomplished Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="#createProject">Create Project</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="#contact">Contact Us</a>
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

    <!-- Header -->
    <header class="masthead">
        <div class="container">
            <div class="intro-text">
                <div class="intro-heading">DreamFactory</div>
                <div class="intro-lead-in">Where Ideas Become Reality</div>
            </div>
        </div>
    </header>

    <!-- Ongoing Projects Grid -->
    <!-- sql query to list top 3 projects that were just made-->
    <section class="bg-light" id="ongoingProjects">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading text-uppercase">Ongoing Projects</h2>
                </div>
            </div>

            <div class="row">
                <?php

                foreach ($ongoing_projects as $ongoing_project) {
                    echo '
                        <div class="col-md-4 col-sm-6 portfolio-item">
                            <div align="center">
                                <a class="portfolio-link" data-toggle="modal" href="project1">
                                    <img class="img-fluid" src="' . $ongoing_project['image_url'] . '" style="max-height: 200px; width: auto;">
                                </a>
                            </div>
                            <div class="portfolio-caption">
                                <br>
                                <h4 align="center">' . $ongoing_project['title'] . '</h4>
                                <p align="center" class="text-muted">' . $ongoing_project['keyword_links'] . '</p>
                                <p align="center" class="text-muted">' . 100 * $ongoing_project['funding_amount'] / $ongoing_project['funding_goal'] . '% funded (<b>$' . $ongoing_project['funding_amount'] . '.00</b> of <b>$' . $ongoing_project['funding_goal'] . '.00)</b></p>
                            </div>
                        </div>
                    ';
                }

                ?>
            </div>

            <br>
            <center>
                <a class="btn btn-primary btn-xl text-uppercase js-scroll-trigger" href="browse_projects.php">Browse more...</a>
            </center>
        </div>
    </section>

    <!-- Accomplished Projects Grid -->
    <!-- sql query to list top 3 projects that just ended -->
    <section id="accomplishedProjects">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading text-uppercase">Accomplished Projects</h2>
                </div>
            </div>
            <div class="row">
                <?php

                foreach ($funded_projects as $funded_project) {
                    echo '
                        <div class="col-md-4 col-sm-6 portfolio-item">
                        <div align="center">
                            <a class="portfolio-link" data-toggle="modal" href="project1">
                                <img class="img-fluid" src="' . $funded_project['image_url'] . '" style="max-height: 200px; width: auto;">
                            </a>
                            <div class="portfolio-caption">
                                <br>
                                <h4 align="center">' . $funded_project['title'] . '</h4>
                                <p align="center" class="text-muted">' . $funded_project['keyword_links'] . '</p>
                                <p align="center" class="text-muted">' . ceil (100 * $funded_project['funding_amount'] / $funded_project['funding_goal'] ). '% funded (<b>$' . $funded_project['funding_amount'] . '.00</b> of <b>$' . $funded_project['funding_goal'] . '.00)</b></p>
                            </div>
                            </div>
                        </div>
                    ';
                }

                ?>
            </div>

            <br>
            <center>
                <a class="btn btn-primary btn-xl text-uppercase js-scroll-trigger" href="browse_projects.php">Browse more...</a>
            </center>
        </div>
    </section>

    <!-- Create Project Grid -->
    <section id="createProject">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading text-uppercase">Create Project</h2>
                    <h3 class="section-subheading text-muted">Kick start your dream with DreamFactory!</h3>
                </div>
            </div>
            <div class="row text-center">
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fa fa-circle fa-stack-2x text-primary"></i>
                        <i class="fa fa-shopping-cart fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="service-heading">Total Number of Users</h4>
                    <?php
                    echo '
                    <h2 class="text-muted">'  . $total_users[0] . ' users</p>
                    ';
                    ?>
                </div>
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fa fa-circle fa-stack-2x text-primary"></i>
                        <i class="fa fa-laptop fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="service-heading">Total Number of Projects</h4>
                    <?php
                    echo '
                    <h2 class="text-muted">'  . $total_projects[0] . ' projects</p>
                    ';
                    ?>
                </div>
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fa fa-circle fa-stack-2x text-primary"></i>
                        <i class="fa fa-lock fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="service-heading">Average Funding Amount</h4>
                    <?php
                    echo '
                    <h2 class="text-muted">$'  . $total_amount[0] / $total_projects[0] . ' per project</p>
                    ';
                    ?>
                </div>
            </div>

            <br> <br>
            <center>
                <a class="btn btn-primary btn-xl text-uppercase js-scroll-trigger" href="create_project.php">Create Project</a>
            </center>

        </div>
    </section>

    <!-- Contact -->
    <section id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading text-uppercase">Contact Us</h2>
                    <h3 class="section-subheading text-muted">Drop us an email if you have any enquiries.</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form id="contactForm" name="sentMessage" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input class="form-control" id="name" type="text" placeholder="Your Name *" required data-validation-required-message="Please enter your name.">
                                    <p class="help-block text-danger"></p>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" id="email" type="email" placeholder="Your Email *" required data-validation-required-message="Please enter your email address.">
                                    <p class="help-block text-danger"></p>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" id="phone" type="tel" placeholder="Your Phone *" required data-validation-required-message="Please enter your phone number.">
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <textarea class="form-control" id="message" placeholder="Your Message *" required data-validation-required-message="Please enter a message."></textarea>
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-12 text-center">
                                <div id="success"></div>
                                <button id="sendMessageButton" class="btn btn-primary btn-xl text-uppercase" type="submit">Send Message</button>
                            </div>
                        </div>
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

<?php
    include 'footer.php'
?>
