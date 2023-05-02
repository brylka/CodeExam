<?php
    require_once 'user_access.php';
    redirectIfNotLoggedIn();

    // Funkcja sprawdzająca, czy kod zawiera niedozwolone funkcje
	function has_disallowed_function($code, $disallowed_functions) {
		$tokens = token_get_all($code);
		foreach ($tokens as $token) {
			if ($token[0] == T_STRING && in_array($token[1], $disallowed_functions)) {
				return true;
			}
		}
		return false;
	}

    // Pobierz identyfikator ucznia
    $student_id = isset($_POST['username']) ? $_POST['username'] : $_COOKIE['username'];

    // Wczytaj kod z pliku current.php w katalogu ucznia
    $student_directory = "work/{$student_id}";
    $code_file = "{$student_directory}/current.php";

    if (!file_exists($code_file)) {
        echo "The current.php file does not exist for this student!";
        exit();
    }

    $code = file_get_contents($code_file);

	// Ogranicz dozwolone funkcje
	$disallowed_functions = [
		'exec',
		'shell_exec',
		'system',
		'passthru',
		'popen',
		'proc_open',
		'pcntl_exec',
		'eval',
		'assert',
	];

	if (has_disallowed_function($code, $disallowed_functions)) {
		echo "Illegal functions used in the code!";
		exit();
	}

    // Wykonaj kod PHP
    ob_start();
    include $code_file;
    $output = ob_get_contents();
    ob_end_clean();

    echo $output;