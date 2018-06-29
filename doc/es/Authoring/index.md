# Autoríag

evaluación asistida por computadora de trabajos matemáticos en las siguientes dases.

1. [Autoría](../Authoring/index.md),
2. [Pruebas](Testing.md) and
3. [Despliegue](Deploying.md) questions.
4. [Añadir reguntas a un examen (cuestionario)](Quiz.md) y uso por estudiantes.
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

Una `stackQuestion` es el objeto básico en el sistema. de hecho, STACK está diseñado como un vehículo para manejar estas preguntas.
La tabla inferior muestra los campos que componen una pregunta.
El únco campo que es obligatorio está puesto en **negritas**.

| Nombre                                                     | Tipo                                                       | Detalles
| ---------------------------------------------------------- | ---------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Nombre                                                     | Meta                                                       | Nombra una pregunta
| [Variables de pregunta](KeyVals.md#Question_variables)     | [QVariables de Pregunta](KeyVals.md#Question_variables)    | Estas son variables potencialmente aleatorias que pueden ser usadas para generar una pregunta.
| [Question text](CASText.md#question_text)                  | [CASText](CASText.md)                                      | This is the question the student actually sees
| [General feedback](CASText.md#General_feedback)            | [CASText](CASText.md)                                      | The worked solution is only available after an item is closed.
| [Question note](Question_note.md)                          | [CASText](CASText.md)                                      | Two randomly generated question versions are different, if and only if the question note is different.  Use this field to store useful information which distinguishes versions.
| [Inputs](Inputs.md)                                        |                                                            | The inputs are the things, such as form boxes, with which the student actually interacts.
| [Potential response trees](Potential_response_trees.md)    |                                                            | These are the algorithms which establish the mathematical properties of the students' answers and generate feedback.
| [Options](Options.md)                                      | Options                                                    | Many behaviours can be changed with the options.
| [Testing](Testing.md)                                      |                                                            | These are used for automatic testing of an item and for quality control.

# See also

* [Answer tests](Answer_tests.md),
* [Frequently Asked Questions](Author_FAQ.md),
* [KeyVals](KeyVals.md)
* [Deploying question versions](Deploying.md)
* Specific adaptations of [Maxima](../CAS/Maxima.md).
* [Import and Export](ImportExport.md) of STACK questions.
* [Question blocks](Question_blocks.md)


