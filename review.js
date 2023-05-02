const versionSlider = document.getElementById("versionSlider");
const versionDisplay = document.getElementById("versionDisplay");
const codeContainer = document.getElementById("code");

let versions = [];

function getStudentId() {
    return document.getElementById("reviewTitle").dataset.studentId;
}

function loadVersions() {
    const student_id = getStudentId();

    $.ajax({
        type: "GET",
        url: "list_versions.php",
        data: { student_id: student_id },
        dataType: "json",
        success: function (response) {
            versions = response;
            versionSlider.max = versions.length - 1;
            updateCode();
        },
    });
}

function updateCode() {
    const student_id = getStudentId();
    const versionIndex = versionSlider.value;
    const versionTimestamp = versions[versionIndex];
    const versionDate = new Date(versionTimestamp * 1000);
    const formattedDate = versionDate.toLocaleDateString("pl-PL");
    const formattedTime = versionDate.toLocaleTimeString("pl-PL");

    $.ajax({
        type: "GET",
        url: "get_version.php",
        data: {
            student_id: student_id,
            timestamp: versionTimestamp,
        },
        success: function (response) {
            codeContainer.textContent = response;
            versionDisplay.textContent = `Version ${Number(versionIndex) + 1} of ${versions.length} (${formattedDate} ${formattedTime})`;
        },
    });
}

versionSlider.addEventListener("input", updateCode);

loadVersions();