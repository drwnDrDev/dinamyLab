# Revisión de exámenes y remisiones — 14 de noviembre de 2026

## Comparación con ExamenSeeder

El seeder define únicamente **Hemoclasificación** (CUP 911017, $10.000) y **Prueba de embarazo** (CUP 904508, $16.000). La comparación es contra el código del seeder, no contra una base de datos instalada.

De las 24 entradas de `resources/utils/listas/examenes.csv`, **23 faltan**. Prueba de embarazo sí existe, pero la lista indica $20.000: diferencia de $4.000. Hemoclasificación está en el seeder y no en la lista. No se modificaron tarifas existentes.

| Examen faltante | Precio de lista (COP) |
| --- | ---: |
| GLICEMIA | 20,000 |
| COLESTEROL | 20,000 |
| COLESTEROL  HDL | 20,000 |
| COLESTEROL  LDL | 20,000 |
| TRIGLICERIDOS | 20,000 |
| PERFIL LIPIDICO | 80,000 |
| ACIDO URICO | 25,000 |
| PARCIAL DE ORINA | 20,000 |
| COPROLOGICO | 22,000 |
| K O H PIEL Y UÑAS | 20,000 |
| FROTIS DE GARGANTA | 20,000 |
| R A TEST / FACTOR REUMATOIDEO | 20,000 |
| PROTEINA C REACTIVA/ PCR | 20,000 |
| ANTIESTREPTOLISINA | 20,000 |
| GLICEMIA POST PRANDIAL | 40,000 |
| FROTIS VAGINAL | 25,000 |
| FROTIS DE SECRESION URETRAL | 25,000 |
| GRAM DE CUALQUIER MUESTRA | 20,000 |
| K O H  CUALQUIER MUESTRA | 20,000 |
| SEROLOGIA | 20,000 |
| CURVA DE TOLERANCIA A LA GLUCOSA  (3 MUESTRAS) | 75,000 |
| CURVA DE TOLERANCIA A LA GLUCOSA  (5 MUESTRAS) | 115,000 |
| EOSINOFILOS EN MOCO NASAL | 25,000 |

## Remisiones

Las 25 entradas de `remisiones.csv` faltan en ExamenSeeder. Frotis vaginal y frotis de secreción uretral también aparecen en la lista de exámenes locales con el mismo precio ($25.000); debe decidirse su modalidad al crear cada orden, sin duplicar automáticamente el catálogo.

| Remisión | Precio de lista (COP) |
| --- | ---: |
| FROTIS VAGINAL | 25,000 |
| FROTIS DE SECRESION URETRAL | 25,000 |
| HIV  ANTICUERPOS I Y II | 50,000 |
| HEPATITIS  C (HVC) | 50,000 |
| ANTIGENO DE SUPERFICIE PARA HEPATITIS B ( HBs Ag) | 48,000 |
| Anti-HBs (anticuerpos para hepatitis B) | 48,000 |
| Cuadro hematico/ hemograma | 20,000 |
| Hemograma IV | 35,000 |
| Antigeno Especifico de Prostata (PSA) | 57,000 |
| Gonadotropina corionica-Sub-unidad Betacuantitativa | 40,000 |
| Inmunoglobulina E | 45,000 |
| FROTIS DE SANGRE PERIFERICA | 15,000 |
| HORMONA ESTIMULANTE DEL TIROIDES | 42,000 |
| TIROXINA  (T4) | 42,000 |
| TRIYODOTIRONINA (T3) | 45,000 |
| PERFIL TIROIDEO (TSH, T3, T4) | 120,000 |
| TIROXINA LIBRE  (T4 L) | 42,000 |
| TIEMPO DE PROTROMBINA  (PT) INR | 20,000 |
| TIEMPO PARCIAL DE TROMBOPLASTINA (PTT) | 20,000 |
| HEMOGLOBINA GLICOSILADA (HbA 1 C) | 40,000 |
| CREATININA | 20,000 |
| TRANSAMINASA GLUTAMICO OXALACETICA(GOT) /ASAT | 20,000 |
| TRANSAMINASA GLUTAMICO PIRUVICA (GPT) / ALAT | 20,000 |
| FOSFATASA ALCALINA | 25,000 |
| UROCULTIVO | 70,000 |

La fila `PERFIL TIROIDEO (TSH, T3, T4),120000` contiene comas sin comillas. Para importarla con un lector CSV estándar debe entrecomillarse el nombre. Este informe tomó la última coma como separador del precio.

## Categorías del JSON

Se encontró `gemini-code-1794684327220.json`; no existe el nombre `gemini-code-179464327220.json` indicado en la solicitud. Contiene cuatro grupos: Perfil Lipidico (4 componentes), Examenes De Manipulacion De Alimentos (3), Curva De Tolerancia A La Glucosa 3 Muestras (3) y Curva De Tolerancia A La Glucosa 5 Muestras (5).

`ExamenCategoriaSeeder` crea las cuatro categorías y asocia exámenes existentes sin duplicar relaciones ni eliminar asociaciones previas. Normaliza mayúsculas, tildes, espacios y puntuación; reconoce Colesterol/Colesterol Total y Coprológico/Coproanálisis. Advierte cada componente ausente. Con el ExamenSeeder actual las cuatro categorías quedarán vacías hasta completar el catálogo.

Los valores `rango_referencia` se conservan en el JSON. No se convierten automáticamente en referencias clínicas: los tiempos de glicemia pueden ser parámetros de una curva y no exámenes independientes; requieren definir unidades, parámetros y CUPS antes de crear esos registros. Tampoco se inventan CUPS para los exámenes faltantes.

## Seguimiento y archivos

Se amplió el modelo existente OrdenRemision con estado, inicialmente «pendiente de muestra», y sincronización automática de la orden al guardar o eliminar remisiones. El estado se modifica desde la edición de la orden y se muestra en su detalle. Una orden con al menos un procedimiento o remisión solo queda terminada cuando todos están terminados o entregados. Pendientes, en proceso y anulados bloquean el cierre. Al reabrir un componente se limpia la fecha de terminación. Las órdenes de solo remisiones también pueden terminarse. El proyecto no tiene un sistema de notificaciones de cierre; el control se aplica a `ordenes_medicas.terminada`.

Los logos del reporte se resuelven exclusivamente en `storage/app/public/logos`. Las firmas se resuelven en `storage/app/private` (también admite la subcarpeta `firmas`), sin recurrir al disco público. Los archivos existentes no se movieron. El reporte admite PNG/JPEG; los SVG existentes requieren conversión para ese reporte. No se encontró un campo independiente para sellos.

## Aplicación

Con PHP 8.5 y las dependencias instaladas:

```bash
php artisan migrate
php artisan db:seed --class=ExamenCategoriaSeeder
```

La migración agrega el estado y recalcula las órdenes existentes; las remisiones históricas quedan pendientes hasta que se actualicen explícitamente. El seeder de categorías también está incluido después de ExamenSeeder en CatalogoBaseSeeder.
