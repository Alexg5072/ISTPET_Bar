# 🚀 Guía de Despliegue Automatizado en AWS con GitHub Actions

Esta guía detalla la configuración paso a paso para que cada `git push` a la rama `main` construya la imagen Docker y la despliegue automáticamente en AWS.

---

## 1. Crear el Repositorio en Amazon ECR

1. Abre la consola de **AWS** y busca el servicio **Amazon Elastic Container Registry (ECR)**.
2. Haz clic en **Create repository** (Crear repositorio).
3. Configuración:
   - **Visibilidad:** Private (Privado).
   - **Nombre del repositorio:** `istpet-bar`
4. Guarda los cambios.

---

## 2. Configurar Secretos en GitHub

Ve a tu repositorio en GitHub:
`Settings` -> `Secrets and variables` -> `Actions` -> `New repository secret`

Agrega los siguientes secretos:

| Nombre del Secreto | Descripción | Ejemplo / Valor |
|---|---|---|
| `AWS_ACCESS_KEY_ID` | Access Key de IAM con permisos en ECR | `AKIAIOSFODNN7EXAMPLE` |
| `AWS_SECRET_ACCESS_KEY` | Secret Key de IAM | `wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY` |
| `AWS_REGION` | Región de AWS donde está ECR | `us-east-1` |
| `AWS_ACCOUNT_ID` | ID de tu cuenta de AWS (12 dígitos) | `123456789012` |
| `ECR_REPOSITORY` | Nombre del repositorio en ECR | `istpet-bar` |
| `EC2_HOST` | *(Opcional)* IP pública o DNS de tu instancia EC2 | `34.201.55.10` |
| `EC2_USER` | *(Opcional)* Usuario SSH de la instancia | `ubuntu` |
| `EC2_SSH_KEY` | *(Opcional)* Contenido completo de tu clave privada `.pem` | `-----BEGIN RSA PRIVATE KEY-----...` |

> 🔒 **Gobernanza de Seguridad:** Nunca agregues archivos con claves privadas o contraseñas en el repositorio de Git. Todos los datos sensibles deben gestionarse mediante GitHub Secrets y el `.env` en el servidor.

---

## 3. Despliegue en AWS EC2 (Servidor)

Si utilizas una máquina virtual EC2 con Ubuntu:

### Paso 3.1: Instalar Docker en EC2 (Solo una vez)
```bash
sudo apt update && sudo apt install -y docker.io docker-compose-v2 awscli
sudo usermod -aG docker $USER
newgrp docker
```

### Paso 3.2: Preparar la carpeta y variables en EC2
```bash
mkdir -p ~/istpet-bar && cd ~/istpet-bar
# Crear el archivo .env con las variables de producción
nano .env
```
Asegúrate de configurar en ese `.env`:
```env
APP_NAME="ISTPET Bar"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://tu-dominio-o-ip
APP_KEY=base64:... # Generada con php artisan key:generate

DB_CONNECTION=mysql
DB_HOST=127.0.0.1   # o endpoint de RDS
DB_PORT=3306
DB_DATABASE=istpet_bar
DB_USERNAME=istpet_user
DB_PASSWORD=TuPasswordSeguro

RUN_MIGRATIONS=true
RUN_SEEDERS=false
```

### Paso 3.3: Copiar `docker-compose.yml` al servidor
Puedes copiar el archivo [`docker-compose.yml`](../docker-compose.yml) a `~/istpet-bar/docker-compose.yml`.

---

## 4. Alternativa Serverless: AWS App Runner

Si prefieres no administrar una máquina virtual:
1. Ve a **AWS App Runner** -> **Create service**.
2. Selecciona **Source code repository** -> **Container registry** -> **Amazon ECR**.
3. Selecciona la imagen `istpet-bar:latest`.
4. En **Deployment settings**, marca **Automatic** (cada vez que GitHub Actions suba una nueva imagen a ECR, App Runner desplegará automáticamente).
5. Configura el puerto: `80`.
6. En **Environment variables**, ingresa tus variables de producción (`APP_KEY`, `DB_HOST`, etc.).

---

## 5. Pruebas Locales con Docker Compose

Para verificar todo el entorno localmente antes de enviar a AWS:

```bash
# 1. Copiar variables de entorno
copy .env.example .env

# 2. Generar APP_KEY en local si no la tienes
php artisan key:generate

# 3. Construir e iniciar contenedores
docker compose up -d --build

# 4. Ver logs en tiempo real
docker compose logs -f app
```
Acceso local: `http://localhost:8000`
