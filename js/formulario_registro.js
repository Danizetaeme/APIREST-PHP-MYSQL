$(document).ready(function () {
    // Manejar el envío del formulario del modal
    $('#guardarEmpleado').click(function () {
        // Obtener los datos del formulario
        var formData = $('#nuevoEmpleadoForm').serialize();

        // Enviar los datos al servidor utilizando AJAX
        $.ajax({
            type: 'POST',
            url: 'controlador/registro_persona.php',
            data: formData,
            dataType: 'json', // Respuesta en formato JSON
            success: function (response) {
                if (response.success) {
                    // Si la creación fue exitosa, muestro un mensaje de éxito
                    alert('Éxito: ' + response.message);

                    // Cerrar el modal
                    $('#nuevoEmpleadoModal').modal('hide');

                    // Realizar acciones adicionales si es necesario

                } else {
                    // Si hubo un error, muestra un mensaje de error
                    alert('Error: ' + response.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Mostrar el error específico en la consola
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);

                // Mostrar errores en una alerta con detalles específicos
                alert('Error en la solicitud AJAX:\n' + textStatus + '\n' + errorThrown);

                // Aquí puedes realizar otras acciones de manejo de errores si es necesario.
            }

        });
    });
});
