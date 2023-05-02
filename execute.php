<?php
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
        echo "Plik current.php nie istnieje dla tego ucznia!";
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
		echo "Niedozwolone funkcje użyte w kodzie!";
		exit();
	}

    // Wykonaj kod PHP
    ob_start();
    include $code_file;
    $output = ob_get_contents();
    ob_end_clean();

    echo $output;