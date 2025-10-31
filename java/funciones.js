function obtenerNombreProyecto() {
    var ot = document.getElementById("ot").value;
    if (ot.trim() !== "") {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("nombreDelProyecto").value = this.responseText;
            }
        };
        xhttp.open("GET", "../php/obtener_nombre_proyecto.php?ot=" + ot, true);
        xhttp.send();
    } else {
        document.getElementById("nombreDelProyecto").value = "";
    }
}
function actualizarActividades() {
    var ot = document.getElementById("ot").value;
    if (ot.trim() !== "") {
        fetch("../php/obtener_actividades.php?ot=" + encodeURIComponent(ot))
            .then(response => response.json())
            .then(data => {
                document.querySelectorAll(".actividad").forEach(select => {
                    select.innerHTML = '<option value="">Actividad</option>';
                    data.forEach(actividad => {
                        let option = document.createElement("option");
                        option.value = actividad.id;
                        option.textContent = actividad.descripcion;
                        select.appendChild(option);
                    });
                });
            })
            .catch(error => console.error("Error:", error));
    } else {
        document.querySelectorAll(".actividad").forEach(select => {
            select.innerHTML = '<option value="">Actividad</option>';
        });
    }
}