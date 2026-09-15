# 🔐 Credenciales ISTPET Bar — Sistema de Pedidos

## Panel de Administración
URL: `http://127.0.0.1:8000/admin/dashboard`

| Rol | Correo | Contraseña | Acceso |
|---|---|---|---|
| Superadmin | admin@istpet.edu.ec | Admin2026! | Todo el sistema |
| Admin | admin2@istpet.edu.ec | Admin2026! | Todo excepto Configuración |
| Cajero | cajero@istpet.edu.ec | Cajero2026! | Dashboard, Pedidos, Stock |
| Visor | director@istpet.edu.ec | Visor2026! | Solo lectura |

## Kiosco (público)
URL: `http://127.0.0.1:8000/kiosco`
> Sin credenciales — acceso libre por sede

## Notas
- Cambiar contraseñas antes de producción
- El Superadmin es el único que puede acceder a Configuración
- El Cajero NO tiene acceso a: Productos, Categorías, Combos, Reportes, Caja, Promociones, Usuarios, Configuración
