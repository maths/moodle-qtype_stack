# Nota de pregunta

La nota de pregunta es [CASText (TextoCAS)](CASText.md).  La nota de pregunta es usada para decidir si variantes generadas aleatoriamente son la misma o diferentes.

_Dos variantes de pregunta son iguales si y solamente si las notas de pregunta son iguales._

En particular, cuando nosotros generamos estadísticas acerca de los intentos  de estudiantes, nosotros agrupamos los intentos de acuerdo a la igualdad de sus notas de pregunta.
Dos versiones no son necesariamente diferentes si sus [variables de pregunta](KeyVals.md#Question_variables)
son diferentes, y por esta razón una nota es útil.  El profesor necesita elegir qué identifica cada versión única - esto no puede ser automatizado.

El profesor también puede dejar información útil acerca de la respuesta en la nota de la pregunta. Por ejemplo, podría usar una nota como

    \[ \frac{d}{d{@v@}}{@p@} = {@diff(p,v)@} \]

Esto es muy útil, particularmente cuando los estudiantes preguntan acerca de la versión que les fue dada. El profesor solamente necesita ver la nota de la pregunta para obtener tanto la pregunta, como la respuesta.

La nota de la pregunta es usada al [desplegar](Deploying.md) variantes de pregunta.
