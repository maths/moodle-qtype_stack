# Guía rápida de autoría 1

Evaluación asistida por computadora de trabajos matemáticos en las siguientes fases.

1. [Autoría](../Authoring/index.md)
2. [Pruebas](Testing.md)
3. [Desplegado](Deploying.md)
4. [Reportes](Reporting.md)

Cada uno de estos enlaces contiene instrucciones detalladas. El propósito de esta página es guiarlo a lo lago de un ejemplo simple.

## Introducción ##

El tipo de pregunta STACK para Moodle está diseñado como un vehículo para gestionar preguntas matemáticas. Implícito dentro de esto está una estructura que las representa.
Esta página explica el proceso de fabricación de una pregunta, al trabajar mediante un ejemplo.

Las preguntas son editadas por medio del exámen (cuestionario) de Moodle.  En Moodle, vaya al Banco de preguntas y pídale crear una nueva pregunta STACK.  No se espante por el hecho de que el formato para editar se vea complicado.

Hay muchos campos, pero solo unos cuantos son obligatorios.  Estos son el nombre de la pregunta y el  [texto de la pregunta](CASText.md#question_text). El texto de la pregunta es la cadena de caracteres que, de hecho, es mostrada al estudiante; o sea, esta es  "la pregunta".
Si Usted tiene una contestación para ingresar (el ajuste por defecto es  tener una) la respuesta del profesor debe de estar no-vacía.  Los nodos en los árboles de respuesta potenciales tienen campos obligatorios (el ajuste por defecto es proporcionar un árbol con un nodo).

## Una pregunta de ejemplo ##

Ahora estamos listos para editar una pregunta de ejemplo.  El nombre de la pregunta es obligatorio en Moodle, por lo que elegiremos uno ahora, por ejemplo, `Question 1`.

Asegúrese de que el texto de la pregunta contenga la siguiente información. Debería de ser posible copiar y pegar, ¡pero asegúrese de que Usted no copia las marcas (tags) pre-formateadas HTML!

<textarea readonly="readonly" rows="3" cols="50">
Diferencíe \((x-1)^3\) con respecto a \(x\).
[[input:ans1]]
[[validation:ans1]]</textarea>

Hay varias cosas que tener en cuenta acerca de este texto.

* El texto contiene entornos matemáticos LaTeX.  NO USE entornos matemáticos `$..$` ni `$$..$$`.  En su lugar Usted debe utilizar `\(..\)` y `\[..]\` para matemáticas en-línea (inline) y mostradas, respectivamente.  (¡Existe un convertidor masivo automático si Usted tuviera muchos materiales antiguos!)
* La marca (tag) `[[input:ans1]]` será remplazada por una [entrada](Inputs.md) marcada `ans1`; por ejemplo, esto denota la posición de la caja adentro de la cual el estudiante pone su respuesta.
* La marca (tag) `[[validation:ans1]]` será remplazada por cualquier retroalimentación relacionada con la validez de la respuesta ingresada `ans1`.

Por defecto, una nueva pregunta automáticamente tiene una [entrada](Inputs.md), y un algoritmo para valorar la respuesta.

Deslice el cursor hacia abajo:  habrá una sección de [entradas](Inputs.md) del formato para editar.
Dentro de la respuesta modelo en la respuesta está una expresión CAS sintácticamente válida, como por ejemplo.

    3*(x-1)^2

Ahora ya tenemos una pregunta, y la respuesta modelo.  A continuación tenemos que decidir si la respuesta del estudiante es correcta.

## Establecer las propiedades de la respuesta del estudiante vía el árbol de respuesta potencial {#Answer_props_via_prt}

Para establecer propiedades de las respuestas del estudiante, necesitamos un algoritmo conocido como un  [árbol de respuesta potencial](Potential_response_trees.md).

Este árbol nos permitirá establecer las propiedades matemáticas de la respuesta del estudiante y, basándonos en estas propiedades, proporcionar resultados, tales como retroalimentación y un puntaje.

A su debido tiempo, proporcionaremos [retroalimentación](Feedback.md), que revisa

1. Que la respuesta sea correcta.
2. Ver si el estudiante integró por error.
3. Ver si es probable que el estudiante expandió y diferenció.

Por defecto, una nueva pregunta contiene un [árbol de repuesta potencial](Potential_response_trees.md) llamado `prt1`.
Este es el _nombre_ de la respuesta potencial, y puede ser cualquier cosa razonable (letras, opcionalmente seguidas por números, no más de  18 caracteres).
Puede haber cualquier número de [árboles de respuesta potencial](Potential_response_trees.md) (incluyendo cero).
La retroalimentación generada por estos árboles remplaza la marca (tag) `[[feedback:prt1]]`.
Por defecto, eta marca (tag) es colocada en el campo de Retroalimentación específica (Specific feedback), pero también podría estar colocada en  el texto de la pregunta.

Un árbol de respuesta potencial es una gráfica dirigida acíclica no-vacía de _nodos de respuesta potencial_.  Por defecto, nosotros tenemos un nodo de respuesta potencial, y este nodo es bastante simple.

1. `SAns` es comparada con `TAns` con la prueba de respuesta, posiblemente con una opción.
2. Si `true` (verdadera) entonces ejecutamos la rama `true` (verdadera).
3. Si `false` (falsa) entonces ejecutamos la rama `false` (falsa).

La prueba de respuestapor sí misma en ocasiones produce [retroalimentación](Feedback.md) para el estudiante (que el profesor podría elegir suprimir con la opcón quiet).  La prueba de respuesta también produce The answer test also produces una [nota de respuesta](Potential_response_trees.md#Answer_note) interna para el profesor, la cual es esecial para Reportar intentos del estudiante más tarde.

Cada rama podría entonces:

* Asignar/actualizar el puntaje.
* Asignar [retroalimentación](Feedback.md) formativa al estudiante.
* Dejar una [nota de respuesta](Potential_response_trees.md#Answer_note) para propósitos de [Reportes](Reporting.md).
* Nominar al nodo de respuesta potencial siguiente, o terminar el proceso `[stop]`.

Mosotros nos referimos  la respuesta del estudiante en cálculos de álgebra de computadora mediante el uso del nombre `ans1` dado que nosotros le dimos este nombre al ingreso en el texto de la pregunta.  La respuesta modelo era `3*(x-1)^2`.  Actualice  los campos del formato de tal forma que 

     SAns = ans1
     TAns = 3*(x-1)^2
     Answer test = AlgEquiv

Después presione el botón para `[Guardar cambios]`.  Si la progunta no puede guardarse, revise cuidadosamente por sihubiera errores, corríjalos y guarde nuevamente.

Esto ha creado y guardado una pregunta mínima.  Para recapitular, nosotros :

1. Escribimos la pregunta
2. Escribimos la respuesta modelo
3. Indicamos que queremos establecer que la respuesta del estudiante sea algebraicamente equivalente a la respuesta modelo `3*(x-1)^2`.

A continuación deberíamos de probar nuestra pregunta, al presionar el botón para pre-visualizar del Banco de preguntas.

## Previsualizar la pregunta ##

Asumiendo que no hay errores, Usted podría ahora elegir el enlace para "previsualizar la pregunta" desde elBanco de preguntas de Moodle.
Esto nos lleva a un nuevo formato, donde el profesor puede experimentar con la pregunta.

El examen (cuestionario) de Moodle es muy flexible. debajo de las opciones para los intentos, asegúrese de que tiene configurado "Como se comportan las preguntas" a "Modo adaptivo". Si fuera necesario "Comience nuevamente con estas opciones".

Intente ingresar

    3*(x-1)^2

dentro de la caja para respuesta.

El comportamiento por defecto para STACK es usar  "validación instantánea".  Esto es, cuando el estudiante termina de teclear el sistema valida automáticamente su respuesta y proporciona retroalimentación. Si esto no sucede automáticamente, presione el botón para `[Revisar]`.

el sistema primeramente establece la validez sintáctica de esta respuesta.

Presione nuevamente el botón para `[Revisar]`.

El sistema ejecuta el árbol de respuesta potencial y establece si es que su respuesta es algebraicamente equivalente
a la respuesta modelo `3*(x-1)^2`.  A continuación, intente tener la respuesta erronea.  Si su servidor no tiene la  "validación instantánea" activada (una opción administrativa/de la instalación) Usted necesitará enviar cada respuesta dos veces.
tenga en cuenta que todas sus respuestas son almacenadas en una tabla de intentos.

A nosotros realmente nos gustaría añadir una mejor retroalimentación, por lo que ahora es el momento para editar nuevamente la pregunta. Regrese a la página del Banco de preguntas y haga click en el enlace para editar la pregunta.

## Mejor retroalimentación ##

¿Qué pasaría si el resultado de aplicar la primera prueba de respuesta fuera falso?
Nos gustaría revisar que el estudiante no haya integrado por error, y logramos esto al añadir otro nodo de respuesta potencial.

Cierre la ventana de pre-visualización y edite nuevamente lapregunta. Deslice el curso abajo hasta el
Árbol de Respuesta Potencial y haga click en  el botón para  `[Añadirotro nodo]` al fondo de
la lista de nodos.

Desde la rama falsa del Nodo 1, cambie el campo para "Siguiente" de forma tal que esté configurado a  `[Nodo 2]`.
Si la primera prueba es falsa, entonces nosotros realizaremos la prueba en el Nodo 2.

Si el estudiante ha integrado, podría haber o no haber añadido una constante de integración.
Si ha añadido tal constante, ¡nosotros no sabemos cual letra han usado! Así pues,la mejor maneera para resolver
este problema es diferenciar su respuesta y compararla a la pregunta.

Actualice le formato de forma tal que el Nodo 2 tenga

    SAns = diff(ans1,x)
    TAns = (x-1)^3
    Answer test = AlgEquiv

Esto nos da la prueba, ¿pero qué pasa con los resultados?

1. En la rama verdadera configure el `score=0`
2. En la rama verdadera configure la retroalimentación a `¡Al parecer Usted ha integrado por error!`

Notice here that STACK also adds an "intelligent note to self" in the [nota de respuesta](Potential_response_trees.md#Answer_note) field.
This is useful for statistical grouping of similar outcomes when the feedback depends on randomly generated questions,
and different responses. You have something definite to group over.  This is discussed in [reportes](Reporting.md).

Press the `[Save changes]` button and preview the question.

## Better feedback still: the form of the answer ##

It is common for students to give the correct answer but use a quite inappropriate method.
For example, they may have expanded out the polynomial and hence give the answer in unfactored form.
In this situation, we might like to provide some encouraging feedback to explain to the student what they have done wrong.

Go back and `[Add another node]` in a similar way as before.  After all, we need to apply another answer test to spot this.

To use this potential response, edit Node 1, and now change the true branch to make the Next node point to the new Node 3.
If we enter Node 3, we know the student has the correct answer. We only need to establish if it is factored or not.
To establish this we need to use a different [answer tests](Answer_tests.md).

Update the form so that Node 3 has

    SAns = ans1
    TAns = 3*(x-1)^2
    Answer test = FacForm
    Test option\s = x
    Quiet = Yes.

The FacForm answer test provides feedback automatically which would be inappropriate here.
We just need to look at whether the answer is factored.  Hence we choose the quiet option.
We needed to add \(x\) to the "Test opts" to indicate which variable we are using.

We need to assign outcomes.

1. On the true branch set the `score=1`
2. On the false branch set the `score=1` (well, you may disagree here, but that is up to you!)
3. On the false branch set the feedback to something like

<textarea readonly="readonly" rows="3" cols="75">
Your answer is unfactored. There is no need to expand out the expression in this question. You can differentiate using the chain rule directly and keep the answer in factored form.</textarea>


This new feedback can be tested by typing in an expanded answer, i.e. `3*x^2-6*x+3`.

You can continue to add more potential response nodes as the need arises. These can test for more subtle errors
based upon the common mistakes students make. In each case an [pruebas de respuesta](Answer_tests.md) can be used to
make a different kind of distinction between answers.

## Random questions ##

At this point you might consider saving as a new question.

It is common to want to use random numbers in questions. This is straightforward to do, and we
make use of the optional [variables de pregunta](KeyVals.md#Question_variables) field.

STACK 3 uses Maxima's syntax for assignment, which is unusual.  In particular the colon `:` is used to assign a value to a variable.  So to assign the value of `5` to `n` we use the syntax `n:5`.

Modify the [Variables de pregunta](KeyVals.md#Question_variables) from the previous example so that

    p:(x-1)^3;

Then change the [texto de pregunta](CASText.md#question_text) to

<textarea readonly="readonly" rows="3" cols="50">
Differentiate {@p@} with respect to \(x\).
[[input:ans1]]
[[validation:ans1]]</textarea>

and in the inputs change the model answer to

    diff(p,x)

Notice that now we have defined a local variable `p`, and used the value of this in the Question text.  The difference is between mathematics enclosed between `\(..\)` symbols and `{@..@}` symbols. All the text-based fields in the question, including feedback, are [texto CAS](CASText.md).  This is HTML into which mathematics can be inserted.  LaTeX is placed between `\(..\)`s, and CAS expressions (including your variables) between matching `{@..@}` symbols.  There is more information in the specific documentation.   The CAS expressions are evaluated in the context of the random variables and displayed.

Since we have used `{@p@}` here, the user will not see a \(p\) on the screen when the question is instantiated, but the _displayed value_ of `p`.

Notice also that in the model answer there is a CAS command to differentiate the value of `p` with respect to `x`.
It is necessary for the CAS to work out the answer in a random question.
You now need to go through the potential response tree to use the variable `p` or `diff(p,x)` (or perhaps some other CAS expression) as appropriate.

We are now in a position to generate a random question. To do this modify the [question variables](KeyVals.md#Question_variables) to be

    n : 2+rand(3);
    p : (x-1)^n;

In this new example, we have an extra variable `n` which is defined to be a random number.

This is then used to define the variable `p` which is in turn used in the question itself.

When generating random questions in CAA we talk about _random numbers_ when we really mean _pseudo-random numbers_.
To keep track of which random numbers are generated for each user, there is a special command in STACK,
which you should use instead of [Maxima](../CAS/Maxima.md)'s random command.

This is the `rand` command which is a general "random thing" generator, see the page on [random generation](../CAS/Random.md) for full details.
It can be used to generate random numbers and also to make selections from a list.

### The question note ###

The question note enables the teacher to track which version of the question is given to each student.
Two versions are the same if and only if the [question note](Question_note.md) is the same.
Hence a random question may not have an empty question note.

Fill this in as

    \[ \frac{d}{d{@x@}}{@p@} = {@diff(p,x)@} \]

It is crucial to do this now since questions with `rand()` in the question variables may not have an empty question note.  By enforcing this now we prevent frustration later when it would be otherwise impossible to distinguish between random versions of a question.

Edit your trial question, save and preview it to get new random versions of the question.

### Further randomisation ###

At this point you might consider saving as a new question.

As a specific example of some of these features, try the question illustrated below.
This contains random numbers, and also examples of variables and expressions selected from a list.

    n : rand(5)+3;
    v : rand([x,s,t]);
    p : rand([sin(n*v),cos(n*v)]);

Then change the Question text to

<textarea readonly="readonly" rows="3" cols="50">
Differentiate {@p@} with respect to {@v@}.
[[input:ans1]]
[[validation:ans1]]</textarea>

Again, we need to use expressions such as `diff(p,v)` throughout the potential response tree, and even in one place `diff(ans1,v)`.

Delete Node 3.  Factored form tests no longer make sense in the context of this question.

It is often a good idea to use variables in the question at the outset, even if there is no intention to randomly generate a question initially. Also, as questions become increasingly complex, it is a good habit to comment complicated lines in the Maxima code in the Question variables and Feedback variables, in order to make the code easier to read for anyone wishing to edit the question. Comments are entered as follows: `v : rand([x,s,t]) /* Set v randomly to x, s, or t */`.

You will also need to update the question note to be

    \[ \frac{d}{d{@v@}}{@p@} = {@diff(p,v)@} \]

## Question tests ##

Testing questions is time consuming and tedious, but important to ensure questions work.  To help with this process STACK enables teachers to define "question tests".  These are the same principle as "unit tests" in software engineering.

From the question preview window, click on `Question tests & deployed versions` link in the top right of the page.

Please read the page on [testing](Testing.md).

Please ensure you have deleted the third node from the potential response tree!  Click `Add a test case` to add a test to your question.  Fill in the following information

    ans1 = diff(p,v)
    score = 1
    penalty = 0
    answernote = prt1-1-T

The system will automatically evaluate `diff(p,v)` to create `ans1` and then mark the question using this information.  It will match up the actual outcomes with those you specified.  This automates the testing process.

You can add as many tests as you think is needed, and it is usually a sensible idea to add one for each case you anticipate.  Here it would be sensible to test if the student has integrated by mistake.

If your question uses randomisation, then you need to [deploy instances](Deploying.md) of it before you can present it to students. This is done via the deployment interface on the top of the testing page.

# Next steps #

You might like to look at Moodle's quiz settings, creating a simple quiz.  This is, strictly speaking, a completely Moodle issue and there is every reason to combine STACK questions with other Moodle question types.  Some very brief notes are included in the [quiz quickstart guide](Authoring_quick_start_quiz.md).

STACK's question type is very flexible.

* You can add a worked solution in the [Retroalimentación general](CASText.md#General_feedback).
* You can change the behaviour of the question with the [opciones](Options.md)
* You can add plots to all the [CASText](CASText.md) fields with the [`plot`](../CAS/Maxima.md#plot) command.
* Usted puede  añadir soporte para [múltiples idiomas](Languages.md).

The next part of the authoring quick start guide looks at [multi-part mathematical questions](Authoring_quick_start_2.md).


