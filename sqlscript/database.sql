CREATE DATABASE questionmark;
USE questionmark;

CREATE TABLE Usuario (
                         id_usuario INT PRIMARY KEY AUTO_INCREMENT,                   -- 1
                         nombre_completo VARCHAR(255) NOT NULL,                      -- 2
                         ano_nacimiento INT NOT NULL,                                -- 3
                         sexo ENUM('M', 'F', 'N') NOT NULL,                         -- 4
                         pais VARCHAR(100),                                          -- 5
                         ciudad VARCHAR(100),                                        -- 6
                         email VARCHAR(255) NOT NULL UNIQUE,                        -- 7
                         contrasenia VARCHAR(255) NOT NULL,                         -- 8
                         nombre_usuario VARCHAR(50) NOT NULL UNIQUE,                -- 9
                         foto_perfil VARCHAR(255),                                  -- 10
                         rol ENUM('usuario', 'editor', 'administrador') DEFAULT 'usuario', -- 11
                         cuenta_validada BOOLEAN DEFAULT FALSE,                     -- 12
                         fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,         -- 13 (movido aquí)
                         token_validacion VARCHAR(255)                               -- 14 (ahora el último)
);
