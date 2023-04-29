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

	// Wczytaj kod z pliku saved_code.php
	$code = file_get_contents('work/current.php');

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
    include 'work/current.php';
    $output = ob_get_contents();
    ob_end_clean();

    echo $output;

?>