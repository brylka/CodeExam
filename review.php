<?php
    require_once 'user_access.php';
    redirectIfNotLoggedIn();
    redirectIfNotTeacher();

    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
    $task_file = isset($_GET['task_file']) ? $_GET['task_file'] : null;
?><!DOCTYPE html>
<html>
<head>
    <title>Review Student Progress</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        #code {
            white-space: pre-wrap;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            font-family: "Courier New", Courier, monospace;
        }
		#versionSlider {
			width: 500px;
			margin: 0 auto;
		}
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="/">CodeExam</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <select class="custom-select" id="languageSelector">
                    <option value="en">English</option>
                    <option value="pl">Polski</option>
                </select>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo $_COOKIE['username']; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="logout.php"></a>
                </div>
            </li>
        </ul>
    </div>
</nav>


    <div class="container">
        <h1 id="reviewTitle" data-student-id="<?php echo htmlspecialchars($student_id); ?>" data-task-file="<?php echo htmlspecialchars($task_file); ?>"></h1>
        <div>
            <input type="range" min="0" max="0" value="0" class="slider" id="versionSlider">
            <span id="versionDisplay"></span>
        </div>
        <div id="code"></div>
    </div>
    <script src="review.js"></script>
    <script src="translations.js"></script>
</body>
</html>