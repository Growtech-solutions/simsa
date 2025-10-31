<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../conexion_transimex.php'; 

    $conexion_transimex = new mysqli($host_transimex, $usuario_transimex, $contrasena_transimex, $base_de_datos_transimex);

    if ($conexion_transimex->connect_error) {
        die("Error de conexión: " . $conexion_transimex->connect_error);
    }

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $curp = $_POST['CURP'];
    $rfc = $_POST['RFC'];
    $imss = $_POST['IMSS'];
    $cp = $_POST['CP'];
    $cla = $_POST['estado'];
    $fecha_nacimiento = $_POST['fechanacimiento'];
    $fecha_ingreso = $_POST['fechaingreso'];
    $puesto = $_POST['puesto'];
    $empresa = $_POST['empresa'];
    $tarjeta = $_POST['tarjeta'];
    $salario = $_POST['salario'];
    $forma_pago = $_POST['forma_pago'];
    $tipo_contrato = $_POST['tipo_contrato'];
    $header_loc = $_POST['header_loc'];
    $area = $_POST['area'];
    $estado = 1;

    $sql = "INSERT INTO trabajadores (nombre, apellidos, curp, rfc, nss, codigo_postal, clave_entidad_fed, fecha_nacimiento, fecha_ingreso, puesto, empresa, clave_bancaria, salario, estado, contrato, area, forma_de_pago) 
            VALUES ('$nombre', '$apellido', '$curp', '$rfc', '$imss', '$cp', '$cla', '$fecha_nacimiento', '$fecha_ingreso', '$puesto', '$empresa', '$tarjeta', '$salario', '$estado', '$tipo_contrato','$area', '$forma_pago')"; 

    if ($conexion_transimex->query($sql) === TRUE) {
        $id = $conexion_transimex->insert_id;
        $carpeta_trabajador = "../../transimex/documentos/RecursosHumanos/trabajadores/$nombre $apellido $id/";
        if (!file_exists($carpeta_trabajador)) {
            mkdir($carpeta_trabajador, 0777, true);
        }

        $nombres_documentos = array("curp_file", "rfc_file", "nss_file", "perfil");
        foreach ($nombres_documentos as $nombre_documento) {
            if (isset($_FILES[$nombre_documento]) && $_FILES[$nombre_documento]['error'] == UPLOAD_ERR_OK) {
                switch ($nombre_documento) {
                    case 'curp_file':
                        $nuevo_nombre = "curp.pdf";
                        break;
                    case 'rfc_file':
                        $nuevo_nombre = "rfc.pdf";
                        break;
                    case 'nss_file':
                        $nuevo_nombre = "nss.pdf";
                        break;
                    case 'perfil':
                        $nuevo_nombre = "perfil.png";
                        break;
                    default:
                        $nuevo_nombre = $_FILES[$nombre_documento]['name'];
                        break;
                }
                $ruta_temporal = $_FILES[$nombre_documento]['tmp_name'];
                $ruta_destino = $carpeta_trabajador . $nuevo_nombre;
                move_uploaded_file($ruta_temporal, $ruta_destino);
            }
        }
        $confirmacion = "Registro insertado correctamente.";
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
        $conexion_transimex->close();
        exit();
    } else {
        $confirmacion = "Error al procesar el formulario.";
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
        $conexion_transimex->close();
        exit();
    }
}
?>
