<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crud en PHP y MySQL</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="/public\css\index.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,500">
    <link rel="stylesheet" href="https://kit-pro.fontawesome.com/releases/latest/css/pro.min.css"
        integrity="sha384-xjhO1C0tj1uq0J5KA04i8FVzpGB6Fj4gYpDlHTtqSpM6SZFiwe5w5R92bJagD5Jkh" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/6a8b5ae6b1.js" crossorigin="anonymous"></script>
</head>

<body>
    <header class="bg-dark text-light text-center py-3">
        <h1 class="display-4">Lista de Empleados</h1>
    </header>

    <div class="container-fluid row">
        <!-- LISTADO DE EMPLEADOS -->
        <div class="col-12 p-4 text-center">
            <button type="button" class="btn btn-success mb-4" data-bs-toggle="modal"
                data-bs-target="#nuevoEmpleadoModal">
                Nuevo Empleado
            </button>
            <table class="table custom-table mx-auto">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">NOMBRE</th>
                        <th scope="col">APELLIDOS</th>
                        <th scope="col">DEPARTAMENTO</th>
                        <th scope="col">CARGO</th>
                        <th scope="col">SALARIO</th>
                        <th scope="col">FECHA DE CONTRATACIÓN</th>
                        <th scope="col">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>


                    <!-- RECORRIDO DE DATOS A LA BBDD -->
                    <?php
                    include "modelo/conexion.php";

                    // Paginación
                    $registros_por_pagina = 50;
                    if (isset($_GET['pagina'])) {
                        $pagina = $_GET['pagina'];
                    } else {
                        $pagina = 1;
                    }
                    $inicio = ($pagina - 1) * $registros_por_pagina;

                    // Consulta SQL con límite y paginación
                    $sql = $conexion->query("SELECT e.emp_no, e.first_name, e.last_name, d.dept_name, t.title, s.salary, e.hire_date
                        FROM employees AS e
                        INNER JOIN dept_emp AS de ON e.emp_no = de.emp_no
                        INNER JOIN departments AS d ON de.dept_no = d.dept_no
                        INNER JOIN titles AS t ON e.emp_no = t.emp_no
                        INNER JOIN salaries AS s ON e.emp_no = s.emp_no
                        WHERE de.to_date = '9999-01-01' AND t.to_date = '9999-01-01' AND s.to_date = '9999-01-01'
                        ORDER BY e.hire_date
                        LIMIT $inicio, $registros_por_pagina");

                    while ($datos = $sql->fetch_object()) { ?>
                        <tr>
                            <td>
                                <?= $datos->emp_no ?>
                            </td>
                            <td>
                                <?= $datos->first_name ?>
                            </td>
                            <td>
                                <?= $datos->last_name ?>
                            </td>
                            <td>
                                <?= $datos->dept_name ?>
                            </td> <!-- departamento -->
                            <td>
                                <?= $datos->title ?>
                            </td> <!-- cargo -->
                            <td>
                                <?= $datos->salary ?>
                            </td> <!-- salario -->
                            <td>
                                <?= $datos->hire_date ?>
                            </td> <!-- fecha contratación -->
                            <td>
                                <a href="" class="btn btn-small btn-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="" class="btn btn-small btn-danger"><i class="fa-solid fa-trash"></i></a>
                                <a href="./ver_perfil.php?emp_no=<?= $datos->emp_no ?>" class="btn btn-small btn-primary"><i
                                        class="fa-solid fa-eye"></i></i> Ver Perfil</a>
                            </td>
                        </tr>
                    <?php }
                    ?>

                </tbody>
            </table>

            <!-- paginación de Bootstrap con botón para siguiente página -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php
                    // Calcular el número total de páginas
                    $sql_total = $conexion->query("SELECT COUNT(*) as total FROM employees");
                    $total_registros = $sql_total->fetch_object()->total;
                    $total_paginas = ceil($total_registros / $registros_por_pagina);

                    // Calcular la página anterior y siguiente
                    $pagina_anterior = $pagina - 1;
                    $pagina_siguiente = $pagina + 1;

                    // Mostrar botones de paginación
                    if ($pagina > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?pagina=1">1</a></li>';
                        if ($pagina > 2) {
                            echo '<li class="page-item"><span class="page-link">...</span></li>';
                        }
                    }

                    if ($pagina > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?pagina=' . $pagina_anterior . '">' . $pagina_anterior . '</a></li>';
                    }

                    echo '<li class="page-item active"><span class="page-link">' . $pagina . '</span></li>';

                    if ($pagina < $total_paginas) {
                        echo '<li class="page-item"><a class="page-link" href="?pagina=' . $pagina_siguiente . '">' . $pagina_siguiente . '</a></li>';
                        echo '<li class="page-item"><a class="page-link" href="?pagina=' . ($pagina + 1) . '">Siguiente</a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </div>
    </div>



    <!-- Modal para Nuevo Empleado -->
    <div class="modal fade" id="nuevoEmpleadoModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario para agregar nuevo empleado -->
                    <form id="nuevoEmpleadoForm">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date">
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Género</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="" disabled selected>Selecciona tu género</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dept_no" class="form-label">Departamento</label>
                            <select class="form-select" id="dept_no" name="dept_no">
                                <option value="" disabled selected>Elige departamento</option>
                                <option value="d009">Customer Service</option>
                                <option value="d005">Development</option>
                                <option value="d002">Finance</option>
                                <option value="d003">Human Resources</option>
                                <option value="d001">Marketing</option>
                                <option value="d004">Production</option>
                                <option value="d006">Quality Management</option>
                                <option value="d008">Research</option>
                                <option value="d007">Sales</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Cargo</label>
                            <input type="text" class="form-control" id="title" name="title">
                        </div>
                        <div class="mb-3">
                            <label for="salary" class="form-label">Salario</label>
                            <input type="number" class="form-control" id="salary" name="salary">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="guardarEmpleado">Guardar</button>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="js/formulario_registro.js"></script>
</body>

</html>