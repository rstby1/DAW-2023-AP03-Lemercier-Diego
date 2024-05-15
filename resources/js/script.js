var fileInput = document.getElementById('fileInput');
var nombreArchivo = document.getElementById('nombreArchivo');

fileInput.addEventListener('change', function () {
    if (fileInput.files.length > 0) {
        nombreArchivo.textContent = "File selected: " + fileInput.files[0].name;
    } else {
        nombreArchivo.textContent = "";
    }
});