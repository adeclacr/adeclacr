<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recopilar los datos del formulario
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $mensaje = $_POST["mensaje"];

    // Verificar si se envió un archivo adjunto
    $archivoAdjunto = null;
    if ($_FILES["adjunto"]["size"] > 0) {
        $archivoAdjunto = $_FILES["adjunto"]["tmp_name"];
    }

    // Dirección de correo a la que se enviará el mensaje
    $destinatario = "adecla.aurora@gmail.com";

    // Asunto del correo
    $asunto = "Mensaje de contacto desde el sitio web";

    // Construir el cuerpo del mensaje
    $cuerpoMensaje = "Nombre: $nombre\n";
    $cuerpoMensaje .= "Email: $email\n";
    $cuerpoMensaje .= "Mensaje:\n$mensaje";

    // Encabezados del correo
    $headers = "From: $nombre <$email>";

    // Si se adjuntó un archivo, adjuntarlo al correo
    if ($archivoAdjunto) {
        $adjunto = file_get_contents($archivoAdjunto);
        $adjuntoCodificado = chunk_split(base64_encode($adjunto));
        $nombreArchivo = basename($_FILES["adjunto"]["name"]);

        $headers .= "\nMIME-Version: 1.0\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".md5(time())."\"\n";
        $mensajeCorreo = "--PHP-mixed-".md5(time())."\n";
        $mensajeCorreo .= "Content-Transfer-Encoding: 7bit\n\n";
        $mensajeCorreo .= "$cuerpoMensaje\n\n";
        $mensajeCorreo .= "--PHP-mixed-".md5(time())."\n";
        $mensajeCorreo .= "Content-Type: application/octet-stream; name=\"$nombreArchivo\"\n";
        $mensajeCorreo .= "Content-Transfer-Encoding: base64\n";
        $mensajeCorreo .= "Content-Disposition: attachment\n\n";
        $mensajeCorreo .= "$adjuntoCodificado\n";
        $mensajeCorreo .= "--PHP-mixed-".md5(time())."--";

        // Enviar el correo con archivo adjunto
        if (mail($destinatario, $asunto, $mensajeCorreo, $headers)) {
            echo "¡El mensaje ha sido enviado correctamente!";
        } else {
            echo "Hubo un error al enviar el mensaje. Por favor, inténtelo de nuevo más tarde.";
        }
    } else {
        // Si no se adjunta un archivo, enviar solo el mensaje de texto
        if (mail($destinatario, $asunto, $cuerpoMensaje, $headers)) {
            echo "¡El mensaje ha sido enviado correctamente!";
        } else {
            echo "Hubo un error al enviar el mensaje. Por favor, inténtelo de nuevo más tarde.";
        }
    }
} else {
    // Si se intenta acceder al archivo directamente, redireccionar al formulario de contacto
    header("Location: formulario_contacto.html");
}
?>
