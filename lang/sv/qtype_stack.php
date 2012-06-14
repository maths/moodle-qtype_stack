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
$string['ATAlgEquiv_SA_not_set'] = 'Ditt svar borde vara en mängd. Mängder matas in i formatet {a,b,c}.';
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
$string['ATInt_EqFormalDiff'] = 'Den formella derivatan av ditt svar är densamma som funktionen du ombads att integrera. Trots det, så skiljer ditt svar sig väsentligen från rätt svar, dvs. inte bara med en integrationskonstant. Kontakta din lärare.';
$string['ATInt_error_list'] = 'Svarstestet misslyckades. Var vänlig och kontakta din systemadministratör';
$string['ATInt_generic'] = 'Derivatan av ditt svar borde sammanfall amed den funktion du ombads att integrera, nämnligen {$a->m0}  Derivatan av ditt svar, med avseende på {$a->m1} är {$a->m2} så du måste ha gjort något fel.';
$string['ATInt_wierdconst'] = 'Den formella derivatan av ditt svar är densamma som funktionen du ombads att integrera, men du har en konstig integrationskonstant. Fråga din lärare.';
$string['ATList_wrongentries'] = 'Felen är markerade med rött: {$a->m0}';
$string['ATLowestTerms_entries'] = 'Följande termer i ditt svar är inte förkortade så långt som möjligt. {$a->m0} Var så vänlig och pröva igen.';
$string['ATLowestTerms_wrong'] = 'Du behöver förenkla bråk i ditt svar.';
$string['ATMatrix_wrongentries'] = 'Felen är markerade med rött: {$a->m0}';
$string['ATMatrix_wrongsz'] = 'Din matris borde ha dimensionerna {$a->m0} gånger {$a->m1}, men den är de facto {$a->m2} gånger {$a->m3}.';
$string['ATNumSigFigs_error_list'] = 'Svarstestet misslyckades. Kontakta din systemadministratör';
$string['ATNumSigFigs_Inaccurate'] = 'Precisionen på ditt svar är felaktig. Antingen har du avrundat fel, eller så har du avrundat ett delsvar, som ger upphov till senare fel.';
$string['ATNumSigFigs_NotDecimal'] = 'Ditt svar borde vara ett decimaltal, men är det inte.';
$string['ATNumSigFigs_WrongDigits'] = 'Ditt svar innehåller fel antal betydande siffror.';
$string['ATPartFrac_denom_ret'] = 'Om ditt svar skrivs som ett enda bråk, så blir nämnaren {$a->m0}. Den borde bli {$a->m1}.';
$string['ATPartFrac_diff_variables'] = 'Du använder andra variabler än de som har getts i frågan. Var vänlig och kontrollera dem.';
$string['ATPartFrac_error_list'] = 'Svarstestet misslyckades. Kontakta din systemadministratör';
$string['ATPartFrac_ret_expression'] = 'Ditt svar skrivet som ett enda bråk är: {$a->m0}';
$string['ATPartFrac_single_fraction'] = 'Ditt svar verkar vara ett enda bråk, men det borde vara i partialbråksform.';
$string['ATSet_wrongentries'] = 'Följande svar är felaktiga. De kan vara återgivna i en förenklad form jämfört med vad du matade in. {$a->m0}';
$string['ATSingleFrac_div'] = 'Ditt svar innehåller bråk inuti bråk. Du behöver förenkla dessa och svara som ett enda bråk.';
$string['ATSingleFrac_error_list'] = 'Svarstestet misslyckades. Kontakta din systemadministratör';
$string['ATSingleFrac_part'] = 'Ditt svar ska vara ett enda bråk av formen $\\frac ab$.';
$string['ATSingleFrac_ret_exp'] = 'Ditt svar stämmer inte. Du måste ha gjort något fel.';
$string['ATSingleFrac_var'] = 'Du använder andra variabler än de som har getts i frågan. Var vänlig och kontrollera dem.';
$string['ATSysEquiv_SA_extra_variables'] = 'Ditt svar innehåller för många variabler.';
$string['ATSysEquiv_SA_missing_variables'] = 'Ditt svar saknar en eller flera variabler.';
$string['ATSysEquiv_SA_not_eq_list'] = 'Ditt svar borde vara en lista av ekvationer.';
$string['ATSysEquiv_SA_not_list'] = 'Ditt svar borde vara en lista.';
$string['ATSysEquiv_SA_not_poly_eq_list'] = 'En eller flera av dina ekvationer är inte ett polynom.';
$string['ATSysEquiv_SA_system_overdetermined'] = 'Felen är markerade med rött: {$a->m0}';
$string['ATSysEquiv_SA_system_underdetermined'] = 'De givna ekvationerna verkar stämma, men du behöver flera till.';
$string['ATSysEquiv_SB_not_eq_list'] = 'Lärarens svar är inte en lista av ekvationer.';
$string['ATSysEquiv_SB_not_list'] = 'Lärarens svar är inte en lista. Kontakta din lärare.';
$string['ATSysEquiv_SB_not_poly_eq_list'] = 'Lärarens svar borde vara en lista med polynomekvationer, men är inte det. Kontakta din lärare.';
$string['AT_NOTIMPLEMENTED'] = 'Detta svarstest har inte implementerats ännu.';
$string['checkanswertype'] = 'Kontrollera svarets typ';
$string['defaultprtcorrectfeedback'] = 'Rätt svar, fint jobbat!';
$string['defaultprtincorrectfeedback'] = 'Ditt svar är tyvärr felaktigt. Försök på nytt!';
$string['defaultprtpartiallycorrectfeedback'] = 'Ditt svar är delvist korrekt. Du uppmanas att försöka på nytt.';
$string['Illegal_floats'] = 'Ditt svar innehåller flyttal, vilket är förbjudet i denhär frågan. Du behöver mata in flyttal som bråk. 0,33... ska t.ex. matas in som 1/3.';
$string['inputstatus'] = 'Status';
$string['Lowest_Terms'] = 'Ditt svar innehåller oförkortade bråk. Var snäll och förkorta och försök igen.';
$string['markmode'] = 'Poängsättning vid flera svar';
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
$string['Subst'] = 'Ditt svar skulle vara rätt ifall du gjorde följande variabelbyte: {$a->m0}';
$string['teacheranswer'] = 'Lärarens svar';
$string['teachersanswer'] = 'Modellsvar';
$string['TEST_FAILED'] = 'Svarstestet kunde inte utföras på rätt sätt. Var så vänlig och kontakta din lärare.';
$string['ATCompSquare_false_no_summands'] = 'Den kompletterade kvadraten är av formen \\( a(\\cdots\\cdots)^2 + b\\), där \\(a\\) och \\(b\\) är oberoende av din variabel.  Flera än en av termerna i ditt svar verkar bero på variabeln.';
$string['AT_InvalidOptions'] = 'Parameterfältet är felaktigt: {$a->errors}';
$string['AT_MissingOptions'] = 'Variabelnamnet saknas i CAS-parameterfältet.';
$string['FacForm_UnPick_intfac'] = 'Du behöver bryta ut en gemensam faktor.';
$string['FacForm_UnPick_morework'] = 'Du kan ännu förenkla {$a->m0} vidare.';
$string['false'] = 'Falskt';
$string['inputtypeboolean'] = 'Sant/Falskt';
$string['irred_Q_commonint'] = 'Du behöver bryta ut en gemensam faktor.';
$string['Maxima_DivisionZero'] = 'Division med noll.';
$string['stackCas_allFailed'] = 'CAS returnerade inga evaluerade uttryck. Kontrollera din koppling till CAS (Maxima).';
$string['stackCas_apostrophe'] = 'Apostrofer är förbjudna i svaret.';
$string['stackCas_failedReturn'] = 'CAS returnerade inget data.';
$string['stackCas_finalChar'] = '\'{$a->char}\' är ett felaktigt sista tecken i {$a->cmd}';
$string['stackCas_forbiddenWord'] = 'Uttrycket {$a->forbid} är förbjudet.';
$string['stackCas_missingLeftBracket'] = 'Du saknar en vänsterparentes <span class="stacksyntaxexample">{$a->bracket}</span> i uttrycket {$a->cmd}.';
$string['stackCas_missingRightBracket'] = 'Du saknar en högerparentes <span class="stacksyntaxexample">{$a->bracket}</span> i uttrycket {$a->cmd}.';
$string['stackCas_percent'] = 'Förbudet procenttecken % i uttrycket {$a->expr}.';
$string['stackCas_spaces'] = 'Förbjudna mellanslag i uttrycket {$a->expr}.';
$string['stackCas_unknownFunction'] = 'Obekant funktion: {$a->forbid}.';
$string['stackDoc_404message'] = 'Filen hittades inte.';
$string['true'] = 'Sant';
$string['generalfeedback'] = 'Allmän feedback';
$string['generalfeedbacktags'] = 'Allmän feedback får inte innehålla \'{$a}\'.';
$string['healthcheck'] = 'STACK systemkontroll';
$string['healthchecklatex'] = 'Kontrollera att LaTeX visas på rätt sätt';
$string['healthcheck_desc'] = '<a href="{$a->link}">Systemkontrollskriptet</a> hjälper dig att kontrollera att alla apekter av STACK fungerar korrekt.';
$string['prtcorrectfeedback'] = 'Standardfeedback för rätt svar';
$string['specificfeedback'] = 'Specifik feedback';
$string['specificfeedbacktags'] = 'Den specifika feedbacken får inte innehålla \'{$a}\'.';
$string['testsuitecolexpectedscore'] = 'Förväntad poäng';
$string['addanothertestcase'] = 'Lägg till ett nytt testfall...';
$string['addatestcase'] = 'Lägg till ett testfall...';
$string['addingatestcase'] = 'Lägger till ett testfall till fråga {$a}';
$string['answernote'] = 'Svarsnot';
$string['answernote_err'] = 'Svarsnoter får inte innehålla tecknet |. Tecknet används av STACK för att dela på svarsnoter automatiskt.';
$string['createtestcase'] = 'Skapa testfall';
$string['deletetestcase'] = 'Radera testfall {$a->no} för fråga {$a->question}';
$string['deletetestcaseareyousure'] = 'Är du säker på att du vill radera testfall {$a->no} för fråga "{$a->question}"?';
$string['deletethistestcase'] = 'Radera detta testfall...';
$string['deploy'] = 'Ta denna variant i bruk';
$string['deployedvariantoptions'] = 'Följande slumpvarianter har tagits i bruk:';
$string['deployedvariants'] = 'I bruk tagna slumpvarianter';
$string['editthistestcase'] = 'Redigera detta testfall...';
$string['expectedanswernote'] = 'Förväntad svarsnot';
$string['multcross'] = 'Kryss';
$string['multdot'] = 'Punkt';
$string['notestcasesyet'] = 'Inga testfall har ännu lagts till.';
$string['questiondoesnotuserandomisation'] = 'Den här frågan använder inte randomisering.';
$string['questionnotdeployedyet'] = 'Inga slumpvarianter av den här frågan har ännu tagits i bruk.';
$string['questionnote'] = 'Frågenot';
$string['questionnotempty'] = 'Frågenoten får inte vara tom då rand() förekommer i frågevariablerna. Frågenoten används för att skilja olika slumpvarianter av frågan åt.';
$string['questionnotetags'] = 'Frågenoten får inte innehålla "{$a}".';
$string['questionpreview'] = 'Förhandsgranskning av fråga';
$string['showingundeployedvariant'] = 'Visar oanvänd slumpvariant {$a}';
$string['testcasexresult'] = 'Testfall {$a->no} {$a->result}';
$string['undeploy'] = 'Ta ur bruk';
$string['ATSet_wrongsz'] = 'Din mängd borde ha {$a->m0} olika element, men den har {$a->m1}.';
$string['expectedoutcomes'] = 'Förväntat resultat';
$string['expectedpenalty'] = 'Förväntat avdrag';
$string['expectedscore'] = 'Förväntad poäng';
$string['forbidfloat'] = 'Förbjud flyttal';
$string['inputstatusnameinvalid'] = 'Ogiltigt';
$string['inputtype'] = 'Inmatningstyp';
$string['inputtypealgebraic'] = 'Algebraisk inmatning';
$string['inputtypedropdown'] = 'Rullgardinsmeny';
$string['inputtypesinglechar'] = 'Ett enda tecken';
$string['inputtypetextarea'] = 'Textyta';
$string['markmodepenalty'] = 'Avdrag';
$string['mustverify'] = 'Studenten måste bekräfta';
$string['penalty'] = 'Avdrag';
$string['pluginnameadding'] = 'Lägger till en STACK-fråga';
$string['pluginnameediting'] = 'Redigerar en STACK-fråga';
$string['questionvariables'] = 'Frågevariabler';
$string['requirelowestterms'] = 'Kräv förkortade bråk';
$string['showvalidation'] = 'Visa validering';
$string['stackCas_failedValidation'] = 'CASText kunde inte valideras';
$string['stackDoc_index'] = 'Kategoriindex';
$string['stackInstall_testsuite_fail'] = 'Vissa test misslyckades!';
$string['stackInstall_testsuite_pass'] = 'Alla test lyckades!';
$string['testingquestion'] = 'Testar fråga "{$a}"';
$string['testsuitecolpassed'] = 'Resultat';
$string['testsuitefail'] = 'Misslyckades';
$string['testsuitepass'] = 'Lyckades';
$string['variantsselectionseed'] = 'Slumpgrupp';
$string['stackCas_MissingStars'] = 'Du verkar sakna asterisker *. Tänkte du kanske skriva {$a->cmd}?';
$string['verifyquestionandupdate'] = 'Verifiera frågetexten och uppdatera formuläret';
