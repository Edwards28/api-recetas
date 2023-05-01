DROP DATABASE IF EXISTS `api_recetas`;

CREATE DATABASE IF NOT EXISTS `api_recetas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci;

USE `api_recetas`;

CREATE TABLE usuarios (
  id INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  correo_electronico VARCHAR(255) NOT NULL,
  contrasena VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
);

INSERT INTO usuarios (nombre, correo_electronico, contrasena)
VALUES
  ('Davis Merlos', 'davis@gmail.com', '$2y$10$c5csIF4fmktltFGu.19f3OsFkiwg9f/YjiPT25RHwQh08vKgkMSP2');

CREATE TABLE categorias (
  id INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
);

INSERT INTO categorias (nombre)
VALUES
  ('Postres'),
  ('Carnes'),
  ('Pescados'),
  ('Ensaladas'),
  ('Sopas');

CREATE TABLE recetas (
  id INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT,
  categoria_id INT NOT NULL,
  tiempo_preparacion INT NOT NULL,
  porciones INT NOT NULL,
  imagen VARCHAR(255),
  usuario_id INT NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE ingredientes (
  id INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
);

INSERT INTO ingredientes (nombre) VALUES ('Harina');
INSERT INTO ingredientes (nombre) VALUES ('Levadura');
INSERT INTO ingredientes (nombre) VALUES ('Azúcar');
INSERT INTO ingredientes (nombre) VALUES ('Sal');
INSERT INTO ingredientes (nombre) VALUES ('Aceite');
INSERT INTO ingredientes (nombre) VALUES ('Mantequilla');
INSERT INTO ingredientes (nombre) VALUES ('Huevos');
INSERT INTO ingredientes (nombre) VALUES ('Leche');
INSERT INTO ingredientes (nombre) VALUES ('Crema de leche');
INSERT INTO ingredientes (nombre) VALUES ('Queso');
INSERT INTO ingredientes (nombre) VALUES ('Jamón');
INSERT INTO ingredientes (nombre) VALUES ('Pollo');
INSERT INTO ingredientes (nombre) VALUES ('Pescado');
INSERT INTO ingredientes (nombre) VALUES ('Mariscos');
INSERT INTO ingredientes (nombre) VALUES ('Arroz');
INSERT INTO ingredientes (nombre) VALUES ('Fideos');
INSERT INTO ingredientes (nombre) VALUES ('Pan rallado');
INSERT INTO ingredientes (nombre) VALUES ('Cebolla');
INSERT INTO ingredientes (nombre) VALUES ('Ajo');
INSERT INTO ingredientes (nombre) VALUES ('Tomate');
INSERT INTO ingredientes (nombre) VALUES ('Pimiento');
INSERT INTO ingredientes (nombre) VALUES ('Zanahoria');
INSERT INTO ingredientes (nombre) VALUES ('Apio');
INSERT INTO ingredientes (nombre) VALUES ('Perejil');
INSERT INTO ingredientes (nombre) VALUES ('Cilantro');
INSERT INTO ingredientes (nombre) VALUES ('Jengibre');

CREATE TABLE recetas_ingredientes (
  id INT NOT NULL AUTO_INCREMENT,
  receta_id INT NOT NULL,
  ingrediente_id INT NOT NULL,
  cantidad VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (receta_id) REFERENCES recetas(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE favoritos (
  id INT NOT NULL AUTO_INCREMENT,
  usuario_id INT NOT NULL,
  receta_id INT NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (receta_id) REFERENCES recetas(id) ON DELETE CASCADE ON UPDATE CASCADE
);

