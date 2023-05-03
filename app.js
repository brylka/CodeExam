$(document).ready(function () {

	var selectedStudent = "";

	// Inicjalizuj edytor CodeMirror
	const codeEditor = CodeMirror(document.getElementById("code"), {
		mode: "php",
		theme: "monokai",
		lineNumbers: true,
		indentUnit: 4,
		indentWithTabs: true,
		height: "50vh",
		extraKeys: { "Ctrl-Space": "autocomplete" },
	});

	// Włączanie podpowiedzi dla różnych języków
	codeEditor.on("inputRead", function onChange(editor, input) {
		const excludedChars = [";", " ", "{", "(", "}", ")", "\"", "'", ":", "[", "]", ",", ".",
			"+", "-", "*", "/", "=", "$", "%", "&", "|", "<", ">", "!", "?", "~", "^", "@", "`"];
		if (excludedChars.includes(input.text[0])) {
			return;
		}
		CodeMirror.commands.autocomplete(editor, null, { completeSingle: false });
	});
	
	function isTeacher() {
		const cookies = document.cookie.split("; ");
		for (let i = 0; i < cookies.length; i++) {
			const cookie = cookies[i].split("=");
			if (cookie[0] === "userType" && cookie[1] === "teacher") {
				return true;
			}
		}
		return false;
	}
	
	function showModal() {
		$("#infoModal").modal("show");
	}

	// Zablokowanie możliwości wklejania
	codeEditor.on("paste", function (editor, event) {
		event.preventDefault();
		showModal();
	});

	// Zablokowanie możliwości kopiowania
	codeEditor.on("copy", function (editor, event) {
		event.preventDefault();
		showModal();
	});

	function saveCodeToServer(code, isStudent) {
		const task_file = $('#taskFileName').val();
		if (isStudent && task_file) {
			$.ajax({
				type: "POST",
				url: "save_code.php",
				data: {
					code: code,
					username: getUsername(),
					task_file: task_file
				},
				success: function () {
					console.log("Code saved");
				},
				error: function () {
					console.error("An error occurred");
				},
			});
		}
	}

	function updateCodeFromServer(loadOnceForStudent) {
		const task_file = $('#taskFileName').val();
		if (!task_file) {
			return;
		}
		if (!isTeacher()) {
			selectedStudent = $('.student-task').first().data('student-id');
		} else {
			selectedStudent = $('.btn-warning.studentDropdown').attr('id').replace('studentDropdown', '');
		}
		if (task_file && (isTeacher() || loadOnceForStudent)) {
			requestUrl = 'get_code.php?username=' + (selectedStudent !== "" ? selectedStudent : getUsername()) + '&task_file=' + task_file;
			$.ajax({
				type: "GET",
				url: requestUrl,
				success: function (response) {
					const currentCode = codeEditor.getValue();
					if (response !== currentCode) {
						codeEditor.setValue(response);
					}
				},
				error: function () {
					console.error("An error occurred");
				}
			});
		}
	}

    setInterval(updateCodeFromServer, 1000);

	codeEditor.on("change", function () {
		const code = codeEditor.getValue();
		saveCodeToServer(code, !isTeacher());
	});

	$("#run-code").click(function () {
		const code = codeEditor.getValue();
		const outputType = $("input[name='outputType']:checked").val();
		const selectedStudentId = $(this).data('student-id');
		const url = "execute.php";
		const task_file = $('#taskFileName').val();
		const data = { code: code, student_id: selectedStudentId || getUsername(), task_file: task_file };

		$.ajax({
			type: "POST",
			url: url,
			data: data,
			success: function (response) {
				const iframe = document.getElementById("output");
				const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
				iframeDoc.open();

				if (outputType === "html") {
					iframeDoc.write(response);
				} else {
					const escapedResponse = $('<div/>').text(response).html();
					const preElement = `<pre>${escapedResponse}</pre>`;
					iframeDoc.write(preElement);
				}
				iframeDoc.close();
			},
			error: function () {
				console.error("An error occurred");
			},
		});
	});

	// Pobierz treść zadania na podstawie nazwy pliku
	function getTaskContentByFile(file) {
		$.ajax({
			url: 'get_task_content.php',
			type: 'GET',
			data: { task_file: file },
			dataType: 'json',
			success: function (data) {
				if (isTeacher()) {
					$('#taskTitle').val(data.title);
				}
				$('#taskContent').html(data.content);
			},
			error: function () {
				console.error('Failed to assign task content');
			},
		});
	}

	// Event listener dla zmiany zadania
	$('.student-task').on('click', function (e) {
		e.preventDefault();
		const task_file = $(this).data('task-file');
		const student_id = $(this).data('student-id');
		const selectedStudent = $(this).data('student-id');

		$('#taskFileName').val(task_file);
		if (isTeacher()) {
			$('.studentDropdown').removeClass('btn-warning').addClass('btn-success');
			$('#studentDropdown' + student_id).removeClass('btn-success').addClass('btn-warning');
		} else {
			$('.student-task').removeClass('btn-success').addClass('btn-dark');
			$(this).removeClass('btn-dark').addClass('btn-success')
		}

		$('#run-code').data('student-id', selectedStudent);
		$('#review-link').attr('href', 'review.php?student_id=' + selectedStudent + '&task_file=' + task_file);
		$('#generate-pdf-link').attr('href', 'generate_pdf.php?username=' + selectedStudent + '&task_file=' + task_file);

		getTaskContentByFile(task_file);
		updateCodeFromServer(true);
	});

	// Pobierz treść zadania z serwera
	function getTaskContent() {
		$.ajax({
			url: 'get_task_content.php',
			type: 'GET',
			dataType: 'text',
			success: function (data) {
				$('#taskContent').html(data);
			},
			error: function () {
				console.error('Failed to assign job content');
			},
		});
	}

	// Zapisz treść zadania na serwerze lub utwórz nowe zadanie, jeśli nie ma jeszcze pliku zadania
	function saveOrUpdateTask() {
		const task_title = $('#taskTitle').val();
		const task_content = $('#taskContent').html();
		const task_file = $('#taskFileName').val();

		$.ajax({
			url: 'manage_task.php',
			type: 'POST',
			data: { task_title: task_title, task_content: task_content, task_file: task_file },
			dataType: 'json',
			success: function (response) {
				if (response.status === 'success') {
					$('#taskContent').css('background-color', '#ffffff');
					if (!task_file) {
						const new_task_file = response.task_file;
						getTaskContentByFile(new_task_file);
					}
				} else {
					console.error('Failed to save or update task content');
				}
			},
			error: function () {
				console.error('Failed to save or update task content');
			},
		});
	}

	// Zapisz treść zadania na serwerze
	$('#save-task').on('click', function () {
		saveOrUpdateTask();
	});

	// Utwórz nowe zadanie
	$('#create-task').on('click', function () {
		$('#taskFileName').val('NEW');
		saveOrUpdateTask();
		$('#taskTitle').val('');
		$('#taskContent').html('');
		$('#taskFileName').val('');
	});

	function getUsername() {
		const cookies = document.cookie.split("; ");
		for (let i = 0; i < cookies.length; i++) {
			const cookie = cookies[i].split("=");
			if (cookie[0] === "username") {
				return cookie[1];
			}
		}
		return "";
	}

	if (isTeacher()) {
		$('#taskContent').on('input', function () {
			$(this).css('background-color', '#FFCCFF');
		});
	}

	updateCodeFromServer(true);
});