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
		if (isStudent) {
			$.ajax({
				type: "POST",
				url: "save_code.php",
				data: {
					code: code,
					username: getUsername(),
				},
				success: function () {
					console.log("Code saved");
				},
				error: function () {
					alert("An error occurred");
				},
			});
		}
	}

	function updateCodeFromServer(loadOnceForStudent) {
		var requestUrl = '';

		if (isTeacher() || loadOnceForStudent) {
			requestUrl = 'get_code.php?username=' + (selectedStudent !== "" ? selectedStudent : getUsername());

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
					alert("An error occurred");
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
		const data = selectedStudentId ? { code: code, username: selectedStudentId } : { code: code };

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
				alert("An error occurred");
			},
		});
	});


	// Pobierz treść zadania
	getTaskContent();

	// Zapisz treść zadania na serwerze
	$('#save-task').on('click', function () {
		saveTaskContent();
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
				console.error('Nie udało się pobrać treści zadania');
			},
		});
	}

	// Zapisz treść zadania na serwerze
	function saveTaskContent() {
		const content = $('#taskContent').html();

		$.ajax({
			url: 'save_task_content.php',
			type: 'POST',
			data: { content: content },
			dataType: 'json',
			success: function (response) {
				if (response.status === 'success') {
					$('#taskContent').css('background-color', '#ffffff');
				} else {
					console.error('Nie udało się zapisać treści zadania');
				}
			},
			error: function () {
				console.error('Nie udało się zapisać treści zadania');
			},
		});
	}

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

	$('.student-directory').on('click', function(e) {
		e.preventDefault();
		selectedStudent = $(this).data('student-id');
		var requestUrl = 'get_code.php?username=' + selectedStudent;

		$('#run-code').data('student-id', selectedStudent);
		$('#review-link').attr('href', 'review.php?student_id=' + selectedStudent);
		$('#generate-pdf-link').attr('href', 'generate_pdf.php?username=' + selectedStudent);

		$.ajax({
			url: requestUrl,
			type: 'GET',
			success: function(response) {
				const currentCode = codeEditor.getValue();
				if (response !== currentCode) {
					codeEditor.setValue(response);
				}
			},
			error: function() {
				alert('Wystąpił błąd podczas wczytywania kodu ucznia.');
			}
		});
	});

	if (isTeacher()) {
		$('#taskContent').on('input', function () {
			$(this).css('background-color', '#FFCCFF');
		});
	}

	if (!isTeacher()) {
		setInterval(getTaskContent, 10000);
	}

	updateCodeFromServer(true);
});