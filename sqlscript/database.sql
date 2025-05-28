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

-- Tabla partida
CREATE TABLE partida (
                         id_partida INT AUTO_INCREMENT PRIMARY KEY,
                         id_usuario INT NOT NULL,
                         fecha_inicio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         fecha_fin DATETIME,
                         puntaje INT DEFAULT 0,
                         estado ENUM('activa', 'terminada', 'abandonada') DEFAULT 'activa',
                         FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

-- Tabla pregunta
CREATE TABLE pregunta (
                          id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
                          texto VARCHAR(500) NOT NULL,
                          opcion_a VARCHAR(255) NOT NULL,
                          opcion_b VARCHAR(255) NOT NULL,
                          opcion_c VARCHAR(255) NOT NULL,
                          opcion_d VARCHAR(255) NOT NULL,
                          respuesta_correcta ENUM('A', 'B', 'C', 'D') NOT NULL,
                          categoria VARCHAR(100) NOT NULL,
                          dificultad ENUM('facil', 'media', 'dificil') DEFAULT 'media',
                          creada_por INT,
                          estado ENUM('activa', 'desactivada', 'pendiente') DEFAULT 'activa',
                          FOREIGN KEY (creada_por) REFERENCES usuario(id_usuario)
);

-- Tabla preguntas_partida (relación entre partidas y preguntas)
CREATE TABLE preguntas_partida (
                                   id_pregunta_partida INT AUTO_INCREMENT PRIMARY KEY,
                                   id_partida INT NOT NULL,
                                   id_pregunta INT NOT NULL,
                                   respondida_correctamente BOOLEAN,
                                   FOREIGN KEY (id_partida) REFERENCES partida(id_partida),
                                   FOREIGN KEY (id_pregunta) REFERENCES pregunta(id_pregunta)
);

-- Tabla reporte_pregunta (para reportes de usuarios)
CREATE TABLE reporte_pregunta (
                                  id_reporte INT AUTO_INCREMENT PRIMARY KEY,
                                  id_pregunta INT NOT NULL,
                                  id_usuario INT NOT NULL,
                                  motivo VARCHAR(500),
                                  fecha_reporte DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  FOREIGN KEY (id_pregunta) REFERENCES pregunta(id_pregunta),
                                  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

INSERT INTO pregunta (texto, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, categoria, dificultad, creada_por, estado) VALUES
                                                                                                                                        ('¿Cuál es la capital de Francia?', 'Madrid', 'París', 'Berlín', 'Lisboa', 'B', 'Geografía', 'facil', NULL, 'activa'),
                                                                                                                                        ('¿Quién escribió "Cien años de soledad"?', 'Pablo Neruda', 'Gabriel García Márquez', 'Mario Vargas Llosa', 'Jorge Luis Borges', 'B', 'Literatura', 'facil', NULL, 'activa'),
                                                                                                                                        ('¿Cuántos planetas tiene el sistema solar?', '8', '9', '7', '10', 'A', 'Ciencia', 'facil', NULL, 'activa'),
                                                                                                                                        ('¿Cuál es el símbolo químico del oro?', 'Au', 'Ag', 'Fe', 'Pb', 'A', 'Ciencia', 'facil', NULL, 'activa'),
                                                                                                                                        ('¿En qué año comenzó la Segunda Guerra Mundial?', '1914', '1939', '1945', '1929', 'B', 'Historia', 'media', NULL, 'activa'),
                                                                                                                                        ('¿Cuál es el idioma más hablado en el mundo?', 'Inglés', 'Mandarín', 'Español', 'Hindi', 'B', 'Cultura', 'facil', NULL, 'activa'),
                                                                                                                                        ('¿Quién pintó "La última cena"?', 'Miguel Ángel', 'Rafael', 'Leonardo da Vinci', 'Donatello', 'C', 'Arte', 'media', NULL, 'activa'),
                                                                                                                                        ('¿Cuál es el país con mayor población?', 'India', 'Estados Unidos', 'China', 'Indonesia', 'C', 'Geografía', 'facil', NULL, 'activa'),
                                                                                                                                        ('¿Qué instrumento mide la presión atmosférica?', 'Barómetro', 'Termómetro', 'Anemómetro', 'Higrómetro', 'A', 'Ciencia', 'media', NULL, 'activa'),
                                                                                                                                        ('¿Cuál es el río más largo del mundo?', 'Amazonas', 'Nilo', 'Yangtsé', 'Misisipi', 'B', 'Geografía', 'media', NULL, 'activa'),
                                                                                                                                        ('¿Quién descubrió América?', 'Cristóbal Colón', 'Américo Vespucio', 'Fernando de Magallanes', 'Hernán Cortés', 'A', 'Historia', 'facil', NULL, 'activa'),
                                                                                                                                        ('¿Cuál es el resultado de 9 x 7?', '56', '63', '72', '49', 'B', 'Matemática', 'facil', NULL, 'activa'),
                                                                                                                                        ('¿En qué continente está Egipto?', 'Asia', 'África', 'Europa', 'Oceanía', 'B', 'Geografía', 'facil', NULL, 'activa'),
                                                                                                                                        ('¿Qué gas respiramos principalmente?', 'Oxígeno', 'Nitrógeno', 'Dióxido de carbono', 'Hidrógeno', 'B', 'Ciencia', 'facil', NULL, 'activa'),
                                                                                                                                        ('¿Qué deporte se juega en Wimbledon?', 'Golf', 'Tenis', 'Fútbol', 'Rugby', 'B', 'Deportes', 'facil', NULL, 'activa'),
                                                                                                                                        ('¿Quién fue el primer hombre en pisar la Luna?', 'Buzz Aldrin', 'Neil Armstrong', 'Yuri Gagarin', 'Michael Collins', 'B', 'Historia', 'media', NULL, 'activa'),
                                                                                                                                        ('¿Cuál es el país más grande del mundo?', 'Canadá', 'Estados Unidos', 'China', 'Rusia', 'D', 'Geografía', 'media', NULL, 'activa'),
                                                                                                                                        ('¿Qué animal es conocido como el rey de la selva?', 'Elefante', 'Tigre', 'León', 'Pantera', 'C', 'Cultura', 'facil', NULL, 'activa'),
                                                                                                                                        ('¿Cuál es el metal más ligero?', 'Hierro', 'Aluminio', 'Litio', 'Plomo', 'C', 'Ciencia', 'media', NULL, 'activa'),
                                                                                                                                        ('¿Qué país ganó el Mundial de fútbol en 2018?', 'Brasil', 'Alemania', 'Francia', 'Argentina', 'C', 'Deportes', 'media', NULL, 'activa');
