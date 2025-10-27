# Sistema de Gestión de Proyectos y Tareas

Este es un sistema pequeño de gestión de proyectos y tareas, desarrollado con Laravel 12 para el backend y Vue.js junto con Tailwind CSS 4 para el frontend. El objetivo es proporcionar una aplicación moderna y eficiente para la administración básica de proyectos y tareas.

---

## Requisitos del Sistema

- PHP 8.2 o superior
- MySQL (la base de datos debe ser creada previamente)
- Node.js (versión recomendada LTS) y npm
- Composer
- Servidor web local o integrado (php artisan serve)

## Tecnologías Utilizadas

- Backend: Laravel Framework 12, Laravel Sanctum para autenticación, migraciones y seeders incorporados.
- Frontend: Vue.js 3, Pinia para gestión del estado, Vite como bundler, Tailwind CSS para estilos.
- Base de datos: Mysql

---

## Instalación y Configuración

### Backend

1. Ingrese a la carpeta `backend`.
2. Copie el archivo `.env.example` a `.env` y configure las variables de entorno, como las credenciales para la base de datos.
3. Cree la base de datos MySQL conforme a configuración en el archivo `.env`.
4. Instale las dependencias de PHP ejecutando: composer install
5. Ejecute migraciones y seeders para preparar la base de datos: php artisan migrate --seed
6. Inicie el servidor de desarrollo con:php artisan serve


### Frontend

1. Ingrese a la carpeta `frontend`.
2. Instale las dependencias de Node.js con: npm install
3. Inicie el servidor de desarrollo ejecutando: npm run dev

   
---

## Usuarios de Prueba

- Correo electrónico: [admin@gmail.com](mailto:admin@gmail.com)
- Contraseña: `12345678`

---

## Uso

Acceda mediante el navegador a la dirección proporcionada por el servidor de desarrollo del fronted (por defecto, `http://localhost:5173`), para interactuar con el sistema de gestión.

