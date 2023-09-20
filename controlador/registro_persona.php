<?php
include "conexion.php";

// Obtener datos del formulario
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$birth_name = $_POST['birth_name'];
$gender = $_POST['gender'];
$dept_no = $_POST['dept_no'];
$title = $_POST['title'];
$salary = $_POST['salary'];

// Query para insertar al nuevo empleado en la base de datos
$sql = "INSERT INTO employees (first_name, last_name, birth_name, gender, hire_date) 
        VALUES ('$first_name', '$last_name', '$birth_name', '$gender', NOW())";

if ($conn->query($sql) === TRUE) {
    // Obtener el ID del empleado recién insertado
    $emp_no = $conn->insert_id;

    // Query para insertar la relación con el departamento
    $sql_dept = "INSERT INTO dept_emp (emp_no, dept_no, from_date, to_date)
                VALUES ($emp_no, '$dept_no', NOW(), '9999-01-01')";

    if ($conn->query($sql_dept) === TRUE) {
        // Query para insertar el título del empleado
        $sql_title = "INSERT INTO titles (emp_no, title, from_date, to_date)
                      VALUES ($emp_no, '$title', NOW(), '9999-01-01')";

        if ($conn->query($sql_title) === TRUE) {
            // Query para insertar el salario del empleado
            $sql_salary = "INSERT INTO salaries (emp_no, salary, from_date, to_date)
                           VALUES ($emp_no, $salary, NOW(), '9999-01-01')";

            if ($conn->query($sql_salary) === TRUE) {
                // Respuesta exitosa
                $response = [
                    "success" => true,
                    "message" => "Empleado registrado con éxito."
                ];
            } else {
                // Error al insertar salario
                $response = [
                    "success" => false,
                    "message" => "Error al insertar el salario: " . $conn->error
                ];
            }
        } else {
            // Error al insertar título
            $response = [
                "success" => false,
                "message" => "Error al insertar el título: " . $conn->error
            ];
        }
    } else {
        // Error al insertar departamento
        $response = [
            "success" => false,
            "message" => "Error al insertar el departamento: " . $conn->error
        ];
    }
} else {
    // Error al insertar empleado
    $response = [
        "success" => false,
        "message" => "Error al insertar el empleado: " . $conn->error
    ];
}

// Devolver respuesta JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
