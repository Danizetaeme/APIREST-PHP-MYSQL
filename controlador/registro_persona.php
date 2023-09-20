<?php
// Incluir el archivo de conexión a la base de datos
include_once '../modelo/conexion.php';

// Inicializar un arreglo para la respuesta JSON
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $dept_no = $_POST['dept_no'];
    $title = $_POST['title'];
    $salary = $_POST['salary'];

    // Query SQL para insertar un nuevo empleado
    $sql = "INSERT INTO employees (first_name, last_name, birth_date, gender, dept_no, title, salary) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Preparar la consulta
    if ($stmt = $conexion->prepare($sql)) {
        // Vincular los parámetros
        $stmt->bind_param("sssssss", $first_name, $last_name, $birth_date, $gender, $dept_no, $title, $salary);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Empleado registrado correctamente.";
        } else {
            $response['success'] = false;
            $response['message'] = "Error al registrar el empleado: " . $stmt->error;
        }

        // Cerrar la consulta
        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = "Error en la preparación de la consulta: " . $conexion->error;
    }
} else {
    $response['success'] = false;
    $response['message'] = "Solicitud no válida";
}

// Cerrar la conexión a la base de datos
$conexion->close();

// Enviar la respuesta JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
