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



-- Tabla preguntas_juego (reemplaza a preguntas)
CREATE TABLE `preguntas_juego` (
                                   id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
                                   `texto` varchar(500) NOT NULL,
                                   `categoria` varchar(100) NOT NULL,
                                   `dificultad` enum('facil','media','dificil') DEFAULT 'media',
                                   `creada_por` int(11) DEFAULT NULL,
                                   `estado` enum('activa','desactivada','pendiente') DEFAULT 'activa',
                                   FOREIGN KEY (creada_por) REFERENCES usuario(id_usuario)
);
-- Tabla preguntas_juego (reemplaza a preguntas)
CREATE TABLE `respuestas_juego` (
                                    `id_respuesta` INT AUTO_INCREMENT PRIMARY KEY,
                                    `id_pregunta` int(11) NOT NULL,
                                    `opcion_a` varchar(255) NOT NULL,
                                    `opcion_b` varchar(255) NOT NULL,
                                    `opcion_c` varchar(255) NOT NULL,
                                    `opcion_d` varchar(255) NOT NULL,
                                    `respuesta_correcta` enum('A','B','C','D') NOT NULL,
                                    FOREIGN KEY (id_pregunta) REFERENCES preguntas_juego(id_pregunta) );

-- Tabla reporte_pregunta (modificada para que la fk sea preguntas_juego)
CREATE TABLE reporte_pregunta (
                                  id_reporte INT AUTO_INCREMENT PRIMARY KEY,
                                  id_pregunta INT NOT NULL,
                                  id_usuario INT NOT NULL,
                                  motivo VARCHAR(500),
                                  fecha_reporte DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  FOREIGN KEY (id_pregunta) REFERENCES preguntas_juego(id_pregunta),
                                  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);



INSERT INTO `preguntas_juego` (`id_pregunta`, `texto`, `categoria`, `dificultad`, `creada_por`, `estado`) VALUES
                                                                                                              (1, '¿Cuál es la capital de Francia?', 'Geografía', 'facil', NULL, 'activa'),
                                                                                                              (2, '¿Quién escribió \"Cien años de soledad\"?', 'Literatura', 'facil', NULL, 'activa'),
                                                                                                              (3, '¿Cuántos planetas tiene el sistema solar?', 'Ciencia', 'facil', NULL, 'activa'),
                                                                                                              (4, '¿Cuál es el símbolo químico del oro?', 'Ciencia', 'facil', NULL, 'activa'),
                                                                                                              (5, '¿En qué año comenzó la Segunda Guerra Mundial?', 'Historia', 'media', NULL, 'activa'),
                                                                                                              (6, '¿Cuál es el idioma más hablado en el mundo?', 'Cultura', 'facil', NULL, 'activa'),
                                                                                                              (7, '¿Quién pintó \"La última cena\"?', 'Arte', 'media', NULL, 'activa'),
                                                                                                              (8, '¿Cuál es el país con mayor población?', 'Geografía', 'facil', NULL, 'activa'),
                                                                                                              (9, '¿Qué instrumento mide la presión atmosférica?', 'Ciencia', 'media', NULL, 'activa'),
                                                                                                              (10, '¿Cuál es el río más largo del mundo?', 'Geografía', 'media', NULL, 'activa'),
                                                                                                              (11, '¿Quién descubrió América?', 'Historia', 'facil', NULL, 'activa'),
                                                                                                              (12, '¿Cuál es el resultado de 9 x 7?', 'Matemática', 'facil', NULL, 'activa'),
                                                                                                              (13, '¿En qué continente está Egipto?', 'Geografía', 'facil', NULL, 'activa'),
                                                                                                              (14, '¿Qué gas respiramos principalmente?', 'Ciencia', 'facil', NULL, 'activa'),
                                                                                                              (15, '¿Qué deporte se juega en Wimbledon?', 'Deportes', 'facil', NULL, 'activa'),
                                                                                                              (16, '¿Quién fue el primer hombre en pisar la Luna?', 'Historia', 'media', NULL, 'activa'),
                                                                                                              (17, '¿Cuál es el país más grande del mundo?', 'Geografía', 'media', NULL, 'activa'),
                                                                                                              (18, '¿Qué animal es conocido como el rey de la selva?', 'Cultura', 'facil', NULL, 'activa'),
                                                                                                              (19, '¿Cuál es el metal más ligero?', 'Ciencia', 'media', NULL, 'activa'),
                                                                                                              (20, '¿Qué país ganó el Mundial de fútbol en 2018?', 'Deportes', 'media', NULL, 'activa');


INSERT INTO `respuestas_juego` (`id_respuesta`, `id_pregunta`, `opcion_a`, `opcion_b`, `opcion_c`, `opcion_d`, `respuesta_correcta`) VALUES
                                                                                                                                         (1, 1, 'Madrid', 'París', 'Berlín', 'Lisboa', 'B'),
                                                                                                                                         (2, 2, 'Pablo Neruda', 'Gabriel García Márquez', 'Mario Vargas Llosa', 'Jorge Luis Borges', 'B'),
                                                                                                                                         (3, 3, '8', '9', '7', '10', 'A'),
                                                                                                                                         (4, 4, 'Au', 'Ag', 'Fe', 'Pb', 'A'),
                                                                                                                                         (5, 5, '1914', '1939', '1945', '1929', 'B'),
                                                                                                                                         (6, 6, 'Inglés', 'Mandarín', 'Español', 'Hindi', 'B'),
                                                                                                                                         (7, 7, 'Miguel Ángel', 'Rafael', 'Leonardo da Vinci', 'Donatello', 'C'),
                                                                                                                                         (8, 8, 'India', 'Estados Unidos', 'China', 'Indonesia', 'C'),
                                                                                                                                         (9, 9, 'Barómetro', 'Termómetro', 'Anemómetro', 'Higrómetro', 'A'),
                                                                                                                                         (10, 10, 'Amazonas', 'Nilo', 'Yangtsé', 'Misisipi', 'B'),
                                                                                                                                         (11, 11, 'Cristóbal Colón', 'Américo Vespucio', 'Fernando de Magallanes', 'Hernán Cortés', 'A'),
                                                                                                                                         (12, 12, '56', '63', '72', '49', 'B'),
                                                                                                                                         (13, 13, 'Asia', 'África', 'Europa', 'Oceanía', 'B'),
                                                                                                                                         (14, 14, 'Oxígeno', 'Nitrógeno', 'Dióxido de carbono', 'Hidrógeno', 'B'),
                                                                                                                                         (15, 15, 'Golf', 'Tenis', 'Fútbol', 'Rugby', 'B'),
                                                                                                                                         (16, 16, 'Buzz Aldrin', 'Neil Armstrong', 'Yuri Gagarin', 'Michael Collins', 'B'),
                                                                                                                                         (17, 17, 'Canadá', 'Estados Unidos', 'China', 'Rusia', 'D'),
                                                                                                                                         (18, 18, 'Elefante', 'Tigre', 'León', 'Pantera', 'C'),
                                                                                                                                         (19, 19, 'Hierro', 'Aluminio', 'Litio', 'Plomo', 'C'),
                                                                                                                                         (20, 20, 'Brasil', 'Alemania', 'Francia', 'Argentina', 'C');