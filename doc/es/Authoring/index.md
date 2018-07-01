# Autoría

Evaluación asistida por computadora de trabajos matemáticos en las siguientes fases.

1. [Autoría](../Authoring/index.md),
2. [Pruebas](Testing.md) y
3. [Despliegue](Deploying.md) de preguntas.
4. [Añadir preguntas a un examen (cuestionario)](Quiz.md) y uso por estudiantes.
5. [Reportes](Reporting.md) y análisis estadístico.

Aquellos que sean nuevos en STACK probablemente preferirían la [Guía rápida de autoría](Authoring_quick_start.md).

* [Guía rápida de autoría 1](Authoring_quick_start.md) Una pregunta básica.
* [Guía rápida de autoría 2](Authoring_quick_start_2.md) Preguntas matemáticas multi-partes.
* [Guía rápida de autoría 3](Authoring_quick_start_3.md) Desactivando la simplificación.

También hay [Preguntas de muestra](Sample_questions.md).
Esta página es una referencia para todos los campos en una pregunta.

## Cómo se comportan las preguntas STACK  ##

* Guías para estudiantes acerca de [valoración de la respuesta](../Students/Answer_assessment.md).
* [Proporcionar retroalimentación](Feedback.md).

## Estructura de datos de pregunta STACK  ##

Una `stackQuestion` es el objeto básico en el sistema. De hecho, STACK está diseñado como un vehículo para manejar estas preguntas.
La tabla inferior muestra los campos que componen una pregunta.
El único campo que es obligatorio está puesto en **negritas**.

| Nombre                                                     | Tipo                                                       | Detalles
| ---------------------------------------------------------- | ---------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Nombre                                                     | Meta                                                       | Nombra una pregunta
| [Variables de pregunta](KeyVals.md#Question_variables)     | [Variables de Pregunta](KeyVals.md#Question_variables)     | Estas son variables potencialmente aleatorias que pueden ser usadas para generar una pregunta.
| [Texto de pregunta](CASText.md#question_text)              | [CASText](CASText.md)                                      | Esta es la pregunta que el estudiante realmente ve
| [Retroalimentación general](CASText.md#General_feedback)   | [CASText](CASText.md)                                      | La solución trabajada solamente está disponible después de que un ítem sea cerrado.
| [Nota d epregunta](Question_note.md)                       | [CASText](CASText.md)                                      | Dos versiones de pregunta generadas aleatoriamente son diferentes si, y solamente si, la nota de la pregunta es diferente.  Use este campo para almacenar información útil que distingue las versiones.
| [Entradas](Inputs.md)                                      |                                                            | Las entradas son las cosas, como las cajas de formato, con las cuales el estudiante de hecho interactua.
| [Árboles de respuesta potencial](Potential_response_trees.md)|                                                          | Estos son los algoritmos que establecen las propiedades matemáticas de las respuestas del estudiante y generan retroalimentación.
| [Opciones](Options.md)                                     | Opciones                                                   | Muchos comportamientos pueden ser cambiados con las opciones.
| [Pruebas](Testing.md)                                      |                                                            | Estas son usadas para pruebas automáticas de un ítem y para control de calidad.

# Vea también

* [Pruebas de respuesta](Answer_tests.md),
* [Preguntas Frecuentes](Author_FAQ.md),
* [KeyVals](KeyVals.md)
* [Desplegar versiones de pregunta](Deploying.md)
* Adaptaciones específicas de [Maxima](../CAS/Maxima.md).
* [Importación y exportación](ImportExport.md) de preguntas STACK.
* [Bloques de pregunta](Question_blocks.md)


