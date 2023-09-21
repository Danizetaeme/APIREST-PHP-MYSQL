<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Empleado</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Perfil de Empleado</h2>
                    </div>
                    <div class="card-body">
                        <?php
                        include "modelo/conexion.php";

                        if (isset($_GET['emp_no'])) {
                            $emp_no = $_GET['emp_no'];

                            $sql = $conexion->query("SELECT e.emp_no, e.first_name, e.last_name, e.gender, d.dept_name, t.title, s.salary, e.hire_date, e.birth_date
                                                    FROM employees AS e
                                                    INNER JOIN dept_emp AS de ON e.emp_no = de.emp_no
                                                    INNER JOIN departments AS d ON de.dept_no = d.dept_no
                                                    INNER JOIN titles AS t ON e.emp_no = t.emp_no
                                                    INNER JOIN salaries AS s ON e.emp_no = s.emp_no
                                                    WHERE e.emp_no = $emp_no");

                            $datos = $sql->fetch_object();
                            if ($datos) {
                                ?>
                                <div class="text-center">
                                    <i class="material-icons" style="font-size: 72px; color: #007bff;">person</i>
                                </div>
                                <h3 class="text-center">
                                    <?= $datos->first_name ?>
                                    <?= $datos->last_name ?>
                                </h3>
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>ID:</strong>
                                        <?= $datos->emp_no ?>
                                    </li>
                                    <li class="list-group-item"><strong>Género:</strong>
                                        <?= $datos->gender ?>
                                    </li>
                                    <li class="list-group-item"><strong>Departamento:</strong>
                                        <?= $datos->dept_name ?>
                                    </li>
                                    <li class="list-group-item"><strong>Cargo:</strong>
                                        <?= $datos->title ?>
                                    </li>
                                    <li class="list-group-item"><strong>Salario:</strong>
                                        <?= $datos->salary ?>
                                    </li>
                                    <li class="list-group-item"><strong>Fecha de Contratación:</strong>
                                        <?= $datos->hire_date ?>
                                    </li>
                                    <li class="list-group-item"><strong>Fecha de Nacimiento:</strong>
                                        <?= $datos->birth_date ?>
                                    </li>                               
                                </ul>
                                <div class="text-center mt-3">
                                        <a href="index.php" class="btn btn-primary"><i
                                                class="material-icons align-middle">arrow_back</i>Volver Atrás</a>
                                    </div>
                                <?php
                            } else {
                                echo "Empleado no encontrado";
                            }
                        } else {
                            echo "Empleado no especificado";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>