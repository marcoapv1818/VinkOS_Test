# Utiliza la imagen oficial de atmoz/sftp
FROM atmoz/sftp:latest

# Copia los archivos que deseas compartir por SFTP desde tu máquina local al contenedor
COPY ./data-test/ /home/vinkOS/archivosVisitas/
