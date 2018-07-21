# Guía rápida de autoría 2: preguntas matemáticas multi-parte

Esta es la segunda parte del [inicio rápido de autoría](Authoring_quick_start.md).  El propósito es escribir preguntas multi-parte.

### Ejemplo 1 ###

Encuentre la ecuación de la línea tangente a \(x^3-2x^2+x\) en el punto \(x=2\).

1. Diferenciar \(x^3-2x^2+x\) con respecto a \(x\).
2. Evaluar su derivada en \(x=2\).
3. Desde aquí, encontrar la ecuación de la línea tangente. \(y=...\)

Dado que las tres partes se refieren a un polinomio, si se usan preguntas generadas aleatoriamente entonces cada una
de estas partes necesita referenciar a una sola ecuación genrada aleatoriamente. . Por consiguiente las partes 1.-3. realmente forman
un ítem. Tenga en cuenta aquí que la parte 1. es independiente de las otras. La parte 2. requiere tanto el primero como el segundo ingreso.
Parte 3. podría fácilmente ser calificada independientemente, o tomar en cuenta las partes 1 y 2. Tenga en cuenta también que el profesor puede 
elegir otorgar puntuación  "en adelante" .

### Ejemplo 2 ###

Considere la siguiente pregunta, que se hace a estudiantes de esuela relativamente jóvenes.

Expandir \((x+1)(x+2)\).

En el contexto en el que se usa, es apropiado proporcionarle a los estudiantes la
oportunidad de "llenar los huecos", en la ecuación siguiente.

    (x+1)(x+2) = [?] x2 + [?] x + [?].

Nosostros discutimos que esto es realmente "una pregunta" con "tres entradas".
Aun más, es probable que el profesor querrá que los estudiantes completen todas las cajas
antes de asignar cualquier retroalimentación, aun y cuando sea generada retroalimentación separada para cada entrada
(por ejemplo, coeficiente). Esta retroalimentación debería de estar toda agrupada en un sitio en lapantalla. Más aun,
para identificar las causas posibles de errores algebraicos, un procedimiento de calificación automático
necesitará todos los coeficientes simultáneamente. No es satisfactorio el tener tres procedimientos de calificación
totalmente independientes.

Estos dos ejempos ilustarn dos posiciones extremas.

1. Todas las entradas dentro de un único ítem multi-parte pueden ser valoradas independientemente.
2. Todas las entradas dentro de un ítem multi-parte deben ser completadas antes de que el ítem pueda ser valorado.

El desarrollar preguntas multi-parte que satisfagan estas dos posiciones extremas debería de ser relativamente sencillo.
Sin embargo, es más común el tener preguntas multi-parte que están entre estos extremos, como en el caso de nuestro primer ejemplo.

### Procesamiento de la respuesta ###

El procesamiento de la respuesta es el medio mpor el cual la respuesta de un estudiante es evaluada y la retroalimentación, de varias formas,
es asignada. La observación crucial en STACK es una separación completa entre dos componentes importantes.

1. una lista de [entradas](Inputs.md);
2. una lista de [árboles de respuesta potencial](Potential_response_trees.md).

## [Entradas](Inputs.md) ##

El [texto de la pregunta](CASText.md#question_text), por ejemplo, el texto que de hecho es mostrado al estudiante, pued etener un número arbitrario de [entradas](Inputs.md). Un elemento puede estar posicionado
en cualquier lugar dentro del texto de la pregunta, incluyendo adentro de expresiones matemáticas, por ejemplo,  ecuaciones (_note_: MathJax actualmente no soporta elementos de forma adentro de ecuaciones).
Cada entrada estará asociada con un número de campos. Por ejemplo

1. El nombre de una variable CAS a la cual es asignada la respuesta del estudiante (si hubiera) durante el procesamiento de la respuesta.
   Esto podríoa ser asignado automáticamente, por ejemplo, en orden `ans1`, `ans2`, etc. Cada variable es conocida como una variable respuesta.
2. El tipo de la entrada. Ejemplos incluyen
  1. entrada algebraica lineal directa, por ejemplo `2*e^x`.
  2. herramienta de entrada gráfica, por ejemplo, un deslizador.
  3. Selección Falso/Verdadero.
3. La respuesta correcta del profesor.

## [Árboles de respuesta potencial](Potential_response_trees.md) ##

Un árbol de respuesta potencial (técnicamente una gráfica dirigida acíclica) consistes de un número arbitrario de nodos enlazados
que llamamos respuestas potenciales. En cada nodo dos expresiones son comparadas usando una Prueba de Respuesta especificada,
y el resultado es o VERDADERO o FALSO. Una rama correspondiente del arbol tiene la oportunidad para

1. Ajustar el puntaje, (por ejemplo, asignar un avalor, añadir o restar un valor);
2. Añadir retroalimentación escrita específicamente para el estudiante;
3. Generar una "Nota de respuesta", usada por el profesor para valoración evaluativa;
4. Nominar al siguiente nodo, o terminar el proceso.

Cada pregunta tendrá cero o más árboles de rtespuesta potencial. Para cada árbol de respuesta potencial habrá lo siguiente.

1. Un número máximo de puntos disponibles, por ejemplo, puntaje.
2. Una lista de cuales variables de respuesta son necesarias para este árbol de respuesta. Solamente cuando un estudiante ha
   proporcionado uan respuesta **válida** a todos los elementos en esta lista será recorrido el árbol y se asignarán los resultados.
3. Una colección de variables de respuesta potencial, que pueden depender de las variables de respuesta relevantes, variables de pregunta y etcétera. Estos son evaluados antes de que sea recorrido el árbol de respuesta potencial.
4. Un punto nominado en la pregunta misma dentro del cual es insertada la retroalimentación.
   Esta retroalimentación será el puntaje, y la retroalimentación generada por este árbol.

El permitir cero árboles de respuesta potencial es necesario para incluir una pregunta de encuesta la cual no es evaluada automáticamente. Una entrada que no es usada en cualquier árbol de respuesta potencial es
por lo tanto tratada como una encuesta y simplemente es grabada.

Para ilustrar preguntas matemática smulti-parte nosotros empezamos por escribir un ejemplo. Nosotros asumimos que Usted apenas ha estudiado la [guía de inicio rápido de autoría](Authoring_quick_start.md) por lo que esta es de alguna manera abreviada.

## Escribiendo una pregunta multi-parte ##

Comience con uan nueva pregunta STACK, y dele un nombre a la pregunta, por ejemplo "Línea stangentes".  Esta pregunta tendrá tres partes.  Comience por copiar las variables de la pregunta y el texto de la pregunta como sigue. Tenga en cuenta que nosotros no hemos incluido ninguna aleatorización, pero hemos usado nombres de variables en el comienzo para facilitar esto.

__Variables de pregunta:__

     p:x^3-2*x^2+x;
     pt:2;
     ta1:diff(p,x);
     ta2:subst(x=pt,diff(p,x));
     ta3:remainder(p,(x-pt)^2);

__Texto de pregunta__

Este texto debería de ser copiado dentro del editor en modo HTML.

<textarea readonly="readonly" rows="5" cols="120">
<p>encontrar la ecuación de la línea tangente a {@p@} en elpunto \(x={@pt@}\).</p>
<p>1. Diferenciar {@p@} con respecto a \(x\). [[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</p>
<p>2. Evaluar su dericada en \(x={@pt@}\). [[input:ans2]] [[validation:ans2]] [[feedback:prt2]]</p>
<p>3. Por lo tanto, encontrar la ecuación de la línea tangente. \(y=\)[[input:ans3]] [[validation:ans3]] [[feedback:prt3]]</p>
</textarea>

LLene la respuesta para `ans1` (la cual existe por defecto) y quite la marca (tag) de `feedback` (retroalimentación) de la sección de  "retroalimentación específica". Nosotros elegimos incluir la retroalimentación dentro de las partes de esta pregunta.
Tenga en cuenta que hay un árbol de respuesta potencial para cada "parte".

Actualice le formato y entonces asegúrese de que las Respuestas Modelo  (Model Answers) estén llenadas como`ta1`, `ta2` y `ta3`.

STACK ha creado los tres árboles de respuesta potencial al detyectar las marcas (tags) de retroalimentación automáticamente. A continuación necesitamos editar los árboles de respuesta potencial. estos establecerán las propiedades de las respuestas del estudiante.

###Etapa 1: obtenr una pregunta funcional###

La primera etapa es incluir los árboles de respuest apotencial más simples. Estos simplemente asegurarán que las respuestas sean "correctas".  En cada árvol de respuest potencial, asegúrese de que  \(\mbox{ans}_i\) es algebraicamente equivalente a \(\mbox{ta}_i\), para \(i=1,2,3\).  En esta etapa nosotros tenemos una pregunta funcional. Guárdela y previsualice la pregunta. Para referencia, las respuestas correctas son

     ans1 = 3*x^2-4*x+1
     ans2 = 5
     ans3 = 5*x-8

###Etapa 2: continuar con la calificación###

A continuación implementaremos una calificación siguiente simple.

Observe cuidadosamente la parte 2.  Esta no pide la "respuesta correcta" sino solamente pide que el estudiante haya evaluado la expresión en a parte 1 correctamente en el punto correcto.  Así, la primera tarea es establecer esto apropiadamente al evaluar la respuesta dada en la primera parte, y compararla con la segunda parte.  Actualice le nodo 1 de `prt2` para establecer lo siguiente.

    AlgEquiv(ans2,subst(x=pt,ans1))

A continuación, añada un solo nodo (a `prt2`), y asegúrese de que este nodo establezca que

    AlgEquiv(ans1,ta1)

Ahora nosotros enlazamos la rama verdadera del nodo 1 al nodo 2 (de `prt2`).  Ahora nosotros tenemos tres resultados .

Nodo 1: ¿evaluaron la expresión en la parte 1 correctamente? Si fuera "si" entonces vaya al nodo 2, de lo contrario  si fuera "no" entonces termine sin puntaje.

Nodo 2: ¿tuvieron correcta la parte 1?  si fuera "si" entonces esta es la situación ideal, puntaje completo.  Si fuera"no" entonces elegir puntaje, conforme convenga a su gusto en esta situación, y añada alguna retroalimentación como la siguiente en el Nodo 2, retroalimentación para falso.

<textarea readonly="readonly" rows="5" cols="120">
Su respuest para esta parte es correcta; ¡aunque sin embargo Usted ha tenido la parte 1 equivocada! ¡ Por favor, intente nuevamente ambas partes!
</textarea>

###Etapa 3: añadiendo pruebas de pregunta###

It is probably sensible to add question tests.  From the question preview window, click on `Question tests & deployed versions` link in the top right of the page.

Add a test to your question which contains the correct answers, as follows.

     ans1 = 3*x^2-4*x+1
     ans2 = 5
     ans3 = 5*x-8

The marks should all be "1" and the answer notes as follows.

     prt1 = prt1-1-T
     prt2 = prt2-2-T
     prt3 = prt3-1-T


Now add a new answer test to check the follow-through marking.  For example, make the following mistake in part 1, but use it in part 2 correctly.

     ans1 = 3*x^2-4
     ans2 = 8
     ans3 = y=8*x-8

The marks should all be "0" and the answer notes as follows.

     prt1 = prt1-1-F
     prt2 = prt2-2-F
     prt3 = prt3-1-F

When you run the tests you can also look at the feedback to confirm the system is giving the kind of feedback you want for these types of mistake.

###Stage 4: Random question###

Next we can add a randomly generated polynomial to the question.  Because we used variable names throughout the question from the start, this should be a simple matter of redefining the value of `p` in the question variables as follows.

    p : (2+rand(3))*x^3+(2+rand(3))*x^2+(2+rand(3))*x;

You will need to add a non-empty question note to enable grouping of random versions.  E.g. the following string will suffice.

    {@p@}

We now need to update the question tests to reflect this.  In the first test, you are free to use `ta1` etc to specify the correct answers.

In the second test you might as well leave the test as is.

# Next steps #

* You might like to look at the entry for [feedback](Feedback.md).
* Quality control and  testing your question can be made easier by looking at [testing](Testing.md).
* You might like to look at more information on [Maxima](../CAS/index.md), particularly the Maxima documentation if you are not very familiar with Maxima's syntax and function names. A graphical Maxima interface like [wxMaxima](http://andrejv.github.com/wxmaxima/) can also be very helpful for finding the appropriate Maxima commands easily.

The next part of the authoring quick start guide looks at [turning simplification off](Authoring_quick_start_3.md).

