-- Crear base de datos para gestión de empleados (Versión 2 - Mejorada)
CREATE DATABASE IF NOT EXISTS gestion_empleados;
USE gestion_empleados;

-- Crear tabla de usuarios administradores para autenticación
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Crear tabla de roles/cargos predefinidos
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cargo VARCHAR(100) NOT NULL UNIQUE,
    departamento VARCHAR(50) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Modificar tabla de empleados para usar referencias a roles
CREATE TABLE IF NOT EXISTS empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    rol_id INT NOT NULL,
    correo_electronico VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    fecha_ingreso DATE NOT NULL,
    fecha_salida DATE NULL,
    salario DECIMAL(10,2),
    activo BOOLEAN DEFAULT TRUE,
    notas TEXT,
    creado_por INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id),
    FOREIGN KEY (creado_por) REFERENCES usuarios(id)
);

-- Crear tabla de historial de cambios
CREATE TABLE IF NOT EXISTS empleados_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT NOT NULL,
    campo_modificado VARCHAR(50) NOT NULL,
    valor_anterior TEXT,
    valor_nuevo TEXT,
    usuario_id INT NOT NULL,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Insertar usuario administrador por defecto (password: admin123)
INSERT INTO usuarios (username, email, password_hash, nombre_completo) VALUES
('Admin', 'admin@gmail.com', '$2y$12$wh1dsDjf.KeANkAgDECZbOaF1HvH5/jLYmeX7uG7vPrODZ3vUGfuW', 'Administrador del Sistema');

-- Insertar roles predefinidos para empresa de desarrollo web y marketing
INSERT INTO roles (nombre_cargo, departamento, descripcion) VALUES
-- Desarrollo Web
('Desarrollador Frontend', 'Desarrollo', 'Especialista en tecnologías del lado del cliente (HTML, CSS, JavaScript, React, Vue)'),
('Desarrollador Backend', 'Desarrollo', 'Especialista en tecnologías del lado del servidor (PHP, Python, Node.js, bases de datos)'),
('Desarrollador Full Stack', 'Desarrollo', 'Desarrollador con conocimientos tanto frontend como backend'),
('DevOps Engineer', 'Desarrollo', 'Especialista en infraestructura, CI/CD y automatización de despliegues'),
('QA Tester', 'Desarrollo', 'Especialista en pruebas de software y control de calidad'),

-- Diseño y UX
('Diseñador UX/UI', 'Diseño', 'Especialista en experiencia de usuario e interfaces'),
('Diseñador Gráfico', 'Diseño', 'Especialista en diseño visual y materiales gráficos'),

-- Marketing Digital
('Marketing Manager', 'Marketing', 'Responsable de estrategias de marketing digital y campañas'),
('Community Manager', 'Marketing', 'Especialista en redes sociales y comunidades online'),
('SEO Specialist', 'Marketing', 'Especialista en optimización para motores de búsqueda'),

-- Gestión
('Project Manager', 'Gestión', 'Responsable de la gestión y coordinación de proyectos'),
('Scrum Master', 'Gestión', 'Facilitador de metodologías ágiles y equipos de desarrollo');

-- Insertar empleados de ejemplo con referencias a roles
INSERT INTO empleados (nombre_completo, rol_id, correo_electronico, telefono, fecha_ingreso, salario, creado_por) VALUES
('Juan Pérez García', 3, 'juan.perez@empresa.com', '+34 600 123 456', '2023-01-15', 45000.00, 1),
('María González López', 6, 'maria.gonzalez@empresa.com', '+34 600 234 567', '2023-03-20', 38000.00, 1),
('Carlos Rodríguez Martín', 11, 'carlos.rodriguez@empresa.com', '+34 600 345 678', '2022-11-10', 50000.00, 1),
('Ana Fernández Silva', 8, 'ana.fernandez@empresa.com', '+34 600 456 789', '2023-06-05', 42000.00, 1),
('Luis Martínez Torres', 1, 'luis.martinez@empresa.com', '+34 600 567 890', '2023-02-28', 40000.00, 1);

-- Crear índices para mejorar rendimiento
CREATE INDEX idx_empleados_rol ON empleados(rol_id);
CREATE INDEX idx_empleados_activo ON empleados(activo);
CREATE INDEX idx_empleados_fecha_ingreso ON empleados(fecha_ingreso);
CREATE INDEX idx_historial_empleado ON empleados_historial(empleado_id);
CREATE INDEX idx_historial_fecha ON empleados_historial(fecha_cambio);
