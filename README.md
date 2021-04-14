# **Flip My Learning**

Flip My Learning es un plugin de tipo local para la plataforma Moodle que muestra visualizaciones para docentes y estudiantes 
sobre diversos indicadores. Para los docentes, las visualizaciones están enfocadas a analizar sesiones de estudio, 
entrega de tareas, calificaciones de estudiantes, intentos de evaluaciones y predicción 
del abandono estudiantil. Para los estudiantes, las visualizaciones se enfocan en analizar 
las sesiones de estudio y el tiempo invertido en el curso.s   

Este plugin es el resultado del Proyecto de Titulación Universitaria de los autores. Fue desarrollado con el objetivo de 
contribuir a la comunidad de investigación en Analíticas del Aprendizaje.

## License ##

2020 Edisson Sigua <edissonf.sigua@gmail.com>, Bryan Aguilar <bryan.aguilar6174@gmail.com>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.


## Instalación ##
1. Descarga o clona el proyecto
2. Mueve la carpeta **fliplearning** dentro de la carpeta **local** ubicada en la raíz de moodle
3. Ingresar en moodle como administrador
4. Comprueba que esté **fliplearning** en la lista de los plugins que requieren atención
5. Haz clic en“*Actualizar la base de datos de moodle ahora*”para iniciar la instalación
6. Purga las cachés de la plataforma

## #  Requisitos 
- Moodle > 3.X

## | Librerías y frameworks empleados ##

| Nombre | Descripción | Versión |
| --- | --- | --- |
| AlertifyJS  | Cuadros de diálogo para Notificaciones | 1.11.4 |
| Axios  | Solicitudes HTTP  |  0.19.0 |
| Highcharts  | Gráficos interactivos  | 8.2.2  |
| Vuejs Datepicker | Selector de fechas para vuejs |  1.6.2  |
| Moment.js  | Manipulación de fechas  |  2.24.0  |
| Moment Timezone  | Muestra fechas en cualquier zona horaria  |  2.24.0  |
| Vue Draggable  |  Arrastrar y soltar elementos para vuejs |  2.23.2  |
| Sortable  | Ordenar los elementos arrastrados y soltados (*Dependencia de Vue Draggable*)  |  1.10.0-rc3  |
| Vue JS  | Marco de javascript progresivo  |  2.6.12  |
| Vuetify  | Librería de componentes visuales para vuejs | 2.4.9  |
| PHP-ML  | Librería con algoritmos de Machine Learning | 0.9.0  |