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
    $sql = "INSERT INTO employees (first_name, last_name, birth_date, gender) 
            VALUES (?, ?, ?, ?)";

    // Preparar la consulta
    if ($stmt = $conexion->prepare($sql)) {
        // Vincular los parámetros
        $stmt->bind_param("ssss", $first_name, $last_name, $birth_date, $gender);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Obtener el ID del empleado recién insertado
            $employee_id = $stmt->insert_id;

            // Insertar también en la tabla de salarios (supongo que hay una relación entre employees y salaries)
            $sql_salaries = "INSERT INTO salaries (emp_no, salary, from_date, to_date) 
                             VALUES (?, ?, NOW(), NOW())";

            if ($stmt_salaries = $conexion->prepare($sql_salaries)) {
                // Vincular los parámetros para la tabla de salarios
                $stmt_salaries->bind_param("id", $employee_id, $salary);

                // Ejecutar la consulta para la tabla de salarios
                $stmt_salaries->execute();
            }

            // Insertar el cargo en la tabla de títulos (supongo que hay una relación entre employees y titles)
            $sql_titles = "INSERT INTO titles (emp_no, title, from_date, to_date) 
                           VALUES (?, ?, NOW(), NOW())";

            if ($stmt_titles = $conexion->prepare($sql_titles)) {
                // Vincular los parámetros para la tabla de títulos
                $stmt_titles->bind_param("is", $employee_id, $title);

                // Ejecutar la consulta para la tabla de títulos
                $stmt_titles->execute();
            }

            // Finalmente, insertar en la tabla dept_emp (supongo que hay una relación entre employees y dept_emp)
            $sql_dept_emp = "INSERT INTO dept_emp (emp_no, dept_no, from_date, to_date) 
                             VALUES (?, ?, NOW(), NOW())";

            if ($stmt_dept_emp = $conexion->prepare($sql_dept_emp)) {
                // Vincular los parámetros para la tabla dept_emp
                $stmt_dept_emp->bind_param("is", $employee_id, $dept_no);

                // Ejecutar la consulta para la tabla dept_emp
                $stmt_dept_emp->execute();
            }

            $response['success'] = true;
            $response['message'] = "Empleado registrado correctamente.";
        } else {
            $response['success'] = false;
            $response['message'] = "Error al registrar el empleado: " . $stmt->error;
        }

        // Cerrar las consultas
        $stmt->close();
        if (isset($stmt_salaries)) $stmt_salaries->close();
        if (isset($stmt_titles)) $stmt_titles->close();
        if (isset($stmt_dept_emp)) $stmt_dept_emp->close();
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
