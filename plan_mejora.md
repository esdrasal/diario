Plan de Optimización y Migración del Proyecto Diario Bíblico
1. Objetivo principal
Llevar un registro claro y visual del progreso en la lectura completa de la Biblia.

Saber qué pasajes se han leído y cuáles no.

Poder utilizar versiones libres de derechos como la NTV, o la 1960 sin infringir licencias.

2. Usuarios y acceso
Uso personal con opción de compartir logros.

Comparar avances con amigos, totales y por libro.

Acceso optimizado para dispositivos móviles y PC.

3. Funcionalidades actuales
Creación y gestión básica de usuarios.

Marcar versículos como leídos/no leídos.

Conteo total de versículos leídos.

Visualización de totales de lectura.

4. Mejoras deseadas
Interfaz mejorada para móvil y PC.

Registro y seguimiento de rachas.

Exportación de datos (opcional, futuro).

Páginas para:

Mostrar todos los registros de lectura ordenados por fecha.

Visualizar gráficos de días con mayor lectura.

Mostrar qué libros se han leído más o menos.

Incorporar mapas de calor de lectura.

5. Tecnología y arquitectura
Migración a CakePHP para mejor estructura, mantenimiento y escalabilidad.

Integración de Chart.js para gráficos interactivos con tooltips.

Hosting gratuito en InfinityFree, con limitaciones que se deben considerar.

6. Seguridad
Implementar manejo seguro de contraseñas (hashing).

Validación de datos en formularios.

Protección contra CSRF.

Sesiones seguras.

Prevención de inyección SQL.

7. Nuevas funcionalidades avanzadas
Registro automático de lectura a medida que la persona avanza (scroll, tiempo en pantalla).

Capturar lo que realmente se está leyendo sin necesidad de clic.

Ideas para implementar:

Detectar scroll y calcular tiempo de exposición a cada versículo.

Guardar progreso basado en posición visible.

Posibilidad de guardar “checkpoint” cada cierto intervalo.

8. Plan de pasos concretos
Paso 1: Migración a CakePHP
Analizar el código actual y separar lógica, vistas y controladores.

Crear la estructura MVC en CakePHP.

Migrar modelos para manejar usuarios, libros, capítulos y lectura.

Implementar rutas amigables.

Paso 2: Seguridad
Configurar autenticación segura con hashing (bcrypt).

Añadir validaciones en formularios.

Habilitar protección CSRF integrada en CakePHP.

Mejorar manejo de sesiones.

Paso 3: Interfaz
Diseñar y adaptar la interfaz para dispositivos móviles y PC.

Usar CSS responsive y frameworks si se desea (Bootstrap, Tailwind).

Optimizar experiencia de usuario para lectura y marcación de versículos.

Paso 4: Gráficos con Chart.js
Integrar Chart.js en vistas.

Crear gráficos de barras y mapas de calor.

Hacer gráficos interactivos con tooltips que muestren detalles al pasar el mouse.

Añadir filtros por fecha y libro.

Paso 5: Registro automático de lectura
Investigar y prototipar detección de scroll y tiempo en pantalla.

Implementar guardado automático del progreso basado en posición visible.

Evaluar performance y usabilidad.