<?php
// This file is part of Stack - http://stack.bham.ac.uk//
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.



/**
 * Strings for component 'qtype_stack', language 'es', branch 'MOODLE_23_STABLE'
 *
 * @package    qtype
 * @subpackage stack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'STACK';
$string['pluginname_help'] = 'STACK es un sistema de evaluación en matemáticas.';
$string['pluginnameadding'] = 'Agregando una pregunta STACK';
$string['pluginnameediting'] = 'Editando una pregunta STACK';
$string['pluginnamesummary'] = 'STACK provee preguntas matemáticas para cuestionarios en moodle. Utiliza un sistema de álgebra computacional para establecer las propiedades matemáticas en las respuestas de estudiantes.';

// General strings.
$string['errors'] = 'Errores';
$string['debuginfo'] = 'Información de depurado';
$string['exceptionmessage'] = '{$a}';

// Strings used on the editing form.
$string['addanothernode'] = 'Agregar otro nodo';
$string['answernote'] = 'Nota de respuesta';
$string['answernote_err'] = 'Nota de respuesta no deben contener el carácter |. Este carácter es insertado por STACK y luego usado para dividir automaticamente notas de respuesta.';
$string['answernote_help'] = 'Es una etiqueta clave para los informes. Está diseñada para registrar ruta única a través del árbol y el resultado de cada prueba de respuesta. Esto se genera automáticamente, pero se puede cambiar si tiene sentido.';
$string['answernote_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Potential_response_trees.md#Answer_note';
$string['answernotedefaultfalse'] = '{$a->prtname}-{$a->nodename}-F';
$string['answernotedefaulttrue'] = '{$a->prtname}-{$a->nodename}-T';
$string['answernoterequired'] = 'Nota de respuesta no debe estar vacío.';
$string['assumepositive'] = 'Asumir positivo';
$string['assumepositive_help'] = 'Esta opción habilita la variable assume_pos de Maxima.';
$string['assumepositive_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#Assume_Positive';
$string['autosimplify'] = 'Autosimplificar';
$string['autosimplify_help'] = 'Fija la variable "simp" de Maxima para el arbol de respuestas potenciales.';
$string['autosimplify_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Maxima.md#Simplification';
$string['boxsize'] = 'Tamaño de la caja';
$string['boxsize_help'] = 'Ancho de la forma html de entrada.';
$string['boxsize_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Box_Size';
$string['checkanswertype'] = 'Comprobar tipo';
$string['checkanswertype_help'] = 'Si es verdadero, las respuestas de diferente tipo (p.e. expresión, ecuación, matriz, lista, conjunto) son evaludadas como invalidas.';
$string['checkanswertype_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Check_Type';
$string['complexno'] = 'Visualización de sqrt(-1)';
$string['complexno_help'] = 'Controla el significado y visualización del simbolo i y sqrt(-1)';
$string['complexno_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#sqrt_minus_one.';
$string['defaultprtcorrectfeedback'] = 'Respuesta correcta, bien hecho.';
$string['defaultprtincorrectfeedback'] = 'Respuesta incorrecta.';
$string['defaultprtpartiallycorrectfeedback'] = 'Respuesta parcialmente correcta.';
$string['branchfeedback'] = 'Retroalimentación de la rama del nodo';
$string['branchfeedback_help'] = 'Es un CASText que puede depender de variables de pregunta, elementos de entrada ó variables de retroalimentación. Se evalúa y se muestra al estudiante si pasan por esta rama.';
$string['inputtest'] ='Prueba de entrada';
$string['falsebranch'] = 'Rama falsa';
$string['falsebranch_help'] = 'Este campo controla la prueba a la respuesa cuando es falsa
### Modo y Calificación
Como es ajustada la calificación. = significa una calificación especifica, +/- significa sumar o restar calificación de la calificación total.
 
### Penalización
Es la penalización acumulativa en el modo adaptivo o interactivo.

### Siguiente
Salta a cualquier otro nodo, o se detene.

### Nota de respuesta
Es una etiqueta para propósitos de reporte. Esta diseñada para guardar la ruta única a través del árbol y el resultado de cada prueba de respuesta. Es automaticamente generado, pero se puede cambiar si es necesario.
';
$string['feedbackvariables'] = 'Variables de retroalimentación';
$string['feedbackvariables_help'] = 'Las variables de retroalimentación permiten manipular cualquiera de las entradas, junto con las variables de pregunta, antes de atravesar el árbol. Las variables definidas aquí pueden ser utilizadas en cualquier otro lugar del árbol.';
$string['feedbackvariables_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/KeyVals.md#Feedback_variables';
$string['fieldshouldnotcontainplaceholder'] = '{$a->field} no debería contener [[{$a->type}:...]] marcador.';
$string['forbidfloat'] = 'Prohibir flotantes';
$string['forbidfloat_help'] = 'Si es verdadero, entonces cualquier respuesta del estudiante que contenga números de punto flotante será invalida.';
$string['forbidfloat_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Forbid_Floats';
$string['forbidwords'] = 'Palabras prohibidas ';
$string['forbidwords_help'] = 'Es una lista separada por comas de cadenas de texto que serán prohibidas como respuesta de estudiantes.';
$string['forbidwords_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/CASText.md#Forbidden_Words';
$string['generalfeedback'] = 'Retroalimentación general';
$string['generalfeedback_help'] = 'Retroalimentación general es un CASText. También conocida como "solución desarrollada", está se muestra al estudiante después de haber intentado resolver la pregunta. La retroalimentación general se muestra a todos los estudiantes, a diferencia de la retroalimentación que depende de la respuesta que el alumno ingreso. Esta retroalimentación general también puede depender de las variables de la pregunta.';
$string['generalfeedback_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/CASText.md#general_feedback';
$string['showvalidation'] = 'Mostrar validación';
$string['showvalidation_help'] = 'Al establecer esta opción se muestra cualquier retroalimentación para la entrada, incluyendo repetir las expresiones en la notación tradicional de dos dimensiones.';
$string['showvalidation_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Show_validation';
$string['htmlfragment'] = 'Parece que tienes elementos HTML en la expresión.';
$string['illegalcaschars'] = 'Los carácteres @ y \$ no están permitidos en la entrada CAS.';
$string['inputheading'] = 'Entrada: {$a}';
$string['inputtype'] = 'Tipo de entrada';
$string['inputtype_help'] = 'Determina el tipo del elemento de entrada, formas como Algebraica, Verdadero/Falso, Área de texto.';
$string['inputtype_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md';
$string['inputtypealgebraic'] = 'Algebraica';
$string['inputtypeboolean'] = 'Verdadero/Falso';
$string['inputtypedropdown'] = 'Menu desplegable';
$string['inputtypesinglechar'] = 'Un solo carácter';
$string['inputtypetextarea'] = 'Área de texto';
$string['inputtypematrix'] = 'Matriz';
$string['insertstars'] = 'Insertar asteriscos';
$string['insertstars_help'] = 'Si es verdadero entonces el sistema insertará automaticamente asteriscos dentro de cualquier patrón dentro de Sintaxis extricta. En otro caso, se mostrá un error';
$string['insertstars_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Insert_Stars';
$string['multiplicationsign'] = 'Signo de multiplicación';
$string['multiplicationsign_help'] = 'Controla como se mostrará el signo de multiplicación.';
$string['multiplicationsign_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#multiplication';
$string['multcross'] = 'Cruz';
$string['multdot'] = 'Punto';
$string['mustverify'] = 'Verificar respuesta';
$string['mustverify_help'] = 'Especifica si la respuesta del estudiante se muestra antes de ser calificada.';
$string['mustverify_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Student_must_verify';
$string['next'] = 'Siguiente';
$string['nextcannotbeself'] = 'Un nodo no puede ligarse a sí mismo con el siguiente nodo.';
$string['nodehelp'] = 'Nodo del álbol de respuesta';
$string['nodehelp_help'] = '### Prueba
Es usada para comparar de expresiones y establecer si satifacen algún criterio matemático.

### SAns
Es el primer argumento para la función que compara la respuesta. Es un prueba asimétrica que debe considerarse como "la respuesta del alumno" aunque puede ser cualquier expresión CAS válida, y también puede depener de las variables de la pregunta o variables de la retroalimentación.

### TAns
Es el segundo argumento de la función que compara la respuesta. Es un prueba asimétrica que debe considerarse como "la respuesta del alumno" aunque puede ser cualquier expresión CAS válida, y también puede depener de las variables de la pregunta o variables de la retroalimentación.

### Opciones
Este campo permite habilitar que la función que compara la respuesta acepte opciones, p.e. variables o preciones numericas.

### Silencio
Cuando es habilitado, cualquier retroalimentación automaticamente generada por la función que compara la respuesta, es suprimida, y no es mostrada al estudiante. Los campos de retroalimentiación en las ramas del árbol no son afectados por esta opción.
';
$string['nodeloopdetected'] = 'Se ha detectado un ciclo en este PRT.';
$string['nodenotused'] = 'No hay otros nodos en el PRT que enlazan a este nodo.';
$string['nodex'] = 'Nodo {$a}';
$string['nodexdelete'] = 'Borrar nodo {$a}';
$string['nodexfalsefeedback'] = 'Retroalimentación rama falsa';
$string['nodextruefeedback'] = 'Retroalimentación rama verdadera';
$string['nodexwhenfalse'] = 'Si la prueba es falsa';
$string['nodexwhentrue'] = 'Si la prueba es verdadera';
$string['nonempty'] = 'No debe estar vacío.';
$string['penalty'] = 'Penalización';
$string['penalty_help'] = 'El sistema de penalización descuenta este valor del resultado de cada PRT para cada intento diferente y valido que no fué completamente correcto.';
$string['penalty_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Feedback.md';
$string['penaltyerror'] = 'La penalización es un valor númerico entre 0 y 1.';
$string['penaltyerror2'] = 'La penalización debe estar en blanco, o ser un valor númerico entre 0 y 1.';
$string['prtcorrectfeedback'] = 'Retroalimentación standard cuando es correcto';
$string['prtheading'] = 'Árbol de respuestas potenciales: {$a}';
$string['prtincorrectfeedback'] = 'Retroalimentación standard cuando es incorrecto';
$string['prtpartiallycorrectfeedback'] = 'Retroalimentación standard cuando es parcialmente correcto';
$string['prtwillbecomeactivewhen'] = 'Este árbol de respuestas potenciales se activará cuando el estudiante haya contestado: {$a}';
$string['questionnote'] = 'Notas de la pregunta';
$string['questionnote_help'] = 'Notas de la pregunta es un CASText. Su propósito es distiguir entre las versiones aleatorias de una pregunta. Dos versiones de una pregunta serán iguales si y solo si las notas de la pregunta son iguales.  Para un análisis posterior es muy importante crear una buena nota de pregunta.';
$string['questionnote_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_note.md';
$string['questionnotempty'] = 'Notas de la pregunta no puede estar vacío cuando rand() aparece en las variables de la pregunta. Notas de la pregunta es usada para distiguir entre dos diferentes versiones aleatorias de una pregunta.';
$string['questionsimplify'] = 'Simplificar a nivel de pregunta';
$string['questionsimplify_help'] = 'Establece la variable global "simp" de Maxima para toda la pregunta.';
$string['questionsimplify_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Maxima.md#Simplification';
$string['questiontext'] = 'Texto de la pregunta';
$string['questiontext_help'] = 'El texto de la pregunta es un CASText. Esta es la "pregunta" que el estudiante realmente ve. Debes poner los elementos de entrada, y las cadenas de validación en este campo y sólo en este campo. Por ejemplo, usar `[[input:ans1]] [[validation:ans1]]`.';
$string['questiontext_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/CASText.md#question_text';
$string['questiontextmustcontain'] = 'EL texto de la pregunta debería contener el símbolo \'{$a}\'.';
$string['questiontextnonempty'] = 'El texto de la pregunta no debe estar vacío.';
$string['questiontextonlycontain'] = 'El texto de la pregunta solo debe contener el símbolo \'{$a}\' una vez.';
$string['questiontextfeedbackonlycontain'] = 'El texo de la pregunta en combinación con la retroalimentación especifica sólo debe contener la palabra \'{$a}\' una véz.';
$string['questionvalue'] = 'Valor de la pregunta';
$string['questionvaluepostive'] = 'El valor de la pregunta debe ser positivo';
$string['questionvariables'] = 'Variables de la pregunta';
$string['questionvariables_help'] = 'Este campo permite definir y manipular variables CAS, p.e. crear versiones aleatorias. Las variables están disponibles en todas las partes de la pregunta.';
$string['questionvariables_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/KeyVals.md';
$string['quiet'] = 'Silencio';
$string['quiet_help'] = 'Cuando es habilitado, cualquier retroalimentación automaticamente generada por la función que compara la respuesta, es suprimida, y no es mostrada al estudiante. Los campos de retroalimentiación en las ramas del árbol no son afectados por esta opción.';
$string['requiredfield'] = 'Este campo es requerido!';
$string['requirelowestterms'] = 'Requerir mínima expresión';
$string['requirelowestterms_help'] = 'Cuando esta opción es Si, cual quier coeficiente o números racionales dentro de una expresión, deberán ser escritos en su mínima expresión. De otra manera la respuesta será rechazada como inválida.';
$string['requirelowestterms_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Require_lowest_terms';
$string['sans'] = 'SAns';
$string['sans_help'] = 'Es el primer argumento para la función que compara la respuesta. Es un prueba asimétrica que debe considerarse como "la respuesta del alumno" aunque puede ser cualquier expresión CAS válida, y también puede depener de las variables de la pregunta o variables de la retroalimentación.';
$string['sans_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['sansinvalid'] = 'SAns es invalida: {$a}';
$string['sansrequired'] = 'SAns no debe estar vacía.';
$string['stop'] = '[Detener]';
$string['score'] = 'Calificación';
$string['scoreerror'] = 'La calificación debe ser un número entre 0 y 1.';
$string['scoremode'] = 'Modo';
$string['specificfeedback'] = 'Retroalimentación especifica';
$string['specificfeedback_help'] = 'Por default, la retroalimentación de cada álbol de posibles respuestas se muestra en este bloque. Esto pude moverse al texto de la pregunta, pero en este caso Moodle tendrá menos control sobre lo mostrado por diferentes comportamientos. Nota que este bloque no es un CASText.';
$string['specificfeedbacktags'] = 'La retroalimentiación específica no debe contener las palabra(s) \'{$a}\'.';
$string['sqrtsign'] = 'Radical para la raíz cuadrada';
$string['sqrtsign_help'] = 'Controla como se muestran los numeros irracionales.';
$string['sqrtsign_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#surd';
$string['strictsyntax'] = 'Sintaxis estricta';
$string['strictsyntax_help'] = '¿La entrada debe ser usando la sintaxis estricta de Maxima? Si no, esto incrementa la gama de patrones que indican la falta de astericos, incluyendo el uso de funciones y notación científica en la entrada.';
$string['strictsyntax_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Strict_Syntax';
$string['strlengtherror'] = 'Esta cadena no debe exceder 255 carácteres de tamaño.';
$string['syntaxhint'] = 'Sugerir sintaxis';
$string['syntaxhint_help'] = 'Cuando el estudiante deje en blanco el campo de respuesta aparecerá el contenido sugerido en esta caja.';
$string['syntaxhint_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Syntax_Hint';
$string['tans'] = 'TAns';
$string['tans_help'] = 'Es el segundo argumento de la función que compara la respuesta. Es un prueba asimétrica que debe considerarse como "la respuesta del alumno" aunque puede ser cualquier expresión CAS válida, y también puede depener de las variables de la pregunta o variables de la retroalimentación.';
$string['tans_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['tansinvalid'] = 'TAns es invalida: {$a}';
$string['tansrequired'] = 'TAns no debe esta vacía.';
$string['teachersanswer'] = 'Respuesta modelo';
$string['teachersanswer_help'] = 'El profesor debe especificar un modelo de respuesta para cada entrada. Este modelo debe ser una expresión de Maxima, y puede ser construida usando las variables de la pregunta.';
$string['teachersanswer_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#model_answer';
$string['testoptions'] = 'Opciones';
$string['testoptions_help'] = 'Este campo permite habilitar que la función que compara la respuesta acepte opciones, p.e. variables o preciones numericas.';
$string['testoptions_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['testoptionsinvalid'] = 'Las opciones de la prueba son invalidas: {$a}';
$string['testoptionsrequired'] = 'Las opciones de la prueba son requeridas para esta prueba.';
$string['truebranch'] = 'Rama verdadera';
$string['truebranch_help'] = 'Este campo controla la prueba a la respuesa cuando es verdadera
### Modo y Calificación
Como es ajustada la calificación. = significa una calificación especifica, +/- significa sumar o restar calificación de la calificación total.
 
### Penalización
Es la penalización acumulativa en el modo adaptivo o interactivo.

### Siguiente
Salta a cualquier otro nodo, o se detene.

### Nota de respuesta
Es una etiqueta para propósitos de reporte. Esta diseñada para guardar la ruta única a través del árbol y el resultado de cada prueba de respuesta. Es automaticamente generado, pero se puede cambiar si es necesario.
';
$string['variantsselectionseed'] = 'Grupo aleatorio';
$string['variantsselectionseed_help'] = 'Normalmente puedes dejar esta caja en blanco. Desde luego, si quieres dos diferentes preguntas en un cuestionario que usen la misma semilla aleatoria, entonces escribe la misma cadena en esta caja para las dos preguntas (e implementa el mismo conjunto de semillas aleatorias, si estas usando versiones implementadas) y entonces las semillas aleatorias para estas dos preguntas serán sincronizadas.';
$string['verifyquestionandupdate'] = 'Verificar el texto de la pregunta y actualizar formulario';

// Strings used by input elements.
$string['booleangotunrecognisedvalue'] = 'Entrada invalida.';
$string['dropdowngotunrecognisedvalue'] = 'Entrada invalida.';
$string['pleaseananswerallparts'] = 'Por favor, responde todas las partes de la pregunta.';
$string['pleasecheckyourinputs'] = 'Por favor, verifica que tu respuesta fue interpretada como se esperaba.';
$string['singlechargotmorethanone'] = 'Solo puedes introducir un carácter aquí.';

// Admin settings.
$string['settingajaxvalidation'] = 'Validación instantánea';
$string['settingajaxvalidation_desc'] = 'With this setting turned on, the students current input will be validated whenever they pause in their typing. This gives a better user experience, but is likely to increase the server load.';
$string['settingcasdebugging'] = 'Depurado CAS ';
$string['settingcasdebugging_desc'] = 'Whether to store debugging information about the CAS connection.';
$string['settingcasmaximaversion'] = 'Versión de Maxima';
$string['settingcasmaximaversion_desc'] = 'The version of Maxima being used.';
$string['settingcasresultscache'] = 'CAS result caching';
$string['settingcasresultscache_db'] = 'Cache in the database';
$string['settingcasresultscache_desc'] = 'This setting determines whether calls the to CAS are cached. This setting should be turned on unless you are doing development that involves changing the Maxima code. The current state of the cache is shown on the healthcheck page.  If you change your settings, e.g. the gnuplot command, you will need to clear the cache before you can see the effects of these changes.';
$string['settingcasresultscache_none'] = 'Do not cache';
$string['settingcastimeout'] = 'CAS connection timeout';
$string['settingcastimeout_desc'] = 'The timout to use when trying to connect to Maxima.';
$string['settingplatformtype'] = 'Platform type';
$string['settingplatformtype_desc'] = 'Stack needs to know what sort of operating system it is running on. The Server and MaximaPool options give better performance at the cost of having to set up an additional server. The option "Linux (optimised)" is explained on the Optimising Maxima page in the documentation.';
$string['settingplatformtypeunix'] = 'Linux';
$string['settingplatformtypeunixoptimised'] = 'Linux (optimised)';
$string['settingplatformtypewin']  = 'Windows';
$string['settingplatformtypeserver'] = 'Server';
$string['settingplatformtypemaximapool'] = 'MaximaPool';
$string['settingplatformmaximacommand'] = 'Maxima command';
$string['settingplatformmaximacommand_desc'] = 'Stack needs to know the shell command to start Maxima.  If this is blank, Stack will make an educated guess.';
$string['settingplatformplotcommand'] = 'Plot command';
$string['settingplatformplotcommand_desc'] = 'Stack needs to know the gnuplot command.  If this is blank, Stack will make an educated guess.';

// Strings used by interaction elements.
$string['false'] = 'Falso';
$string['notanswered'] = 'No respondido';
$string['true'] = 'Verdadero';
$string['ddl_empty'] = 'No hay opciones en el menú desplegable. Por favor ingresa un grupo de valores enlazados con a,b,c,d';

// Strings used by the question test script.
$string['addanothertestcase'] = 'Agregar otro caso de prueba...';
$string['addatestcase'] = 'Agregar caso de prueba...';
$string['addingatestcase'] = 'Agregando caso de prueba a la pregunta {$a}';
$string['completetestcase'] = 'Rellenar el resto del formulario haciendo un caso de prueba';
$string['createtestcase'] = 'Crear caso de prueba';
$string['currentlyselectedvariant'] = 'Esta variante se muestra abajo';
$string['deletetestcase'] = 'Borrar el caso de prueba {$a->no} para la pregunta {$a->question}';
$string['deletetestcaseareyousure'] = '¿Esta seguro que desea borrar este caso de prueba {$a->no} para la pregunta {$a->question}?';
$string['deletethistestcase'] = 'Borrar este caso de prueba...';
$string['deploy'] = 'Implementar';
$string['deployedvariantoptions'] = 'Las siguientes variantes han sido implementadas:';
$string['deployedvariants'] = 'Variantes implementadas';
$string['editingtestcase'] = 'Editando el caso de prueba {$a->no} para la pregunta {$a->question}';
$string['editthistestcase'] = 'Editando el caso de prueba...';
$string['expectedanswernote'] = 'Nota de respuesta esperada';
$string['expectedoutcomes'] = 'Resultado esperado';
$string['expectedpenalty'] = 'Penalización esperada';
$string['expectedscore'] = 'Calificación esperada';
$string['inputdisplayed'] = 'Mostrado como';
$string['inputentered'] = 'Valor ingresado';
$string['inputexpression'] = 'Prueba de la entrada';
$string['inputname'] = 'Nombre de la entrada';
$string['inputstatus'] = 'Estado';
$string['inputstatusname'] = 'Vacio';
$string['inputstatusnameinvalid'] = 'Invalido';
$string['inputstatusnamevalid'] = 'Valido';
$string['inputstatusnamescore'] = 'Calificación';
$string['notestcasesyet'] = 'No se han agregado casos de prueba aún.';
$string['penalty'] = 'Penalización';
$string['prtname'] = 'Nombre del PRT';
$string['questiondoesnotuserandomisation'] = 'Esta pregunta no usa aleatorización.';
$string['questionnotdeployedyet'] = 'Aún no han sido implementadas variantes para esta pregunta.';
$string['questionpreview'] = 'Previsualizar pregunta';
$string['questiontests'] = 'Casos de prueba';
$string['runquestiontests'] = 'Correr casos de prueba...';
$string['showingundeployedvariant'] = 'Mostrando la variante sin implementar: {$a}';
$string['alreadydeployed'] = ' Una variante de esta nota de pregunta ya se ha implementado.';
$string['switchtovariant'] = 'Cambiar a una variarte arbitraria';
$string['testcasexresult'] = 'Caso de prueba {$a->no} {$a->result}';
$string['testingquestion'] = 'Probando pregunta {$a}';
$string['testinputs'] = 'Entradas a probar';
$string['testthisvariant'] = 'Cambiar la prueba a esta variante';
$string['undeploy'] = 'Desimplementar';
$string['deploymany'] = 'Intentar automaticamente implementar el siguiente numero de variantes:';
$string['deploymanynotes'] = 'Nota: STACK se dará por vencido si hay 3 intentos fallidos para generar una nueva nota de pregunta, o cuando una prueba a la pregunta falle.';
$string['deploymanyerror'] = 'Error en la entrada del usuario: no se puede implementar las variantes "{$a->err}".';
$string['deploymanynonew'] = 'Too many repeated existing question notes were generated.';
$string['deploymanysuccess'] = 'El número de nuevas variantes fue creado con éxito, probadas e implementadas: {$a->no}.';

// Support scripts (CAS chat, healthcheck, etc.)
$string['all'] = 'All';
$string['chat'] = 'Send to the CAS';
$string['chat_desc'] = 'The <a href="{$a->link}">CAS chat script</a> lets you test the connection to the CAS, and try out Maxima syntax.';
$string['chatintro'] = 'This page enables CAS text to be evaluated directly. It is a simple script which is a useful minimal example, and a handy way to check if the CAS is working, and to test various inputs.';
$string['chattitle'] = 'Test the connection to the CAS';
$string['clearthecache'] = 'Clear the cache';
$string['healthcheck'] = 'STACK healthcheck';
$string['healthcheck_desc'] = 'The <a href="{$a->link}">healthcheck script</a> helps you verify that all aspects of Stack are working properly.';
$string['healthcheckcache_db'] = 'CAS results are being cached in the database.';
$string['healthcheckcache_none'] = 'CAS results are not being cached.';
$string['healthcheckcachestatus'] = 'The cache currently contains {$a} entries.';
$string['healthcheckconfig'] = 'Maxima configuration file';
$string['healthcheckconfigintro1'] = 'Found, and using, Maxima in the following directory:';
$string['healthcheckconfigintro2'] = 'Trying to automatically write the Maxima configuration file.';
$string['healthcheckconnect'] = 'Trying to connect to the CAS';
$string['healthcheckconnectintro'] = 'We are trying to evaluate the following CAS text:';
$string['healthchecklatex'] = 'Check LaTeX is being converted correctly';
$string['healthchecklatexintro'] = 'STACK generates LaTeX on the fly, and enables teachers to write LaTeX in questions. It assumes that LaTeX will be converted by a moodle filter.  Below are samples of displayed and inline expressions in LaTeX which should be appear correctly in your browser.  Problems here indicate incorrect moodle filter settings, not faults with STACK itself. Stack only uses the single and double dollar notation itself, but some question authors may be relying on the other forms.';
$string['healthchecklatexmathjax'] = 'One way to get equiation rendering to work is to copy the following code into the <b>Within HEAD</b> setting on <a href="{$a}">Additional HTML</a>.';
$string['healthcheckmaximabat'] = 'The maxima.bat file is missing';
$string['healthcheckmaximabatinfo'] = 'This script tried to automatically copy the maxima.bat script from inside "C:\Program files\Maxima-1.xx.y\bin" into "{$a}\stack". However, this seems not to have worked. Please copy this file manually.';
$string['healthcheckplots'] = 'Graph plotting';
$string['healthcheckplotsintro'] = 'There should be two different plots.  If two identical plots are seen then this is an error in naming the plot files. If no errors are returned, but a plot is not displayed then one of the following may help.  (i) check read permissions on the two temporary directories. (ii) change the options used by GNUPlot to create the plot. Currently there is no web interface to these options.';
$string['stackInstall_testsuite_title'] = 'A test suite for STACK Answer tests';
$string['stackInstall_testsuite_title_desc'] = 'The <a href="{$a->link}">answer-tests script</a> verifies that the answer tests are performing correctly. They are also useful to learn by example how each answer-test can be used.';
$string['stackInstall_testsuite_intro'] = 'This page allows you to test that the STACK answer tests are functioning correctly.  Note that only answer tests can be checked through the web interface.  Other Maxima commands need to be checked from the command line: see unittests.mac.';
$string['stackInstall_testsuite_choose'] = 'Please choose an answer test.';
$string['stackInstall_testsuite_pass'] = 'All tests passed!';
$string['stackInstall_testsuite_fail'] = 'Not all tests passed!';
$string['answertest'] = 'Prueba';
$string['answertest_help'] = 'An answer test is used to compare two expressions to establish whether they satisfy some mathematical criteria.';
$string['answertest_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['testsuitecolpassed'] = 'Passed?';
$string['studentanswer'] = 'Student response';
$string['teacheranswer'] = 'Teacher answer';
$string['options'] = 'Opciones';
$string['testsuitefeedback'] = 'Feedback';
$string['testsuitecolerror'] = 'CAS errors';
$string['testsuitecolrawmark'] = 'Raw mark';
$string['testsuitecolexpectedscore'] = 'Expected mark';
$string['testsuitepass'] = 'Pass';
$string['testsuitefail'] = 'Fail';
$string['testsuitenotests']       = 'Number of tests: {$a->no}. ';
$string['testsuiteteststook']     = 'Tests took {$a->time} seconds. ';
$string['testsuiteteststookeach'] = 'Average per test: {$a->time} seconds. ';
$string['stackInstall_input_title'] = "A test suite for validation of student's input";
$string['stackInstall_input_title_desc'] = 'The <a href="{$a->link}">input-tests script</a> provides test cases of how STACK interprests mathematical expressions.  They are also useful to learn by example.';
$string['stackInstall_input_intro'] = "This page allows you to test how STACK interprets various inputs from a student.  This currently only checks with the most liberal settings, trying to adopt an informal syntax and insert stars.  <br />'V' columns record validity as judged by PHP and the CAS.  V1 = PHP valid, V2 = CAS valid.";
$string['phpvalid'] = 'V1';
$string['phpcasstring'] = 'PHP output';
$string['phpsuitecolerror'] = 'PHP errors';
$string['phpvalidatemismatch'] = '[PHP validate mismatch]';
$string['casvalidatemismatch'] = '[CAS validate mismatch]';
$string['casvalid'] = 'V2';
$string['casvalue'] = 'CAS value';
$string['casdisplay'] = 'CAS display';
$string['cassuitecolerrors'] = 'CAS errors';

$string['texdisplayedbracket'] = 'Displayed bracket';
$string['texinlinebracket'] = 'Inline bracket';
$string['texdoubledollar'] = 'Double dollar';
$string['texsingledollar'] = 'Single dollar';

// Used in casstring.class.php.
$string['stackCas_spaces']                  = 'Se encontraron espacios en la expresión {$a->expr}.';
$string['stackCas_percent']                 = 'Se encontró &#037 en la expresión {$a->expr}.';
$string['stackCas_missingLeftBracket']      = 'Olvidaste un paréntesis izquierdo <span class="stacksyntaxexample">{$a->bracket}</span> en la expresión: {$a->cmd}.';
$string['stackCas_missingRightBracket']     = 'Olvidaste un paréntesis derecho  <span class="stacksyntaxexample">{$a->bracket}</span> en la expresión: {$a->cmd}.';
$string['stackCas_apostrophe']              = 'Los apostrófos no son permitidos en las respuestas.';
$string['stackCas_newline']                 = 'Retornos de carro no son permitidos en las respuestas.';
$string['stackCas_forbiddenChar']           = 'Comandos CAS no deben contener los siguientes caráctes: {$a->char}.';
$string['stackCas_finalChar']               = '\'{$a->char}\' es un carácter invalido en {$a->cmd}';
$string['stackCas_MissingStars']            = 'Parece que olvidaste el carácter *. Talvéz pretendiste escribir {$a->cmd}.';
$string['stackCas_unknownFunction']         = 'Función desconocida: {$a->forbid}.';
$string['stackCas_unsupportedKeyword']      = 'Palabra no soportada: {$a->forbid}.';
$string['stackCas_forbiddenWord']           = 'La expresión {$a->forbid} esta prohibida.';
$string['stackCas_bracketsdontmatch']       = 'Existen paréntesis mal anidados en la expresión: {$a->cmd}.';

// Used in cassession.class.php.
$string['stackCas_CASError']                = 'El CAS ha regresado el siguiente error:';
$string['stackCas_allFailed']               = 'Falló el CAS al regresar ninguna expresión evaluada. Por favor comprueba la conexión con el CAS.';
$string['stackCas_failedReturn']            = 'Falló el CAS al regresar ningún dato.';

// Used in castext.class.php.
$string['stackCas_tooLong']                 = 'CASText statement is too long.';
$string['stackCas_MissingAt']               = 'Hace falta un signo de @.';
$string['stackCas_MissingDollar']           = 'Hace falta un signo de $';
$string['stackCas_MissingOpenHint']         = 'Missing opening hint';
$string['stackCas_MissingClosingHint']      = 'Missing closing /hint';
$string['stackCas_MissingOpenDisplay']      = 'Hace falta un \[';
$string['stackCas_MissingCloseDisplay']     = 'Hace falta un \]';
$string['stackCas_MissingOpenInline']       = 'Hace falta un \(';
$string['stackCas_MissingCloseInline']      = 'Hace falta un \)';
$string['stackCas_MissingOpenHTML']         = 'Hace falta una etiqueta html abierta';
$string['stackCas_MissingCloseHTML']        = 'Hace falta una etiqueta html cerrada';
$string['stackCas_failedValidation']        = 'Falló la validación del CASText. ';
$string['stackCas_invalidCommand']          = 'Comandos CAS no validos. ';
$string['stackCas_CASErrorCaused']          = 'causó el siguiente error:';

$string['Maxima_DivisionZero']  = 'División por cero.';
$string['Lowest_Terms']   = 'Tu respuesta contiene fracciones que no están escritas en su mínima expresión. Por favor, cancela facores e intenta de nuevo.';
$string['Illegal_floats'] = 'Tu respuesta contiene números con punto decimal, eso no esta permitido en esta pregunta. Necesitas escribir el número en fraciones. Por ejemplo, debes escribir 1/3 no 0.3333.';
$string['qm_error'] = 'Tu respuesta contiene signos de interrogación, esto no esta permitido como respuestas. Debes reemplazarlos por valores especificos.';
// TODO add this to STACK...
// $string['CommaError']     = 'Your answer contains commas which are not part of a list, set or matrix.  <ul><li>If you meant to type in a list, please use <tt>{$a[0]}</tt>,</li><li>If you meant to type in a set, please use <tt>{$a[1]}</tt>.</li></ul>';

// Answer tests.
$string['stackOptions_AnsTest_values_AlgEquiv']           =  "AlgEquiv";
$string['stackOptions_AnsTest_values_EqualComAss']        =  "EqualComAss";
$string['stackOptions_AnsTest_values_CasEqual']           =  "CasEqual";
$string['stackOptions_AnsTest_values_SameType']           =  "SameType";
$string['stackOptions_AnsTest_values_SubstEquiv']         =  "SubstEquiv";
$string['stackOptions_AnsTest_values_SysEquiv']           =  "SysEquiv";
$string['stackOptions_AnsTest_values_Expanded']           =  "Expanded";
$string['stackOptions_AnsTest_values_FacForm']            =  "FacForm";
$string['stackOptions_AnsTest_values_SingleFrac']         =  "SingleFrac";
$string['stackOptions_AnsTest_values_PartFrac']           =  "PartFrac";
$string['stackOptions_AnsTest_values_CompSquare']         =  "CompletedSquare";
$string['stackOptions_AnsTest_values_NumRelative']        =  "NumRelative";
$string['stackOptions_AnsTest_values_NumAbsolute']        =  "NumAbsolute";
$string['stackOptions_AnsTest_values_NumSigFigs']         =  "NumSigFigs";
$string['stackOptions_AnsTest_values_GT']                 =  "Num-GT";
$string['stackOptions_AnsTest_values_GTE']                =  "Num-GTE";
$string['stackOptions_AnsTest_values_LowestTerms']        =  "LowestTerms";
$string['stackOptions_AnsTest_values_Diff']               =  "Diff";
$string['stackOptions_AnsTest_values_Int']                =  "Int";
$string['stackOptions_AnsTest_values_String']             =  "String";
$string['stackOptions_AnsTest_values_StringSloppy']       =  "StringSloppy";
$string['stackOptions_AnsTest_values_RegExp']             =  "RegExp";

$string['AT_NOTIMPLEMENTED']        = 'This answer test has not been implemented. ';
$string['TEST_FAILED']              = 'La prueba a la respuesta no se ejecutó correctamente:Por favor avisa a tu profesor. {$a->errors}';
$string['AT_MissingOptions']        = 'Se omitió alguna opción cuando se ejecutó la prueba. ';
$string['AT_InvalidOptions']        = 'El campo opción es inválido. {$a->errors}';

$string['ATAlgEquiv_SA_not_expression'] = 'La respuesta debería ser una expresión, no una ecuación, desigualdad, lista, conjunto o matriz.';
$string['ATAlgEquiv_SA_not_matrix']     = 'La respuesta debería ser una matriz, pero no es así.';
$string['ATAlgEquiv_SA_not_list']       = 'La respuesta debería ser una lista, pero no es así. La sintaxis para intruducir una lista es: [a,b,c].';
$string['ATAlgEquiv_SA_not_set']        = 'La respuesta debería ser un conjunto, pero no es así. La sintaxis para intrudicir un conjunto es: {a,b,c}.';
$string['ATAlgEquiv_SA_not_equation']   = 'La respuesta debería ser una ecuación, pero no es así.';
$string['ATAlgEquiv_TA_not_equation']   = 'La respuesta es una ecuación, pero la expresión con la que fué comparada no lo es. Talvéz escribiste algo como "y=2*x+1" cuando solo es necesario escribir "2*x+1"';
$string['ATAlgEquiv_SA_not_inequality'] = 'La respuesta debería ser una desigualdad, pero no es así.';
$string['Subst']                        = 'La respuesta será correcta si utilizas la siguiente sustitución de variables. {$a->m0} ';


$string['ATInequality_nonstrict']       = 'La desigualdad debería ser estricta, pero no es así! ';
$string['ATInequality_strict']          = 'La desigualdad no debería ser estricta! ';
$string['ATInequality_backwards']       = 'La desigualdad parece estar al revés. ';

$string['ATLowestTerms_wrong']          = 'Necesitas cancelar fraciones en tu respuesta. ';
$string['ATLowestTerms_entries']        = 'Los siguientes términos no están en su mínima expresión en tu respuesta.  {$a->m0} Por favor intenta de nuevo.  ';


$string['ATList_wronglen']          = 'La lista debería tener {$a->m0} elementos, pero actualmente tiene {$a->m1}. ';
$string['ATList_wrongentries']      = 'Las siguientes entradas en rojo son incorrectas. {$a->m0} ';

$string['ATMatrix_wrongsz']         = 'La matrix debería ser {$a->m0} por {$a->m1}, pero actualmente es {$a->m2} por {$a->m3}. ';
$string['ATMatrix_wrongentries']    = 'Las siguientes entradas en rojo son incorrectas. {$a->m0} ';

$string['ATSet_wrongsz']            = 'El conjunto debería tener {$a->m0} elementos diferentes, pero actualmente tiene {$a->m1}. ';
$string['ATSet_wrongentries']       = 'Las siguientes entradas son incorrectas, aunque parece que están en forma simplificada de lo que actualmente entró. {$a->m0} ';

$string['irred_Q_factored']         = 'El término {$a->m0} debería estar desarrollado, pero no es así. ';
$string['irred_Q_commonint']        = 'Tienes que extraer factor común. ';  // Needs a space at the end.
$string['irred_Q_optional_fac']     = 'Podrías trabajar un poco mas, {$a->m0} puede factorizarse mas.  Desde luego, no es necesario. ';

$string['FacForm_UnPick_morework']  = 'Podría hacer mas trabajo en el término {$a->m0}. ';
$string['FacForm_UnPick_intfac']    = 'Es necesario extraer factor común. ';

$string['ATFacForm_error_list']     = 'La prueba a la respuesta ha fallado. Por favor contacta al administrador del sistema';
$string['ATFacForm_error_degreeSA'] = 'El CAS no pudo establecer el grado algebraico de tu respuesta.';
$string['ATFacForm_isfactored']     = 'La respuesta esta factorizada, bien hecho. ';  // Needs a space at the end.
$string['ATFacForm_notfactored']    = 'La respuesta no esta factorizada. '; // Needs a space at the end.
$string['ATFacForm_notalgequiv']    = 'Nota que tu respuesta no es algebraicamente equivalente a la respuesta correcta. Debiste cometer un error. '; // needs a space at the end.

$string['ATPartFrac_error_list']        = 'La prueba a la respuesta falló. Por favor contacta al administrador del sistema';
$string['ATPartFrac_true']              = '';
$string['ATPartFrac_single_fraction']   ='La respuesta parece ser una fracción común, cuando debería ser una fracción parcial. ';
$string['ATPartFrac_diff_variables']    ='Las variables en la respuesta son diferentes a las de la pregunta, por favor revisa. ';
$string['ATPartFrac_denom_ret']         ='If your answer is written as a single fraction then the denominator would be {$a->m0}. In fact, it should be {$a->m1}. ';
$string['ATPartFrac_ret_expression']    ='Your answer as a single fraction is {$a->m0} ';

$string['ATSingleFrac_error_list']     = 'The answer test failed.  Please contact your systems administrator';
$string['ATSingleFrac_true']           = '';
$string['ATSingleFrac_part']           = 'Your answer needs to be a single fraction of the form \( {a}\over{b} \). ';
$string['ATSingleFrac_var']            = 'The variables in your answer are different to the those of the question, please check them. ';
$string['ATSingleFrac_ret_exp']        = 'Your answer is not algebraically equivalent to the correct answer. You must have done something wrong. ';
$string['ATSingleFrac_div']            = 'Your answer contains fractions within fractions.  You need to clear these and write your answer as a single fraction.';

$string['ATCompSquare_true']            = '';
$string['ATCompSquare_false']           = '';
$string['ATCompSquare_not_AlgEquiv']    = 'Your answer appears to be in the correct form, but is not equivalent to the correct answer.';
$string['ATCompSquare_false_no_summands']     = 'The completed square is of the form \( a(\cdots\cdots)^2 + b\) where \(a\) and \(b\) do not depend on your variable.  More than one of your summands appears to depend on the variable in your answer.';


$string['ATInt_error_list']         = 'The answer test failed.  Please contact your systems administrator';
$string['ATInt_const_int']          = 'You need to add a constant of integration. This should be an arbitrary constant, not a number.';
$string['ATInt_const']              = 'You need to add a constant of integration, otherwise this appears to be correct.  Well done.';
$string['ATInt_EqFormalDiff']       = 'The formal derivative of your answer does equal the expression that you were asked to integrate.  However, your answer differs from the correct answer in a significant way, that is to say not just, eg, a constant of integration.  Please ask your teacher about this.';
$string['ATInt_wierdconst']         = 'The formal derivative of your answer does equal the expression that you were asked to integrate.  However, you have a strange constant of integration.  Please ask your teacher about this.';
$string['ATInt_diff']               = 'It looks like you have differentiated instead!';
$string['ATInt_generic']            = 'The derivative of your answer should be equal to the expression that you were asked to integrate, that was: {$a->m0}  In fact, the derivative of your answer, with respect to {$a->m1} is: {$a->m2} so you must have done something wrong!';

$string['ATDiff_error_list']        = 'The answer test failed.  Please contact your systems administrator';
$string['ATDiff_int']               = 'It looks like you have integrated instead!';

$string['ATNumSigFigs_error_list']  = 'The answer test failed.  Please contact your systems administrator';
$string['ATNumSigFigs_NotDecimal']  = 'Your answer should be a decimal number, but is not! ';
$string['ATNumSigFigs_Inaccurate']  = 'The accuracy of your answer is not correct.  Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.';
$string['ATNumSigFigs_WrongDigits'] = 'Your answer contains the wrong number of significant digits. ';

$string['ATSysEquiv_SA_not_list']               = 'Your answer should be a list, but it is not!';
$string['ATSysEquiv_SB_not_list']               = 'The Teacher\'s answer is not a list.  Please contact your teacher.';
$string['ATSysEquiv_SA_not_eq_list']            = 'Your answer should be a list of equations, but it is not!';
$string['ATSysEquiv_SB_not_eq_list']            = 'Teacher answer is not a list of equations';
$string['ATSysEquiv_SA_not_poly_eq_list']       = 'One or more of your equations is not a polynomial!';
$string['ATSysEquiv_SB_not_poly_eq_list']       = 'The Teacher\'s answer should be a list of polynomial equations, but is not.  Please contact your teacher.';
$string['ATSysEquiv_SA_missing_variables']      = 'Your answer is missing one or more variables!';
$string['ATSysEquiv_SA_extra_variables']        = 'La respuesta incluye muchas variables!';
$string['ATSysEquiv_SA_system_underdetermined'] = 'The equations in your system appear to be correct, but you need others besides.';
$string['ATSysEquiv_SA_system_overdetermined']  = 'The entries in red below are those that are incorrect. {$a->m0} ';

$string['ATRegEx_missing_option']               = 'Missing regular expression in CAS Option field.';

$string['studentValidation_yourLastAnswer']  = 'Tu respuesta fue interpretado como: {$a}';
$string['studentValidation_invalidAnswer']   = 'Esta respuesta es invalida. ';
$string['stackQuestion_noQuestionParts']        = 'Este elemento no tiene partes de pregunta para la respuesta.';

// Documentation strings.
$string['stackDoc_404']                 = 'Error 404';
$string['stackDoc_docs']                = 'Documentación de STACK';
$string['stackDoc_docs_desc']           = '<a href="{$a->link}">Documentación para  STACK</a>: Wiki estático y local.';
$string['stackDoc_home']                = 'Inicio de la Documentación';
$string['stackDoc_index']               = 'Indice de Categorias';
$string['stackDoc_parent']              = 'Padre';
$string['stackDoc_siteMap']             = 'Mapa del sitio';
$string['stackDoc_404message']          = 'Archivo no encontrado.';
$string['stackDoc_directoryStructure']  = 'Estructura del directorio';


