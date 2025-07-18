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
-- Tabla con las preguntas que se han realizado (mostrado en pantalla)
CREATE TABLE `preguntas_jugadas` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `id_usuario` int(11) NOT NULL,
                                     `id_pregunta` int(11) NOT NULL,
                                     `fecha_jugada` datetime DEFAULT current_timestamp(),
                                     PRIMARY KEY (`id`)
)
-- Tabla de conteo de preguntas que se utiliza para sacar el ratio
CREATE TABLE `pregunta_estadisticas` (
                                         `id_pregunta` int(11) NOT NULL,
                                         `veces_respondida` int(11) NOT NULL DEFAULT 0,
                                         `veces_acertada` int(11) NOT NULL DEFAULT 0,
                                         PRIMARY KEY (`id_pregunta`),
                                         FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas_juego` (`id_pregunta`)
)
-- Tabla de conteo de preguntas respondidas y acertadas del usuario que se utiliza para sacar el ratio
CREATE TABLE `usuario_estadisticas` (
                                        `id_usuario` int(11) NOT NULL,
                                        `preguntas_respondidas` int(11) NOT NULL DEFAULT 0,
                                        `respuestas_correctas` int(11) NOT NULL DEFAULT 0,
                                        PRIMARY KEY (`id_usuario`),
                                        FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
)
CREATE TABLE `preguntas_sugeridas` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                       `texto` varchar(255) NOT NULL,
                                       `opcion_a` varchar(255) NOT NULL,
                                       `opcion_b` varchar(255) NOT NULL,
                                       `opcion_c` varchar(255) NOT NULL,
                                       `opcion_d` varchar(255) NOT NULL,
                                       `respuesta_correcta` enum('A','B','C','D') NOT NULL,
                                       `categoria` enum('Arte','Ciencia','Cultura','Deporte','Geografía','Historia','Literatura','Matematicas') NOT NULL,
                                       `id_usuario` int(11) NOT NULL,
                                       `estado` enum('pendiente','activa','rechazada') DEFAULT 'pendiente',
                                       `dificultad` enum('facil','media','dificil') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


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
(20, '¿Qué país ganó el Mundial de fútbol en 2018?', 'Deportes', 'media', NULL, 'activa'),
(21, '¿Cuál es la capital de Mongolia?', 'Geografía', 'dificil', NULL, 'activa'),
(22, '¿Quién escribió "La montaña mágica"?', 'Literatura', 'dificil', NULL, 'activa'),
(23, '¿Cuál es la constante de Planck?', 'Ciencia', 'dificil', NULL, 'activa'),
(24, '¿Qué emperador romano legalizó el cristianismo?', 'Historia', 'dificil', NULL, 'activa'),
(25, '¿Cuál es el nombre del teorema que establece a² + b² = c²?', 'Matemática', 'facil', NULL, 'activa'),
(26, '¿Qué país tiene más volcanes activos?', 'Geografía', 'media', NULL, 'activa'),
(27, '¿Quién pintó "El jardín de las delicias"?', 'Arte', 'dificil', NULL, 'activa'),
(28, '¿Qué país fue el primero en legalizar el matrimonio igualitario?', 'Cultura', 'media', NULL, 'activa'),
(29, '¿Cuál es la raíz cuadrada de 144?', 'Matemática', 'facil', NULL, 'activa'),
(30, '¿Qué partícula subatómica tiene carga negativa?', 'Ciencia', 'media', NULL, 'activa'),
(31, '¿Cuál es la fórmula química del ozono?', 'Ciencia', 'dificil', NULL, 'activa'),
(32, '¿En qué año cayó el Imperio Bizantino?', 'Historia', 'dificil', NULL, 'activa'),
(33, '¿Quién ganó más Grand Slams en tenis hasta 2024?', 'Deportes', 'dificil', NULL, 'activa'),
(34, '¿Qué autor escribió "Crimen y castigo"?', 'Literatura', 'media', NULL, 'activa'),
(35, '¿Cuál es el nombre del satélite natural de Marte más grande?', 'Ciencia', 'dificil', NULL, 'activa'),
(36, '¿En qué país se encuentra el desierto de Atacama?', 'Geografía', 'media', NULL, 'activa'),
(37, '¿Qué número es primo: 51, 53 o 55?', 'Matemática', 'media', NULL, 'activa'),
(38, '¿Qué instrumento utilizaba Beethoven a pesar de su sordera?', 'Arte', 'media', NULL, 'activa'),
(39, '¿Qué equipo ha ganado más Copas Libertadores?', 'Deportes', 'dificil', NULL, 'activa'),
(40, '¿Quién fue la primera mujer en ganar un Nobel?', 'Historia', 'media', NULL, 'activa'),

-- Cultura - difícil
(41, '¿Qué civilización construyó Machu Picchu?', 'Cultura', 'dificil', NULL, 'activa'),

-- Arte - fácil
(42, '¿Quién pintó "La Mona Lisa"?', 'Arte', 'facil', NULL, 'activa'),

-- Matemática - difícil
(43, '¿Cuál es la integral indefinida de 2x?', 'Matemática', 'dificil', NULL, 'activa');



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
(20, 20, 'Brasil', 'Alemania', 'Francia', 'Argentina', 'C'),
(21, 21, 'Ulaanbaatar', 'Bakú', 'Tiflis', 'Hanoi', 'A'),
(22, 22, 'Franz Kafka', 'Thomas Mann', 'Hermann Hesse', 'Albert Camus', 'B'),
(23, 23, '6.626 × 10⁻³⁴ Js', '3.00 × 10⁸ m/s', '9.81 m/s²', '1.602 × 10⁻¹⁹ C', 'A'),
(24, 24, 'Nerón', 'Julio César', 'Constantino', 'Marco Aurelio', 'C'),
(25, 25, 'Teorema de Pitágoras', 'Teorema de Tales', 'Teorema de Euclides', 'Ley de Ohm', 'A'),
(26, 26, 'Japón', 'Indonesia', 'Chile', 'Italia', 'B'),
(27, 27, 'El Bosco', 'Caravaggio', 'Velázquez', 'Dalí', 'A'),
(28, 28, 'Países Bajos', 'España', 'Canadá', 'Argentina', 'A'),
(29, 29, '12', '11', '13', '10', 'A'),
(30, 30, 'Protón', 'Electrón', 'Neutrón', 'Ión', 'B'),
(31, 31, 'O₂', 'O₃', 'CO₂', 'NO₂', 'B'),
(32, 32, '1453', '1492', '1517', '1300', 'A'),
(33, 33, 'Roger Federer', 'Novak Djokovic', 'Rafael Nadal', 'Pete Sampras', 'B'),
(34, 34, 'Fiódor Dostoievski', 'León Tolstói', 'Antón Chéjov', 'Nikolái Gógol', 'A'),
(35, 35, 'Fobos', 'Deimos', 'Titán', 'Ganimedes', 'A'),
(36, 36, 'Perú', 'Chile', 'Bolivia', 'México', 'B'),
(37, 37, '51', '53', '55', 'Ninguno', 'B'),
(38, 38, 'Violín', 'Flauta', 'Piano', 'Clarinete', 'C'),
(39, 39, 'Boca Juniors', 'River Plate', 'Peñarol', 'Independiente', 'A'),
(40, 40, 'Marie Curie', 'Rosalind Franklin', 'Ada Lovelace', 'Emilie du Châtelet', 'A');

INSERT INTO Usuario (
    nombre_completo,
    ano_nacimiento,
    sexo,
    pais,
    ciudad,
    email,
    nombre_usuario,
    contrasenia,
    foto_perfil,
    rol,
    cuenta_validada,
    fecha_registro,
    token_validacion
) VALUES (
             'Editor',
             1990,
             'M',
             'Argentina',
             'Buenos Aires',
             'editor@gmail.com',
             'editor',
             '123',
             NULL,
             'editor',
             1,
             NOW(),
             NULL
         );
INSERT INTO Usuario (
    nombre_completo,
    ano_nacimiento,
    sexo,
    pais,
    ciudad,
    email,
    nombre_usuario,
    contrasenia,
    foto_perfil,
    rol,
    cuenta_validada,
    fecha_registro,
    token_validacion
) VALUES (
             'Admin',
             1990,
             'M',
             'Argentina',
             'Buenos Aires',
             'admin@gmail.com',
             'admin',
             '123',
             NULL,
             'administrador',
             1,
             NOW(),
             NULL
         );

-- agrego usuarios para aprovechar los graficos

INSERT INTO Usuario
(nombre_completo, ano_nacimiento, sexo, pais, ciudad, email, contrasenia, nombre_usuario, foto_perfil, rol, cuenta_validada)
VALUES
    ('Ana López', 2010, 'F', 'Argentina', 'Buenos Aires', 'ana.lopez@example.com', 'pass123', 'analopez', NULL, 'usuario', 1),
    ('Bruno Martínez', 1985, 'M', 'Argentina', 'Córdoba', 'bruno.martinez@example.com', 'pass123', 'brunom', NULL, 'usuario', 1),
    ('Carla Gómez', 1950, 'F', 'Chile', 'Santiago', 'carla.gomez@example.com', 'pass123', 'carlag', NULL, 'usuario', 1),
    ('Diego Fernández', 2005, 'M', 'Uruguay', 'Montevideo', 'diego.fernandez@example.com', 'pass123', 'dfernandez', NULL, 'usuario', 1),
    ('Elena Ruiz', 1990, 'F', 'Argentina', 'Rosario', 'elena.ruiz@example.com', 'pass123', 'elenar', NULL, 'usuario', 1),
    ('Facundo Navarro', 1975, 'M', 'Paraguay', 'Asunción', 'facundo.navarro@example.com', 'pass123', 'facunavarro', NULL, 'usuario', 1),
    ('Gabriela Sánchez', 1960, 'F', 'Argentina', 'Mendoza', 'gabriela.sanchez@example.com', 'pass123', 'gabsanchez', NULL, 'usuario', 1),
    ('Hugo Díaz', 2015, 'M', 'Chile', 'Valparaíso', 'hugo.diaz@example.com', 'pass123', 'hugodiaz', NULL, 'usuario', 1),
    ('Irene Torres', 1988, 'N', 'Argentina', 'La Plata', 'irene.torres@example.com', 'pass123', 'irenet', NULL, 'usuario', 1),
    ('Javier Molina', 1945, 'M', 'Uruguay', 'Salto', 'javier.molina@example.com', 'pass123', 'javmolina', NULL, 'usuario', 1);
