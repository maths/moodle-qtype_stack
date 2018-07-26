# Guía rápida de autoría: configurando un examen

Una vez que Usted haya escrito preguntas, es probable que las quiera incluir en un examen (cuestionario). Alternativamente, Usted podría querer configurar un examen usando las preguntas de muestra.  

El propósito de este documento es proporcionar una guía, desde el punto de vista de un principiante, de  algunos de los pasos que deben ser tomados al configurar preguntas matemáticas dentro de un examen Moodle, usando el paquete de evaluación asistida por computadora  STACK.  Tome en cuenta que este documento corre el riesgo de duplicar la documentación del examen de Moodle, la cual debería de ser consultada.

*Estos párrafos han sido editados a partir de notas creadas por Dr Maureen McIver, Department of Mathematical Sciences, Loughborough University, UK, July 2016.*

## Encontrar o escribir algunas  preguntas

Usted necesita comenzar identificando preguntas para el examen y la forma más fácil de hacer esto es comenzar con una pregunta que ya está escrita y modificarlña para que se ajuste a sus necesidades.  

Una vez que Usted se haya acostumbrado a la idea de exportar, importar, copiar y modificar preguntas entonces Usted podría encontrar más útil el comenzar desde otras preguntas, o escribir las suyas propias. Vea la [Guía de inicio rápido de autoría](Authoring_quick_start.md).  

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
* Revise cuidadosamente cual comportamiento está usando. Si Usted está usando el `Adaptivo` Usted tendrá un botón para `Revisar`, pero si Usted eestá usando `retroalimentación diferida` no lo tendrá.
* Para cambiar el comportamiento, haga click en `Comenzar otra vez`. 
* Ingrese las respuestas que Usted piensa que son incorrectas o parcialmente correctas para ver qué retroalimentación obtiene. Haga esto tantas veces como quiera para familiarizarse con ingresar respuestas en STACK. Cuando haya terminado, haga click en `Cerrar previsualización`.

## Pruebas de preguntas y Variantes desplegadas

Las `Pruebas de Pregunta` sirven dos propósitos: (1) para asegurar que funciona y (2) para comunicarle a otros lo que hace.  Estas corresponden a las "pruebas de unidad (unit tests)" de la ingeniería de software estándar y revisarán que el procesamiento de STACK de una respuesta específica a una versión particular de una pregunta que surge, funciona tal como Usted esperaría que debería de hacerlo.   Cuando menos, USted quiere asegurarse de que si el estudiante ingresa lo que Usted piensa que es la respuesta correcta a una versión particular de la pregunta entonces ellos obtendrán el puntaje completo.   Las pruebas están configuradas para un conjunto general de parámetros aleatorios y entonces cuando Usted `Despliegue variantes` (vea más adelante) cada prueba es verificada para cada versión particular de la pregunta generada. Para la documentación completa vea [pruebas de pregunta](Testing.md).

Haga click en el ícono para `Previsualizar` para la pregunta y después haga click en `Pruebas de pregunta y versiones desplegadas`.  Esto lo lleva a Usted a una página que es única para el tipo de pregunta STACK, (que ningún otro tipo de pregunta de Moodle tiene estas facilidades).

El propósito primario de esta página es añadir "[pruebas de pregunta](Testing.md)".    Esta página también le permite hacer lo siguiente.

1. Send the question variables to an interactive CAS session.  Very useful for writing the worked solution in an interactive environment, but you will have to cut and paste the worked solution back into the "general feedback" section when editing the question.
2. You can export a single question (note that Moodle normally expects you to export a whole category)
3. Deploy questions.

You can add more test cases by clicking on `Add another test case`.  All you then need to do is  enter a response that you want to check in the input box then click on `Fill in the rest of the form` to make a passing test-case (risky!) then click `Create test case`.  You can also edit or delete the existing test cases by clicking on the relevant button.  

Note, the testing never "simplifies" the input, so you may need to `ev(...,simp)` if you want to simplify the input, or part of an input, before the system assesses it.

Once you have devised the question tests you need to go to the `Deployed variants` section at the top of the screen and put a number (e.g. 10) in the box `Attempt to deploy the following number of variants` and click `Go`.  This enables STACK to produce a set of versions of the question from which an individual question for a student will be chosen at random (questions are not generated on the fly).  STACK will check that each version that it generates behaves as it should by running each version through the question tests that you have set up.  Success will be indicated by a box saying that `All tests passed!` and failure of a particular version of a question to behave as it should, will be highlighted.  STACK doesn't usually manage to be able to produce as many variants as you request before it starts duplicating versions.  If STACK produces more than three existing duplicates it gives up.

## Constructing a Moodle quiz

Once you have constructed a bank of questions you can put them into a Moodle quiz.   I will only give brief details about this and you may want to consult with other local CAA people for help with this.  

Included within the sample materials is a "Syntax quiz" and it is recommended that you put a copy of this on your own page so that students can practice the syntax of how to enter answers into a STACK quiz before they try a specific quiz for your module, and also check that they can read the mathematics on their machine.

### Setting up the quiz

1. Go to the Moodle page and click `Turn editing on`.  
2. Go to the block where you want to put the quiz or add an additional block and click `Add an activity or resource' then click `Quiz' then click `Add'.  
3. Give the quiz a name and put any description you want in the Description box.  LaTeX can be used here if you want.  
4. Click on `Timing` and fix the opening and closing times.  
5. Click on `Grade` and fix the `Attempts allowed`.  E.g. you could use `Unlimited` for a practice quiz and `1` for a coursework quiz.  
6. Click on `Review options` and I turn off `Right answer` for both practice and coursework quizzes.  I allowed `General feedback` (worked solution) to be on for a practice quiz but turned it off the coursework quiz. (I didn't want worked solutions to be available when the students were doing the coursework.)  
7. Finish by clicking `Save and return to module`.  

Don't forget to ensure that the `Show` button next to the quiz is on as well as the `Show` button next to the topic when you want the students to see the quiz.

Note, the Moodle question bank will create a category for the quiz.  It is sometimes sensible to put all the questions used in the quiz into this category, but not that you will only see the category if you have previously navigated to the quiz.

### Adding questions to the quiz

1. Turn editing on and click on the quiz then click `Edit quiz`.  
2. Click `Add`  then click `from a question bank`, select a category then one or more of the STACK questions you have created.
3. Click `Add selected questions to the quiz` then click `Save` and return to the main module page.  
4. Click on `Edit settings` and check that all the settings are as you wish (see previous section) and if not, change these and save.  

To preview the quiz click on it, then click `Preview quiz now`, answer questions and click on `Submit all and finish`.

### Extra time students

If you have students who need extra time you need to set up `Groups` with these students in.  E.g. who needs 25% extra time.  

1. In the Administration block, click on `Users` then `Groups` then `Create group`.  
2. Give the group a name, e.g. "25% extra time".  You can put more details of who the group is for in the `Group description` box.  Click `Save changes`. 
3. `Add/remove users` then click on the ID for a particular students for this group and click `Add` to put them in the group.  Repeat for each student who needs to be in this group.  
4. Set up other groups for students who need different amounts of extra time, if necessary.

Once you have set up the groups, go back and click on the Moodle quiz on the Learn server page.  In the Administration block click on `Group overrides` then click `Add group override`, choose the relevant group and set the appropriate `Time limit` for the quiz for that group and click `Save`.  Repeat for each group requiring a different amount of extra time.

## Viewing students' results

To see the students' results in Excel for a particular quiz go to the Moodle server page and in the `Administration` box click on `Activity results` then click `Export` then click `Excel spreadsheet` then click on the test name then click `Download'.  


