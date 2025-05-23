CREATE DATABASE questionmark;
USE questionmark;

CREATE TABLE Usuarios (
                          id_usuario INT PRIMARY KEY AUTO_INCREMENT,
                          nombre_completo VARCHAR(255) NOT NULL,
                          ano_nacimiento INT NOT NULL,
                          sexo ENUM('M', 'F', 'N') NOT NULL,
                          pais VARCHAR(100),
                          ciudad VARCHAR(100),
                          email VARCHAR(255) NOT NULL UNIQUE,
                          contrasenia VARCHAR(255) NOT NULL,
                          nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
                          foto_perfil VARCHAR(255),
                          rol ENUM('usuario', 'editor', 'administrador') DEFAULT 'usuario',
                          cuenta_validada BOOLEAN DEFAULT FALSE,
                          fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);
