# VinkOS_Test
Prueba Desarrollo de VinkOS

## Esquema de Base de Datos

Este proyecto utiliza una base de datos MySQL para almacenar información sobre visitantes. A continuación se describe el esquema de la tabla `visitante`. Se muestra ![Diagrama del Proyecto](diagrama.png)


### Tabla `visitante`

La tabla `visitante` está definida con el siguiente esquema:

```sql
CREATE TABLE visitante (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    fechaPrimeraVisita DATETIME NOT NULL,
    fechaUltimaVisita DATETIME NOT NULL,
    visitasTotales INT NOT NULL,
    visitasAnioActual INT NOT NULL,
    visitasMesActual INT NOT NULL,
    CONSTRAINT chk_fechaUltimaMayorFechaPrimera CHECK (fechaUltimaVisita >= fechaPrimeraVisita)
);

```

### Descripción de Campos

- **`id`**:
  - **Tipo**: `INT`
  - **Descripción**: Identificador único para cada visitante. Se incrementa automáticamente con cada nuevo registro.

- **`email`**:
  - **Tipo**: `VARCHAR(255)`
  - **Descripción**: Dirección de correo electrónico del visitante. Este campo es único y no puede ser nulo.

- **`fechaPrimeraVisita`**:
  - **Tipo**: `DATETIME`
  - **Descripción**: Fecha y hora de la primera visita del usuario. Este campo es obligatorio.

- **`fechaUltimaVisita`**:
  - **Tipo**: `DATETIME`
  - **Descripción**: Fecha y hora de la última visita del usuario. Este campo es obligatorio.

- **`visitasTotales`**:
  - **Tipo**: `INT`
  - **Descripción**: Número total de visitas realizadas por el usuario.

- **`visitasAnioActual`**:
  - **Tipo**: `INT`
  - **Descripción**: Número de visitas realizadas por el usuario en el año actual.

- **`visitasMesActual`**:
  - **Tipo**: `INT`
  - **Descripción**: Número de visitas realizadas por el usuario en el mes actual.

### Restricciones

- **`chk_fechaUltimaMayorFechaPrimera`**:
  - **Descripción**: Esta restricción garantiza que la `fechaUltimaVisita` no sea anterior a la `fechaPrimeraVisita`, asegurando la consistencia de los datos.
