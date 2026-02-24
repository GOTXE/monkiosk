# Plan y Ejecucion - Footer OFAP 601 + GitHub

## Objetivo
Agregar un footer en la web de gestion con:
- texto `OFAP 601`
- icono oficial de GitHub
- enlace del icono al repositorio.

## Ejecucion
1. Se actualiza `estado_quioscos/index.html`:
- se añade `<footer>` al final del contenedor.
- se incluye SVG del icono oficial de GitHub dentro de un enlace.
- el enlace apunta a `https://github.com/GOTXE/monkiosk`.

2. Se actualiza `estado_quioscos/styles.css`:
- estilos de footer para mantener contraste sobre fondo navy.
- hover/focus visible en el icono para accesibilidad.
