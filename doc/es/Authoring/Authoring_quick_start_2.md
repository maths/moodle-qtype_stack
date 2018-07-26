# Guía rápida de autoría 2: preguntas matemáticas multi-parte

Esta es la segunda parte del [inicio rápido de autoría](Authoring_quick_start.md).  El propósito es escribir preguntas multi-parte.

### Ejemplo 1 ###

Encuentre la ecuación de la línea tangente a \(x^3-2x^2+x\) en el punto \(x=2\).

1. Diferenciar \(x^3-2x^2+x\) con respecto a \(x\).
2. Evaluar su derivada en \(x=2\).
3. Desde aquí, encontrar la ecuación de la línea tangente. \(y=...\)

Dado que las tres partes se refieren a un polinomio, si se usan preguntas generadas aleatoriamente, entonces cada una
de estas partes necesita referenciar a una sola ecuación genrada aleatoriamente. Por consiguiente, las partes 1.-3. realmente forman
un ítem. Tenga en cuenta aquí que la parte 1. es independiente de las otras. La parte 2. requiere tanto el primero como el segundo ingreso.
La parte 3. podría fácilmente ser calificada independientemente, o tomar en cuenta las partes 1 y 2. Tenga en cuenta también que el profesor puede elegir otorgar puntuación  "en adelante" .

### Ejemplo 2 ###

Considere la siguiente pregunta, que se hace a estudiantes de esuela relativamente jóvenes.

Expandir \((x+1)(x+2)\).

En el contexto en el que se usa, es apropiado proporcionarle a los estudiantes la
oportunidad de "llenar los huecos", en la ecuación siguiente.

    (x+1)(x+2) = [?] x2 + [?] x + [?].

Nosostros discutimos que esto es realmente "una pregunta" con "tres entradas".
Aun más, es probable que el profesor querrá que los estudiantes completen todas las cajas
antes de asignar cualquier retroalimentación, aun y cuando sea generada retroalimentación separada para cada entrada
(por ejemplo, coeficiente). Esta retroalimentación debería de estar toda agrupada en un sitio en la pantalla. Más aun,
para identificar las causas posibles de errores algebraicos, un procedimiento de calificación automático
necesitará todos los coeficientes simultáneamente. No es satisfactorio el tener tres procedimientos de calificación
totalmente independientes.

Estos dos ejempos ilustran dos posiciones extremas.

1. Todas las entradas dentro de un único ítem multi-parte pueden ser valoradas independientemente.
2. Todas las entradas dentro de un ítem multi-parte deben ser completadas antes de que el ítem pueda ser valorado.

El desarrollar preguntas multi-parte que satisfagan estas dos posiciones extremas debería de ser relativamente sencillo.
Sin embargo, es más común el tener preguntas multi-parte que están entre estos extremos, como en el caso de nuestro primer ejemplo.

### Procesamiento de la respuesta ###

El procesamiento de la respuesta es el medio por el cual la respuesta de un estudiante es evaluada y la retroalimentación, de varias formas,
es asignada. La observación crucial en STACK es una separación completa entre dos componentes importantes.

1. una lista de [entradas](Inputs.md);
2. una lista de [árboles de respuesta potencial](Potential_response_trees.md).

## [Entradas](Inputs.md) ##

El [texto de la pregunta](CASText.md#question_text), por ejemplo, el texto que de hecho es mostrado al estudiante, puede tener un número arbitrario de [entradas](Inputs.md). Un elemento puede estar posicionado
en cualquier lugar dentro del texto de la pregunta, incluyendo adentro de expresiones matemáticas, por ejemplo,  ecuaciones (_nota_: MathJax actualmente no soporta elementos de forma adentro de ecuaciones).
Cada entrada estará asociada con un número de campos. Por ejemplo

1. El nombre de una variable CAS a la cual es asignada la respuesta del estudiante (si hubiera) durante el procesamiento de la respuesta.
   Esto podría ser asignado automáticamente, por ejemplo, en orden `ans1`, `ans2`, etc. Cada variable es conocida como una variable respuesta.
2. El tipo de la entrada. Ejemplos incluyen
  1. entrada algebraica lineal directa, por ejemplo `2*e^x`.
  2. herramienta de entrada gráfica, por ejemplo, un deslizador.
  3. Selección Falso/Verdadero.
3. La respuesta correcta del profesor.

## [Árboles de respuesta potencial](Potential_response_trees.md) ##

Un árbol de respuesta potencial (técnicamente una gráfica dirigida acíclica) consiste de un número arbitrario de nodos enlazados
que llamamos respuestas potenciales. En cada nodo dos expresiones son comparadas usando una Prueba de Respuesta especificada,
y el resultado es, o VERDADERO o FALSO. Una rama correspondiente del arbol tiene la oportunidad para

1. Ajustar el puntaje, (por ejemplo, asignar un valor, añadir o restar un valor);
2. Añadir retroalimentación escrita específicamente para el estudiante;
3. Generar una "Nota de respuesta", usada por el profesor para valoración evaluativa;
4. Nominar al siguiente nodo, o terminar el proceso.

Cada pregunta tendrá cero o más árboles de respuesta potencial. Para cada árbol de respuesta potencial habrá lo siguiente.

1. Un número máximo de puntos disponibles, por ejemplo, puntaje.
2. Una lista de cuales variables de respuesta son necesarias para este árbol de respuesta. Solamente cuando un estudiante ha
   proporcionado uan respuesta **válida** a todos los elementos en esta lista será recorrido el árbol y se asignarán los resultados.
3. Una colección de variables de respuesta potencial, que pueden depender de las variables de respuesta relevantes, variables de pregunta y así sucesivamente. Estos son evaluados antes de que sea recorrido el árbol de respuesta potencial.
4. Un punto nominado en la pregunta misma dentro del cual es insertada la retroalimentación.
   Esta retroalimentación será el puntaje, y la retroalimentación textual generada por este árbol.

El permitir cero árboles de respuesta potencial es necesario para incluir una pregunta de encuesta la cual no es evaluada automáticamente. Una entrada que no es usada en cualquier árbol de respuesta potencial es
por lo tanto tratada como una encuesta y simplemente es grabada.

Para ilustrar preguntas matemáticas multi-parte, nosotros empezamos por escribir un ejemplo. Nosotros asumimos que Usted apenas ha estudiado la [guía de inicio rápido de autoría](Authoring_quick_start.md) por lo que esta está algo abreviada.

## Escribiendo una pregunta multi-parte ##

Comience con uan nueva pregunta STACK, y dele un nombre a la pregunta, por ejemplo "Líneas tangentes".  Esta pregunta tendrá tres partes.  Comience por copiar las variables de la pregunta y el texto de la pregunta como sigue. Tenga en cuenta que nosotros no hemos incluido ninguna aleatorización, pero hemos usado nombres de variables en el comienzo para facilitar esto.

__Variables de pregunta:__

     p:x^3-2*x^2+x;
     pt:2;
     ta1:diff(p,x);
     ta2:subst(x=pt,diff(p,x));
     ta3:remainder(p,(x-pt)^2);

__Texto de pregunta__

Este texto debería de ser copiado dentro del editor en modo HTML.

<textarea readonly="readonly" rows="5" cols="120">
<p>Encontrar la ecuación de la línea tangente a {@p@} en el punto \(x={@pt@}\).</p>
<p>1. Diferenciar {@p@} con respecto a \(x\). [[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</p>
<p>2. Evaluar su derivada en \(x={@pt@}\). [[input:ans2]] [[validation:ans2]] [[feedback:prt2]]</p>
<p>3. A PARTIR DE ALLÍ, encontrar la ecuación de la línea tangente. \(y=\)[[input:ans3]] [[validation:ans3]] [[feedback:prt3]]</p>
</textarea>

LLene la respuesta para `ans1` (la cual existe por defecto) y quite la marca (tag) de `feedback` (retroalimentación) de la sección de  "retroalimentación específica". Nosotros elegimos incluir la retroalimentación Adentro de las partes de esta pregunta.
Tenga en cuenta que hay un árbol de respuesta potencial para cada "parte".

Actualice EL formato y entonces asegúrese de que las Respuestas Modelo  (Model Answers) estén llenadas como`ta1`, `ta2` y `ta3`.

STACK ha creado los tres árboles de respuesta potencial al detectar las marcas (tags) de retroalimentación automáticamente. A continuación necesitamos editar los árboles de respuesta potencial. estos establecerán las propiedades de las respuestas del estudiante.

###Etapa 1: obtenEr una pregunta funcional###

La primera etapa es incluir los árboles de respuesta potencial más simples. Estos simplemente asegurarán que las respuestas sean "correctas".  En cada árBol de respuestA potencial, asegúrese de que  \(\mbox{ans}_i\) es algebraicamente equivalente a \(\mbox{ta}_i\), para \(i=1,2,3\).  En esta etapa nosotros tenemos una pregunta funcional. Guárdela y previsualice la pregunta. Para referencia, las respuestas correctas son

     ans1 = 3*x^2-4*x+1
     ans2 = 5
     ans3 = 5*x-8

###Etapa 2: continuar con la calificación###

A continuación implementaremos una calificación siguiente simple.

Observe cuidadosamente la parte 2.  Esta no pide la "respuesta correcta" sino solamente pide que el estudiante haya evaluado la expresión en La parte 1 correctamente en el punto correcto.  Así, la primera tarea es establecer esto apropiadamente al evaluar la respuesta dada en la primera parte, y compararla con la segunda parte.  Actualice EL nodo 1 de `prt2` para establecer lo siguiente.

    AlgEquiv(ans2,subst(x=pt,ans1))

A continuación, añada un solo nodo (a `prt2`), y asegúrese de que este nodo establezca que

    AlgEquiv(ans1,ta1)

Ahora nosotros enlazamos la rama verdadera del nodo 1 al nodo 2 (de `prt2`).  Ahora nosotros tenemos tres resultados .

Nodo 1: ¿evaluaron la expresión en la parte 1 correctamente? Si fuera "si" entonces vaya al nodo 2, de lo contrario  si fuera "no" entonces termine sin puntaje.

Nodo 2: ¿tuvieron correcta la parte 1?  si fuera "si" entonces esta es la situación ideal, puntaje completo.  Si fuera"no" entonces elegir puntaje, conforme convenga a su gusto en esta situación, y añada alguna retroalimentación como la siguiente en el Nodo 2, retroalimentación para falso.

<textarea readonly="readonly" rows="5" cols="120">
Su respuesta para esta parte es correcta; ¡aunque sin embargo Usted ha tenido la parte 1 equivocada! ¡ Por favor, intente nuevamente ambas partes!
</textarea>

###Etapa 3: añadiendo pruebas de pregunta###

Probalemente sea muy apropiado el añadir pruebas de pregunta. Desde la ventana para previsualizar pregunta, haga click en el enlace para  `Pruebas de pregunta y versiones desplegadas` en la parte superior derecha de la página.

Añada una prueba para su pregunta que contenga las respuestas correctas, como sigue.

     ans1 = 3*x^2-4*x+1
     ans2 = 5
     ans3 = 5*x-8

Los puntajes deberían de ser todos "1" y las notas de respuesta como siguen.

     prt1 = prt1-1-T
     prt2 = prt2-2-T
     prt3 = prt3-1-T


Ahora añada una nueva prueba de respuesta para revisar la siguiente calificación de seguimiento.  Por ejemplo, haga el error siguiente en la parte 1, pero úsela correctamente en la parte 2.

     ans1 = 3*x^2-4
     ans2 = 8
     ans3 = y=8*x-8

Los puntajes deberían de ser todos "0" y las notas de respuesta como sigue.

     prt1 = prt1-1-F
     prt2 = prt2-2-F
     prt3 = prt3-1-F

Cuando Usted corra las pruebas, Usted puede también ver la retroalimentación para confirmar que el sistema está dando el tipo de retroalimentación que Usted quiere para estos tipos de errores.

###Etapa 4: Pregunta aleatoria###

A continuación podemos añadir un polinomio generado aleatoriamente a la pregunta.  Debido a que nosotros usamos nombres de variables en toda la pregunta desde el inicio, esto debería de ser un asunto simplemente de redefinir el valor de `p` en las variables de la pregunta como sigue.

    p : (2+rand(3))*x^3+(2+rand(3))*x^2+(2+rand(3))*x;

Usted necesitará añadir una nota de pregunta no-vacía para habilitar el agrupamiento de versiones aleatorias. Por ejemplo, la siguiente cadena de caracteres será suficiente.

    {@p@}

Ahora nosotros necesitamos actualizar las pruebas de pregunta para reflejar esto. En la primera prueba, Usted es libre para usar `ta1` etc para especificar las respuestas correctas.

En la segunda prueba, Usted podría tal vez dejar el texto como está.

# Pasos siguientes #

* Tal vez le agradaría ver la entrada para [retroalimentación](Feedback.md).
* El control de calidad y las pruebas de su pregunta pueden hacerse más fáciles al ver [pruebas](Testing.md).
* Tal vez le agradaría ver más información sobre [Maxima](../CAS/index.md), particularmente la documentación de Maxima si Usted no está familiarizado con la sintaxis de Maxima y los nombres de funciones. Una interfaz gráfica de Maxima como [wxMaxima](http://andrejv.github.com/wxmaxima/) también podría ser muy útil para encontrar fácilmente los comandos apropiados de Maxima.

La siguiente parte de la guía de inicio rápido tratará sobre [desactivar la simplificación](Authoring_quick_start_3.md).

