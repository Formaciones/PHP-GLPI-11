# Comandos de base de datos para Formaciones

Estos ejemplos trabajan con la tabla creada por el plugin `formaciones`:

```sql
glpi_plugin_formaciones_formaciones
```

Credenciales del contenedor MariaDB:

```text
MARIADB_ROOT_PASSWORD=root123
MARIADB_DATABASE=glpidb
MARIADB_USER=glpi
MARIADB_PASSWORD=glpi123
```

## Entrar en el contenedor

```bash
docker exec -it glpi-db bash
```

## Conectarse a MariaDB desde dentro del contenedor

Con el usuario de la aplicacion:

```bash
mariadb -u glpi -pglpi123 glpidb
```

Con el usuario root:

```bash
mariadb -u root -proot123 glpidb
```

## Ejecutar los ficheros SQL desde el host

Si estas en `C:\xampp\htdocs`, puedes copiar el SQL al contenedor y ejecutarlo:

```bash
docker cp labs/07/database/insert_formaciones.sql glpi-db:/tmp/insert_formaciones.sql
docker exec -it glpi-db mariadb -u glpi -pglpi123 glpidb -e "source /tmp/insert_formaciones.sql"
```

Tambien puedes ejecutar el fichero combinado:

```bash
docker cp labs/07/database/formaciones_ejemplos.sql glpi-db:/tmp/formaciones_ejemplos.sql
docker exec -it glpi-db mariadb -u glpi -pglpi123 glpidb -e "source /tmp/formaciones_ejemplos.sql"
```

## Comandos rapidos dentro de MariaDB

Insertar:

```sql
INSERT INTO glpi_plugin_formaciones_formaciones
    (name, description, state, date_creation, date_mod)
VALUES
    ('PHP + GLPI', 'Curso de desarrollo de plugins para GLPI', 1, NOW(), NOW());
```

Consultar:

```sql
SELECT id, name, description, state, date_creation, date_mod
FROM glpi_plugin_formaciones_formaciones
ORDER BY id DESC;
```

Actualizar:

```sql
UPDATE glpi_plugin_formaciones_formaciones
SET description = 'Descripcion actualizada desde SQL',
    date_mod = NOW()
WHERE name = 'PHP + GLPI';
```
