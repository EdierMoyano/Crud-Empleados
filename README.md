# Sistema de Gestión de Empleados - PHP/MySQL

## Descripción
Aplicación CRUD básica para la gestión de empleados desarrollada con PHP orientado a objetos y MySQL, sin uso de frameworks.

## Credenciales Predeterminadas 
User: Admin
Password : Admin123

## Características
- ✅ Crear, listar, editar y eliminar empleados
- ✅ Validación de campos obligatorios
- ✅ Validación de correo electrónico único
- ✅ Interfaz responsive con Bootstrap
- ✅ Seguridad básica contra inyecciones SQL (PDO)
- ✅ Ordenamiento por fecha de ingreso (descendente)

## Requisitos del Sistema
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)

## Instalación

### 1. Configurar Base de Datos
1. Crear una base de datos MySQL
2. Ejecutar el script SQL ubicado en `scripts/database.sql`
3. Configurar las credenciales de conexión en `config/database.php`

### 2. Configurar Servidor Web
1. Copiar todos los archivos al directorio del servidor web
2. Asegurar que PHP tenga permisos de lectura/escritura
3. Verificar que la extensión PDO MySQL esté habilitada

### 3. Configuración de Base de Datos
Editar el archivo `config/database.php` con sus credenciales:

\`\`\`php
private $host = 'localhost';        // Servidor de base de datos
private $db_name = 'gestion_empleados'; // Nombre de la base de datos
private $username = 'root';         // Usuario de MySQL
private $password = '';             // Contraseña de MySQL
\`\`\`

## Estructura del Proyecto

\`\`\`
/
├── config/
│   └── database.php          # Configuración de conexión a BD
├── models/
│   └── Empleado.php         # Modelo de datos del empleado
├── includes/
│   ├── header.php           # Encabezado común
│   └── footer.php           # Pie de página común
├── scripts/
│   └── database_setup.sql   # Script de creación de BD
├── index.php                # Lista de empleados (página principal)
├── crear.php                # Formulario de creación
├── editar.php               # Formulario de edición
└── README.md                # Este archivo
\`\`\`

## Uso de la Aplicación

### Página Principal (index.php)
- Muestra lista de empleados ordenada por fecha de ingreso
- Botones para editar y eliminar cada empleado
- Enlace para crear nuevo empleado

### Crear Empleado (crear.php)
- Formulario con validación de campos obligatorios
- Validación de formato de email
- Verificación de email único

### Editar Empleado (editar.php)
- Formulario pre-rellenado con datos actuales
- Mismas validaciones que creación
- Verificación de email único excluyendo el registro actual

## Campos del Empleado
- **Nombre Completo**: Texto obligatorio (máx. 100 caracteres)
- **Cargo**: Texto obligatorio (máx. 50 caracteres)
- **Correo Electrónico**: Email válido y único (máx. 100 caracteres)
- **Fecha de Ingreso**: Fecha obligatoria

## Seguridad Implementada
- Uso de PDO con prepared statements
- Sanitización de datos con `htmlspecialchars()`
- Validación de entrada en servidor
- Confirmación antes de eliminar registros

## Tecnologías Utilizadas
- **Backend**: PHP 7.4+ (Orientado a Objetos)
- **Base de Datos**: MySQL con PDO
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **Iconos**: Font Awesome 6

## Posibles Mejoras Futuras
- Paginación para listas grandes
- Búsqueda con  filtros
- Exportación a Excel/PDF
- Sistema de autenticación
- Logs de auditoría

## Soporte
Para reportar problemas o sugerencias, contactar al desarrollador.


## Credenciales Predeterminadas 
User: Admin
Password : Admin123
