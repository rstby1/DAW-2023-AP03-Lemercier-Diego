var fileInput = document.getElementById('fileInput');
var nombreArchivo = document.getElementById('nombreArchivo');

fileInput.addEventListener('change', function () {
    if (fileInput.files.length > 0) {
        nombreArchivo.textContent = "Nombre del archivo seleccionado: " + fileInput.files[0].name;
    } else {
        nombreArchivo.textContent = "";
    }
});