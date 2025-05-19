
use WeConnect_bd;

CREATe TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(100) NOT NULL UNIQUE,
    apellido VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    telefono VARCHAR(15),
    area_trabajo VARCHAR(100),
    premium BOOLEAN DEFAULT FALSE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    foto_perfil VARCHAR(255)
);

CREATE TABLE admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(100) NOT NULL,
    FOREIGN KEY (usuario) REFERENCES usuario(usuario)
);

CREATE TABLE categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT
);
INSErt INTO categoria (nombre, descripcion) VALUES
('Tecnología', 'Productos y servicios relacionados con tecnología'),
('Hogar', 'Productos y servicios para el hogar'),
('Salud', 'Productos y servicios relacionados con la salud'),
('Belleza', 'Productos y servicios de belleza y cuidado personal'),
('Deportes', 'Productos y servicios deportivos'),
('Moda', 'Ropa, accesorios y moda en general'),
('Alimentos', 'Productos alimenticios y bebidas'),
('Automotriz', 'Productos y servicios para automóviles'),
('Otros', 'Otros productos y servicios no categorizados');


CREATE TABLE producto (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    imagen VARCHAR(255) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario VARCHAR(100) NOT NULL,
    FOREIGN KEY (categoria) REFERENCES categoria(nombre),
    FOREIGN KEY (usuario) REFERENCES usuario(usuario)
);

CREATE TABLE servicio (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario VARCHAR(100) NOT NULL,
    FOREIGN KEY (categoria) REFERENCES categoria(nombre),
    FOREIGN KEY (usuario) REFERENCES usuario(usuario)
);
USE WeConnect_bd;

CREATE TABLE comentarios_servicio (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    id_servicio INT NOT NULL,
    usuario_alias VARCHAR(100) NOT NULL,
    valoracion INT CHECK (valoracion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_servicio) REFERENCES servicio(id_servicio) ON DELETE CASCADE,
    FOREIGN KEY (usuario_alias) REFERENCES usuario(usuario) ON DELETE CASCADE
);
CREATE TABLE comentarios_producto (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    usuario_alias VARCHAR(100) NOT NULL,
    valoracion INT CHECK (valoracion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE,
    FOREIGN KEY (usuario_alias) REFERENCES usuario(usuario) ON DELETE CASCADE
);
USE WeConnect_bd;


CREATE TABLE necesidades_ofertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('necesidad', 'oferta') NOT NULL, 
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    usuario_alias VARCHAR(100) NOT NULL, 
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
    categoria_b2b VARCHAR(100),
    estado ENUM('activo', 'completado', 'cerrado') DEFAULT 'activo', 

    FOREIGN KEY (usuario_alias) REFERENCES usuario(usuario) ON DELETE CASCADE
  
);
