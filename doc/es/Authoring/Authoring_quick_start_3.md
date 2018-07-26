# Guía rápida de autoría 3: desactivando la simplificación

Esta es la tercera parte de la [guía rápida de autoría](Authoring_quick_start.md).  Se asume que Usted ya ha trabajado la [guía rápida de autoría 1](Authoring_quick_start.md) y la [guía rápida de autoría 2](Authoring_quick_start_2.md). Su propósito es discutir algunos problemas comunes que surgen al escribir preguntas particularmente elementales donde el CAS podría hacer demasiado.

### Pregunta de ejemplo ###

Dado un número complejo \(z=ae^{ib}\) determine \(|z^{n}|\) y \(\arg(z^{n})\).

Donde \(a\), \(b\) y \(n\) son números generados aleatoriamente.

## Simplificación desactivada ##

Es tentador cuando se escriben preguntas como esta el operar al  _nivel de visualización._  Nosotros podríamos generar aleatoriamente \(a\), \(b\) y \(n\) y podríamos insertarlos dentro del texto de la pregunta. Por ejemplo:

     \(\right({@a@}e^{{@b@} i}\left)^{@n@}\)

Lo que estamos haciendo aquí es tratar a todas las variables separadamente, no estamos creando un único objeto CAS para el número complejo. Esto está bien, pero causa problemas y es dificil de leer porque mezcla CAS y LaTeX.

La alternativa es desactivar la simplificación y usar el CAS para representar expresiones más directamente. La siguiente es una única expresión Maxima.

     {@(a*%e^(b*%i))^n@}

Desde luego, nosotros no queremos que Maxima _realmente calcule la potencia_ simplemente ¡que  _la represente!_  Para ver la diferencia, Usted puede copiar lo siguiente dentro de una sesión de escritorio de Maxima.

    kill(all);
    simp:true;
    (3*%e^(%i*%pi/2))^4;
    simp:false;
    (3*%e^(%i*%pi/2))^4;

El resolver problemas al nivel del CAS, y no al nivel de la visualización, es a menudo mejor.    Dígale a STACK que configure `simp:false` en la pregunta, deslícese hacia el fondo del formato y debajo de `Opciones` configure `Simplificar a nivel-de-pregunta` para que sea `No`.

Esto tiene algunos inconvenientes.  Al haber desactivado la simplificación, ¡ahora nosotros necesitamos volver a activarla selectivamente! Para hacer esto, nosotros usamos comandos de Maxima como el siguiente.

    a : ev(2+rand(15),simp);

En particular, nosotros vamos a definir las variables de la pregunta como sigue.

    a : ev(2+rand(15),simp);
    b : ev((-1)^rand(2)*((1+rand(10)))/(2+rand(15)),simp);
    n : ev(3+rand(20),simp);
    q : a*%e^(b*%i*%pi);
    p : ev(mod(b*n,2),simp);

Una alternativa útil cuando necesitan ser simplificadas muchas expresiones consecutivas es usar lo siguiente.

    simp : true;
    a : 2+rand(15);
    b : (-1)^rand(2)*((1+rand(10)))/(2+rand(15));
    n : 3+rand(20);
    simp : false;
    q : a*%e^(b*%i*%pi);
    p : ev(mod(b*n,2),simp);

Las circunstancias particulares dictarán si es que es mejor tener muchas variables y usar la visualización, o si es mejor poner `simp:false` y trabajar con esto.  La dificultad a menudo está con el menos unario.  El insertar números adentro de expresiones tales como `y={@m@}x+{@c@}` si \(c<0\) está en que será visualizada como \(y=3x+-5\), por ejemplo.  Mientras que la simplificación esté desactivada "off", las rutinas de visualización en Maxima (a menudo) trabajarán con el menos unario de una forma sensata.

## La importancia de la nota de pregunta ##

Observe que al definir `b` nosotros tenemos un cociente que bien podría "simplificarse" cuando se cancelen las fracciones.  Por lo tanto, no hay una correspondencia de uno-a-uno entre las variables aleatorias y las versiones reales de la pregunta.  En algunas situaciones podría similarmente no haber una correspondencia uno-a-uno entre los valores de variables específicas y las preguntas reales.  Nosotros no podemos usar los valores de las variables de preguntas como una clave única a las versiones de pregunta (aunque en este caso esto estaría bien porque todas las cancelaciones algebraicas ocurren dentro de la definición de `b` por lo que terminamos con una clave única).

Es por esto que el profesor debe dejar una nota de pregunta que tenga sentido.  Dos versiones de una pregunta son _definidas_ que son la misma si, y solamente si, la nota de pregunta es la misma.

El campo de nota de pregunta es ["texto CAS"](CASText.md), similar al texto de la pregunta.  Nosotros podríamos escribir algo como

    {@[a,b,n]@}

O nosotros podríamos dejar algo que tenga más sentido:

    {@q^n = a^n*(cos(p*%i*%pi)+%i*sin(p*%i*%pi))@}

Tenga en cuenta que, nosotros probablemente no queremos evaluar `a^n` aquí ya que no es probable que sea "más simple".  Depende del profesor, pero el poner la respuesta adentro de la nota de respuesta ayuda si los estudiantes vienen y le piden la respuesta a sus versiones de la pregunta...

## Pregunta multi-parte ##

Esta pregunta tiene dos partes independientes.  Por lo tanto, probablemente necesite dos árboles de respuesta potencial para valorar cada parte.

El texto de la pregunta podría parecerse al siguiente:

    Dado un número complejo \(\displaystyle z={@q@}\) determine
    \( |z^{@n@}|= \) [[input:ans1]] [[validation:ans1]] [[feedback:prt1]]
    y \( \arg(z^{@n@})= \) [[input:ans2]] [[validation:ans2]] [[feedback:prt2]]

quite la marca (tag) `[[feedback:prt1]]` del campo de Retroalimentación específica.  Está colocado aquí por defecto, pero solamente puede ocurrir una vez.

Actualice el formato.  Porque hay dos entradas y dos árboles de respuesta potencial estas serán creadas automáticamente.

Nosotros necesitamos proporcionar respuestas modelo para cada parte.  En términos de nuestras variables de la pregunta,

    ans1 : a^n
    ans2 : p*%pi

## Evaluación de las respuestas ##

Es improbable que el propósito de esta pregunta sea el decidir si el estudiante puede resolver potencias de números enteros. Así que asumiremos que es aceptable el ingresar una respuesta como \(a^b\) para la primera parte, en lugar de calcularla como un número entero. Si la aleatorización fuera más conservadora, este cálculo podría ser un objetivo adicional de la pregunta.

Por lo tanto, para `prt1` llene la siguiente información

    SAns:ans1
    TAns:a^n
    answertest:AlgEquiv

Si Usted realmente quiere probar el número entero, Usted necesita calcular `ev(a^n,simp)` y entonces usar la prueba `EqualComAss` para establecer que el estudiante tiene el número entero correcto.

Para `prt2` nosotros necesitamos establecer que el estudiante tiene el argumento correcto.  Dado que esto es módulo \(2\pi\) nosotros podemos usar las funciones trigonométricas. LLene la siguiente información

    SAns:[cos(ans2),sin(ans2)]
    TAns:[cos(n*b*%pi),sin(n*b*%pi)]
    answertest:AlgEquiv
    quiet:yes

La prueba `AlgEquiv` es feliz de comparar listas, pero no tiene sentido el preguntarle al estudiante cual elemento de la lista es  "incorrecto". De hecho, el hacer eso sería confuso, por lo que hemos seleccionado la opción silenciosa `quiet` para suprimir la retroalimentación de la prueba de respuesta generada automáticamente.

Una vez más, si Usted quiere hacer una prueba para el argumento del principio, Usted necesitará revisar que el valor del estudiante también cae dentro del rango correspondiente usando las pruebas `NUM-GTE` para establecer "mayor o igual a".  Esto puede hacerse al añadir un nodo adicional.  Probablemente sea una idea sensata el proporcionar retroalimentación en ambas propiedades aquí. La variable `p` en las variables de pregunta ayudará con esto.

## Pruebas de pregunta ##

¡Por favor, cree algunas preubas de pregunta!  Esto ahorrará tiempo a la larga, al permitirle a Usted el probar automáticamente su pregunta para cada versión aleatoria que desea desplegar.  Usted debería crear un caso de prueba para cada resultado que espere. Por lo tanto, necesitamos

    ans1:a^n
    ans2:n*b*%pi

como las dos respuestas correctas, y luego respuestas incorrectas para asegurarnos que estas sean atrapadas.  Si Usted ha impuesto la _forma_ de la respuesta, por ejemplo _representación de entero_ para `ans1` y _argumento principal_ para `ans2`, Usted necesita añadir pruebas para distinguir entre estas.  Para la primera parte \(a^n\) y el número entero que representa, por ejemplo `ev(a^n,simp)`.  Para la segunda parte entre \(b\times n\) y la variable `q`.

## Retroalimentación general ##

La retroalimentación general (previamente conocida como la solución trabajada) puede mostrar algunos de los pasos en el desarrollo. Por ejemplo,

    Es razonable que las leyes de los índices debrían de aplicar también.  Esto es llamado el Teorema De Moivre.
    \[ {@q^n@} ={@a^n@} e^{@b*n*%i*%pi@}.\]
    Recuerde que
    \[ e^{i\theta} = \cos(\theta)+i\sin(\theta).\]
    Trabajando con el argumento del principio \( 0\leq \theta \leq 2\pi \) nos da
    \[ {@q^n@} = {@a^n@} e^{@b*n*%i*%pi@} = {@a^n@} e^{@ev(b*n,simp)*%i*%pi@} = {@a^n@} e^{@p*%i*%pi@}.\]

# Pasos siguientes #

Se dan más ejemplos en la página sobre [matrices](../CAS/Matrix.md).

El XML de esta pregunta está incluido junto con las [preguntas de muestra](Sample_questions.md).  Por favor vea las otras [preguntas de muestra](Sample_questions.md) que son distribuidas con STACK para más ejemplos.

