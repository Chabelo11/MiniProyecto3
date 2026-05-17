-- ============================================================
--  AGENDA FULLSTACK  |  Mini Proyecto 3
--  Base de datos: MySQL / MariaDB
-- ============================================================

-- ------------------------------------------------------------
-- Tabla: usuarios
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id               INT          NOT NULL AUTO_INCREMENT,
    nombre_de_usuario VARCHAR(50)  NOT NULL UNIQUE,
    password         VARCHAR(255) NOT NULL,
    foto             VARCHAR(255)          DEFAULT NULL,
    token            VARCHAR(255)          DEFAULT NULL,
    token_expiracion DATETIME              DEFAULT NULL,
    fecha_registro   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabla: contactos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS contactos (
    id             INT          NOT NULL AUTO_INCREMENT,
    usuario_id     INT          NOT NULL,
    nombre         VARCHAR(100) NOT NULL,
    apellido       VARCHAR(100)          DEFAULT NULL,
    telefono       VARCHAR(20)  NOT NULL,
    email          VARCHAR(120)          DEFAULT NULL,
    direccion      VARCHAR(255)          DEFAULT NULL,
    notas          TEXT                  DEFAULT NULL,
    foto           VARCHAR(255)          DEFAULT NULL,
    fecha_creacion TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_contactos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
