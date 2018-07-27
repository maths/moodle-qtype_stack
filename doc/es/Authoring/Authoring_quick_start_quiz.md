# Guía rápida de autoría: configurando un examen

Una vez que Usted haya escrito preguntas, es probable que las quiera incluir en un examen ('cuestionario' en el español internacional). Alternativamente, Usted podría querer configurar un examen usando las preguntas de muestra.  

El propósito de este documento es proporcionar una guía, desde el punto de vista de un principiante, de  algunos de los pasos que deben ser tomados al configurar preguntas matemáticas dentro de un examen Moodle, usando el paquete de evaluación asistida por computadora  STACK.  Tome en cuenta que este documento corre el riesgo de duplicar la documentación del examen de Moodle, la cual debería de ser consultada.

*Estos párrafos han sido editados a partir de notas creadas por Dr Maureen McIver, Department of Mathematical Sciences, Loughborough University, UK, July 2016.*

## Encontrar o escribir algunas  preguntas

Usted necesita comenzar identificando preguntas para el examen y la forma más fácil de hacer esto es comenzar con una pregunta que ya está escrita y modificarla para que se ajuste a sus necesidades.  

Una vez que Usted se haya acostumbrado a la idea de exportar, importar, copiar y modificar preguntas, entonces Usted podría encontrar más útil el comenzar desde otras preguntas, o escribir las suyas propias. Vea la [Guía de inicio rápido de autoría](Authoring_quick_start.md).  

## Importar preguntas desde un servidor existente

Para exportar preguntas existentes:

1. Ingrese al módulo en el servidor Moodle desde el cual Usted desea exportar preguntas y haga click en el bloque de Administración del `Banco de preguntas` y después haga click en `Exportar`.  
2. Haga click en `Formato Moodle XML` y después elija la categoría que desea exportar.  Moodle solamente le permite exportar categorías individuales.
3. Esto descargará un archivo con todas las preguntas de esa categoría.

Para importar esas preguntas adentro de su curso

1. Ingrese a su módulo en el servidor Moodle y haga click en `Banco de preguntas` en el bloque de Administración.
2. Haga click en `Importar`.  
3. Haga click en `Formato Moodle XML` y después arrastre y suelte el archivo de preguntas `?.xml`  desde su carpeta de Descargas en su escritorio y después haga click en `Importar` y después haga click en `Continuar`.  
4. Debería de aparecer una copia de las preguntas dentro del Banco de preguntas para su módulo y Usted puede modificarlas como desee.

## Viendo una pregunta STACK existente

* Elija el curso Moodle.
* Dentro del `Banco de preguntas` haga click en el ícono para `Previsualizar` (una lupa de aumento) junto a una pregunta existente.  
* Revise cuidadosamente cual comportamiento está usando. Si Usted está usando el `Adaptivo` Usted tendrá un botón para `Revisar`, pero si Usted está usando `retroalimentación diferida` no lo tendrá.
* Para cambiar el comportamiento, haga click en `Comenzar otra vez`. 
* Ingrese las respuestas que Usted piensa que son incorrectas o parcialmente correctas para ver qué retroalimentación obtiene. Haga esto tantas veces como quiera para familiarizarse con ingresar respuestas en STACK. Cuando haya terminado, haga click en `Cerrar previsualización`.

## Pruebas de preguntas y Variantes desplegadas

Las `Pruebas de Pregunta` sirven dos propósitos: (1) para asegurar que funciona y (2) para comunicarle a otros lo que hace.  Estas corresponden a las "pruebas de unidad (unit tests)" de la ingeniería de software estándar y revisarán que el procesamiento de STACK de una respuesta específica a una versión particular de una pregunta que surge, funciona tal como Usted esperaría que debería de hacerlo.   Cuando menos, Usted quiere asegurarse de que si el estudiante ingresa lo que Usted piensa que es la respuesta correcta a una versión particular de la pregunta, entonces ellos obtendrán el puntaje completo.   Las pruebas están configuradas para un conjunto general de parámetros aleatorios y entonces cuando Usted `Despliegue variantes` (vea más adelante) cada prueba es verificada para cada versión particular de la pregunta generada. Para la documentación completa vea [pruebas de pregunta](Testing.md).

Haga click en el ícono para `Previsualizar` para la pregunta y después haga click en `Pruebas de pregunta y versiones desplegadas`.  Esto lo lleva a Usted a una página que es única para el tipo de pregunta STACK, (que ningún otro tipo de pregunta de Moodle tiene estas facilidades).

El propósito primario de esta página es añadir "[pruebas de pregunta](Testing.md)".    Esta página también le permite hacer lo siguiente.

1. Enviar las variables de pregunta a una sesión interactiva CAS.  Muy útil para escribir la solución trabajada en un entorno interactivo, pero Usted tendrá que cortar y pegar la solución trabajada de vuelta a la sección de "retroalimentación general" cuando edite la pregunta.
2. Usted puede exportar una sola pregunta (tenga en cuenta que Moodle normalmente espera que Usted exporte una categoría completa).
3. Desplegar preguntas.

Usted puede añadir más casos de prueba al hacer click en `Añadir otro caso de prueba`.  Todo lo que Usted necesita hacer es ingresar una respuesta que Usted desea revisar en la caja de entrada y después hacer click en `LLenar el resto del formato` para hacer un caso-de-prueba aprobatorio (¡riesgoso!) y después hacer click en `Crear caso de prueba`.  Usted también puede editar o eliminar los casos de prueba existentes al hacer click en el botón relevante.  

Tenga en cuenta que, el probar nunca "simplifica" la entrada, por lo que Usted podría necesitar `ev(...,simp)` si Usted desea simplificar la entrada, o parte de una entrada, antes de que el sistema la evalue.

Una vez que Usted haya desarrollado las pruebas de pregunta, Usted necesita ir a la sección de `Desplegar variantes` en la parte superior de la pantalla y  poner un número (por ejemplo 10) en la caja para `Intentar desplegar el siguiente número de variantes` y hacer click en `Ir`.  Esto le permite a STACK producir un conjunto de versiones de la pregunta a partir de los cuales será elegida al azar una pregunta individual para un estudiante (las preguntas no son generadas al vuelo).  STACK verificará que cada versión que genere se comporte como debe de ser  al correr cada versión a través de  las pruebas de pregunta que Usted haya configurado.  El éxito será indicado por una caja que dice  `¡Todas las pruebas pasadas!` y la falla de una versión particular de una pregunta que no se comporte como debería será resaltada.  STACK normalmente no maneja el poder producir tantas variantes como Usted solicite antes de comenzar a duplicar versiones. Si STACK produce más de tres duplicados existentes, se rinde y abandona la misión.

## Construyendo un examen Moodle

Una vez que Usted haya construido un banco de preguntas, Usted puede ponerlas dentro de un examen Moodle. Yo solamente daré breves detalles acerca de esto y Usted podría querer consultar con otras personas locales de Evaluación Asistida por Computadora para que le ayuden con esto.  

Includo con los materiales de muestra está un "Examen de sintaxis (Syntax quiz)" y se le recomienda que ponga una copia de éste en la página de Usted para que los estudiantes puedan practicar la sintaxis de como ingresar respuestas dentro de un examen STACK antes de que ellos intenten resolver un examen específico para su módulo, y también que revisen que ellos pueden leer las matemáticas en sus maquinas.

### Configurando el examen

1. Ir a la página de Moodle y hacer click en `Activar la edición`.  
2. Ir al bloque en donde Usted quiere poner el examen o añadir un bloque adicional y hacer click en `Añadir una actividad o recurso` y después hacer click en `Examen' (Cuestionario en el español internacionale) y después hacer click en `Añadir'.  
3. Dele un nombre al examen y ponga cualquier descripción que desee en la caja para Descripción.  Aquí puede usarse LaTeX si lo desea.  
4. Haga click en `Tiempos` y arregle las horas de apertura y cierre.  
5. Haga click en `Calificación` y arregle los `Intentos permitidos`.  Por ejemplo, Usted podría usar `Ilimitado` para un examen de práctica y `1` para un examen del curso.  
6. Haga click en `Opciones para revisión`. Yo desactivo `Respuesta correcta` para ambos, tanto práctica como exámenes del curso.  Yo permito que la `Retroalimentación general` (solución trabajada) esté activada para un examen de práctica, pero desactivada para el examen del curso. (Yo no querría que las soluciones trabajadas estén disponibles cuando el estudiante esté haciendo los exámenes del curso.)  
7. Termine haciendo click en `Guardar y regresar al módulo`.  

No se olvide de asegurarse de que el botón para `Mostrar` junto al examen esté activado y que también lo esté el botón `Mostrar` junto al tópico cuando Usted quiera que el estudiante vea el examen.

Tenga en cuenta que el Banco de preguntas de Moodle creará una categoría para el examen.  En ocasiones es razonable  el poner todas las preguntas usadas en el examen dentro de esta categoría, pero tenga en cuenta que Usted solamente verá la categoría si Usted previamente ha navegado al examen.

### Añadiendo preguntas al examen

1. Active la edición y haga click en el examen y después haga click en `Editar examen`.  
2. Haga click en `Añadir`  y después haga click en `desde un banco de preguntas`, seleccione una categoría y después seleccione una o más de las preguntas STACK que Usted ha creado.
3. Haga click en `Añadir preguntas seleccionadas al examen` y después haga click en `Guardar` y regrese a la página principal del módulo.  
4. Haga click en `Editar ajustes` y revise que todas las configuraciones estén como Usted las quiere (vea la sección anterior) y en caso contrario, cámbielas y guárdelas.  

Para previsualizar el examen haga click en él y después en `Previsualizar examen ahora`, conteste las preguntas y haga click en `Enviar todo y terminar`.

### Tiempo extra para estudiantes

Si Usted tiene estudiantes que necesitan tiempo extra, Usted debe configurar `Grupos` que tengan dentro a estos estudiantes.  Por ejemplo, quienes necesiten 25% de tiempo extra.  

1. En el bloque de Administración, haga click en `Usuarios` y después en `Grupos` y después en `Crear grupo`.  
2. Dele un nombre al grupo, por ejemplo "25% tiempo extra".  Usted puede poner más detalles acerca de para quienes es este grupo en la caja para `Descripción del grupo`.  Haga click en `Guardar cambios`. 
3. `Añadir/quitar  usuarios` y después haga click en la ID para los estudiantes particulares para este grupo y haga click en `Añadir` para ponerlos dentro del grupo.  Repita para cada estudiante que necesita estar en este grupo.  
4. Configure otros grupos para estudiantes que necesiten cantidades diferentes de tiempo extra, si fuera necesario.

Una vez que Usted haya configurado los grupos, regrese y haga click en el examen del módulo en la página del servidor de enseñanza.  En el bloque de Administración haga click en `Anulaciones de grupo` y después haga click en `Añadir anulación de grupo`, elija el grupo relevante y configure el `Límite de tiempo` apropiado para el examen para ese grupo y haga click en `Guardar`.  Repita para cada grupo que requiera una cantidad diferente de tiempo extra.

## Ver los resultados de los estudiantes

Para ver los resultados de los estudiantes en Excel para un examen particular, vaya a la página del servidor Moodle y en la caja para  `Administración` haga click en `Resultados de actividad` y después haga click en `Exportar` y después haga click en `Hoja de cálculo Excel` y después haga click en el nombre del examen y en `Descargar'.  


