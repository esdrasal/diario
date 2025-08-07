-- Crear base de datos
CREATE DATABASE IF NOT EXISTS diario_biblico CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE diario_biblico;

-- Tabla de libros
CREATE TABLE libros (
    id INT PRIMARY KEY,
    nombre VARCHAR(100),
    testamento VARCHAR(20),
    capitulos INT,
    versiculos INT,
    abreviatura VARCHAR(10)
);

-- Tabla de capítulos
CREATE TABLE capitulos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL,
    libro_id INT NOT NULL,
    versiculos INT NOT NULL,
    FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1
);

-- Tabla de lecturas
CREATE TABLE lecturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha DATE NOT NULL,
    libro_id INT NOT NULL,
    capitulo_desde INT NOT NULL,
    versiculo_desde INT NOT NULL,
    capitulo_hasta INT NOT NULL,
    versiculo_hasta INT NOT NULL,
    notas TEXT,
    favoritos JSON,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE
);

-- Insertar datos de libros
INSERT INTO libros (id, nombre, testamento, capitulos, versiculos, abreviatura) VALUES
(1, 'Génesis', 'Antiguo', 50, 1533, 'Gn'),
(2, 'Éxodo', 'Antiguo', 40, 1213, 'Ex'),
(3, 'Levítico', 'Antiguo', 27, 859, 'Lv'),
(4, 'Números', 'Antiguo', 36, 1288, 'Nm'),
(5, 'Deuteronomio', 'Antiguo', 34, 959, 'Dt'),
(6, 'Josué', 'Antiguo', 24, 658, 'Jos'),
(7, 'Jueces', 'Antiguo', 21, 618, 'Jc'),
(8, 'Rut', 'Antiguo', 4, 85, 'Rt'),
(9, '1 Samuel', 'Antiguo', 31, 810, '1 S'),
(10, '2 Samuel', 'Antiguo', 24, 695, '2 S'),
(11, '1 Reyes', 'Antiguo', 22, 816, '1 R'),
(12, '2 Reyes', 'Antiguo', 25, 719, '2 R'),
(13, '1 Crónicas', 'Antiguo', 29, 942, '1 Cr'),
(14, '2 Crónicas', 'Antiguo', 36, 822, '2 Cr'),
(15, 'Esdras', 'Antiguo', 10, 280, 'Esd'),
(16, 'Nehemías', 'Antiguo', 13, 406, 'Ne'),
(17, 'Ester', 'Antiguo', 10, 167, 'Est'),
(18, 'Job', 'Antiguo', 42, 1070, 'Job'),
(19, 'Salmos', 'Antiguo', 150, 2461, 'Sal'),
(20, 'Proverbios', 'Antiguo', 31, 915, 'Pr'),
(21, 'Eclesiastés', 'Antiguo', 12, 222, 'Ec'),
(22, 'Cantares', 'Antiguo', 8, 117, 'Cant'),
(23, 'Isaías', 'Antiguo', 66, 1292, 'Is'),
(24, 'Jeremías', 'Antiguo', 52, 1364, 'Jer'),
(25, 'Lamentaciones', 'Antiguo', 5, 154, 'Lam'),
(26, 'Ezequiel', 'Antiguo', 48, 1273, 'Ez'),
(27, 'Daniel', 'Antiguo', 12, 357, 'Dn'),
(28, 'Oseas', 'Antiguo', 14, 197, 'Os'),
(29, 'Joel', 'Antiguo', 3, 73, 'Jl'),
(30, 'Amós', 'Antiguo', 9, 146, 'Am'),
(31, 'Abdías', 'Antiguo', 1, 21, 'Abd'),
(32, 'Jonás', 'Antiguo', 4, 48, 'Jon'),
(33, 'Miqueas', 'Antiguo', 7, 105, 'Mi'),
(34, 'Nahúm', 'Antiguo', 3, 47, 'Na'),
(35, 'Habacuc', 'Antiguo', 3, 56, 'Hab'),
(36, 'Sofonías', 'Antiguo', 3, 53, 'So'),
(37, 'Hageo', 'Antiguo', 2, 38, 'Ag'),
(38, 'Zacarías', 'Antiguo', 14, 211, 'Za'),
(39, 'Malaquías', 'Antiguo', 4, 55, 'Ml'),
(40, 'Mateo', 'Nuevo', 28, 1071, 'Mt'),
(41, 'Marcos', 'Nuevo', 16, 678, 'Mc'),
(42, 'Lucas', 'Nuevo', 24, 1151, 'Lc'),
(43, 'Juan', 'Nuevo', 21, 879, 'Jn'),
(44, 'Hechos', 'Nuevo', 28, 1007, 'Hch'),
(45, 'Romanos', 'Nuevo', 16, 433, 'Rom'),
(46, '1 Corintios', 'Nuevo', 16, 437, '1 Co'),
(47, '2 Corintios', 'Nuevo', 13, 257, '2 Co'),
(48, 'Gálatas', 'Nuevo', 6, 149, 'Gal'),
(49, 'Efesios', 'Nuevo', 6, 155, 'Ef'),
(50, 'Filipenses', 'Nuevo', 4, 104, 'Flp'),
(51, 'Colosenses', 'Nuevo', 4, 95, 'Col'),
(52, '1 Tesalonicenses', 'Nuevo', 5, 89, '1 Ts'),
(53, '2 Tesalonicenses', 'Nuevo', 3, 47, '2 Ts'),
(54, '1 Timoteo', 'Nuevo', 6, 113, '1 Tim'),
(55, '2 Timoteo', 'Nuevo', 4, 83, '2 Tim'),
(56, 'Tito', 'Nuevo', 3, 46, 'Tit'),
(57, 'Filemón', 'Nuevo', 1, 25, 'Flm'),
(58, 'Hebreos', 'Nuevo', 13, 303, 'Heb'),
(59, 'Santiago', 'Nuevo', 5, 108, 'Sto'),
(60, '1 Pedro', 'Nuevo', 5, 105, '1 P'),
(61, '2 Pedro', 'Nuevo', 3, 61, '2 P'),
(62, '1 Juan', 'Nuevo', 5, 105, '1 Jn'),
(63, '2 Juan', 'Nuevo', 1, 13, '2 Jn'),
(64, '3 Juan', 'Nuevo', 1, 14, '3 Jn'),
(65, 'Judas', 'Nuevo', 1, 25, 'Jud'),
(66, 'Apocalipsis', 'Nuevo', 22, 404, 'Ap');

-- Insertar usuario por defecto
INSERT INTO usuarios (nombre, email, password) VALUES 
('Usuario Demo', 'demo@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password

-- Insertar datos de capítulos
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
-- Génesis (libro_id=1)
(1, 1, 31),
(2, 1, 25),
(3, 1, 24),
(4, 1, 26),
(5, 1, 32),
(6, 1, 22),
(7, 1, 24),
(8, 1, 22),
(9, 1, 29),
(10, 1, 32),
(11, 1, 32),
(12, 1, 20),
(13, 1, 18),
(14, 1, 24),
(15, 1, 21),
(16, 1, 16),
(17, 1, 27),
(18, 1, 33),
(19, 1, 38),
(20, 1, 18),
(21, 1, 34),
(22, 1, 24),
(23, 1, 20),
(24, 1, 67),
(25, 1, 34),
(26, 1, 35),
(27, 1, 46),
(28, 1, 22),
(29, 1, 35),
(30, 1, 43),
(31, 1, 55),
(32, 1, 32),
(33, 1, 20),
(34, 1, 31),
(35, 1, 29),
(36, 1, 43),
(37, 1, 36),
(38, 1, 30),
(39, 1, 23),
(40, 1, 23),
(41, 1, 57),
(42, 1, 38),
(43, 1, 34),
(44, 1, 34),
(45, 1, 28),
(46, 1, 34),
(47, 1, 31),
(48, 1, 22),
(49, 1, 33),
(50, 1, 26);
-- Éxodo (libro_id=2)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 2, 22),(2, 2, 25),(3, 2, 22),(4, 2, 31),(5, 2, 23),(6, 2, 30),(7, 2, 25),(8, 2, 32),(9, 2, 35),(10, 2, 29),
(11, 2, 10),(12, 2, 51),(13, 2, 22),(14, 2, 31),(15, 2, 27),(16, 2, 36),(17, 2, 16),(18, 2, 27),(19, 2, 25),(20, 2, 26),
(21, 2, 36),(22, 2, 31),(23, 2, 33),(24, 2, 18),(25, 2, 40),(26, 2, 37),(27, 2, 21),(28, 2, 43),(29, 2, 46),(30, 2, 38),
(31, 2, 18),(32, 2, 35),(33, 2, 23),(34, 2, 35),(35, 2, 35),(36, 2, 38),(37, 2, 29),(38, 2, 31),(39, 2, 43),(40, 2, 38);
-- Levítico (libro_id=3)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 3, 17),(2, 3, 16),(3, 3, 17),(4, 3, 35),(5, 3, 19),(6, 3, 30),(7, 3, 38),(8, 3, 36),(9, 3, 24),(10, 3, 20),
(11, 3, 47),(12, 3, 8),(13, 3, 59),(14, 3, 57),(15, 3, 33),(16, 3, 34),(17, 3, 16),(18, 3, 30),(19, 3, 37),(20, 3, 27),
(21, 3, 24),(22, 3, 33),(23, 3, 44),(24, 3, 23),(25, 3, 55),(26, 3, 46),(27, 3, 34);
-- Números (libro_id=4)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 4, 54),(2, 4, 34),(3, 4, 51),(4, 4, 49),(5, 4, 31),(6, 4, 27),(7, 4, 89),(8, 4, 26),(9, 4, 23),(10, 4, 36),
(11, 4, 35),(12, 4, 16),(13, 4, 33),(14, 4, 45),(15, 4, 41),(16, 4, 50),(17, 4, 13),(18, 4, 32),(19, 4, 22),(20, 4, 29),
(21, 4, 35),(22, 4, 41),(23, 4, 30),(24, 4, 25),(25, 4, 18),(26, 4, 65),(27, 4, 23),(28, 4, 31),(29, 4, 40),(30, 4, 16),
(31, 4, 54),(32, 4, 42),(33, 4, 56),(34, 4, 29),(35, 4, 34),(36, 4, 13);
-- Deuteronomio (libro_id=5)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 5, 46),(2, 5, 37),(3, 5, 29),(4, 5, 49),(5, 5, 33),(6, 5, 25),(7, 5, 26),(8, 5, 20),(9, 5, 29),(10, 5, 22),
(11, 5, 32),(12, 5, 32),(13, 5, 18),(14, 5, 29),(15, 5, 23),(16, 5, 22),(17, 5, 20),(18, 5, 22),(19, 5, 21),(20, 5, 20),
(21, 5, 23),(22, 5, 30),(23, 5, 25),(24, 5, 22),(25, 5, 19),(26, 5, 19),(27, 5, 26),(28, 5, 68),(29, 5, 29),(30, 5, 43),
(31, 5, 39),(32, 5, 25),(33, 5, 33),(34, 5, 20);

-- Josué (libro_id=6)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 6, 18),(2, 6, 24),(3, 6, 17),(4, 6, 24),(5, 6, 15),(6, 6, 27),(7, 6, 26),(8, 6, 35),(9, 6, 27),(10, 6, 43),
(11, 6, 23),(12, 6, 24),(13, 6, 33),(14, 6, 15),(15, 6, 63),(16, 6, 10),(17, 6, 18),(18, 6, 28),(19, 6, 51),(20, 6, 9),
(21, 6, 45),(22, 6, 34),(23, 6, 16),(24, 6, 33);

-- Jueces (libro_id=7)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 7, 36),(2, 7, 23),(3, 7, 31),(4, 7, 24),(5, 7, 31),(6, 7, 40),(7, 7, 25),(8, 7, 35),(9, 7, 57),(10, 7, 18),
(11, 7, 40),(12, 7, 15),(13, 7, 25),(14, 7, 20),(15, 7, 20),(16, 7, 31),(17, 7, 13),(18, 7, 15),(19, 7, 39),(20, 7, 23),
(21, 7, 29);

-- Rut (libro_id=8)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 8, 22),(2, 8, 23),(3, 8, 18),(4, 8, 22);

-- 1 Samuel (libro_id=9)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 9, 28),(2, 9, 36),(3, 9, 21),(4, 9, 22),(5, 9, 12),(6, 9, 21),(7, 9, 17),(8, 9, 22),(9, 9, 27),(10, 9, 27),
(11, 9, 15),(12, 9, 25),(13, 9, 23),(14, 9, 52),(15, 9, 35),(16, 9, 23),(17, 9, 58),(18, 9, 30),(19, 9, 24),(20, 9, 42),
(21, 9, 15),(22, 9, 23),(23, 9, 29),(24, 9, 22),(25, 9, 44),(26, 9, 25),(27, 9, 12),(28, 9, 25),(29, 9, 11),(30, 9, 31),
(31, 9, 13);

-- 2 Samuel (libro_id=10)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 10, 27),(2, 10, 32),(3, 10, 39),(4, 10, 12),(5, 10, 25),(6, 10, 23),(7, 10, 29),(8, 10, 18),(9, 10, 13),(10, 10, 19),
(11, 10, 27),(12, 10, 31),(13, 10, 39),(14, 10, 33),(15, 10, 37),(16, 10, 23),(17, 10, 29),(18, 10, 33),(19, 10, 43),(20, 10, 26),
(21, 10, 22),(22, 10, 51),(23, 10, 39),(24, 10, 25);

-- 1 Reyes (libro_id=11)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 11, 53),(2, 11, 46),(3, 11, 28),(4, 11, 34),(5, 11, 18),(6, 11, 38),(7, 11, 51),(8, 11, 66),(9, 11, 28),(10, 11, 29),
(11, 11, 43),(12, 11, 33),(13, 11, 34),(14, 11, 31),(15, 11, 34),(16, 11, 34),(17, 11, 24),(18, 11, 46),(19, 11, 21),(20, 11, 43),
(21, 11, 29),(22, 11, 53);

-- 2 Reyes (libro_id=12)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 12, 18),(2, 12, 25),(3, 12, 27),(4, 12, 44),(5, 12, 27),(6, 12, 33),(7, 12, 20),(8, 12, 29),(9, 12, 37),(10, 12, 36),
(11, 12, 21),(12, 12, 21),(13, 12, 25),(14, 12, 29),(15, 12, 38),(16, 12, 20),(17, 12, 41),(18, 12, 37),(19, 12, 37),(20, 12, 21),
(21, 12, 26),(22, 12, 20),(23, 12, 37),(24, 12, 20),(25, 12, 30);
-- 1 Crónicas (libro_id=13)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 13, 17),(2, 13, 18),(3, 13, 17),(4, 13, 22),(5, 13, 14),(6, 13, 42),(7, 13, 22),(8, 13, 18),(9, 13, 31),(10, 13, 19),
(11, 13, 23),(12, 13, 16),(13, 13, 22),(14, 13, 15),(15, 13, 19),(16, 13, 14),(17, 13, 19),(18, 13, 34),(19, 13, 11),(20, 13, 37),
(21, 13, 20),(22, 13, 12),(23, 13, 21),(24, 13, 27),(25, 13, 28),(26, 13, 23),(27, 13, 9),(28, 13, 27),(29, 13, 36);

-- 2 Crónicas (libro_id=14)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 14, 18),(2, 14, 17),(3, 14, 17),(4, 14, 22),(5, 14, 14),(6, 14, 42),(7, 14, 22),(8, 14, 18),(9, 14, 31),(10, 14, 19),
(11, 14, 23),(12, 14, 16),(13, 14, 22),(14, 14, 15),(15, 14, 19),(16, 14, 14),(17, 14, 19),(18, 14, 34),(19, 14, 11),(20, 14, 37),
(21, 14, 20),(22, 14, 12),(23, 14, 21),(24, 14, 27),(25, 14, 28),(26, 14, 23),(27, 14, 9),(28, 14, 27),(29, 14, 36),(30, 14, 27),
(31, 14, 21),(32, 14, 33),(33, 14, 25),(34, 14, 33),(35, 14, 27),(36, 14, 23);

-- Esdras (libro_id=15)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 15, 11),(2, 15, 70),(3, 15, 13),(4, 15, 24),(5, 15, 17),(6, 15, 22),(7, 15, 28),(8, 15, 36),(9, 15, 15),(10, 15, 44);

-- Nehemías (libro_id=16)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 16, 11),(2, 16, 20),(3, 16, 32),(4, 16, 23),(5, 16, 19),(6, 16, 19),(7, 16, 73),(8, 16, 18),(9, 16, 38),(10, 16, 39),
(11, 16, 36),(12, 16, 47),(13, 16, 31);

-- Ester (libro_id=17)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 17, 22),(2, 17, 23),(3, 17, 15),(4, 17, 17),(5, 17, 14),(6, 17, 14),(7, 17, 10),(8, 17, 17),(9, 17, 32),(10, 17, 3);

-- Job (libro_id=18)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 18, 22),(2, 18, 13),(3, 18, 26),(4, 18, 21),(5, 18, 27),(6, 18, 30),(7, 18, 21),(8, 18, 22),(9, 18, 35),(10, 18, 22),
(11, 18, 20),(12, 18, 25),(13, 18, 28),(14, 18, 22),(15, 18, 35),(16, 18, 22),(17, 18, 16),(18, 18, 21),(19, 18, 29),(20, 18, 29),
(21, 18, 34),(22, 18, 30),(23, 18, 17),(24, 18, 25),(25, 18, 6),(26, 18, 14),(27, 18, 23),(28, 18, 28),(29, 18, 25),(30, 18, 31),
(31, 18, 40),(32, 18, 22),(33, 18, 33),(34, 18, 37),(35, 18, 16),(36, 18, 33),(37, 18, 24),(38, 18, 41),(39, 18, 30),(40, 18, 24),
(41, 18, 34),(42, 18, 17);

-- Salmos (libro_id=19)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 19, 6),(2, 19, 12),(3, 19, 8),(4, 19, 8),(5, 19, 12),(6, 19, 10),(7, 19, 17),(8, 19, 9),(9, 19, 20),(10, 19, 18),
(11, 19, 7),(12, 19, 8),(13, 19, 6),(14, 19, 7),(15, 19, 5),(16, 19, 11),(17, 19, 15),(18, 19, 50),(19, 19, 14),(20, 19, 9),
(21, 19, 13),(22, 19, 31),(23, 19, 6),(24, 19, 10),(25, 19, 22),(26, 19, 12),(27, 19, 14),(28, 19, 9),(29, 19, 11),(30, 19, 12),
(31, 19, 24),(32, 19, 11),(33, 19, 22),(34, 19, 22),(35, 19, 28),(36, 19, 12),(37, 19, 40),(38, 19, 22),(39, 19, 13),(40, 19, 17),
(41, 19, 13),(42, 19, 11),(43, 19, 5),(44, 19, 26),(45, 19, 17),(46, 19, 11),(47, 19, 9),(48, 19, 14),(49, 19, 20),(50, 19, 23),
(51, 19, 19),(52, 19, 9),(53, 19, 6),(54, 19, 7),(55, 19, 23),(56, 19, 13),(57, 19, 11),(58, 19, 11),(59, 19, 17),(60, 19, 12),
(61, 19, 8),(62, 19, 12),(63, 19, 11),(64, 19, 10),(65, 19, 13),(66, 19, 20),(67, 19, 7),(68, 19, 35),(69, 19, 36),(70, 19, 5),
(71, 19, 24),(72, 19, 20),(73, 19, 28),(74, 19, 23),(75, 19, 10),(76, 19, 12),(77, 19, 20),(78, 19, 72),(79, 19, 13),(80, 19, 19),
(81, 19, 16),(82, 19, 8),(83, 19, 18),(84, 19, 7),(85, 19, 18),(86, 19, 52),(87, 19, 17),(88, 19, 16),(89, 19, 15),(90, 19, 5),
(91, 19, 11),(92, 19, 13),(93, 19, 12),(94, 19, 9),(95, 19, 9),(96, 19, 5),(97, 19, 8),(98, 19, 28),(99, 19, 22),(100, 19, 35),
(101, 19, 45),(102, 19, 48),(103, 19, 43),(104, 19, 13),(105, 19, 31),(106, 19, 7),(107, 19, 10),(108, 19, 10),(109, 19, 9),(110, 19, 8),
(111, 19, 18),(112, 19, 19),(113, 19, 2),(114, 19, 29),(115, 19, 176),(116, 19, 7),(117, 19, 8),(118, 19, 9),(119, 19, 4),(120, 19, 8),
(121, 19, 5),(122, 19, 6),(123, 19, 5),(124, 19, 6),(125, 19, 8),(126, 19, 8),(127, 19, 3),(128, 19, 18),(129, 19, 3),(130, 19, 3),
(131, 19, 21),(132, 19, 26),(133, 19, 9),(134, 19, 8),(135, 19, 24),(136, 19, 13),(137, 19, 10),(138, 19, 7),(139, 19, 12),(140, 19, 15),
(141, 19, 21),(142, 19, 10),(143, 19, 20),(144, 19, 14),(145, 19, 9),(146, 19, 6),(147, 19, 29),(148, 19, 43),(149, 19, 38),(150, 19, 11);

-- Proverbios (libro_id=20)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 20, 33),(2, 20, 22),(3, 20, 35),(4, 20, 27),(5, 20, 23),(6, 20, 35),(7, 20, 27),(8, 20, 36),(9, 20, 18),(10, 20, 32),
(11, 20, 31),(12, 20, 28),(13, 20, 25),(14, 20, 35),(15, 20, 33),(16, 20, 33),(17, 20, 28),(18, 20, 24),(19, 20, 29),(20, 20, 30),
(21, 20, 31),(22, 20, 29),(23, 20, 35),(24, 20, 27),(25, 20, 28),(26, 20, 28),(27, 20, 27),(28, 20, 28),(29, 20, 27),(30, 20, 33),
(31, 20, 31);

-- Eclesiastés (libro_id=21)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 21, 18),(2, 21, 26),(3, 21, 22),(4, 21, 16),(5, 21, 20),(6, 21, 12),(7, 21, 29),(8, 21, 17),(9, 21, 18),(10, 21, 20),
(11, 21, 10),(12, 21, 14);

-- Cantares (libro_id=22)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 22, 17),(2, 22, 17),(3, 22, 11),(4, 22, 16),(5, 22, 16),(6, 22, 12),(7, 22, 14),(8, 22, 14);

-- Isaías (libro_id=23)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 23, 31),(2, 23, 22),(3, 23, 25),(4, 23, 6),(5, 23, 30),(6, 23, 13),(7, 23, 25),(8, 23, 22),(9, 23, 21),(10, 23, 34),
(11, 23, 16),(12, 23, 6),(13, 23, 22),(14, 23, 32),(15, 23, 9),(16, 23, 14),(17, 23, 14),(18, 23, 7),(19, 23, 25),(20, 23, 6),
(21, 23, 17),(22, 23, 25),(23, 23, 18),(24, 23, 23),(25, 23, 12),(26, 23, 21),(27, 23, 13),(28, 23, 29),(29, 23, 24),(30, 23, 33),
(31, 23, 9),(32, 23, 20),(33, 23, 24),(34, 23, 17),(35, 23, 10),(36, 23, 22),(37, 23, 38),(38, 23, 22),(39, 23, 8),(40, 23, 31),
(41, 23, 29),(42, 23, 25),(43, 23, 28),(44, 23, 28),(45, 23, 25),(46, 23, 13),(47, 23, 15),(48, 23, 22),(49, 23, 26),(50, 23, 11),
(51, 23, 23),(52, 23, 15),(53, 23, 12),(54, 23, 17),(55, 23, 13),(56, 23, 12),(57, 23, 21),(58, 23, 14),(59, 23, 21),(60, 23, 22),
(61, 23, 11),(62, 23, 12),(63, 23, 19),(64, 23, 12),(65, 23, 25),(66, 23, 24);

-- Jeremías (libro_id=24)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 24, 19),(2, 24, 37),(3, 24, 25),(4, 24, 31),(5, 24, 31),(6, 24, 30),(7, 24, 34),(8, 24, 22),(9, 24, 26),(10, 24, 25),
(11, 24, 23),(12, 24, 17),(13, 24, 27),(14, 24, 22),(15, 24, 21),(16, 24, 21),(17, 24, 27),(18, 24, 23),(19, 24, 15),(20, 24, 18),
(21, 24, 14),(22, 24, 30),(23, 24, 40),(24, 24, 10),(25, 24, 38),(26, 24, 24),(27, 24, 22),(28, 24, 17),(29, 24, 32),(30, 24, 24),
(31, 24, 40),(32, 24, 44),(33, 24, 26),(34, 24, 22),(35, 24, 19),(36, 24, 32),(37, 24, 21),(38, 24, 28),(39, 24, 18),(40, 24, 16),
(41, 24, 16),(42, 24, 22),(43, 24, 30),(44, 24, 21),(45, 24, 25),(46, 24, 29),(47, 24, 23),(48, 24, 22),(49, 24, 21),(50, 24, 27),
(51, 24, 7),(52, 24, 34);

-- Lamentaciones (libro_id=25)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 25, 22),(2, 25, 22),(3, 25, 66),(4, 25, 22),(5, 25, 22);
-- Ezequiel (libro_id=26)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 26, 28),(2, 26, 10),(3, 26, 27),(4, 26, 17),(5, 26, 17),(6, 26, 14),(7, 26, 27),(8, 26, 18),(9, 26, 11),(10, 26, 22),
(11, 26, 25),(12, 26, 28),(13, 26, 23),(14, 26, 23),(15, 26, 8),(16, 26, 63),(17, 26, 24),(18, 26, 32),(19, 26, 14),(20, 26, 49),
(21, 26, 32),(22, 26, 31),(23, 26, 49),(24, 26, 27),(25, 26, 17),(26, 26, 21),(27, 26, 36),(28, 26, 26),(29, 26, 21),(30, 26, 26),
(31, 26, 18),(32, 26, 32),(33, 26, 33),(34, 26, 31),(35, 26, 15),(36, 26, 38),(37, 26, 28),(38, 26, 23),(39, 26, 29),(40, 26, 49),
(41, 26, 26),(42, 26, 20),(43, 26, 27),(44, 26, 31),(45, 26, 25),(46, 26, 24),(47, 26, 23),(48, 26, 35);

-- Daniel (libro_id=27)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 27, 21),(2, 27, 49),(3, 27, 30),(4, 27, 37),(5, 27, 31),(6, 27, 28),(7, 27, 28),(8, 27, 27),(9, 27, 27),(10, 27, 21),
(11, 27, 45),(12, 27, 13);

-- Oseas (libro_id=28)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 28, 11),(2, 28, 23),(3, 28, 5),(4, 28, 19),(5, 28, 15),(6, 28, 11),(7, 28, 16),(8, 28, 14),(9, 28, 17),(10, 28, 15),
(11, 28, 12),(12, 28, 14),(13, 28, 16),(14, 28, 9);

-- Joel (libro_id=29)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 29, 20),(2, 29, 32),(3, 29, 21);

-- Amós (libro_id=30)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 30, 15),(2, 30, 16),(3, 30, 15),(4, 30, 13),(5, 30, 27),(6, 30, 14),(7, 30, 17),(8, 30, 14),(9, 30, 15);

-- Abdías (libro_id=31)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 31, 21);

-- Jonás (libro_id=32)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 32, 17),(2, 32, 10),(3, 32, 10),(4, 32, 11);

-- Miqueas (libro_id=33)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 33, 16),(2, 33, 13),(3, 33, 12),(4, 33, 13),(5, 33, 15),(6, 33, 16),(7, 33, 20);

-- Nahúm (libro_id=34)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 34, 15),(2, 34, 13),(3, 34, 19);

-- Habacuc (libro_id=35)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 35, 17),(2, 35, 20),(3, 35, 19);

-- Sofonías (libro_id=36)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 36, 18),(2, 36, 15),(3, 36, 20);

-- Hageo (libro_id=37)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 37, 15),(2, 37, 23);

-- Zacarías (libro_id=38)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 38, 21),(2, 38, 13),(3, 38, 10),(4, 38, 14),(5, 38, 11),(6, 38, 15),(7, 38, 14),(8, 38, 23),(9, 38, 17),(10, 38, 12),
(11, 38, 17),(12, 38, 14),(13, 38, 9),(14, 38, 21);

-- Malaquías (libro_id=39)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 39, 14),(2, 39, 17),(3, 39, 18),(4, 39, 6);

-- Mateo (libro_id=40)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 40, 25),(2, 40, 23),(3, 40, 17),(4, 40, 25),(5, 40, 48),(6, 40, 34),(7, 40, 29),(8, 40, 34),(9, 40, 38),(10, 40, 42),
(11, 40, 30),(12, 40, 50),(13, 40, 58),(14, 40, 36),(15, 40, 39),(16, 40, 28),(17, 40, 27),(18, 40, 35),(19, 40, 30),(20, 40, 34),
(21, 40, 46),(22, 40, 46),(23, 40, 39),(24, 40, 51),(25, 40, 46),(26, 40, 75),(27, 40, 66),(28, 40, 20);

-- Marcos (libro_id=41)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 41, 45),(2, 41, 28),(3, 41, 35),(4, 41, 41),(5, 41, 43),(6, 41, 56),(7, 41, 37),(8, 41, 38),(9, 41, 50),(10, 41, 52),
(11, 41, 33),(12, 41, 44),(13, 41, 37),(14, 41, 72),(15, 41, 47),(16, 41, 20);

-- Lucas (libro_id=42)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 42, 80),(2, 42, 52),(3, 42, 38),(4, 42, 44),(5, 42, 39),(6, 42, 49),(7, 42, 50),(8, 42, 56),(9, 42, 62),(10, 42, 42),
(11, 42, 54),(12, 42, 59),(13, 42, 35),(14, 42, 35),(15, 42, 32),(16, 42, 31),(17, 42, 37),(18, 42, 43),(19, 42, 48),(20, 42, 47),
(21, 42, 38),(22, 42, 71),(23, 42, 56),(24, 42, 53);

-- Juan (libro_id=43)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 43, 51),(2, 43, 25),(3, 43, 36),(4, 43, 54),(5, 43, 47),(6, 43, 71),(7, 43, 53),(8, 43, 59),(9, 43, 41),(10, 43, 42),
(11, 43, 57),(12, 43, 50),(13, 43, 38),(14, 43, 31),(15, 43, 27),(16, 43, 33),(17, 43, 26),(18, 43, 40),(19, 43, 42),(20, 43, 31),
(21, 43, 25);

-- Hechos (libro_id=44)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 44, 26),(2, 44, 47),(3, 44, 26),(4, 44, 37),(5, 44, 42),(6, 44, 15),(7, 44, 60),(8, 44, 40),(9, 44, 43),(10, 44, 48),
(11, 44, 30),(12, 44, 25),(13, 44, 52),(14, 44, 28),(15, 44, 41),(16, 44, 40),(17, 44, 34),(18, 44, 28),(19, 44, 41),(20, 44, 38),
(21, 44, 40),(22, 44, 30),(23, 44, 35),(24, 44, 27),(25, 44, 27),(26, 44, 32),(27, 44, 44),(28, 44, 31);

-- Romanos (libro_id=45)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 45, 32),(2, 45, 29),(3, 45, 31),(4, 45, 25),(5, 45, 21),(6, 45, 23),(7, 45, 25),(8, 45, 39),(9, 45, 33),(10, 45, 21),
(11, 45, 36),(12, 45, 21),(13, 45, 14),(14, 45, 23),(15, 45, 33),(16, 45, 27);

-- 1 Corintios (libro_id=46)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 46, 31),(2, 46, 16),(3, 46, 23),(4, 46, 21),(5, 46, 13),(6, 46, 20),(7, 46, 40),(8, 46, 13),(9, 46, 27),(10, 46, 33),
(11, 46, 34),(12, 46, 31),(13, 46, 13),(14, 46, 40),(15, 46, 58),(16, 46, 24);

-- 2 Corintios (libro_id=47)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 47, 24),(2, 47, 17),(3, 47, 18),(4, 47, 18),(5, 47, 21),(6, 47, 18),(7, 47, 16),(8, 47, 24),(9, 47, 15),(10, 47, 18),
(11, 47, 33),(12, 47, 21),(13, 47, 14);

-- Gálatas (libro_id=48)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 48, 24),(2, 48, 21),(3, 48, 29),(4, 48, 31),(5, 48, 26),(6, 48, 18);

-- Efesios (libro_id=49)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 49, 23),(2, 49, 22),(3, 49, 21),(4, 49, 32),(5, 49, 33),(6, 49, 24);

-- Filipenses (libro_id=50)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 50, 30),(2, 50, 30),(3, 50, 21),(4, 50, 23);

-- Colosenses (libro_id=51)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 51, 29),(2, 51, 23),(3, 51, 25),(4, 51, 18);

-- 1 Tesalonicenses (libro_id=52)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 52, 10),(2, 52, 20),(3, 52, 13),(4, 52, 18),(5, 52, 28);

-- 2 Tesalonicenses (libro_id=53)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 53, 12),(2, 53, 17),(3, 53, 18);

-- 1 Timoteo (libro_id=54)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 54, 20),(2, 54, 15),(3, 54, 16),(4, 54, 16),(5, 54, 25),(6, 54, 21);

-- 2 Timoteo (libro_id=55)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 55, 18),(2, 55, 26),(3, 55, 17),(4, 55, 22);

-- Tito (libro_id=56)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 56, 16),(2, 56, 15),(3, 56, 15);

-- Filemón (libro_id=57)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 57, 25);

-- Hebreos (libro_id=58)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 58, 14),(2, 58, 18),(3, 58, 19),(4, 58, 16),(5, 58, 14),(6, 58, 20),(7, 58, 28),(8, 58, 13),(9, 58, 28),(10, 58, 39),
(11, 58, 40),(12, 58, 29),(13, 58, 25);

-- Santiago (libro_id=59)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 59, 27),(2, 59, 26),(3, 59, 18),(4, 59, 17),(5, 59, 20);

-- 1 Pedro (libro_id=60)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 60, 25),(2, 60, 25),(3, 60, 22),(4, 60, 19),(5, 60, 14);
-- 2 Pedro (libro_id=61)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 61, 21),(2, 61, 22),(3, 61, 18);

-- 1 Juan (libro_id=62)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 62, 10),(2, 62, 29),(3, 62, 24),(4, 62, 21),(5, 62, 21);

-- 2 Juan (libro_id=63)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 63, 13);

-- 3 Juan (libro_id=64)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 64, 14);

-- Judas (libro_id=65)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 65, 25);

-- Apocalipsis (libro_id=66)
INSERT INTO capitulos (numero, libro_id, versiculos) VALUES
(1, 66, 20),(2, 66, 29),(3, 66, 22),(4, 66, 11),(5, 66, 14),(6, 66, 17),(7, 66, 17),(8, 66, 13),(9, 66, 21),(10, 66, 11),
(11, 66, 19),(12, 66, 17),(13, 66, 18),(14, 66, 20),(15, 66, 8),(16, 66, 21),(17, 66, 18),(18, 66, 24),(19, 66, 21),(20, 66, 15),
(21, 66, 27),(22, 66, 21);
-- Fin de la inserción de capítulos