<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'qtype_stack', language 'sv', branch 'MOODLE_22_STABLE'
 *
 * @package    qtype
 * @subpackage stack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['answertest'] = 'Svarstest';
$string['ATAlgEquiv_SA_not_equation'] = 'Ditt svar borde vara en ekvation.';
$string['ATAlgEquiv_SA_not_expression'] = 'Ditt svar borde vara ett uttryck, inte en ekvation, olikhet, lista, mängd eller matris.';
$string['ATAlgEquiv_SA_not_inequality'] = 'Ditt svar borde vara en olikhet.';
$string['ATAlgEquiv_SA_not_list'] = 'Ditt svar borde vara en lista. Märk att en lista matas in i formatet [a,b,c].';
$string['ATAlgEquiv_SA_not_matrix'] = 'Ditt svar borde vara en matris.';
$string['ATAlgEquiv_SA_not_set'] = 'Dit svar borde vara en mängd. Mängder matas in i formatet {a,b,c}.';
$string['ATAlgEquiv_TA_not_equation'] = 'Ditt svar är en ekvation, men borde inte vara det. Matade du möjligen in något i stil med "y=2*x+1" i en situation där du bara behövde mata in "2*x+1"?';
$string['ATCompSquare_not_AlgEquiv'] = 'Ditt svar verkar vara skrivet på rätt form, men det stämmer inte överens med det rätta svaret.';
$string['ATDiff_error_list'] = 'Svarstestet misslyckades. Var vänlig och kontakta din systemadministratör';
$string['ATDiff_int'] = 'Det ser ut som om du integrerade istället!';
$string['ATFacForm_error_list'] = 'Svarstestet misslyckades. Var vänlig och kontakta din systemadministratör';
$string['ATFacForm_isfactored'] = 'Ditt svar är faktoriserat.';
$string['ATFacForm_notalgequiv'] = 'Ditt svar stämmer inte, så du måste ha gjort något fel.';
$string['ATFacForm_notfactored'] = 'Ditt svar är inte faktoriserat.';
$string['ATInt_const'] = 'Du behöver addera en integrationskonstant. Annars verkar detta stämma.';
$string['ATInt_const_int'] = 'Du behöver addera en integrationskonstant. Konstanten ska vara en symbol för ett godtyckligt tal, inte ett givet tal.';
$string['ATInt_diff'] = 'Det verkar som om du har deriverat i stället!';
$string['ATInt_EqFormalDiff'] = 'Den formella derivatan av ditt svar är densamma som funktionen du ombads att integrera. Trots det, så skiljer ditt svar sig väsentligen från rått svar, dvs. inte bara med en integrationskonstant. Fråga din lärare.';
$string['ATInt_error_list'] = 'Svarstestet misslyckades. Var vänlig och kontakta din systemadministratör';
$string['ATInt_generic'] = 'Derivatan av ditt svar borde sammanfallamed den funktion du ombads att integrera, nämnligen {a->m0}  Derivatan av ditt svar, med avseende på {a->m1} är {$a->m2} så du måste ha gjort något fel.';
$string['ATInt_wierdconst'] = 'Den formella derivatan av ditt svar är densamma som funktionen du ombads att integrera, men du har en konstig integrationskonstant. Fråga din lärare.';
$string['ATList_wrongentries'] = 'Felen är markerade med rött. {$a->m0}';
$string['ATLowestTerms_entries'] = 'Följande termer i ditt svar är inte förkortade så långt som möjligt. {$a->m0} Var så vänlig och pröva igen.';
$string['ATLowestTerms_wrong'] = 'Du behöver förenkla bråk i ditt svar.';
$string['ATMatrix_wrongentries'] = 'Felen är markerade med rött. {$a->m0}';
$string['ATMatrix_wrongsz'] = 'Din matris borde ha dimensionerna {a->m0} gånger {a->m1}, men den är de facto {a->m2} gånger {a->m3}.';
$string['ATNumSigFigs_error_list'] = 'Svarstestet misslyckades. Kontakta din systemadministratör';
$string['ATNumSigFigs_Inaccurate'] = 'Precisionen på ditt svar är felaktig. Antingen har du inte avrundat rätt, eller så har du avrundat ett delsvar, som ger upphov till senare fel.';
$string['ATNumSigFigs_NotDecimal'] = 'Ditt svar borde vara ett decimaltal, men är det inte.';
$string['ATNumSigFigs_WrongDigits'] = 'Ditt svar innehåller fel antal betydande siffror.';
$string['ATPartFrac_denom_ret'] = 'Om ditt svar skrivs som ett enda bråk, så blir nämnaren{a->m0}. Den borde bli {a->m1}.';
$string['ATPartFrac_diff_variables'] = 'Du använder andra variabler än de som har givits i frågan. Var vänlig och kontrollera dem.';
$string['ATPartFrac_error_list'] = 'Svarstestet misslyckades. Kontakta din systemadministratör';
$string['ATPartFrac_ret_expression'] = 'Ditt svar som ett enda bråk är {$a->m0}';
$string['ATPartFrac_single_fraction'] = 'Ditt svar verkar vara ett enda bråk, men det borde vara i partialbråksform.';
$string['ATSet_wrongentries'] = 'Följande svar är felaktiga. De kan vara återgivna i en förenklad form jämfört med vad du matade in. {$a->m0}';
$string['ATSingleFrac_div'] = 'Ditt svar innehåller bråk inuti bråk. Du behöver förenkla dessa och svara som ett enda bråk.';
$string['ATSingleFrac_error_list'] = 'Svarstestet misslyckades. Kontakta din systemadministratör';
$string['ATSingleFrac_part'] = 'Ditt svar ska vara ett enda bråk av formen $\\frac ab$.';
$string['ATSingleFrac_ret_exp'] = 'Ditt svar stämmer inte. Du måste ha gjort något fel.';
$string['ATSingleFrac_var'] = 'Du använder andra variabler än de som har givits i frågan. Var vänlig och kontrollera dem.';
$string['ATSysEquiv_SA_extra_variables'] = 'Ditt svar innehåller för många variabler.';
$string['ATSysEquiv_SA_missing_variables'] = 'Ditt svar saknar en eller flera variabler.';
$string['ATSysEquiv_SA_not_eq_list'] = 'Ditt svar borde vara en lista av ekvationer.';
$string['ATSysEquiv_SA_not_list'] = 'Ditt svar borde vara en lista.';
$string['ATSysEquiv_SA_not_poly_eq_list'] = 'En eller flera av dina ekvationer är inte ett polynom.';
$string['ATSysEquiv_SA_system_overdetermined'] = 'Felen är markerade med rött. {$a->m0}';
$string['ATSysEquiv_SA_system_underdetermined'] = 'De givna ekvationerna verkar stämma, men du behöver flera till.';
$string['ATSysEquiv_SB_not_eq_list'] = 'Lärarens svar är inte en lista av ekvationer.';
$string['ATSysEquiv_SB_not_list'] = 'Lärarens svar är inte en lista. Kontakta din lärare.';
$string['ATSysEquiv_SB_not_poly_eq_list'] = 'Lärarens svar borde vara en lista med polynomekvationer, men är inte det. Kontakta din lärare.';
$string['AT_NOTIMPLEMENTED'] = 'Detta svarstest har inte implementerats ännu.';
$string['checkanswertype'] = 'Kontrollera svarets typ';
$string['defaultprtcorrectfeedback'] = 'Rätt svar, fint jobbat!';
$string['defaultprtincorrectfeedback'] = 'Ditt svar är tyvärr felaktigt. Försök på nytt!';
$string['defaultprtpartiallycorrectfeedback'] = 'Ditt svar är delvist korrekt. Du uppmanas att korrigera de felaktiga delarna och skicka in dem på nytt.';
$string['Illegal_floats'] = 'Ditt svar innehåller flyttal, vilket är förbjudet i denhär frågan. Du behöver mata in flyttal som bråk, t.ex. 0,33... ska matas in som 1/3.';
$string['inputstatus'] = 'Status';
$string['Lowest_Terms'] = 'Ditt svar innehåller oförkortade bråk. Var snäll och förkorta och försök igen.';
$string['markmode'] = 'Markera ändring';
$string['markmodefirst'] = 'Första svaret';
$string['markmodelast'] = 'Sista svaret';
$string['notanswered'] = 'Obesvarad';
$string['prtincorrectfeedback'] = 'Standardfeedback för fel svar';
$string['prtpartiallycorrectfeedback'] = 'Standardfeedback för delvist korrekt';
$string['questiontests'] = 'Automatisk testning av frågor';
$string['runquestiontests'] = 'Kör automatiska frågetest...';
$string['stackQuestion_noQuestionParts'] = 'Här finns inga delfrågor för dig att besvara.';
$string['studentValidation_invalidAnswer'] = 'Detta svar är ogiltigt.';
$string['studentValidation_yourLastAnswer'] = 'Ditt senaste svar tolkades på följande sätt:';
$string['Subst'] = 'Ditt svar skulle vara rätt ifall du gjorde följande variabelbyte. {$a->m0}';
$string['teacheranswer'] = 'Lärarens svar';
$string['teachersanswer'] = 'Modellsvar';
$string['TEST_FAILED'] = 'Svarstestet kunde inte utföras på rätt sätt. Var så vänlig och kontakta din lärare.';
