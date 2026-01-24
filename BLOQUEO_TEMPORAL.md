# 📚 Book Rush - Sistema de Bloqueo Temporal

## 🔒 Cómo Funciona el Bloqueo Temporal

### Descripción
Se implementó un **sistema de castigo por errores** que bloquea temporalmente al usuario por **5 minutos** si comete **más de 3 errores consecutivos** en un capítulo.

### Flujo de Funcionamiento

1. **Usuario responde una pregunta incorrectamente**
   - El intento fallido se registra en la tabla `intentos_fallidos`
   
2. **Se cuentan los intentos en los últimos 5 minutos**
   - Si hay 1-2 intentos: El usuario puede continuar intentando
   - Si hay 3 o más intentos: **Usuario BLOQUEADO por 5 minutos**

3. **Usuario bloqueado ve un modal**
   - Mensaje: "Has superado el límite de 3 intentos fallidos. Estás bloqueado por 5 minutos."
   - Botón para volver al capítulo
   - Los botones de respuesta se deshabilitan

4. **Respuesta correcta limpia los intentos**
   - Si el usuario acierta una pregunta, todos los intentos fallidos de ese capítulo se eliminan
   - Puede continuar sin restricciones

### Tabla de Base de Datos

```sql
CREATE TABLE intentos_fallidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_libro INT NOT NULL,
    CAPITULO INT NOT NULL,
    fecha_bloqueo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(ID),
    FOREIGN KEY (id_libro) REFERENCES libros(id_libro)
);
```

### Archivos Modificados

- **trivia/trivia.php**: Lógica principal de bloqueo
  - `verificarBloqueo()`: Verifica si usuario está bloqueado
  - Registro de intentos fallidos
  - Limpieza de intentos al acertar
  - Modal de bloqueo en JavaScript

### Parámetros Configurables

Puedes modificar estos valores en `trivia.php`:

```php
// Línea ~20: Cambiar tiempo de bloqueo (en minutos)
AND fecha_bloqueo > DATE_SUB(NOW(), INTERVAL 5 MINUTE)

// Línea ~104: Cambiar número de intentos permitidos
if ($intentos >= 3) {
```

### Prueba del Sistema

1. Abre un capítulo en trivia
2. Selecciona **3 respuestas incorrectas** consecutivamente
3. En la 4ª pregunta verás el modal de bloqueo
4. Espera 5 minutos o intenta con una respuesta correcta para limpiar los intentos

### Notas Importantes

- El bloqueo es **por usuario, por libro y por capítulo**
- Si hay un error de conexión, se ignora (no se registra intento)
- Los intentos se limpian automáticamente después de 5 minutos
- Los intentos también se limpian si el usuario acierta una pregunta

---

**Implementado**: 26 de Diciembre de 2025
