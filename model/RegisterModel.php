<?php

class RegisterModel
{
private $database;

public function __construct($database){
    $this->database = $database;
}



    public function register($nombre_completo, $ano_nacimiento, $sexo, $pais, $ciudad, $mail, $nombre_usuario, $contrasenia, $repetirContrasenia, $foto_perfil) {
        // Validar contraseñas
        $errorContrasenia = $this->contraseniasCoinciden($contrasenia, $repetirContrasenia);
        if ($errorContrasenia) {
            return $errorContrasenia; // Devuelve el string con el error
        }

        // Validar usuario existente
        $errorUsuario = $this->usuarioYaExiste($mail, $nombre_usuario);
        if ($errorUsuario) {
            return $errorUsuario; // Devuelve el string con el error
        }

        // Guardar imagen y validar resultado
        $resultadoImagen = $this->savePicture($foto_perfil);
        if (!str_starts_with($resultadoImagen, "view/img_page/")) {
            return $resultadoImagen; // Si no empieza con la ruta esperada, es un error
        }




        // Insertar en la base de datos y obtener token para validación
        // con el resultadoimagen que es ya el formato con el que quero que se guarde en la BD para luego acceder a el
        $token = $this->mandarAbaseDeDatos($nombre_completo, $ano_nacimiento, $sexo, $pais, $ciudad, $mail, $nombre_usuario, $contrasenia, $resultadoImagen);
        if (!is_string($token)) {
            // Si mandarAbaseDeDatos no devuelve un token válido, es error
            return "Error al registrar el usuario.";
        }


// Enviar email con API de Brevo
        $link = "http://localhost/index.php?controller=register&method=validate&token=" . $token;

        $data = [
            "sender" => [
                "name" => "QuestionMark",
                "email" => "nadalwebss@gmail.com"  // o tunombre@outlook.com
            ]
            ,
            "to" => [["email" => $mail]],
            "subject" => "Confirma tu cuenta",
            "htmlContent" => "<p>Gracias por registrarte, <strong>$nombre_usuario</strong>.<br> Hacé clic en el siguiente enlace para validar tu cuenta:</p>
                          <p><a href='$link'>$link</a></p>"
        ];


        $headers = [
            "accept: application/json",
            "api-key: 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",
            "content-type: application/json"
        ];

        $ch = curl_init("https://api.brevo.com/v3/smtp/email");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 201) {
            return "Error al enviar el correo de confirmación.";
        }
        return true;


    }








    private function mandarAbaseDeDatos($nombre_completo, $ano_nacimiento, $sexo, $pais, $ciudad, $mail, $nombre_usuario, $contrasenia, $foto_perfil)
    {

        $token = $this->generateToken();

        $sql = "INSERT INTO Usuario (nombre_completo, ano_nacimiento, sexo, pais, ciudad, email, nombre_usuario, contrasenia, foto_perfil, rol, cuenta_validada, fecha_registro, token_validacion)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'usuario', FALSE, NOW(), ?)";

        $this->database->execute($sql, [
            $nombre_completo,
            $ano_nacimiento,
            $sexo,
            $pais,
            $ciudad,
            $mail,
            $nombre_usuario,
            $contrasenia,
            $foto_perfil,
            $token
        ]);

        return $token; // lo devolvés para mandar mail con el link


    }

    private function contraseniasCoinciden($contrasenia, $repetirContrasenia) {
        if ($contrasenia === $repetirContrasenia) {
            return null; // OK
        } else {
            return "Las contraseñas no coinciden.";
        }
    }

    private function usuarioYaExiste($mail, $nombre_usuario) {
        $sql = "SELECT COUNT(*) AS total FROM Usuario WHERE email = ? OR nombre_usuario = ?";
        $result = $this->database->query($sql, [$mail, $nombre_usuario]);
        $row = $result->fetch_assoc();
        if ($row['total'] > 0) {
            return "El email o nombre de usuario ya están en uso.";
        }
        return null; // OK
    }





    private function savePicture($foto_perfil) {
        if (!$foto_perfil || $foto_perfil['error'] !== UPLOAD_ERR_OK) {
            return "No se recibió ninguna imagen válida.";
        }

        $fileExtension = strtolower(pathinfo($foto_perfil['name'], PATHINFO_EXTENSION));
        $validExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
        if (!in_array($fileExtension, $validExtensions)) {
            return "Extensión de archivo no válida: " . $fileExtension;
        }

        $id = $this->uuid(); // nombre único
        $nuevoNombreArchivo = $id . "." . $fileExtension;

        // Ruta absoluta para guardar la imagen
        $carpetaDestino = "C:/xampp/htdocs/view/img_page/";
        $rutaDestino = $carpetaDestino . $nuevoNombreArchivo;

        // Verificar que la carpeta exista y sea escribible
        if (!is_dir($carpetaDestino) || !is_writable($carpetaDestino)) {
            return "El directorio no es escribible: " . $carpetaDestino;
        }

        // Mover el archivo
        if (!move_uploaded_file($foto_perfil['tmp_name'], $rutaDestino)) {
            return "Error al mover el archivo de imagen.";
        }

        // Ruta relativa que se guarda en la base de datos y se usa en vistas
        return "view/img_page/" . $nuevoNombreArchivo;
    }



    //funcion para crear nombres unicos y randoms para que  cuando vaya a guardar el nombre del archivo de la foto de perfil a la BD
    private function uuid()
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }



    private function generateToken()
    {
        return bin2hex(random_bytes(32)); // token de 64 caracteres hexadecimales
    }

    public function validarCuenta($token) {
        // Primero buscamos si existe el token
        $sql = "SELECT id_usuario FROM Usuario WHERE token_validacion = ? AND cuenta_validada = 0";
        $usuario = $this->database->fetchOne($sql, [$token]);

        if (!$usuario) {
            return "Token inválido o la cuenta ya fue validada.";
        }

        // Si existe, actualizamos la cuenta como validada
        $sqlUpdate = "UPDATE Usuario SET cuenta_validada = 1, token_validacion = NULL WHERE id_usuario = ?";
        $this->database->execute($sqlUpdate, [$usuario['id_usuario']]);

        return true;
    }

}










