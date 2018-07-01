# Guía rápida de autoría 1

Evaluación asistida por computadora de trabajos matemáticos en las siguientes fases.

1. [Autoría](../Authoring/index.md)
2. [Pruebas](Testing.md)
3. [Desplegado](Deploying.md)
4. [Reportes](Reporting.md)

Cada uno de estos enlaces contiene instrucciones detalladas. El propósito de esta página es guiarlo a lo largo de un ejemplo simple.

## Introducción ##

El tipo de pregunta STACK para Moodle está diseñado como un vehículo para gestionar preguntas matemáticas. Implícito dentro de esto está una estructura de datos que las representa.
Esta página explica el proceso de fabricación de una pregunta, al trabajar mediante un ejemplo.

Las preguntas son editadas por medio del exámen (cuestionario) de Moodle.  En Moodle, vaya al Banco de preguntas y pídale crear una nueva pregunta STACK.  No se asuste por el hecho de que el formato para editar se vea complicado.

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
Dentro de la respuesta modelo en la respuesta escriba una expresión CAS sintácticamente válida, como por ejemplo.

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
Por defecto, esta marca (tag) es colocada en el campo de Retroalimentación específica (Specific feedback), pero también podría estar colocada en  el texto de la pregunta.

Un árbol de respuesta potencial es una gráfica dirigida acíclica no-vacía de _nodos de respuesta potencial_.  Por defecto, nosotros tenemos un nodo de respuesta potencial, y este nodo es bastante simple.

1. `SAns` es comparada con `TAns` con la prueba de respuesta, posiblemente con una opción.
2. Si `true` (verdadera) entonces ejecutamos la rama `true` (verdadera).
3. Si `false` (falsa) entonces ejecutamos la rama `false` (falsa).

La prueba de respuesta por sí misma en ocasiones produce [retroalimentación](Feedback.md) para el estudiante (que el profesor podría elegir suprimir con la opción quiet).  La prueba de respuesta también produce una [nota de respuesta](Potential_response_trees.md#Answer_note) interna para el profesor, la cual es esencial para Reportar intentos del estudiante más tarde.

Cada rama podría entonces:

* Asignar/actualizar el puntaje.
* Asignar [retroalimentación](Feedback.md) formativa al estudiante.
* Dejar una [nota de respuesta](Potential_response_trees.md#Answer_note) para propósitos de [Reportes](Reporting.md).
* Nominar al nodo de respuesta potencial siguiente, o terminar el proceso `[stop]`.

Mosotros nos referimos a la respuesta del estudiante en cálculos de álgebra de computadora mediante el uso del nombre `ans1`, dado que nosotros le dimos este nombre al ingreso en el texto de la pregunta.  La respuesta modelo era `3*(x-1)^2`.  Actualice  los campos del formato de tal forma que 

     SAns = ans1
     TAns = 3*(x-1)^2
     Answer test = AlgEquiv

Después presione el botón para `[Guardar cambios]`.  Si la pregunta no puede guardarse, revise cuidadosamente por si hubiera errores, corríjalos y guarde nuevamente.

Esto ha creado y guardado una pregunta mínima.  Para recapitular, nosotros :

1. Escribimos la pregunta
2. Escribimos la respuesta modelo
3. Indicamos que queremos establecer que la respuesta del estudiante sea algebraicamente equivalente a la respuesta modelo `3*(x-1)^2`.

A continuación deberíamos de probar nuestra pregunta, al presionar el botón para pre-visualizar del Banco de preguntas.

## Previsualizar la pregunta ##

Asumiendo que no hay errores, Usted podría ahora elegir el enlace para "previsualizar la pregunta" desde el Banco de preguntas de Moodle.
Esto nos lleva a un nuevo formato, donde el profesor puede experimentar con la pregunta.

El examen (cuestionario) de Moodle es muy flexible. Debajo de las opciones para los intentos, asegúrese de que tiene configurado "Como se comportan las preguntas" a "Modo adaptativo". Si fuera necesario "Comience nuevamente con estas opciones".

Intente ingresar

    3*(x-1)^2

dentro de la caja para respuesta.

El comportamiento por defecto para STACK es usar  "validación instantánea".  Esto es, cuando el estudiante termina de teclear, el sistema valida automáticamente su respuesta y proporciona retroalimentación. Si esto no sucede automáticamente, presione el botón para `[Revisar]`.

El sistema primeramente establece la validez sintáctica de esta respuesta.

Presione nuevamente el botón para `[Revisar]`.

El sistema ejecuta el árbol de respuesta potencial y establece si es que su respuesta es algebraicamente equivalente
a la respuesta modelo `3*(x-1)^2`.  A continuación, intente tener la respuesta erronea.  Si su servidor no tiene la  "validación instantánea" activada (una opción administrativa/de la instalación) Usted necesitará enviar cada respuesta dos veces.
Tenga en cuenta que todas sus respuestas son almacenadas en una tabla de intentos.

A nosotros realmente nos gustaría añadir una mejor retroalimentación, por lo que ahora es el momento para editar nuevamente la pregunta. Regrese a la página del Banco de preguntas y haga click en el enlace para editar la pregunta.

## Mejor retroalimentación ##

¿Qué pasaría si el resultado de aplicar la primera prueba de respuesta fuera falso?
Nos gustaría revisar que el estudiante no haya integrado por error, y logramos esto al añadir otro nodo de respuesta potencial.

Cierre la ventana de pre-visualización y edite nuevamente la pregunta. Deslice el cursor hacia abajo hasta el
Árbol de Respuesta Potencial y haga click en  el botón para  `[Añadir otro nodo]` al fondo de
la lista de nodos.

Desde la rama falsa del Nodo 1, cambie el campo para "Siguiente" de forma tal que esté configurado a  `[Nodo 2]`.
Si la primera prueba es falsa, entonces nosotros realizaremos la prueba en el Nodo 2.

Si el estudiante ha integrado, podría haber o no haber añadido una constante de integración.
Si ha añadido tal constante, ¡nosotros no sabemos cual letra ha usado! Así pues, la mejor manera para resolver
este problema es diferenciar su respuesta y compararla a la pregunta.

Actualice el formato de forma tal que el Nodo 2 tenga

    SAns = diff(ans1,x)
    TAns = (x-1)^3
    Answer test = AlgEquiv

Esto nos da la prueba, ¿pero qué pasa con los resultados?

1. En la rama verdadera configure el `score=0`
2. En la rama verdadera configure la retroalimentación a `¡Al parecer Usted ha integrado por error!`

Aquí tome en cuenta que STACK también añade una "nota inteligente para sí mismo" en el campo de la [nota de respuesta](Potential_response_trees.md#Answer_note).
Esto es útil para agruparmiento estadístico de resultados similares cuando la retroalimentación depende de preguntas generadas aleatoriamente,
y respuestas diferentes. Usted tiene algo definido para agruparlo. Esto se discute en [reportes](Reporting.md).

Presione el botón para `[Guardar cambios]` y pre-visualice la pregunta.

## Retroalimentación todavía mejor: la forma de la respuesta ##

Es común que los estudiantes den la respuesta correcta pero usen un método bastante inapropiado.
Por ejemplo, ellos podrían haber expandido el polinomio y así dar la respuesta en una forma no factorizada.
En esta situación, nos gustaría proporcionar una retroalimentación alentadora para explicarle al estudiante lo que ha hecho mal.

Regrese y elija `[Añadir otro nodo]` de forma similar a la anterior.  Después de todo, nosotros necesitamos aplicar otra prueba de respuesta para identificar esto.

Para usar esta respuesta potencial, edite el Nodo 1, y ahora cambie la rama verdadera (true) para hacer que el Nodo siguiente apunte al nuevo Nodo 3.
Si nosotros entramos al Nodo 3, sabemos que el estudiante tiene la respuesta correcta. Solamente necesitamos establecer si está factorizada o no.
Para establecer esto, necesitamos usar usar una [prueba de respuesta](Answer_tests.md) diferente.

Actualice el formato de forma tal que el Nodo 3 tenga

    SAns = ans1
    TAns = 3*(x-1)^2
    Answer test = FacForm
    Test option\s = x
    Quiet = Yes.

La prueba de respuesta FacForm proporciona retroalimentación automáticamente, lo que sería inapropiado aquí.
Nosotros solamente necesitamos ver si es que la respuesta está factorizada. Por eso usamos la opción quiet (que significa silenciosa).
Necesitamos añadir  \(x\) a las "Test option\s" (Opciones de la prueba), para indicar cual variable estamos usando.

Necesitamos asignar resultados.

1. En la rama verdadera (true) configure el `score=1`
2. En la rama falsa configure el `score=1` (bueno, Usted podría estar en desacuerdo aquí, ¡pero eso dependerá de Usted!)
3. En la rama falsa configure la retroalimentación a algo parecido a lo siguiente

<textarea readonly="readonly" rows="3" cols="75">
Su respuesta está sin factorizar. No hay necesidad de expandir la expresión en esta pregunta. Usted puede diferenciar usando la regla de cadena directamente y conservar la respuesta en forma factorizada.</textarea>


Esta nueva retroalimentación puede ser probada al escribir una respuesta expandida; por ejemplo `3*x^2-6*x+3`.

Usted puede continuar y añadir más nodos de respuesta potencial conforme surja la necesidad. Estos pueden probar más errores sutiles
basándose en los errores que comunmente hacen los estudiantes. En cada caso puede usarse una [prueba de respuesta](Answer_tests.md) para hacer un tipo diferente de distinción entre las respuestas.

## Preguntas aleatorias ##

En este punto Usted podría considerar guardarla como una nueva pregunta.

Es común el querer usar números aleatorios en preguntas. Esto es sencillo de hacer, y nosotros
hacemos uso del campo opcional [variables de pregunta](KeyVals.md#Question_variables).

STACK 3 usa la sintaxis de Maximapara tarea, lo cual es inusual.  En particular el caracter de sos puntos `:` es usado para asignarle un valor a una variable. Así es que, para asignarle el valor de `5` a `n` usamos la sintaxis `n:5`.

Modifique las [Variables de pregunta](KeyVals.md#Question_variables) del ejemplo anterior de forma tal que

    p:(x-1)^3;

Después cambie el [texto de pregunta](CASText.md#question_text) a

<textarea readonly="readonly" rows="3" cols="50">
Diferenciar {@p@} con respecto a \(x\).
[[input:ans1]]
[[validation:ans1]]</textarea>

y en las entradas cambie la respuesta modelo a

    diff(p,x)

Observe que ahora hemos definido una variable local `p`, ay hemos usado el valor de esta en el texto de la Pregunta.  La diferencia está entre matemáticas rodeadas por símbolos `\(..\)` y por símbolos `{@..@}`. Todos los campos basados en texto en la pregunta, incluyendo la retroalimentación, son [texto CAS (CASText)](CASText.md).  Esto es HTML adentro del cual se pueden insertar matemáticas.  LaTeX es colocado entre `\(..\)`s, y expresiones CAS (incluyendo las variables de Usted) entre símbolos `{@..@}` apareados.  Hay más información en la documentación específica.   Las expresiones CAS sonevaluadas dentro del contexto de las variables aleatorias y mostradas.

Dado que nosotros hemos usado `{@p@}` aquí, el usuario no verá un \(p\) en la pantalla en donde la pregunta está instanciada, sino que verá el _valor mostrado_ de `p`.

Observe también que en la respuesta modelo hay un comando CAS para diferenciar el valor de  `p` con respecto a `x`.
Esto es necesario para que CAS pueda obtener la respuesta en una pregunta aleatoria.
Ahora Usted necesita ir al árbol de respuesta potencial para usar la variable `p` o `diff(p,x)` (o tal vez alguna otra expresión CAS) como sea apropiada.

Ahora estamos en una posición para generar una pregunta aleatoria. Para hacer esto, modifique las [variables de pregunta](KeyVals.md#Question_variables) para que sean

    n : 2+rand(3);
    p : (x-1)^n;

En este nuevo ejemplo, tenemos una variable extra `n` la cual está definida para que sea un número aleatorio.

Esto es entonces usado para definir la variable `p` la cual a su vez es usada dentro de la pregunta misma.

Al generar preguntas aleatorias en  CAA nosotros hablamos de _números aleatorios_ cuando realmente quisiéramos decir _números pseudo aleatorios_.
Para llevar un registro de c uales números aleatorios son generados par cada usuario, existe un comando especial en STACK,
el cual Usted debería de usar en sustitución del comando aleatorio de [Maxima](../CAS/Maxima.md).

Este es el comando `rand` el cual es un genrador de  "cosas aleatorias" general, vea la página en [generación aleatoria](../CAS/Random.md) para los detalles completos.
Puede ser usado para generar números aleatorios y también para hacer slecciones de una lista.

### La nota de la pregunta ###

La nota de la pregunta le permite al profesor llevar registro de cual versión de la pregunta es proporcionada a cada estudiante.
Dos versiones son la misma si, y solamente si, la [nota de pregunta](Question_note.md) es la misma.
Es por esto que una pregunta aleatoria no puede tener una nota de pregunta vacía.

LLene esto como

    \[ \frac{d}{d{@x@}}{@p@} = {@diff(p,x)@} \]

Es crucial hacer esto ahora dado que las preguntas con `rand()` en las variables de pregunta no pueden tener una nota de pregunta vacía.  Al hacer obligatoriamente esto ahora nosotros evitamos frustración más adelante cuando sería imposible de otra forma el distinguir entre versiones aleatorias de una pregunta.

Edite su pregunta de ensayo, guárdela y pre-visualícela para obtener versiones aleatorias nuevas de la pregunta

### Más aleatorización ###

En este punto Usted podría considerar guardarla como una pregunta nueva.

Como un ejemplo específico de algunas de estas características, intente la pregunta ilustrada debajo.
Esta contiene números aleatorios, y también ejemplos de variables y expresiones seleccionadas de entre una lista.

    n : rand(5)+3;
    v : rand([x,s,t]);
    p : rand([sin(n*v),cos(n*v)]);

Entonces cambie el texto de la pregunta a

<textarea readonly="readonly" rows="3" cols="50">
Diferenciar {@p@} con respecto a {@v@}.
[[input:ans1]]
[[validation:ans1]]</textarea>

Otra vez, necesitamos usar expresiones tales como `diff(p,v)` dentro del árbol de respuesta potencial, e inclusive en un lugar `diff(ans1,v)`.

Elimine el Nodo 3.  Las pruebas para forma factorizada ya no tienen sentido dentro del contexto de esta pregunta.

A menudo es una buena idea el usar variables dentro de la pregunta al principio, aun y cuando no hubiera intención de generar aleatoriamente una pregunta inicialmente. También, conforme la pregunta se va volviendo cada vez más compleja, es un buen hábito el comentar las líneas complicadas dentro del código Maxima en las Variables de pregunta y Variables de retroalimentación, para asegurarnos de que el código sea más fácil de leer para cualquiera que edite la pregunta. Los comentarios se ingresan como sigue: `v : rand([x,s,t]) /* Configurar v aleatoriamente a x, s, o t */`.

Usted también necesitará actualizar la nota de pregunta para que sea

    \[ \frac{d}{d{@v@}}{@p@} = {@diff(p,v)@} \]

## Pruebas de pregunta ##

El probar preguntas es muy tardado y tedioso, pero es importante el asegurarse de que la pregunta funciona. Para ayudarle con este proceso STACK le permite al profesor el definir "pruebas de pregunta".  Estas son el mismo principio que las  "pruebas de unidad" en la ingeniería de software.

Desde la ventana de previsualización de pregunta, haga click en `Pruebas de pregunta y versiones desplegadas` en la parte superior derecha de la página.

Por favor vea la página sobre [pruebas](Testing.md).

¡Por favor asegúrese de que haya eliminado el tercer nodo del árbol de respuesta potencial!  Haga click en `Añadir un nuevo caso de prueba` para añadir una prueba a su pregunta.  LLene la siguiente información

    ans1 = diff(p,v)
    score = 1
    penalty = 0
    answernote = prt1-1-T

El sistema evaluará automáticamente `diff(p,v)` para crear `ans1` y entonces calificar la pregunta usando esta información.  Concordará los resultados actuales con los que Usted especificó. Esto automatiza el proceso de probar.

Usted puede añadir tantas pruebas como piense que sean necesarias; y usualmente es una idea muy razonable el añadir una para cada caso que Usted anticipe. Aquí sería sensato probar si es que el estudiante ha integrado por error.

Si su pregunta usa aleatorización, entonces Usted necesita [instancias desplegadas](Deploying.md) de esta antes de que Usted pueda presentar la pregunta a los estudiantes. Esto es hecho vía la interfaz para desplegar en la parte superior de la página de prueba.

# Siguientes pasos #

A Usted le gustaría ojear las configuracioens del examen de Moodle, creando un examen simple. Esto es, estrictamente hablando, un asunto de Moodle completamente y existen todas las razones para combinar preguntas STACK con otros tipos de pregunta de Moodle. Se incluyen algunas notas muy breves en la [Guía de inicio rápido del examen](Authoring_quick_start_quiz.md).

El tipo de pregunta STACK es muy flexible.

* Usted puede añadir una solución trabajada en la [Retroalimentación general](CASText.md#General_feedback).
* Usted puede cambiar el comportamiento de la pregunta con las [opciones](Options.md)
* Usted puede añadir gráficos a todos los campos [CASText](CASText.md) con el comando [`plot`](../CAS/Maxima.md#plot).
* Usted puede añadir soporte para [múltiples idiomas](Languages.md).

La siguiente parte de la guía de inicio rápido de autoría tratará sobre [preguntas matemáticas multi-parte](Authoring_quick_start_2.md).


