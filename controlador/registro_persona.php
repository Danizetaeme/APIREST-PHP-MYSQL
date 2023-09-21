<?php
// Incluir el archivo de conexión a la base de datos
include_once '../modelo/conexion.php';

// Enviar la respuesta JSON
header('Content-Type: application/json');
echo json_encode($response);

// Inicializar un arreglo para la respuesta JSON
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario y realizar una validación básica
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
    $birth_date = isset($_POST['birth_date']) ? $_POST['birth_date'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $dept_no = isset($_POST['dept_no']) ? $_POST['dept_no'] : '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $salary = isset($_POST['salary']) ? $_POST['salary'] : '';

    if (empty($first_name) || empty($last_name) || empty($birth_date) || empty($gender) || empty($dept_no) || empty($title) || empty($salary)) {
        $response['success'] = false;
        $response['message'] = "Por favor, complete todos los campos.";
    } else {
        // Query SQL para insertar un nuevo empleado en la tabla "employees"
        $sql = "INSERT INTO employees (birth_date, first_name, last_name, gender, hire_date) 
                VALUES (?, ?, ?, ?, NOW())";

        // Preparar la consulta
        if ($stmt = $conexion->prepare($sql)) {
            // Vincular los parámetros
            $stmt->bind_param("ssss", $birth_date, $first_name, $last_name, $gender);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Obtener el ID del empleado recién insertado
                $employee_id = $conexion->insert_id;

                // Insertar también en la tabla de salarios
                $sql_salaries = "INSERT INTO salaries (emp_no, salary, from_date, to_date) 
                                 VALUES (?, ?, NOW(), '9999-01-01')";

                if ($stmt_salaries = $conexion->prepare($sql_salaries)) {
                    // Vincular los parámetros para la tabla de salarios
                    $stmt_salaries->bind_param("id", $employee_id, $salary);

                    // Ejecutar la consulta para la tabla de salarios
                    if (!$stmt_salaries->execute()) {
                        $response['success'] = false;
                        $response['message'] = "Error al insertar salario: " . $stmt_salaries->error;
                    }
                }

                // Insertar el cargo en la tabla de títulos
                $sql_titles = "INSERT INTO titles (emp_no, title, from_date, to_date) 
                               VALUES (?, ?, NOW(), '9999-01-01')";

                if ($stmt_titles = $conexion->prepare($sql_titles)) {
                    // Vincular los parámetros para la tabla de títulos
                    $stmt_titles->bind_param("is", $employee_id, $title);

                    // Ejecutar la consulta para la tabla de títulos
                    if (!$stmt_titles->execute()) {
                        $response['success'] = false;
                        $response['message'] = "Error al insertar título: " . $stmt_titles->error;
                    }
                }

                // Insertar en la tabla dept_emp
                $sql_dept_emp = "INSERT INTO dept_emp (emp_no, dept_no, from_date, to_date) 
                                 VALUES (?, ?, NOW(), '9999-01-01')";

                if ($stmt_dept_emp = $conexion->prepare($sql_dept_emp)) {
                    // Vincular los parámetros para la tabla dept_emp
                    $stmt_dept_emp->bind_param("is", $employee_id, $dept_no);

                    // Ejecutar la consulta para la tabla dept_emp
                    if (!$stmt_dept_emp->execute()) {
                        $response['success'] = false;
                        $response['message'] = "Error al insertar departamento: " . $stmt_dept_emp->error;
                    }
                }

                if (!isset($response['success'])) {
                    $response['success'] = true;
                    $response['message'] = "Empleado registrado correctamente.";
                }
            } else {
                $response['success'] = false;
                $response['message'] = "Error al registrar el empleado: " . $stmt->error;
            }

            // Cerrar las consultas
            $stmt->close();
            if (isset($stmt_salaries))
                $stmt_salaries->close();
            if (isset($stmt_titles))
                $stmt_titles->close();
            if (isset($stmt_dept_emp))
                $stmt_dept_emp->close();
        } else {
            $response['success'] = false;
            $response['message'] = "Error en la preparación de la consulta: " . $conexion->error;
        }
    }
} else {
    $response['success'] = false;
    $response['message'] = "Solicitud no válida";
}

// Cerrar la conexión a la base de datos
$conexion->close();


?>