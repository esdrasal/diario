# Configuración de Base de Datos - Diario Bíblico

## 🚀 Instrucciones Completas de Instalación y Ejecución

### 1. Crear la Base de Datos

Ejecutar el archivo SQL para crear la base de datos y las tablas:

```bash
mysql -u root -p < crear_base_datos.sql
```

### 2. Configurar la Conexión

Editar el archivo `includes/database.php` con los datos de tu base de datos:

```php
$host = 'localhost';
$dbname = 'diario_biblico';
$username = 'tu_usuario';
$password = 'tu_password';
```

### 3. Estructura de la Base de Datos

#### Tabla `usuarios`
- `id` - ID único del usuario
- `nombre` - Nombre del usuario
- `email` - Email (único)
- `password` - Contraseña encriptada
- `fecha_registro` - Fecha de registro
- `activo` - Estado del usuario

#### Tabla `libros`
- `id` - ID del libro bíblico
- `nombre` - Nombre del libro
- `testamento` - Antiguo o Nuevo
- `capitulos` - Número de capítulos
- `versiculos` - Total de versículos
- `abreviatura` - Abreviatura del libro

#### Tabla `capitulos`
- `id` - ID único
- `numero` - Número del capítulo
- `libro_id` - ID del libro (foreign key)
- `versiculos` - Número de versículos en el capítulo

#### Tabla `lecturas`
- `id` - ID único de la lectura
- `usuario_id` - ID del usuario (foreign key)
- `fecha` - Fecha de la lectura
- `libro_id` - ID del libro (foreign key)
- `capitulo_desde` - Capítulo inicial
- `versiculo_desde` - Versículo inicial
- `capitulo_hasta` - Capítulo final
- `versiculo_hasta` - Versículo final
- `notas` - Notas de la lectura
- `favoritos` - Versículos favoritos (JSON)

### 4. Usuario Demo

Se crea automáticamente un usuario demo:
- Email: demo@ejemplo.com
- Password: password

### 5. Funciones de Base de Datos

Las principales funciones están en `includes/database.php`:

- `getLibros($pdo)` - Obtener todos los libros
- `getVersiculosPorCapitulo($pdo)` - Obtener estructura de versículos
- `crearUsuario($pdo, $nombre, $email, $password)` - Registrar usuario
- `verificarUsuario($pdo, $email, $password)` - Autenticar usuario
- `guardarLecturaDB($pdo, ...)` - Guardar lectura
- `obtenerLecturasUsuario($pdo, $usuario_id)` - Obtener lecturas del usuario

### 6. Migración desde JSON

Si tenías datos en el archivo JSON anterior (`data/lecturas.json`), necesitarás migrarlos manualmente a la base de datos usando las funciones proporcionadas.

### 7. Requisitos

- PHP 7.4 o superior
- MySQL/MariaDB 5.7 o superior
- Extensión PDO de PHP
- Extensión mysqli de PHP (opcional)

### 8. Ejecutar el Proyecto

#### Opción 1: Con XAMPP/WAMP/LAMP
1. Colocar el proyecto en la carpeta `htdocs` (XAMPP) o `www` (WAMP)
2. Iniciar Apache y MySQL desde el panel de control
3. Ir a: `http://localhost/Diario/login.php`

#### Opción 2: Con PHP Built-in Server
```bash
# En la carpeta del proyecto
php -S localhost:8000
```
Luego ir a: `http://localhost:8000/login.php`

#### Opción 3: Usando el script incluido (Windows)
```bash
# Doble click en:
crear-app.bat
```

#### Opción 4: Usando el script incluido (Linux/Mac)
```bash
chmod +x crear-app.sh
./crear-app.sh
```

### 9. Primer Uso

1. **Acceder al sistema:**
   - Ve a `login.php`
   - Usuario demo: `demo@ejemplo.com`
   - Contraseña: `password`

2. **O registrar nuevo usuario:**
   - Ve a `registro.php`
   - Completa el formulario
   - Inicia sesión con tu cuenta

### 10. Seguridad

- Las contraseñas se encriptan con `password_hash()`
- Se usan prepared statements para evitar inyección SQL
- Validación de sesiones en todas las páginas protegidas