<?php
    require_once 'user_access.php';
    redirectIfNotLoggedIn();
?><!DOCTYPE html>
<html>
<head>
    <title>CodeExam</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/codemirror.min.css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/codemirror.min.js"></script>

	<!-- Link do motywu CSS (opcjonalnie) -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/theme/monokai.min.css" />

	<!-- Linki do trybów (mode) dla języków, których chcesz użyć -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/htmlmixed/htmlmixed.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/xml/xml.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/javascript/javascript.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/css/css.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/clike/clike.min.js"></script>
	<!-- Link do trybu PHP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/php/php.min.js"></script>
	
	<!-- Skrypty i arkusze stylów dla funkcji autouzupełniania -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/addon/hint/show-hint.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/addon/hint/show-hint.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/addon/hint/anyword-hint.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/addon/hint/sql-hint.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/addon/hint/xml-hint.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/addon/hint/html-hint.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/addon/hint/javascript-hint.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/addon/hint/css-hint.min.js"></script>
	
	<!-- Linki do Bootstrap CSS i JS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .CodeMirror {
            height: 50vh;
            width: 100%;
        }
        #taskContent {
            border: 1px solid #ced4da;
            padding: 1rem;
            border-radius: 0.25rem;
            min-height: 50vh;
            user-select: none;
        }
    </style>
</head>
<body>
    <!-- Kod HTML dla modala -->
    <div class="modal" tabindex="-1" role="dialog" id="infoModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Informacja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg <?php echo isTeacher() ? 'navbar-light bg-success' : 'navbar-dark bg-dark'; ?>">
        <a class="navbar-brand" href="#">CodeExam</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <?php
                if (isTeacher()) {
                    $student_directories = get_student_directories();
                    foreach ($student_directories as $student_directory) {
                        echo "<li class='nav-item'><a href='#' class='nav-link student-directory' data-student-id='{$student_directory}'>{$student_directory}</a></li>";
                    }
                }
                ?>
            </ul>
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

    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-9">
                <div id="code"></div>
                <div class="mt-3">
                    <button id="run-code" data-student-id="" class="btn btn-primary">Run Code</button>
                    <div class="form-check form-check-inline ml-3">
                        <input class="form-check-input" type="radio" name="outputType" id="htmlOutput" value="html" checked>
                        <label class="form-check-label" for="htmlOutput">HTML</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="outputType" id="textOutput" value="text">
                        <label class="form-check-label" for="textOutput">Text</label>
                    </div>
                    <?php if (isTeacher()): ?>
                        <a href="review.php" id="review-link" class="mr-3">Review</a>
                        <a href="generate_pdf.php" id="generate-pdf-link" class="mr-3">Generate PDF</a>
                    <?php endif; ?>
                </div>
                <div class="mt-3">
                    <iframe id="output" frameborder="0" width="100%" height="300"></iframe>
                </div>
            </div>
            <div class="col-md-3">
                <div id="taskContent" contenteditable="<?php echo isTeacher() ? 'true' : 'false'; ?>"></div>
                <?php if (isTeacher()): ?>
                    <button id="save-task" class="btn btn-primary mt-2">Zapisz zadanie</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
	
    <script src="app.js"></script>
    <script src="translations.js"></script>
</body>
</html>
<?php
    function get_student_directories() {
        $work_dir = 'work';
        $student_directories = [];

        if ($handle = opendir($work_dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && is_dir("{$work_dir}/{$entry}")) {
                    $student_directories[] = $entry;
                }
            }
            closedir($handle);
        }
        return $student_directories;
    }

    function isTeacher() {
        if (isset($_COOKIE['userType']) && $_COOKIE['userType'] === 'teacher') {
            return true;
        }
        return false;
    }
?>