<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'qtype_stack', language 'de', version '5.0'.
 *
 * @package     qtype_stack
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['ATAddConst_Opt'] = 'Die Antwortüberprüfung ist fehlgeschlagen: Bitte kontaktieren Sie Ihre Trainer/innen. Es gibt einen Fehler bei den Optionen für die AddConst-Antwortüberprüfung.';
$string['ATAddConst_noconst'] = 'Sie müssen eine Konstante hinzufügen. Dies sollte eine beliebige Konstante sein, keine Zahl.';
$string['ATAddConst_severalconst'] = 'Anstelle einer einzigen Integrationskonstante wurden mehrere zusätzliche Konstanten gefunden!';
$string['ATAlgEquiv_SA_not_equation'] = 'Ihre Antwort sollte eine Gleichung sein, ist es aber nicht.';
$string['ATAlgEquiv_SA_not_expression'] = 'Ihre Antwort sollte ein Ausdruck und keine Gleichung/Ungleichung/Liste/Menge/Matrix sein.';
$string['ATAlgEquiv_SA_not_function'] = 'Ihre Antwort sollte eine Funktion sein, die durch den Operator <tt>:=</tt> definiert wird. Sie ist es aber nicht.';
$string['ATAlgEquiv_SA_not_inequality'] = 'Ihre Antwort sollte eine Ungleichung sein, ist es aber nicht.';
$string['ATAlgEquiv_SA_not_list'] = 'Ihre Antwort sollte eine Liste sein, ist es aber nicht. Beachten Sie die Syntax: Bei einer Liste wird die Auflistung der Elemente (durch Kommata getrennt) in eckige Klammern eingeschlossen.';
$string['ATAlgEquiv_SA_not_list_semi'] = 'Ihre Antwort sollte eine Liste sein, ist es aber nicht. Beachten Sie die Syntax: Bei einer Liste wird die Auflistung der Elemente (durch Semikola getrennt) in eckige Klammern eingeschlossen.';
$string['ATAlgEquiv_SA_not_logic'] = 'Ihre Antwort sollte eine Gleichung oder Ungleichung sein, oder eine logische Kombination aus solchen. Sie ist es aber nicht.';
$string['ATAlgEquiv_SA_not_matrix'] = 'Ihre Antwort sollte eine Matrix sein, ist es aber nicht.';
$string['ATAlgEquiv_SA_not_realset'] = 'Ihre Antwort sollte eine Teilmenge der reellen Zahlen sein. Dies könnte eine Menge von Zahlen oder eine Sammlung von Intervallen sein.';
$string['ATAlgEquiv_SA_not_set'] = 'Ihre Antwort sollte eine Menge sein, ist es aber nicht. Beachten Sie die Syntax: Bei einer Menge wird die Auflistung der Elemente (durch Kommata getrennt) in geschweifte Klammern eingeschlossen.';
$string['ATAlgEquiv_SA_not_set_semi'] = 'Ihre Antwort sollte eine Menge sein, ist es aber nicht. Beachten Sie die Syntax: Bei einer Menge wird die Auflistung der Elemente (durch Semikola getrennt) in geschweifte Klammern eingeschlossen.';
$string['ATAlgEquiv_SA_not_string'] = 'Ihre Antwort sollte eine Zeichenfolge sein, ist es aber nicht.';
$string['ATAlgEquiv_TA_not_equation'] = 'Ihre Antwort ist eine Gleichung aber es wird keine Gleichung erwartet. Vielleicht haben Sie etwas wie "y=2*x+1" eingegeben wenn "2*x+1" ausreichend ist.';
$string['ATAntidiff_diff'] = 'Vermutlich haben Sie stattdessen differenziert!';
$string['ATAntidiff_error_list'] = 'Die Antwortüberprüfung ist fehlgeschlagen. Bitte konktaktieren Sie Ihre Administrator/innen.';
$string['ATAntidiff_generic'] = 'Die Ableitung Ihrer Antwort sollte gleich dem Ausdruck sein, den Sie integrieren sollten, also: {$a->m0} Tatsächlich lautet die Ableitung Ihrer Antwort in Bezug auf {$a->m1} jedoch: {$a->m2}. Sie haben etwas falsch gemacht!';
$string['ATCompSquare_SA_not_depend_var'] = 'Ihre Antwort sollte von der Variable {$a->m0} abhängen, was sie nicht tut.';
$string['ATCompSquare_false'] = '';
$string['ATCompSquare_false_no_summands'] = 'Das vollständige Quadrat ist von der Form \\( a(\\cdots\\cdots)^2 + b\\), wobei \\(a\\) und \\(b\\) nicht von Ihrer Variablen abhängen. Mehr als einer Ihrer Summanden scheint von der Variablen aus ihrer Antwort abzuhängen.';
$string['ATCompSquare_not_AlgEquiv'] = 'Ihre Antwort scheint in der richtigen Form zu sein, ist aber nicht äquivalent zur korrekten Antwort.';
$string['ATCompSquare_true'] = '';
$string['ATDiff_error_list'] = 'Die Antwortüberprüfung ist fehlgeschlagen. Bitte kontaktieren Sie Ihren Administrator.';
$string['ATDiff_int'] = 'Vermutlich haben Sie stattdessen integriert!';
$string['ATEqualComAssRules_Opt_Incompatible'] = 'Die Option für diese Antwortüberprüfung enthält Regeln, die nicht mit einander vereinbar sind.  Dies ist ein Fehler.  Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATEqualComAssRules_Opt_List'] = 'Die Option für diese Antwortüberprüfung muss eine nicht-leere Liste von unterstützten Regeln sein. Dies ist ein Fehler. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATEquivFirst_SA_not_list'] = 'Das erste Argument für die Antwortüberprüfung "Equiv" sollte eine Liste sein, aber die Überprüfung ist fehlgeschlagen. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATEquivFirst_SA_wrong_end'] = 'Ihre letzte Antwort ist nicht in der richtigen Form.';
$string['ATEquivFirst_SA_wrong_start'] = 'Die erste Zeile in Ihrem Argument muss "{$a->m0}" lauten.';
$string['ATEquivFirst_SB_not_list'] = 'Das zweite Argument für die Antwortüberprüfung "Equiv" sollte eine Liste sein, aber die Überprüfung ist fehlgeschlagen. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATEquiv_SA_not_list'] = 'Das erste Argument für die Antwortüberprüfung "Equiv" sollte eine Liste sein, aber die Überprüfung ist fehlgeschlagen. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATEquiv_SB_not_list'] = 'Das zweite Argument für die Antwortüberprüfung "Equiv" sollte eine Liste sein, aber die Überprüfung ist fehlgeschlagen. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATFacForm_error_list'] = 'Die Antwortüberprüfung ist fehlgeschlagen. Bitte konktaktieren Sie Ihren Administrator.';
$string['ATFacForm_isfactored'] = 'Ihre Antwort ist faktorisiert. Gut gemacht!';
$string['ATFacForm_notalgequiv'] = 'Ihre Antwort ist nicht algebraisch äquivalent zur korrekten Antwort. Sie haben etwas falsch gemacht.';
$string['ATFacForm_notfactored'] = 'Ihre Antwort ist nicht faktorisiert.';
$string['ATFacForm_notpoly'] = 'Dieser Term sollte ein Polynom sein, ist es aber nicht.';
$string['ATInequality_backwards'] = 'Ihre Ungleichung ist falschherum.';
$string['ATInequality_nonstrict'] = 'Ihre Ungleichung sollte strikt/streng sein!';
$string['ATInequality_strict'] = 'Ihre Ungleichung sollte nicht strikt/streng sein!';
$string['ATInt_EqFormalDiff'] = 'Die formale Ableitung Ihrer Antwort stimmt mit den Ausdruck überein, den Sie laut Aufgabenstellung integrieren sollten. Allerdings weicht Ihre Antwort signifikant von der richtigen Antwort ab (d.h. nicht nur eine unterschiedliche Konstante). Bitte fragen Sie bei ihrem Kursleiter nach.';
$string['ATInt_STACKERROR_OptList'] = 'Die Antwortüberprüfung wurde nicht korrekt durchgeführt: Bitte benachrichtigen Sie Ihre/n Trainer/in. Wenn die Option zur Antwortüberprüfung "Int" eine Liste ist, muss sie genau zwei Elemente enthalten, tut es aber nicht.';
$string['ATInt_const'] = 'Sie müssen eine Konstante bei der Stammfunktion angeben. Ansonsten sieht alles richtig aus. Gut gemacht!';
$string['ATInt_const_int'] = 'Sie müssen eine Konstante bei der Stammfunktion angeben. Dies sollte eine beliebige Konstante sein und kein fester Wert.';
$string['ATInt_diff'] = 'Vermutlich haben Sie stattdessen abgeleitet!';
$string['ATInt_error_list'] = 'Die Antwortüberprüfung ist fehlgeschlagen. Bitte kontaktieren Sie Ihren Systemadministrator.';
$string['ATInt_generic'] = 'Die formale Ableitung Ihrer Antwort sollte mit dem Ausdruck übereinstimmen, den Sie laut Aufgabenstellung integrieren sollten: Also {$a->m0}. Aber die Ableitung Ihrer Antwort nach {$a->m1} ist: {$a->m2}. Daher haben Sie etwas falsch gemacht!';
$string['ATInt_logabs'] = 'Ihr/e Trainer/in erwartet vielleicht, dass Sie das Ergebnis \\(\\int\\frac{1}{x} dx = \\log(|x|)+c\\) verwenden, anstatt \\(\\int\\frac{1}{x} dx = \\log(x)+c\\).  Bitte fragen Sie Ihr/e Trainer/in danach.';
$string['ATInt_logabs_inconsistent'] = 'Es scheint seltsame Unstimmigkeiten zwischen Ihrer Verwendung von \\(\\log(...)\\) und \\(\\log(|...|)\\) zu geben. Bitte fragen Sie Ihre/n Trainer/in diesbezüglich.';
$string['ATInt_weirdconst'] = 'Die formale Ableitung Ihrer Antwort stimmt mit dem Ausdruck überein, den Sie laut Aufgabenstellung integrieren sollten. Die Integrationskonstante ist allerdings seltsam. Bitte fragen Sie Ihren Kursleiter.';
$string['ATLevenshtein_SA_not_string'] = 'Das erste Argument der Levenshtein-Antwortüberprüfung muss eine Zeichenkette sein. Der Test ist fehlgeschlagen. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATLevenshtein_SB_malformed'] = 'Das zweite Argument der Levenshtein-Antwortüberprüfung muss in der Form [erlaubt, verboten] sein, wobei jedes Element eine Liste mit Zeichenketten ist. Dieses Argument ist fehlerhaft, sodass der Test fehlgeschlagen ist. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATLevenshtein_match'] = 'Die nächste Übereinstimmung war "{$a->m0}".';
$string['ATLevenshtein_tol_not_number'] = 'Die Toleranz in der Levenshtein-Antwortüberprüfung muss eine Zahl sein, ist es aber nicht. Der Test ist fehlgeschlagen. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATLevenshtein_upper_not_boolean'] = 'Die Option zur Groß-/Kleinschreibung in der Levenshtein-Antwortüberprüfung muss ein Wahrheitswert sein, ist es aber nicht. Der Test ist fehlgeschlagen. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATList_wrongentries'] = 'Die rot unterstrichenen Einträge sind falsch. {$a->m0}';
$string['ATList_wronglen'] = 'Ihre Liste sollte {$a->m0} Elemente enthalten, sie hat aber {$a->m1}.';
$string['ATLowestTerms_entries'] = 'Die folgenden Ausdrücke sind nicht vollständig gekürzt. {$a->m0} Bitte versuchen Sie es noch einmal.';
$string['ATLowestTerms_not_rat'] = 'Sie müssen folgendes aus dem Nenner Ihres Bruches entfernen: {$a->m0}';
$string['ATLowestTerms_wrong'] = 'Sie müssen die Brüche in Ihrer Antwort eliminieren.';
$string['ATMatrix_wrongentries'] = 'Die rot unterstrichenen Einträge sind falsch. {$a->m0}';
$string['ATMatrix_wrongsz'] = 'Ihre Matrix sollte die Größe {$a->m0} x {$a->m1} haben, sie ist aber vom Typ {$a->m2} x {$a->m3}.';
$string['ATNumDecPlacesWrong_OptNotInt'] = 'Für die Antwortüberprüfung "NumDecPlacesWrong" muss die Test-Option eine positive ganze Zahl sein, aber es wurde "{$a->m0}" empfangen.';
$string['ATNumDecPlaces_Float'] = 'Ihre Antwort muss eine Fließkommazahl sein, ist es aber nicht.';
$string['ATNumDecPlaces_NoDP'] = 'Ihre Antwort sollte eine Dezimalzahl mit einem Dezimalpunkt sein.';
$string['ATNumDecPlaces_OptNotInt'] = 'Für die Antwortüberprüfung "NumDecPlaces" muss die Test-Option eine positive ganze Zahl sein, aber es wurde "{$a->m0}" empfangen.';
$string['ATNumDecPlaces_Wrong_DPs'] = 'Ihre Antwort hat eine falsche Anzahl an Dezimalstellen.';
$string['ATNumSigFigs_Inaccurate'] = 'Die Genauigkeit Ihrer Antwort ist nicht korrekt. Entweder haben Sie das Endergebnis oder einen Zwischenwert falsch gerundet.';
$string['ATNumSigFigs_NotDecimal'] = 'Ihre Antwort sollte eine Dezimalzahl sein; ist sie aber nicht!';
$string['ATNumSigFigs_WrongDigits'] = 'Ihre Antwort hat die falsche Anzahl an Dezimalstellen.';
$string['ATNumSigFigs_WrongSign'] = 'Ihre Antwort hat das falsche Vorzeichen.';
$string['ATNumSigFigs_error_list'] = 'Die Antwortüberprüfung ist fehlgeschlagen. Bitte konktaktieren Sie Ihren Administrator.';
$string['ATNum_OutofRange'] = 'Ein numerischer Ausdruck liegt außerhalb des unterstützten Bereichs. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATNumerical_FAILED'] = 'Ihre Antwort sollte eine Fließkommazahl oder eine Liste oder Menge von Zahlen sein. Das ist sie nicht.';
$string['ATNumerical_SA_not_list'] = 'Ihre Antwort sollte eine Liste sein, ist es aber nicht. Beachten Sie die Syntax: Bei einer Liste wird die Auflistung der Elemente (durch Kommata getrennt) in eckige Klammern eingeschlossen.';
$string['ATNumerical_SA_not_list_semi'] = 'Ihre Antwort sollte eine Liste sein, ist es aber nicht. Beachten Sie die Syntax: Bei einer Liste wird die Auflistung der Elemente (durch Semikola getrennt) in eckige Klammern eingeschlossen.';
$string['ATNumerical_SA_not_number'] = 'Ihre Antwort sollte eine Dezimalzahl sein, ist es aber nicht.';
$string['ATNumerical_SA_not_set'] = 'Ihre Antwort sollte eine Menge sein, ist es aber nicht. Beachten Sie die Syntax: Bei einer Menge wird die Auflistung der Elemente (durch Kommata getrennt) in geschweifte Klammern eingeschlossen.';
$string['ATNumerical_SA_not_set_semi'] = 'Ihre Antwort sollte eine Menge sein, ist es aber nicht. Beachten Sie die Syntax: Bei einer Menge wird die Auflistung der Elemente (durch Semikola getrennt) in geschweifte Klammern eingeschlossen.';
$string['ATNumerical_SB_not_number'] = 'Der für die Teacher\'s Answer angegebene Wert sollte eine Fließkommazahl sein, ist es aber nicht. Dies ist ein interner Fehler im Test. Bitte fragen Sie Ihre/n Trainer/in danach.';
$string['ATNumerical_STACKERROR_tol'] = 'Die numerische Toleranz für die Antwortüberprüfung "Numerical" sollte eine Fließkommazahl sein, ist es aber nicht. Dies ist ein interner Fehler bei der Überprüfung. Bitte fragen Sie Ihre Ihre/n Trainer/in diesbezüglich.';
$string['ATPartFrac_denom_ret'] = 'Schreibt man Ihre Antwort als einen einzelnen Bruch, so lautet der Nenner: {$a->m0}. Allerdings wäre {$a->m1} richtig.';
$string['ATPartFrac_diff_variables'] = 'Verwenden Sie in Ihrer Antwort die Variablen aus der Aufgabenstellung!';
$string['ATPartFrac_error_list'] = 'Die Antwortüberprüfung ist fehlgeschlagen. Bitte konktaktieren Sie Ihren Administrator.';
$string['ATPartFrac_ret_expression'] = 'Ihre Antwort als einzelner Bruch lautet: {$a->m0}';
$string['ATPartFrac_true'] = '';
$string['ATSRegExp_SA_not_string'] = 'Das erste Argument für die Antwortüberprüfung "SRegExp" muss eine Zeichenfolge (String) sein. Die Überprüfung ist fehlgeschlagen. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATSRegExp_SB_not_string'] = 'Das zweite Argument für die Antwortüberprüfung "SRegExp" muss eine Zeichenfolge (String) sein. Die Überprüfung ist fehlgeschlagen. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATSet_wrongentries'] = 'Die folgenden Einträge sind falsch, auch wenn sie in einer vereinfachten Form (im Vergleich zu Ihrer Eingabe) erscheinen. {$a->m0}';
$string['ATSet_wrongsz'] = 'Ihre Menge sollte {$a->m0} verschiedene Elemente enthalten, sie hat aber {$a->m1} Elemente.';
$string['ATSets_SA_not_set'] = 'Ihre Antwort sollte eine Menge sein, ist es aber nicht. Beachten Sie die Syntax: Bei einer Menge wird die Auflistung der Elemente (durch Kommata getrennt) in geschweifte Klammern eingeschlossen.';
$string['ATSets_SA_not_set_semi'] = 'Ihre Antwort sollte eine Menge sein, ist es aber nicht. Beachten Sie die Syntax: Bei einer Menge wird die Auflistung der Elemente (durch Semikola getrennt) in geschweifte Klammern eingeschlossen.';
$string['ATSets_SB_not_set'] = 'Der Antworttest "Mengen" erwartet als ein zweites Argument eine Menge. Dies ist ein Fehler. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATSets_duplicates'] = 'Ihre Menge scheint doppelte Einträge zu enthalten!';
$string['ATSets_missingentries'] = 'Folgendes fehlt in Ihrer Menge. {$a->m0}';
$string['ATSets_wrongentries'] = 'Diese Eingaben sollen nicht Elemente Ihrer Menge sein. {$a->m0}';
$string['ATSingleFrac_div'] = 'Ihre Antwort enthält Brüche innerhalb von Brüchen. Bitte vereinfachen Sie dies zu einem einzelnen Bruch.';
$string['ATSingleFrac_error_list'] = 'Die Antwortüberprüfung ist fehlgeschlagen. Bitte konktaktieren Sie Ihren Administrator.';
$string['ATSingleFrac_part'] = 'Ihre Antwort muss ein einzelner Bruch der Form \\( {a}\\over{b} \\) sein.';
$string['ATSingleFrac_ret_exp'] = 'Ihre Antwort ist nicht algebraisch äquivalent zur korrekten Antwort. Sie haben etwas falsch gemacht.';
$string['ATSingleFrac_true'] = '';
$string['ATSingleFrac_var'] = 'Verwenden Sie in Ihrer Antwort die Variablen aus der Aufgabenstellung!';
$string['ATString_SA_not_string'] = 'Ihre Antwort sollte eine Zeichenkette sein, das ist sie aber nicht.';
$string['ATString_SB_not_string'] = 'Die Antwort des/der Trainers/in sollte eine Zeichenkette sein, aber das ist sie nicht.';
$string['ATSubstEquiv_Opt_List'] = 'Die Option für diese Antwortüberprüfung muss eine Liste sein. Dies ist ein Fehler. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATSysEquiv_SA_extra_variables'] = 'Ihre Antwort enthält zu viele Variablen!';
$string['ATSysEquiv_SA_missing_variables'] = 'In ihre Antwort fehlen eine oder mehrere Variablen!';
$string['ATSysEquiv_SA_not_eq_list'] = 'Ihre Antwort sollte eine Liste von Gleichungen sein; ist sie aber nicht!';
$string['ATSysEquiv_SA_not_list'] = 'Ihre Antwort sollte eine Liste sein; ist sie aber nicht!';
$string['ATSysEquiv_SA_not_poly_eq_list'] = 'Eine oder mehrere Gleichungen sind keine Polynomgleichungen!';
$string['ATSysEquiv_SA_system_overdetermined'] = 'Die rot unterstrichenen Einträge sind falsch. {$a->m0}';
$string['ATSysEquiv_SA_system_underdetermined'] = 'Die Gleichungen in Ihrem System scheinen korrekt zu sein, allerdings fehlen noch weitere.';
$string['ATSysEquiv_SA_wrong_variables'] = 'In Ihrer Antwort werden die falschen Variablen verwendet!';
$string['ATSysEquiv_SB_not_eq_list'] = 'Die Antwort des/der Trainers/in ist keine Liste von Gleichungen';
$string['ATSysEquiv_SB_not_list'] = 'Die Antwort des/der Trainer/in ist keine Liste. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ATSysEquiv_SB_not_poly_eq_list'] = 'Die Antwort des/der Trainer/in sollte eine Liste von Polynomialgleichungen sein; ist sie aber nicht. Bitte kontaktiere Sie Ihre/n Trainer/in.';
$string['ATUnits_SA_bad_units'] = 'Ihre Antwort muss Einheiten enthalten, und Sie müssen die Einheiten mit Multiplikationen an einen Wert anhängen, z.B. <code>3.2*m/s</code>.';
$string['ATUnits_SA_errorbounds_invalid'] = 'Ihre Antwort enthält Fehlergrenzen. Geben Sie in diesem Fall keine Fehlergrenzen an, sondern nur die Menge und die Einheiten.';
$string['ATUnits_SA_excess_units'] = 'Ihre Antwort verwendet Einheiten (oder Variablen), sollte dies jedoch nicht.';
$string['ATUnits_SA_no_units'] = 'Ihre Antwort muss Einheiten beinhalten.';
$string['ATUnits_SA_not_expression'] = 'Ihre Antwort muss eine Zahl mit Einheiten sein. Verwenden Sie keine Mengen, Listen, Gleichungen oder Matrizen.';
$string['ATUnits_SA_only_units'] = 'Ihre Antwort muss eine Zahl zusammen mit Einheiten sein. Ihre Antwort hat nur Einheiten.';
$string['ATUnits_SO_wrong_units'] = 'Die für die numerische Toleranz angegebenen Einheiten müssen mit den für die Muserlösung verwendeten Einheiten übereinstimmen. Dies ist ein interner Fehler bei der Überprüfung. Bitte fragen Sie Ihre/n Trainer/in diesbezüglich.';
$string['ATUnits_compatible_units'] = 'Ihre Einheiten unterscheiden sich von denen ihrer/s Dozierenden, sind aber mit ihnen kompatibel. Numerische Werte werden zum Vergleich in SI-Basiseinheiten konvertiert.';
$string['ATUnits_correct_numerical'] = 'Bitte überprüfen Sie Ihre Einheiten sorgfältig.';
$string['ATUnits_incompatible_units'] = 'Ihre Einheiten sind nicht mit den verwendeten Einheiten ihrer/s Dozierenden kompatibel.';
$string['ATValidator_STACKERROR_ev'] = 'Der Validator hat bei seiner Auswertung einen Fehler ausgelöst.  Dies ist ein Fehler in der Überprüfung.  Bitte wenden Sie sich an Ihre Trainer/innen.';
$string['ATValidator_not_fun'] = 'Der Validator konnte nicht ausgewertet werden.  Haben Sie den richtigen Namen der Validatorfunktion angegeben?  Dies ist ein Fehler in der Überprüfung.  Bitte wenden Sie sich an Ihre Trainer/innen.';
$string['ATValidator_res_not_string'] = 'Das Ergebnis Ihres Validators muss eine Zeichenkette sein, ist es aber nicht.  Dies ist ein Fehler in der Überprüfung.  Bitte wenden Sie sich an Ihre Trainer/innen.';
$string['AT_EmptySA'] = 'Es wurde versucht, eine Antwortüberprüfung mit einer leeren Studentenantwort durchzuführen, wahrscheinlich ein CAS-Validierungsproblem beim Erstellen der Frage.';
$string['AT_EmptyTA'] = 'Es wurde versucht, eine Antwortüberprüfung mit einer leeren Musterlösung durchzuführen, wahrscheinlich ein CAS-Validierungsproblem beim Erstellen der Frage.';
$string['AT_InvalidOptions'] = 'Das Optionsfeld ist ungültig. {$a->errors}';
$string['AT_MissingOptions'] = 'Fehlende Option bei der Antwortüberprüfung.';
$string['Bad_assignment'] = 'Wenn Sie die Werte einer Variablen auflisten, sollten Sie dies auf folgende Weise tun: {$a->m0}. Bitte passen Sie Ihre Eingabe entsprechend an.';
$string['CommaError'] = 'Ihre Antwort enthält Kommata, die nicht Teil einer Liste, Menge oder Matrix sind. <ul><li>Wenn Sie eine Liste eingeben wollten, verwenden Sie bitte <tt>[...]</tt>,</li><li>wenn Sie eine Menge eingeben wollten, verwenden Sie bitte <tt>{...}</tt>.</li></ul>';
$string['Equiv_Illegal_list'] = 'Listen sind in Äquivalenzumformungen nicht erlaubt.';
$string['Equiv_Illegal_matrix'] = 'Matrizen sind in Äquivalenzumformungen nicht erlaubt.';
$string['Equiv_Illegal_set'] = 'Mengen sind in Äquivalenzumformungen nicht erlaubt.';
$string['FacForm_UnPick_morework'] = 'Sie könnten noch etwas an dem Term {$a->m0} arbeiten.';
$string['Illegal_control_flow'] = 'Ihre Antwort enthält Kontrollflussanweisungen wie die <code>if</code>-Bedingung oder die <code>do</code>-Schleife. Diese sind hier nicht erlaubt. Sie sollten wahrscheinlich das Ergebnis dieser Anweisungen als Antwort angeben.';
$string['Illegal_extraevaluation'] = 'Der zusätzliche Evaluierungsoperator <code>"</code> von Maxima wird von STACK nicht unterstützt.';
$string['Illegal_floats'] = 'Ihre Antwort enthält Fließkommazahlen, die in dieser Aufgabe nicht erlaubt sind. Bitte geben Sie die Zahlen als Brüche ein. So sollten Sie 1/3 und nicht 0.3333 (welche nur eine Annäherung darstellt) eingeben.';
$string['Illegal_groupping'] = 'Ihre Antwort enthält Klammern, um Operationen zu gruppieren. Diese sind hier nicht erlaubt. Sie sollten den Ausdruck möglicherweise so umformulieren, dass sie eliminiert werden.';
$string['Illegal_groups'] = 'Ihre Antwort enthält Auswertungsgruppen „(a, b, c)“, die hier nicht erlaubt sind.';
$string['Illegal_identifiers_in_units'] = 'Die Eingabe enthält einen Variablennamen, obwohl nur Einheiten erwartet wurden.';
$string['Illegal_illegal_operation_in_units'] = 'Der Operator <code>{$a}</code> ist in dieser Eingabe nicht erlaubt.';
$string['Illegal_illegal_power_of_ten_in_units'] = 'Der Wert darf keine nicht-ganzzahligen Zehnerpotenzen enthalten.';
$string['Illegal_input_form_units'] = 'Diese Eingabe erwartet einen numerischen Wert, gefolgt von oder multipliziert mit einem Ausdruck, der eine Einheit definiert, z.B. <code>1.23*W/m^2</code>. Beachten Sie, dass die hier erforderliche Einheit etwas anderes sein kann.';
$string['Illegal_lists'] = 'Ihre Antwort enthält Listen "[a,b,c]". Diese sind hier nicht erlaubt.';
$string['Illegal_sets'] = 'Ihre Antwort enthält Mengen "{a,b,c}". Diese sind hier nicht erlaubt.';
$string['Illegal_singleton_floats'] = 'Diese Eingabe akzeptiert keine Dezimalzahlen in der angegebenen Form. Diese Eingabe erfordert einen numerischen Wert, der in einer der folgenden Formen dargestellt wird: <code>{$a->forms}</code>';
$string['Illegal_singleton_integer'] = 'Diese Eingabe akzeptiert keine ganzzahligen Werte. Diese Eingabe erfordert einen numerischen Wert, der in einer der folgenden Formen dargestellt wird: <code>{$a->forms}</code>';
$string['Illegal_singleton_power'] = 'Diese Eingabe erfordert einen numerischen Wert, der in einer der folgenden Formen dargestellt wird: <code>{$a->forms}</code>';
$string['Illegal_strings'] = 'Ihre Antwort enthält Zeichenfolgen ("Strings"), die hier nicht erlaubt sind.';
$string['Illegal_x10'] = 'Ihre Antwort scheint das Zeichen "x" als Multiplikationszeichen zu verwenden. Bitte verwenden Sie <code>*</code> für die Multiplikation.';
$string['Interval_backwards'] = 'Beim Konstruieren eines reellen Intervalls müssen die Grenzen geordnet werden. {$a->m0} sollte {$a->m1} lauten.';
$string['Interval_illegal_entries'] = 'Folgendes sollte während der Konstruktion reeller Mengen nicht auftreten: {$a->m0}';
$string['Interval_notinterval'] = 'Ein Intervall wurde erwartet, aber stattdessen haben wir {$a->m0}.';
$string['Interval_wrongnumargs'] = 'Die Intervallkonstruktion muss genau zwei Argumente haben, es muss sich also um einen Fehler handeln: {$a->m0}.';
$string['Lowest_Terms'] = 'Ihre Antwort enthält Brüche, die nicht vollständig gekürzt sind. Bitte kürzen Sie die Brüche und versuchen Sie es noch einmal.';
$string['Maxima_Args'] = 'args: Das Argument muss ein nicht-atomarer Ausdruck sein.';
$string['Maxima_DivisionZero'] = 'Division durch Null.';
$string['Subst'] = 'Ihre Antwort wäre richtig, wenn man die folgende Variablensubstitution vornimmt. {$a->m0}';
$string['TEST_FAILED'] = 'Der Antworttest wurde nicht korrekt ausgeführt. Bitte kontaktieren Sie Ihre/n Trainer/in. {$a->errors}';
$string['TEST_FAILED_Q'] = 'Der Antworttest wurde nicht korrekt ausgeführt: Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ValidateVarsMissing'] = 'Die folgenden Variablen fehlen: {$a->m0}.';
$string['ValidateVarsSpurious'] = 'Die folgenden Variablen sind nicht notwendig: {$a->m0}.';
$string['Variable_function'] = 'Das Folgende erscheint in Ihrem Ausdruck sowohl als Variable als auch als Funktion: {$a->m0}. Bitte präzisieren Sie Ihre Eingabe. Fügen Sie entweder <code>*</code>-Symbole ein, um Funktionen zu entfernen, oder machen Sie alle vorkommenden Ausdrücke zu Funktionen.';
$string['addanothernode'] = 'Weiteren Knoten hinzufügen';
$string['addanothertestcase'] = 'Weiteren Testfall hinzufügen...';
$string['addatestcase'] = 'Testfall hinzufügen...';
$string['addingatestcase'] = 'Füge Testfall zu Frage {$a} hinzu';
$string['alg_indices_fact'] = 'Die folgenden Gesetze regeln das Rechnen mit Potenzen:
\\[a^ma^n = a^{m+n}\\]
\\[\\frac{a^m}{a^n} = a^{m-n}\\]
\\[(a^m)^n = a^{mn}\\]
\\[a^0 = 1\\]
\\[a^{-m} = \\frac{1}{a^m}\\]
\\[a^{\\frac{1}{n}} = \\sqrt[n]{a}\\]
\\[a^{\\frac{m}{n}} = \\left(\\sqrt[n]{a}\\right)^m\\]';
$string['alg_indices_name'] = 'Potenzgesetze';
$string['alg_inequalities_fact'] = '\\[a>b \\hbox{ bedeutet } a \\hbox{ ist größer als } b\\]
\\[ a < b \\hbox{ bedeutet } a \\hbox{ ist kleiner als } b\\]
\\[a\\geq b \\hbox{ bedeutet } a \\hbox{ ist größer als oder gleich } b\\]
\\[a\\leq b \\hbox{ bedeutet } a \\hbox{ ist kleiner als oder gleich } b\\]';
$string['alg_inequalities_name'] = 'Ungleichungen';
$string['alg_logarithms_fact'] = 'Für jede Basis \\(c>0\\) mit \\(c \\neq 1\\) gilt:
\\[\\log_c(a) = b \\mbox{, bedeutet} a = c^b\\]
\\[\\log_c(a) + \\log_c(b) = \\log_c(ab)\\]
\\[\\log_c(a) - \\log_c(b) = \\log_c\\left(\\frac{a}{c}\\right)\\]
\\[n\\log_c(a) = \\log_c\\left(a^n\\right)\\]
\\[\\log_c(1) = 0\\]
\\[\\log_c(b) = 1\\]
Die Formel für einen Basiswechsel lautet:
\\[\\log_a(x) = \\frac{\\log_b(x)}{\\log_b(a)}\\]
Logarithmen zur Basis \\(e\\), bezeichnet mit \\(\\log_e\\) oder auch \\(\\ln\\), nennt man natürliche Logarithmen. Der Buchstabe \\(e\\) bezeichnet die Exponentialkonstante; diese ist ungefähr \\(2.718\\).';
$string['alg_logarithms_name'] = 'Die Rechenregeln für Logarithmen';
$string['alg_partial_fractions_fact'] = 'Echte Brüche treten bei \\[{\\frac{P(x)}{Q(x)}}\\] dann auf,
wenn \\(P\\) und \\(Q\\) Polynome sind, wobei \\(P\\) kleineren Grades ist als \\(Q\\). In diesem Fall fahren wir wie folgt vor: Schreiben Sie \\(Q(x)\\) in faktorisierter Form,
<ul>
<li>
ein <em>Linearfaktor</em> \\(ax+b\\) im Nenner führt zu einem Partialbruch der Form \\[{\\frac{A}{ax+b}}.\\]
</li>
<li>
ein <em>mehrfacher Linearfator</em> \\((ax+b)^2\\) im Nenner
führt zu einem Partialbruch der Form \\[{A\\over ax+b}+{B\\over (ax+b)^2}.\\]
</li>
<li>
ein <em>quadratischer Faktor</em> \\(ax^2+bx+c\\)
im Nenner führt zu einem Partialbruch der Form \\[{Ax+B\\over ax^2+bx+c}\\]
</li>
<li>
<em>Unechte Brüche</em> erfordern einen zusätzlichen Term, der ein Polynom des Gardes \\(n-d\\) ist, wobei \\(n\\) der Grad des Zählers (d.h. \\(P(x)\\)) und \\(d\\) der Grad des Nenners (d.h. \\(Q(x)\\)) ist.
</li></ul>';
$string['alg_partial_fractions_name'] = 'Partialbruchzerlegung';
$string['alg_quadratic_formula_fact'] = 'Liegt eine quadratische Gleichung der Form
\\[ax^2 + bx + c = 0\\]
vor, so ist die Lösung/sind die Lösungen dieser Gleichung geben durch die quadratische Lösungsformel:
\\[x = \\frac{-b \\pm \\sqrt{b^2 - 4ac}}{2a}.\\]';
$string['alg_quadratic_formula_name'] = 'Die Lösungsformel für quadratische Gleichungen';
$string['all'] = 'Alles';
$string['allnodefeedbackmustusethesameformat'] = 'Alle Rückmeldungen für alle Knoten in einem Rückmeldebaum (PRT) müssen dasselbe Textformat verwenden.';
$string['allowwords'] = 'Erlaubte Wörter';
$string['allowwords_help'] = 'Standardmäßig sind Namen von Funktionen oder Variablen von mehr als zwei Zeichen Länge nicht erlaubt. Dieses ist eine durch Kommata getrennte Liste von Namen von Funktionen oder Variablen, die in der Antwort des Studierenden erlaubt sind.';
$string['allowwords_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Allow_Words';
$string['alreadydeployed'] = 'Eine Variante, die diesem Aufgabenhinweis entspricht, wurde bereits eingesetzt.';
$string['alttextmissing'] = 'Ein oder mehrere Bilder scheinen einen fehlenden oder leeren \'alt\'-Tag in "{$a->field}" ({$a->num}) zu haben.';
$string['ansnotemismatch'] = 'Antworthinweis nicht übereinstimmend';
$string['answernote'] = 'Antworthinweis';
$string['answernote_err'] = 'Antworthinweise dürfen nicht das Zeichen | enthalten. Dieses Zeichen wird von STACK später eingefügt, um die Antworthinweise automatisch zu trennen.';
$string['answernote_err2'] = 'Hinweise zur Antwort dürfen nicht die Zeichen ; oder : enthalten. Diese Zeichen werden verwendet, um Zusammenfassungen von Frageversuchen in Offline-Berichterstellungstools aufzuteilen.';
$string['answernote_help'] = 'Dieses Tag dient zur Berichterstattung. Es bestimmt den eindeutigen Pfad durch den Baum und das Ergebnis jeder Antwort. Es wird automatisch erzeugt, kann aber auch manuell zu etwas Sinnvollem geändert werden.';
$string['answernote_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Potential_response_trees.md#Answer_note';
$string['answernotedefaultfalse'] = '{$a->prtname}-{$a->nodename}-F';
$string['answernotedefaulttrue'] = '{$a->prtname}-{$a->nodename}-T';
$string['answernoterequired'] = 'Antworthinweis darf nicht leer sein.';
$string['answernoteunique'] = 'Doppelte Antwortknoten im Antwortbaum erkannt.';
$string['answertest'] = 'Antwortüberprüfung';
$string['answertest_ab'] = 'Test';
$string['answertest_help'] = 'Eine Antwortüberprüfung ist ein Test um zwei Ausdrücke dahingehend zu vergleichen, ob sie bestimmte mathematische Eigenschaften erfüllen.';
$string['answertest_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_Tests/index.md';
$string['api_advance_variant'] = 'Nächste Variante';
$string['api_choose_file'] = 'Bitte wählen Sie eine Fragedatei';
$string['api_choose_folder'] = 'Wählen Sie einen STACK Ordner';
$string['api_choose_q'] = 'Wählen Sie eine STACK Beispiel-Datei';
$string['api_correct'] = 'Richtige Antworten';
$string['api_correct_answer'] = 'Die korrekte Antwort ist:';
$string['api_display'] = 'Frage anzeigen';
$string['api_display_correct'] = 'Korrekte Antworten anzeigen';
$string['api_display_correct_hide'] = 'Korrekte Antworten verbergen';
$string['api_error_msg'] = 'Bei der Ausführung der Anfrage ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut oder laden Sie die Seite neu.';
$string['api_errors'] = 'Fehler';
$string['api_general_errors'] = 'Allgemeine Fehler z.B. fehlerhaftes XML';
$string['api_local_file'] = 'Oder wählen Sie eine eigene Datei';
$string['api_marks_sub'] = 'Noten für diese Abgabe';
$string['api_out_of'] = 'von';
$string['api_passes'] = 'bestanden';
$string['api_q_select'] = 'Wählen Sie eine Frage';
$string['api_q_xml'] = 'Frage XML';
$string['api_read_only'] = 'Nur Lesezugriff';
$string['api_response'] = 'Antwortzusammenfassung';
$string['api_submit'] = 'Antworten abgeben';
$string['api_valid_all_parts'] = 'Bitte geben Sie gültige Antworten für alle Teilbereiche der Frage ein.';
$string['api_which_typed'] = 'die wie folgt eingegeben werden können';
$string['assumepositive'] = 'Positivitätsannahme';
$string['assumepositive_help'] = 'Diese Option setzt Maxima\'s assume_pos Variable.';
$string['assumepositive_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_options.md#Assume_Positive';
$string['assumereal'] = 'Realannahme';
$string['assumereal_help'] = 'Diese Option setzt die assume_real Variable.';
$string['assumereal_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_options.md#Assume_Real';
$string['autosimplify'] = 'Auto-Vereinfachung';
$string['autosimplify_help'] = 'Setzt die Variable "simp" in Maxima für die gesamte Aufgabe (z. B. Aufgabenvariablen, Fragentext, etc.). Wird in einem Rückmeldebaum ein anderer Wert ausgewählt, dann gilt dieser für alle Ausdrücke, die später innerhalb des Baums definiert werden.';
$string['autosimplify_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Simplification.md';
$string['autosimplifyprt'] = 'Automatisch vereinfachen';
$string['autosimplifyprt_help'] = 'Setzt die Variable "simp" in Maxima für die in diesem Rückmeldebaum definierten Feedbackvariablen. Beachten Sie, dass es von der Methode der Antwortüberprüfung abhängt, ob Ausdrücke vor der Verarbeitung vereinfacht werden. Zum Beispiel werden die Argumente für AlgEquiv vereinfacht, während sie bei EqualComAss nicht vereinfacht werden.';
$string['autosimplifyprt_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Simplification.md';
$string['autotestcase'] = 'Testfall, der annimmt, dass die Eingabe der Trainer/innen die volle Punktezahl erreicht.';
$string['basicquestionreport'] = '<i class="fa fa-bar-chart"></i> Antworten analysieren';
$string['basicquestionreport_help'] = 'Erzeugt einen sehr groben Bericht über die Versuche zu dieser Frage auf dem Server. Nützlich, um zu entscheiden, welche Tests in Rückmeldebäumen hinzugefügt werden können, um das Feedback vor dem Hintergrund, was Studierende tatsächlich antworten, zu verbessern.
(Die meisten Fragen werden nur an einer Stelle verwendet)';
$string['basicreportinputsummary'] = 'Reine Eingaben (unabhängig davon, welche Variante verwendet wurde)';
$string['basicreportraw'] = 'Rohdaten';
$string['booleangotunrecognisedvalue'] = 'Ungültige Eingabe.';
$string['boxsize'] = 'Größe der Eingabebox';
$string['boxsize_help'] = 'Breite der HTML-Eingabebox.';
$string['boxsize_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs/index.md#Box_Size';
$string['branchfeedback'] = 'Knotenzweig Feedback';
$string['branchfeedback_help'] = 'Dieser CASText kann die Aufgabenvariablen, die Eingabeelemente oder andere Feedbackvariablen verwenden. Er wird ausgewertet und angezeigt, sobald ein Studierender diesen Zweig erreicht.';
$string['bulktestallincontext'] = 'Alle überprüfen';
$string['bulktestcontinuefromhere'] = 'Erneut ausführen, oder von hier an fortsetzen';
$string['bulktestindexintro'] = 'Wenn Sie auf einen der Links klicken, werden alle Fragetests in allen STACK-Fragen in diesem Kontext ausgeführt';
$string['bulktestindexintro_desc'] = 'Das <a href="{$a->link}">Frage-Tests in Bulk-Skript ausführen</a> lässt Sie problemlos alle STACK-Fragen in einem bestimmten Kontext ausführen. Dies testet nicht nur die Fragen. Es ist darüberhinaus auch eine gute Art, den CAS-Cache nach dem Löschen erneut zu befüllen.';
$string['bulktestindextitle'] = 'Fragetests in Bulk-Skript ausführen';
$string['bulktestnodeployedseeds'] = 'Diese Frage enthält zufällige Varianten, aber keine eingesetzten Seeds.';
$string['bulktestnogeneralfeedback'] = 'Diese Frage hat kein allgemeines Feedback.';
$string['bulktestnotests'] = 'Diese Frage hat keine Tests.';
$string['bulktestrun'] = 'Alle Fragetests für alle Fragen im System ausführen (langsam, nur für Administratoren)';
$string['bulktesttitle'] = 'Alle Fragetests in {$a} ausführen';
$string['calc_chain_rule_fact'] = 'Mit der folgenden Regel können wir die Ableitung einer Komposition aus zwei Funktionen finden.
Angenommen, wir haben eine Funktion \\(f(g(x))\\) gegeben und setzen dann \\(u=g(x)\\), dann ist die Ableitung nach \\(x\\) gegeben durch
\\[\\frac{df(g(x))}{dx} = \\frac{dg(x)}{dx}\\cdot\\frac{df(u)}{du}{du}.\\]
Alternativ können wir auch schreiben:
\\[\\frac{df(x)}{dx} = f\'(g(x))\\cdot g\'(x).\\]';
$string['calc_chain_rule_name'] = 'Die Kettenregel';
$string['calc_diff_linearity_rule_fact'] = '\\[{{\\rm d}\\,\\over {\\rm d}x}\\big(af(x)+bg(x)\\big)=a{{\\rm d}f(x)\\over {\\rm d}x}+b{{\\rm d}g(x)\\over {\\rm d}x}\\quad a,b {\\rm\\  constant.}\\]';
$string['calc_diff_linearity_rule_name'] = 'Die Linearität der Ableitung';
$string['calc_diff_standard_derivatives_fact'] = 'Die folgende Tabelle zeigt die Ableitungen einiger Standardfunktionen. Es ist nützlich, diese Standardableitungen zu lernen, da sie häufig benötigt werden.

|\\(f(x)\\)|\\(f\'(x)\\)|
|--- |--- |
|\\(k\\), konstant|\\(0\\)|
|\\(x^n\\), beliebige Konstante \\(n\\)|\\(nx^{n-1}\\)|
|\\(e^x\\)|\\(e^x\\)|
|\\(\\ln(x)=\\log_{\\rm e}(x)\\)|\\(\\frac{1}{x}\\)|
|\\(\\sin(x)\\)|\\(\\cos(x)\\)|
|\\(\\cos(x)\\)|\\(-\\sin(x)\\)|
|\\(\\tan(x) = \\frac{\\sin(x)}{\\cos(x)}\\)|\\(\\sec^2(x)\\)|
|\\(cosec(x)=\\frac{1}{\\sin(x)}\\)|\\(-cosec(x)\\cot(x)\\)|
|\\(\\sec(x)=\\frac{1}{\\cos(x)}\\)|\\(\\sec(x)\\tan(x)\\)|
|\\(\\cot(x)=\\frac{\\cos(x)}{\\sin(x)}\\)|\\(-cosec^2(x)\\)|
|\\(\\cosh(x)\\)|\\(\\sinh(x)\\)|
|\\(\\sinh(x)\\)|\\(\\cosh(x)\\)|
|\\(\\tanh(x)\\)|\\(sech^2(x)\\)|
|\\(sech(x)\\)|\\(-sech(x)\\tanh(x)\\)|
|\\(cosech(x)\\)|\\(-cosech(x)\\coth(x)\\)|
|\\(coth(x)\\)|\\(-cosech^2(x)\\)|

 \\[ \\frac{d}{dx}\\left(\\sin^{-1}(x)\\right) =  \\frac{1}{\\sqrt{1-x^2}}\\]
 \\[ \\frac{d}{dx}\\left(\\cos^{-1}(x)\\right) =  \\frac{-1}{\\sqrt{1-x^2}}\\]
 \\[ \\frac{d}{dx}\\left(\\tan^{-1}(x)\\right) =  \\frac{1}{1+x^2}\\]
 \\[ \\frac{d}{dx}\\left(\\cosh^{-1}(x)\\right) =  \\frac{1}{\\sqrt{x^2-1}}\\]
 \\[ \\frac{d}{dx}\\left(\\sinh^{-1}(x)\\right) =  \\frac{1}{\\sqrt{x^2+1}}\\]
 \\[ \\frac{d}{dx}\\left(\\tanh^{-1}(x)\\right) =  \\frac{1}{1-x^2}\\]';
$string['calc_diff_standard_derivatives_name'] = 'Standardableitungen';
$string['calc_int_linearity_rule_fact'] = '\\[\\int \\left(af(x)+bg(x)\\right){\\rm d}x = a\\int\\!\\!f(x)\\,{\\rm d}x \\,+\\,b\\int \\!\\!g(x),{\\rm d}x, \\quad (a,b \\, \\, {\\rm constant.})
\\]';
$string['calc_int_linearity_rule_name'] = 'Die Linearität des Integrals';
$string['calc_int_methods_parts_fact'] = '\\[
\\int_a^b u{{\\rm d}v\\over {\\rm d}x}{\\rm d}x=\\left[uv\\right]_a^b-
\\int_a^b{{\\rm d}u\\over {\\rm d}x}v\\,{\\rm d}x\\]
oder alternativ: \\[\\int_a^bf(x)g(x)\\,{\\rm d}x=\\left[f(x)\\,\\int
g(x){\\rm d}x\\right]_a^b -\\int_a^b{{\\rm d}f\\over {\\rm
d}x}\\left\\{\\int g(x){\\rm d}x\\right\\}{\\rm d}x.\\]';
$string['calc_int_methods_parts_indefinite_fact'] = '\\[
\\int u{{\\rm d}v\\over {\\rm d}x}{\\rm d}x=uv- \\int{{\\rm d}u\\over {\\rm d}x}v\\,{\\rm d}x\\]
oder alternativ: \\[\\int f(x)g(x)\\,{\\rm d}x=f(x)\\,\\int
g(x){\\rm d}x -\\int {{\\rm d}f\\over {\\rm d}x}\\left\\{\\int g(x){\\rm d}x\\right\\}{\\rm d}x.\\]';
$string['calc_int_methods_parts_indefinite_name'] = 'Partielle Integration';
$string['calc_int_methods_parts_name'] = 'Partielle Integration';
$string['calc_int_methods_substitution_fact'] = '\\[
\\int f(u){{\\rm d}u\\over {\\rm d}x}{\\rm d}x=\\int f(u){\\rm d}u
\\quad\\hbox{and}\\quad \\int_a^bf(u){{\\rm d}u\\over {\\rm d}x}\\,{\\rm
d}x = \\int_{u(a)}^{u(b)}f(u){\\rm d}u.
\\]';
$string['calc_int_methods_substitution_name'] = 'Integration durch Substitution';
$string['calc_int_standard_integrals_fact'] = '\\[\\int k\\ dx = kx +c, \\mbox{ wobei k konstant ist.}\\]
\\[\\int x^n\\ dx  = \\frac{x^{n+1}}{n+1}+c, \\quad (n\\ne -1)\\]
\\[\\int x^{-1}\\ dx = \\int {\\frac{1}{x}}\\ dx = \\ln(|x|)+c = \\ln(k|x|) = \\left\\{\\matrix{\\ln(x)+c & x>0\\cr
\\ln(-x)+c & x<0\\cr}\\right.\\]

|\\(f(x)\\)|\\(\\int f(x)\\ dx\\)||
|--- |--- |--- |
|\\(e^x\\)|\\(e^x+c\\)||
|\\(\\cos(x)\\)|\\(\\sin(x)+c\\)||
|\\(\\sin(x)\\)|\\(-\\cos(x)+c\\)||
|\\(\\tan(x)\\)|\\(\\ln(\\sec(x))+c\\)|\\(-\\frac{\\pi}{2} < x < \\frac{\\pi}{2}\\)|
|\\(\\sec x\\)|\\(\\ln (\\sec(x)+\\tan(x))+c\\)|\\( -{\\pi\\over 2}< x < {\\frac{\\pi}{2}}\\)|
|\\(\\mbox{cosec}(x)\\)|\\(\\ln (\\mbox{cose}c(x)-\\cot(x))+c\\quad\\)   |\\(0 < x < \\pi\\)|
|cot\\(\\,x\\)|\\(\\ln(\\sin(x))+c\\)|\\(0< x< \\pi\\)|
|\\(\\cosh(x)\\)|\\(\\sinh(x)+c\\)||
|\\(\\sinh(x)\\)|\\(\\cosh(x) + c\\)||
|\\(\\tanh(x)\\)|\\(\\ln(\\cosh(x))+c\\)||
|\\(\\mbox{coth}(x)\\)|\\(\\ln(\\sinh(x))+c \\)|\\(x>0\\)|
|\\({1\\over x^2+a^2}\\)|\\({1\\over a}\\tan^{-1}{x\\over a}+c\\)|\\(a>0\\)|
|\\({1\\over x^2-a^2}\\)|\\({1\\over 2a}\\ln{x-a\\over x+a}+c\\)|\\(|x|>a>0\\)|
|\\({1\\over a^2-x^2}\\)|\\({1\\over 2a}\\ln{a+x\\over a-x}+c\\)|\\(|x|\\)|
|\\(\\frac{1}{\\sqrt{x^2+a^2}}\\)|\\(\\sinh^{-1}\\left(\\frac{x}{a}\\right) + c\\)|\\(a>0\\)|
|\\({1\\over \\sqrt{x^2-a^2}}\\)|\\(\\cosh^{-1}\\left(\\frac{x}{a}\\right) + c\\)|\\(x\\geq a > 0\\)|
|\\({1\\over \\sqrt{x^2+k}}\\)|\\(\\ln (x+\\sqrt{x^2+k})+c\\)||
|\\({1\\over \\sqrt{a^2-x^2}}\\)|\\(\\sin^{-1}\\left(\\frac{x}{a}\\right)+c\\)|\\(-a\\leq x\\leq a\\)|';
$string['calc_int_standard_integrals_name'] = 'Standardintegrale';
$string['calc_product_rule_fact'] = 'Mit der folgenden Regel kann die Ableitung des Produkts zweier Funktionen bestimmt werden. Nehmen wir an, dass wir \\(f(x)g(x)\\) nach \\(x\\) ableiten möchten.
\\[ \\frac{\\mathrm{d}}{\\mathrm{d}{x}} \\big(f(x)g(x)\\big) = f(x) \\cdot \\frac{\\mathrm{d} g(x)}{\\mathrm{d}{x}}  + g(x)\\cdot \\frac{\\mathrm{d} f(x)}{\\mathrm{d}{x}},\\] or, using alternative notation, \\[ (f(x)g(x))\' = f\'(x)g(x)+f(x)g\'(x). \\]';
$string['calc_product_rule_name'] = 'Die Produktregel';
$string['calc_quotient_rule_fact'] = 'Die Quotientenregel der Differenzialrechnung besagt, dass für zwei beliebige differenzierbare Funktionen \\(f(x)\\) und \\(g(x)\\) gilt:
 \\[\\frac{\\mathrm{d}}{\\mathrm{d}x}\\left(\\frac{f(x)}{g(x)}\\right)=\\frac{g(x)\\cdot\\frac{\\mathrm{d}f(x)}{\\mathrm{d}x}\\ \\ - \\ \\ f(x)\\cdot \\frac{\\mathrm{d}g(x)}{\\mathrm{d}x}}{g(x)^2}. \\]';
$string['calc_quotient_rule_name'] = 'Die Quotientenregel';
$string['calc_rules_fact'] = '<b>Die Produktregel</b><br />Mit der folgeenden Regel kann das Produkt zweier Funktionen abgeleitet werden. Nehmen wir an, dass wir \\(f(x)g(x)\\) nach \\(x\\) ableiten möchten.
\\[ \\frac{\\mathrm{d}}{\\mathrm{d}{x}} \\big(f(x)g(x)\\big) = f(x) \\cdot \\frac{\\mathrm{d} g(x)}{\\mathrm{d}{x}}  + g(x)\\cdot \\frac{\\mathrm{d} f(x)}{\\mathrm{d}{x}},\\] or, using alternative notation, \\[ (f(x)g(x))\' = f\'(x)g(x)+f(x)g\'(x). \\]
<b>Die Quotientenregel</b><br />Die Quotientenregel der Differenzialrechnung besagt, dass für zwei beliebige differenzierbare Funktionen \\(f(x)\\) und \\(g(x)\\),
\\[\\frac{d}{dx}\\left(\\frac{f(x)}{g(x)}\\right)=\\frac{g(x)\\cdot\\frac{df(x)}{dx}\\ \\ - \\ \\ f(x)\\cdot \\frac{dg(x)}{dx}}{g(x)^2} \\] gilt.
<b>Die Kettenregel</b><br />Mit der folgenden Regel können wir die Ableitung einer Komposition aus zwei Funktionen finden.
Angenommen wir haben eine Funktion \\(f(g(x))\\) gegeben und setzen dann \\(u=g(x)\\), dann ist die Ableitung nach \\(x\\) gegeben durch:
\\[\\frac{df(g(x))}{dx} = \\frac{dg(x)}{dx}\\cdot\\frac{df(u)}{du}.\\]
Alertantiv können wir schreiben:
\\[\\frac{df(x)}{dx} = f\'(g(x))\\cdot g\'(x).\\]';
$string['calc_rules_name'] = 'Rechenregeln';
$string['casdisplay'] = 'CAS Anzeige';
$string['cassuitecolerrors'] = 'CAS Fehler';
$string['castext'] = 'CAS Text';
$string['castext_debug_header_disp_no_simp'] = 'Angezeigter Wert';
$string['castext_debug_header_disp_simp'] = 'Vereinfachter angezeigter Wert';
$string['castext_debug_header_key'] = 'Name der Variablen';
$string['castext_debug_header_value_no_simp'] = 'Wert';
$string['castext_debug_header_value_simp'] = 'Vereinfachter Wert';
$string['castext_debug_no_vars'] = 'Diese Frage hat keine Aufgabenvariablen zum Debuggen!';
$string['castext_error_header'] = 'Die Darstellung des Textinhalts ist fehlgeschlagen.';
$string['castext_error_unevaluated'] = 'Dieser Textinhalt wurde nie ausgewertet.';
$string['casvalid'] = 'V2';
$string['casvalidatemismatch'] = '[CAS validate mismatch]';
$string['casvalue'] = 'CAS Wert';
$string['chat'] = 'Sende zum CAS';
$string['chat_desc'] = 'Das <a href="{$a->link}">CAS Chat Skript</a> erlaubt es die Verbindung zum CAS zu testen, und die Maxima Syntax auszuprobieren.';
$string['chatintro'] = 'Diese Seite erlaubt die direkte Auswertung von CAS-Text. Dieses einfache Skript ist ein nützliches Minimalbeispiel und eine praktische Methode um zu überprüfen, ob das CAS funktioniert. Außerdem können verschiedene Eingabeformate getestet werden. Das erste Textfeld ermöglicht das Definieren von Variablen, das zweite ist für den CAS-Text selbst vorgesehen.';
$string['chattitle'] = 'Teste die Verbindung zum CAS';
$string['checkanswertype'] = 'Überprüfung der Antworttypen';
$string['checkanswertype_help'] = 'Falls ja, werden Antworten mit verschiedenen Typen (Term, Gleichung, Matrix, Liste, Menge), als ungültig verworfen.';
$string['checkanswertype_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs/index.md#Check_Type';
$string['clearedthecache'] = 'CAS Caches sind gelöscht worden.';
$string['clearingcachefiles'] = 'Löschen von zwischengespeicherten STACK Plot-Dateien {$a->done}/{$a->total}';
$string['clearthecache'] = 'Cache löschen';
$string['completetestcase'] = 'Füllen Sie den Rest des Formulars aus, um einen Testfall zu erstellen';
$string['complexno'] = 'Bedeutung und Anzeige von sqrt(-1)';
$string['complexno_help'] = 'Steuert die Bedeutung und Anzeige des Symbols i und sqrt(-1)';
$string['complexno_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_options.md#sqrt_minus_one.';
$string['confirmthistestcase'] = 'Aktuelles Test-Verhalten bestätigen';
$string['createtestcase'] = 'Testfall erstellen';
$string['currentlyselectedvariant'] = 'Dies ist die unten gezeigte Variante';
$string['ddl_badanswer'] = 'Das Musterlösungsfeld  für diese Eingabe ist falsch formatiert:  <code>{$a}</code>.';
$string['ddl_duplicates'] = 'Bei der Generierung der Eingabeoptionen wurden doppelte Werte gefunden.';
$string['ddl_empty'] = 'Es wurden keine Auswahlmöglichkeiten für dieses Dropdown-Feld angegeben.';
$string['ddl_nocorrectanswersupplied'] = 'Die/der Trainer/in hat nicht mindestens eine richtige Antwort angegeben.';
$string['ddl_runtime'] = 'Die Eingabe hat folgenden Laufzeitfehler erzeugt, der Sie daran hindert zu antworten. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['ddl_unknown'] = 'STACK empfing <code>{$a}</code>, aber dies wird von Ihre/m Trainer/in nicht als Option aufgeführt.';
$string['debuginfo'] = 'Debug-Informationen';
$string['decimals'] = 'Dezimaltrennzeichen';
$string['decimals_help'] = 'Dezimaltrennzeichen wählen';
$string['decimals_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_options.md#decimals';
$string['defaultmarkzeroifnoprts'] = 'Die Standardbewertung muss 0 sein wenn diese Frage keinen Rückmeldebaum (PRT) hat.';
$string['defaultprtcorrectfeedback'] = 'Richtige Antwort, gut gemacht!';
$string['defaultprtincorrectfeedback'] = 'Falsche Antwort.';
$string['defaultprtpartiallycorrectfeedback'] = 'Ihre Antwort ist teilweise korrekt.';
$string['deletetestcase'] = 'Lösche Testfall {$a->no} für Frage {$a->question}';
$string['deletetestcaseareyousure'] = 'Sind Sie sicher, dass Sie den Testfall {$a->no} für die Frage {$a->question} löschen möchten?';
$string['deletethistestcase'] = 'Diesen Testfall löschen.';
$string['deploy'] = 'Einzelne Variante einsetzen';
$string['deployduplicateerror'] = 'In den bereitgestellten Varianten sind identische Aufgabenhinweise gefunden worden. Wir raten nachdrücklich dazu, jeden Aufgabenhinweis nur einmal bereitzustellen. Andernfalls werden Sie Probleme haben, sinnvolle Ausgaben zu bekommen, wenn Sie die Ausgaben nach Varianten gruppieren. Bitte überlegen Sie Varianten mit identischen Aufgabenhinweisen zu löschen.';
$string['deployedprogress'] = 'Varianten einsetzen';
$string['deployedvariants'] = 'Eingesetzte Varianten';
$string['deployedvariantsn'] = 'Eingesetzte Varianten ({$a})';
$string['deployfromlist'] = 'Geben Sie eine Liste mit natürlichen Zahlen als Seeds an (ein Wert pro Zeile).';
$string['deployfromlistbtn'] = 'Varianten löschen und neue aus der Liste bereitstellen';
$string['deployfromlisterror'] = 'In Ihrer Liste mit ganzen Zahlen wurde ein Fehler festgestellt, sodass keine Änderungen an der Liste der eingesetzten Varianten vorgenommen wurden.';
$string['deployfromlistexisting'] = 'Aktuelle Seeds:';
$string['deploymanybtn'] = '# Varianten bereitstellen:';
$string['deploymanyerror'] = 'Fehler in der Benutzereingabe: Kann "{$a->err}" Variante nicht einsetzen.';
$string['deploymanynonew'] = 'Zu viele wiederholt vorhandene Aufgabenhinweise wurden generiert.';
$string['deploymanynotes'] = 'Versuche, automatisch mehrere Varianten einzusetzen. Beachten Sie, dass STACK aufgibt, wenn es 10 fehlgeschlagene Versuche gibt, einen neuen Aufgabenhinweis zu generieren, oder wenn ein Fragetest fehlschlägt.';
$string['deploymanysuccess'] = 'Anzahl der neuen Varianten, die erfolgreich erstellt, getestet und eingesetzt worden sind: {$a->no}.';
$string['deployoutoftime'] = 'Zeitlimit ungefähr um {$a->time} Sekunden überschritten. Bitte noch einmal versuchen, mehr einzusetzen.';
$string['deployremoveall'] = 'Einsatz aller Varianten zurücknehmen';
$string['deploysystematicbtn'] = 'Seeds einsetzen von 1 bis:';
$string['deploysystematicfrombtn'] = 'Seeds einsetzen von:';
$string['deploysystematicto'] = 'bis:';
$string['deploytestall'] = 'Alle Tests für alle eingesetzten Varianten durchführen (langsam)';
$string['deploytoomanyerror'] = 'STACK wird versuchen, in jedem Aufruf bis zu 100 neue Varianten einzusetzen. Keine neuen Varianten eingesetzt.';
$string['description'] = 'Beschreibung';
$string['description_err'] = 'Die Beschreibung des Knotens ist länger als 255 Zeichen.';
$string['displaymismatch'] = '[LaTeX mismatch]';
$string['dropdowngotunrecognisedvalue'] = 'Ungültige Eingabe.';
$string['editingtestcase'] = 'Bearbeite Testfall {$a->no} für Frage {$a->question}';
$string['editquestioninthequestionbank'] = '<i class="fa fa-pencil"></i> Diese Frage bearbeiten';
$string['editthistestcase'] = 'Diesen Testfall bearbeiten';
$string['equiv_AND'] = 'und';
$string['equiv_ANDOR'] = 'und/oder Durcheinander!';
$string['equiv_IMPLIES'] = 'impliziert';
$string['equiv_LET'] = 'Let';
$string['equiv_MISSINGVAR'] = 'Fehlende Aufgabenstellungen';
$string['equiv_NAND'] = 'NAND';
$string['equiv_NOR'] = 'weder';
$string['equiv_NOT'] = 'nicht';
$string['equiv_OR'] = 'oder';
$string['equiv_SAMEROOTS'] = '(Gleiche Wurzeln)';
$string['equiv_XNOR'] = 'XNOR';
$string['equiv_XOR'] = 'XOR';
$string['equivfirstline'] = 'Sie haben die erste Zeile falsch übernommen.';
$string['equivnocomments'] = 'Die Verwendung von Kommentaren ist bei diesem Eingabetyp nicht erlaubt. Bitte arbeiten Sie einfach Zeile für Zeile.';
$string['errors'] = 'Fehler';
$string['exceptionmessage'] = '{$a}';
$string['expand'] = 'Erweitern';
$string['expandtitle'] = 'Fragekategorien anzeigen';
$string['expectedanswernote'] = 'Erwarteter Antworthinweis';
$string['expectedoutcomes'] = 'Erwartete PRT Ergebnisse: [inputs used]';
$string['expectedpenalty'] = 'Erwartete Abzüge';
$string['expectedscore'] = 'Erwartete Punkte';
$string['exportthisquestion'] = '<i class="fa fa-download"></i> Diese Frage als MoodleXML exportieren';
$string['exportthisquestion_help'] = 'Dies wird eine Moodle-XML-Exportdatei erstellen, die nur diese eine Frage enthält. Ein Beispiel dafür, wann dies nützlich ist, ist, wenn Sie glauben, dass diese Frage einen Fehler in STACK aufzeigt, den Sie den Entwicklern melden möchten.';
$string['fact_sheet_preamble'] = '# Hinweise

STACK enthält eine "Formelsammlung" mit nützlichen Bestandteilen, die eine Lehrperson auf konsistente Weise einfügen kann.  Dies wird durch das "Hinweise"-System ermöglicht.

Hinweise können in jeden [CASText](../../Authoring/CASText.md) eingebunden werden.

Um einen Hinweis einzufügen, verwenden Sie die Syntax

    [[facts:tag]]

Der "tag" kann aus der folgenden Liste ausgewählt werden.

## Alle unterstützten Faktenblätter';
$string['false'] = 'Falsch';
$string['falsebranch'] = 'FALSCH-Zweig';
$string['falsebranch_help'] = 'Diese Felder kontrollieren was passiert, wenn die Antwortüberprüfung negativ ausfällt
### Mod und Punkte
Wie die Bepunktung angepasst wird. "=" setzt die Punkte auf einen bestimmten Wert. "+/-" addieren oder subtrahieren Punkte von der aktuellen Summe.

### Abzüge
Im adaptiven oder interaktiven Modus, ziehe so viele Punkte ab.

### Nächster
Soll zu einem nächsten Knoten gesprungen werden, falls ja zu welchen, ansonsten stoppe.

### Antworthinweis
Dieses Tag dient zur Berichterstattung. Es bestimmt den eindeutigen Pfad durch den Baum und das Ergebnis jeder Antwort. Es wird automatisch erzeugt, kann aber auch manuell zu etwas Sinnvollem geändert werden.';
$string['feedbackfromprtx'] = '[ Feedback von {$a}. ]';
$string['feedbackstyle0'] = 'Formativ';
$string['feedbackstyle1'] = 'Standard';
$string['feedbackstyle2'] = 'Kompakt';
$string['feedbackstyle3'] = 'Nur Symbol';
$string['feedbackstyle_help'] = 'Steuert, wie das PRT-Feedback angezeigt wird.';
$string['feedbackstyle_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Potential_response_trees.md';
$string['feedbackvariables'] = 'Feedback-Variablen';
$string['feedbackvariables_help'] = 'Die Feedback-Variablen erlauben es, die Eingabe zusammen mit den Aufgabenvariablen zu manipulieren, bevor der Rückmeldebaum durchlaufen wird. Variablen, die hier definiert werden, können überall im Rückmeldebaum benutzt werden.';
$string['feedbackvariables_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Variables.md#Feedback_variables';
$string['fieldshouldnotcontainplaceholder'] = '{$a->field} sollten keine [[{$a->type}:...]] Platzhalter enthalten.';
$string['fixdollars'] = 'Dollars korrigieren';
$string['fixdollars_help'] = 'Diese Option ist nützlich, wenn Sie TeX mit <code>$...$</code> und <code>$$...$$</code> Trennzeichen kopieren und einfügen (oder eingeben). Diese Trennzeichen werden während des Speichervorgangs durch die empfohlenen Trennzeichen ersetzt.';
$string['fixdollarslabel'] = 'Beim Speichern <code>$...$</code> mit <code>\\(...\\)</code>, <code>$$...$$</code> mit <code>\\[...\\]</code> und <code>@...@</code> mit <code>{@...@}</code> ersetzen.';
$string['forbiddendoubledollars'] = 'Verwenden Sie die Trennzeichen <code>\\(...\\)</code> für Inline-Maths und <code>\\[...\\]</code> für Anzeige-Maths. <code>$...$</code> und <code>$$...$$</code> sind nicht erlaubt. Es gibt am Ende des Formulars eine Option, um dies automatisch zu beheben.';
$string['forbidfloat'] = 'Verbiete Fließkommazahlen';
$string['forbidfloat_help'] = 'Falls JA werden Antworten von Studierenden, die Fließkommazahlen enthalten, als ungültig verworfen.';
$string['forbidfloat_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs/index.md#Forbid_Floats';
$string['forbidwords'] = 'Verbotene Wörter';
$string['forbidwords_help'] = 'Dies ist eine Komma-separierte Liste von Zeichenketten, die in den Teilnehmendenantworten verboten sind.';
$string['forbidwords_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Forbidden_Words';
$string['generalerrors'] = 'Ihre Frage enthält Fehler. Überprüfen Sie bitte die Frage nachfolgend.';
$string['generalfeedback'] = 'Allgemeines Feedback';
$string['generalfeedback_help'] = 'Das allgemeine Feedback ist ein CAS-Text. Das allgemeine Feedback, auch Musterlösung genannt, wird den Teilnehmenden nach ihrem Beantwortungsversuch gezeigt. Im Gegensatz zum spezifischen Feedback wird es allen Teilnehmenden unabhängig von ihrer eingegebenen Antwort angezeigt. Hier können Aufgabenvariablen verwendet werden.';
$string['generalfeedback_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/CASText.md#general_feedback';
$string['greek_alphabet_fact'] = '||||
|--- |--- |--- |
|Großbuchstaben, \\(\\quad\\)|Kleinbuchstaben, \\(\\quad\\)|name|
|\\(A\\)|\\(\\alpha\\)|alpha|
|\\(B\\)|\\(\\beta\\)|beta|
|\\(\\Gamma\\)|\\(\\gamma\\)|gamma|
|\\(\\Delta\\)|\\(\\delta\\)|delta|
|\\(E\\)|\\(\\epsilon\\)|epsilon|
|\\(Z\\)|\\(\\zeta\\)|zeta|
|\\(H\\)|\\(\\eta\\)|eta|
|\\(\\Theta\\)|\\(\\theta\\)|theta|
|\\(K\\)|\\(\\kappa\\)|kappa|
|\\(M\\)|\\(\\mu\\)|mu|
|\\(N\\)|\\( u\\)|nu|
|\\(\\Xi\\)|\\(\\xi\\)|xi|
|\\(O\\)|\\(o\\)|omicron|
|\\(\\Pi\\)|\\(\\pi\\)|pi|
|\\(I\\)|\\(\\iota\\)|iota|
|\\(P\\)|\\(\\rho\\)|rho|
|\\(\\Sigma\\)|\\(\\sigma\\)|sigma|
|\\(\\Lambda\\)|\\(\\lambda\\)|lambda|
|\\(T\\)|\\(\\tau\\)|tau|
|\\(\\Upsilon\\)|\\(\\upsilon\\)|upsilon|
|\\(\\Phi\\)|\\(\\phi\\)|phi|
|\\(X\\)|\\(\\chi\\)|chi|
|\\(\\Psi\\)|\\(\\psi\\)|psi|
|\\(\\Omega\\)|\\(\\omega\\)|omega|';
$string['greek_alphabet_name'] = 'Das griechische Alphabet';
$string['healthautomaxopt'] = 'Automatisch ein optimiertes Maxima-Image erstellen';
$string['healthautomaxopt_failed'] = 'Optimisiertes Maxima-Image Erstellen FEHLGESCHLAGEN: [{$a->errmsg}]';
$string['healthautomaxopt_nolisp'] = 'Die LISP-Version konnte nicht ermittelt werden, daher wurde das Maxima-Image nicht automatisch erstellt.';
$string['healthautomaxopt_nolisprun'] = 'Es ist nicht möglich lisp.run automatisch zu finden. Versuchen Sie "sudo updatedb" von der Kommandozeile aus und lesen Sie die Dokumentation.';
$string['healthautomaxopt_notok'] = 'Maxima-Image wurde nicht automatisch erstellt.';
$string['healthautomaxopt_ok'] = 'Maxima-Image erstellt um: <tt>{$a->command}</tt>';
$string['healthautomaxopt_succeeded'] = 'Optimisiertes Maxima-Image Erstellen ERFOLGREICH';
$string['healthautomaxoptintro'] = 'Zum Verbessern der Leistung benötigen wir ein optimiertes Maxima auf Linux Servern. Nutzen Sie das Plugin "healthcheck" oder schauen Sie hierzu in die Dokumentation.';
$string['healthcheck'] = 'STACK Funktionscheck';
$string['healthcheck_desc'] = 'Das <a href="{$a->link}">Funktionscheckskript</a> hilft ihnen zu überprüfen, ob die Bestandteile von STACK reibungslos funktionieren.';
$string['healthcheckcache_db'] = 'CAS Ergebnisse werden in der Datenbank gecached.';
$string['healthcheckcache_none'] = 'CAS Ergebnisse werden nicht gecached.';
$string['healthcheckcache_otherdb'] = 'CAS Ergebnisse werden in einer anderen Datenbank gecached.';
$string['healthcheckcachestatus'] = 'Der Cache enthält momentan {$a} Einträge.';
$string['healthcheckconnect'] = 'Versuche zum CAS zu verbinden';
$string['healthcheckconnectintro'] = 'Es wird versucht folgenden CAS-Text auszuwerten:';
$string['healthcheckcreateimage'] = 'Maxima-Image erstellen';
$string['healthcheckfilters'] = 'Stellen Sie bitte sicher, dass der {$a->filter} auf der <a href="{$a->url}">Filter-Übersichtsseite in der Administration</a> aktiviert ist.';
$string['healthcheckgeogebra'] = 'GeoGebra-Block';
$string['healthchecklatex'] = 'Überprüfen Sie, ob LaTeX korrekt konvertiert wurde';
$string['healthchecklatexintro'] = 'STACK generiert LaTeX on-the-fly und ermöglicht es, LaTeX-Code in Aufgabentexten zu verwenden. Es wird davon ausgegangen, dass dieser LaTex-Code anschießend von einem Moodle-Filter konvertiert wird.
Unten sind ein paar Beispiele für abgesetzte und Inline-Formeln in LaTeX, die im Browser korrekt angezeigt werden sollten. Fehler an dieser Stelle zeigen Probleme mit dem Moodle-Filter auf, nicht von STACK selbst.
STACK selbst nutzt die einfache und Doppeldollar-Notation, aber eventuell verwenden manche Frage-Autoren eine andere Notation.';
$string['healthchecklatexmathjax'] = 'STACK basiert auf dem Moodle MathJax-Filter. Eine Alternative besteht darin, JavaScript-Code zu Moodles zusätzlichem HTML hinzuzufügen. Mehr Informationen zu dieser Option finden Sie in den STACK-Installationsdokumenten.';
$string['healthcheckmathsdisplaymethod'] = 'Verwendete Maths Anzeigemethode: {$a}.';
$string['healthcheckmaximaavailable'] = 'Auf diesem Server verfügbare Maxima Versionen';
$string['healthchecknombstring'] = 'STACK v4.3 und höher erfordert das PHP-Modul mbstring, das fehlt. Bitte lesen Sie die Installationsanleitung.';
$string['healthcheckparsons'] = 'Parsons Drag&Drop Beweisblock';
$string['healthcheckplots'] = 'Grafiken zeichnen';
$string['healthcheckplotsintro'] = 'Es sollten zwei verschiedene Grafiken erscheinen. Wenn zwei gleiche Grafiken zu sehen sind, dann zeigt dies einen Fehler in der Benennung der Grafikdateien an. Falls keine Fehler auftauchen, aber eine Grafik fehlt, könnten folgende Hinweise hilfreich sein: (i) Überprüfen Sie die Rechteeinstellungen (insbesondere Leserechte) der zwei temporären Verzeichnisse. (ii) Ändern Sie die Optionen mit denen GNUPlot die Grafiken erstellt. Momentan gibt es kein Webinterface dafür.';
$string['healthchecksamplecas'] = 'Die Ableitung von {@ x^4/(1+x^4) @} ist \\[ \\frac{d}{dx} \\frac{x^4}{1+x^4} = {@ diff(x^4/(1+x^4),x) @}. \\]';
$string['healthchecksamplecasunicode'] = 'Bestätigung, ob Unicode unterstützt wird:\\(\\forall\\) sollte als {@unicode(8704)@} angezeigt werden.';
$string['healthchecksampledisplaytex'] = '\\[\\sum_{n=1}^\\infty \\frac{1}{n^2} = \\frac{\\pi^2}{6}.\\]';
$string['healthchecksampleinlinetex'] = '\\(\\sum_{n=1}^\\infty \\frac{1}{n^2} = \\frac{\\pi^2}{6}\\).';
$string['healthchecksampleplots'] = 'Zwei Beispiels-Plots untenan. {@plot([x^4/(1+x^4),diff(x^4/(1+x^4),x)],[x,-3,3])@} {@plot([sin(x),x,x^2,x^3],[x,-3,3],[y,-3,3],grid2d)@}  Ein dritter, kleinerer Plot mit traditionellen Achsen sollte unten angezeigt werden. {@plot([x,2*x^2-1,x*(4*x^2-3),8*x^4-8*x^2+1,x*(16*x^4-20*x^2+5),(2*x^2-1)*(16*x^4-16*x^2+1)],[x,-1,1],[y,-1.2,1.2],[box, false],[yx_ratio, 1],[axes, solid],[xtics, -3, 1, 3],[ytics, -3, 1, 3],[size,250,250])@}';
$string['healthchecksstacklibrariesworking'] = 'Optionale Maxima Bibliotheken';
$string['healthchecksstacklibrariesworkingfailed'] = 'Die folgenden optionalen Maxima Bibliotheken scheinen nicht zu laden: {$a->err}. Versuchen Sie Ihr Maxima Image neu zu erstellen.';
$string['healthchecksstacklibrariesworkingok'] = 'Die optionalen Maxima Bibliotheken scheinen richtig zu laden.';
$string['healthchecksstacklibrariesworkingsession'] = 'Die Überprüfung der optionalen Maxima Bibliotheken hat folgenden Fehler ausgegeben: {$a->err}';
$string['healthchecksstackmaximanotupdated'] = 'Es scheint, dass STACK nicht ordnungsgemäß aktualisiert wurde. Bitte besuchen Sie die Seite <a href="{$a}">Systemadministration -> Benachrichtigungen</a>.';
$string['healthchecksstackmaximatooold'] = 'So alt, dass die Version unbekannt ist!';
$string['healthchecksstackmaximaversion'] = 'Maxima-Version';
$string['healthchecksstackmaximaversionfixserver'] = 'Generieren Sie bitte den Maxima-Code auf Ihrem MaximaPool-Server neu.';
$string['healthchecksstackmaximaversionfixunknown'] = 'Es ist nicht wirklich klar, wie das passiert ist. Sie werden dieses Problem selbst beheben müssen. Beginnen Sie damit, den CAS-Cache zu löschen.';
$string['healthchecksstackmaximaversionmismatch'] = 'Die verwendete Version der STACK-Maxima-Bibliotheken ({$a->usedversion}) stimmt nicht mit der von dieser Version des STACK-Fragetyps erwarteten ({$a->expectedversion}) überein. {$a->fix}';
$string['healthchecksstackmaximaversionok'] = 'Korrekte und erwartete STACK-Maxima-Bibliotheksversion wird verwendet ({$a->usedversion}).';
$string['healthunabletolistavail'] = 'Der Plattformtyp ist derzeit nicht auf "Linux", ohne DB Cache, eingestellt und kann daher keine verfügbaren Versionen von Maxima auflisten.';
$string['healthuncached'] = 'Ungecachter CAS-Aufruf';
$string['healthuncachedintro'] = 'Dieser Abschnitt sendet immer einen echten Aufruf an das CAS, unabhängig von den aktuellen Cache-Einstellungen. Dies ist erforderlich, um sicherzustellen, dass die Verbindung zum CAS tatsächlich funktioniert.';
$string['healthuncachedstack_CAS_calculation'] = 'Erwartete CAS-Berechnung: {$a->expected}. Aktuelle CAS-Berechnung: {$a->actual}.';
$string['healthuncachedstack_CAS_not'] = 'CAS gab einige Daten wie erwartet zurück, aber es gab Fehler.';
$string['healthuncachedstack_CAS_ok'] = 'CAS gibt Daten wie erwartet zurück. Sie haben eine stehende Verbindung zum CAS.';
$string['healthuncachedstack_CAS_version'] = 'Erwartete Maxima-Version : "{$a->expected}". Aktuelle Maxima-Version: {$a->actual}.';
$string['healthuncachedstack_CAS_versionnotchecked'] = 'Sie haben die "default" Version von Maxima gewählt, also wird kein Maxima-Versionscheck vorgenommen. Ihre reine Verbindung nutzt derzeit Version {$a->actual}.';
$string['hyp_functions_fact'] = 'Hyperbolische Funktionen haben ähnliche Eigenschaften wie trigonometrische Funktionen, können aber in exponentieller Form wie folgt dargestellt werden:
\\[ \\cosh(x)      = \\frac{e^x+e^{-x}}{2}, \\qquad \\sinh(x)=\\frac{e^x-e^{-x}}{2} \\]
 \\[ \\tanh(x)      = \\frac{\\sinh(x)}{\\cosh(x)} = \\frac{{e^x-e^{-x}}}{e^x+e^{-x}} \\]
 \\[ {\\rm sech}(x) ={1\\over \\cosh(x)}={2\\over {\\rm e}^x+{\\rm e}^{-x}}, \\qquad  {\\rm cosech}(x)= {1\\over \\sinh(x)}={2\\over {\\rm e}^x-{\\rm e}^{-x}} \\]
 \\[ {\\rm coth}(x) ={\\cosh(x)\\over \\sinh(x)} = {1\\over {\\rm tanh}(x)} ={{\\rm e}^x+{\\rm e}^{-x}\\over {\\rm e}^x-{\\rm e}^{-x}}\\]';
$string['hyp_functions_name'] = 'Hyperbolische Funktionen';
$string['hyp_identities_fact'] = 'Die Ähnlichkeit zwischen der Art und Weise, wie sich hyperbolische und trigonometrische Funktionen verhalten, ist offensichtlich, wenn man einige grundlegende hyperbolische Identitäten betrachtet:
  \\[{\\rm e}^x=\\cosh(x)+\\sinh(x), \\quad {\\rm e}^{-x}=\\cosh(x)-\\sinh(x)\\]
  \\[\\cosh^2(x) -\\sinh^2(x) = 1\\]
  \\[1-{\\rm tanh}^2(x)={\\rm sech}^2(x)\\]
  \\[{\\rm coth}^2(x)-1={\\rm cosech}^2(x)\\]
  \\[\\sinh(x\\pm y)=\\sinh(x)\\ \\cosh(y)\\ \\pm\\ \\cosh(x)\\ \\sinh(y)\\]
  \\[\\cosh(x\\pm y)=\\cosh(x)\\ \\cosh(y)\\ \\pm\\ \\sinh(x)\\ \\sinh(y)\\]
  \\[\\sinh(2x)=2\\,\\sinh(x)\\cosh(x)\\]
  \\[\\cosh(2x)=\\cosh^2(x)+\\sinh^2(x)\\]
  \\[\\cosh^2(x)={\\cosh(2x)+1\\over 2}\\]
  \\[\\sinh^2(x)={\\cosh(2x)-1\\over 2}\\]';
$string['hyp_identities_name'] = 'Hyperbolische Identitäten';
$string['hyp_inverse_functions_fact'] = '\\[\\cosh^{-1}(x)=\\ln\\left(x+\\sqrt{x^2-1}\\right) \\quad \\text{ für } x\\geq 1\\]
 \\[\\sinh^{-1}(x)=\\ln\\left(x+\\sqrt{x^2+1}\\right)\\]
 \\[\\tanh^{-1}(x) = \\frac{1}{2}\\ln\\left({1+x\\over 1-x}\\right) \\quad \\text{ für } -1< x < 1\\]';
$string['hyp_inverse_functions_name'] = 'Inverse hyperbolische Funktionen';
$string['illegalcaschars'] = 'Die Zeichen @ und \\ sind in der CAS-Eingabe nicht erlaubt.';
$string['inputdisplayed'] = 'Angezeigt als';
$string['inputentered'] = 'Eingegebener Wert';
$string['inputexpression'] = 'Test-Eingabe';
$string['inputextraoptions'] = 'Zusätzliche Optionen';
$string['inputextraoptions_help'] = 'Einige Eingabetypen erfordern zusätzliche Optionen, damit sie funktionieren. Diese können Sie hier eingeben. Dieser Wert ist ein CAS-Ausdruck.';
$string['inputextraoptions_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Extra options';
$string['inputheading'] = 'Eingabe: {$a}';
$string['inputinvalidparamater'] = 'Ungültiger Parameter';
$string['inputlanguageproblems'] = 'Es gibt Unstimmigkeiten in den Eingabe- und Validierungstags zwischen Sprachen.';
$string['inputmonospace'] = 'Monospace Schriftart';
$string['inputmonospace_help'] = 'Wählen Sie die Arten von Eingaben aus, die standardmäßig in einer Monospace-Schriftart dargestellt werden sollen. Dies betrifft alle Fragen, nicht nur neue. Diese Standardeinstellungen können für ein bestimmtes Eingabefeld mit den zusätzlichen Optionen \'monospace\' und \'monospace:false\' überschrieben werden.';
$string['inputname'] = 'Eingabename';
$string['inputnameform'] = 'Die Namen von Eingaben dürfen nur aus Buchstaben, (optional) gefolgt von Zahlen, bestehen. \'{$a}\' ist nicht erlaubt.';
$string['inputnamelength'] = 'Namen von Eingaben können nicht länger als 18 Zeichen sein. \'{$a}\' ist zu lang.';
$string['inputopterr'] = 'Der Wert der Option <code>{$a->opt}</code> kann nicht als <code>{$a->val}</code> angegeben werden.';
$string['inputoptionunknown'] = 'Diese Eingabe unterstützt die Option \'{$a}\' nicht.';
$string['inputremovedconfirm'] = 'Ich bestätige, dass ich dieses Eingabefeld aus der Frage entfernen möchte.';
$string['inputremovedconfirmbelow'] = 'Das Eingabefeld \'{$a}\' wurde entfernt. Bitte bestätigen Sie dies unten.';
$string['inputs'] = 'Eingaben';
$string['inputstatus'] = 'Status';
$string['inputstatusname'] = 'Leer';
$string['inputstatusnameinvalid'] = 'Ungültig';
$string['inputstatusnamescore'] = 'Punkte';
$string['inputstatusnamevalid'] = 'Gültig';
$string['inputtest'] = 'Eingabetest';
$string['inputtype'] = 'Eingabetyp';
$string['inputtype_help'] = 'Dies bestimmt die Art des Eingabeelements, z.B. Formularfeld, Wahr/Falsch, oder Textfeld.';
$string['inputtype_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs/index.md';
$string['inputtypealgebraic'] = 'Algebraische Eingabe';
$string['inputtypeboolean'] = 'Wahr/Falsch';
$string['inputtypecheckbox'] = 'Checkbox';
$string['inputtypedropdown'] = 'Dropdown-Liste';
$string['inputtypegeogebra'] = 'GeoGebra';
$string['inputtypematrix'] = 'Matrix';
$string['inputtypenotes'] = 'Anmerkungen';
$string['inputtypenumerical'] = 'Numerisch';
$string['inputtypeparsons'] = 'Parsons';
$string['inputtyperadio'] = 'Radiobuttons';
$string['inputtypesinglechar'] = 'Einzelnes Zeichen';
$string['inputtypestring'] = 'Zeichenkette';
$string['inputtypetextarea'] = 'Textfeld';
$string['inputtypeunits'] = 'Einheiten';
$string['inputtypevarmatrix'] = 'Matrix mit variabler Größe';
$string['inputvalidatorerr'] = 'Der Name einer Validatorfunktion muss ein gültiger Maxima-Bezeichner sein, der aus Buchstaben a-zA-Z besteht, optional gefolgt von Ziffern.';
$string['inputvalidatorerrcouldnot'] = 'Der optionale Validator hat interne Maxima-Fehler ausgelöst.';
$string['inputvalidatorerrors'] = 'Der optionale Validator liefert Fehler {$a->err}.';
$string['inputwillberemoved'] = 'Dieses Eingabefeld wird nicht mehr im Fragentext erwähnt. Wenn Sie die Frage jetzt speichern, gehen die Daten zu diesem Eingabefeld verloren. Bitte bestätigen Sie, dass Sie dies tun möchten. Alternativ können Sie den Fragetext bearbeiten und die Platzhalter \'[[input:{$a}]]\' und \'[[validation:{$a}]]\' wieder einfügen.';
$string['insertspaces'] = 'Sternchen nur für Leerzeichen einfügen';
$string['insertspacesfunctions'] = 'Einfügen von Sternchen bei implizierter Multiplikation, Leerzeichen und bei nicht verfügbaren User-Funktionen';
$string['insertspacesfunctionssingle'] = 'Einfügen von Sternchen bei implizierter Multiplikation, Leerzeichen, bei nicht verfügbaren User-Funktionen und bei Variablen, bei denen nur von einem Zeichen ausgegangen wird.';
$string['insertstars'] = 'Sternchen einfügen';
$string['insertstars_help'] = 'Diese Option bietet eine Reihe verschiedener Optionen zum Einfügen von Sternchen, bei denen die Multiplikation impliziert ist. Bitte lesen Sie die ausführlichere Dokumentation.';
$string['insertstars_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs/index.md#Insert_Stars';
$string['insertstarsassumesinglechar'] = 'Sternchen einfügen, indem von Variablennamen mit nur einem Zeichen ausgegangen wird';
$string['insertstarsno'] = 'Keine Sternchen einfügen';
$string['insertstarsspaces'] = 'Sternchen für implizierte Multiplikation und für Leerzeichen einfügen';
$string['insertstarsspacessinglechar'] = 'Sternchen einfügen, indem von Variablennamen mit nur einem Zeichen ausgegangen wird, implizierte und für Leerzeichen';
$string['insertstarsyes'] = 'Sternchen nur für implizierte Multiplikation einfügen';
$string['inversetrig'] = 'Inverse trigonometrische Funktionen';
$string['inversetrig_help'] = 'Steuert, wie inverse trigonometrische Funktionen in der CAS-Ausgabe angezeigt werden.';
$string['inversetrig_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_options.md#inverse_trig';
$string['irred_Q_commonint'] = 'Sie müssen noch einen gemeinsamen Faktor ausklammern.';
$string['irred_Q_optional_fac'] = 'Sie könnten noch etwas vereinfachen, so kann {$a->m0} weiter faktorisiert werden. Allerdings ist dies nicht verlangt.';
$string['isbroken'] = 'Als beschädigt speichern';
$string['isbroken_help'] = 'Mit dieser Option können Sie Ihre Aufgabe speichern, auch wenn sie unvollständig oder fehlerhaft ist. Sie oder auch jemand anderes können dann zu einem späteren Zeitpunkt mit der Bearbeitung fortfahren. Bevor die Frage gespeichert wird, müssen dennoch alle notwendigen Felder ausgefüllt und die Checkboxen angekreuzt werden, um etwaige Löschungen von Eingabefeldern oder PRTs zu bestätigen.';
$string['isbrokenlabel'] = 'Die Frage als beschädigt markieren. Die Frage kann dann trotz der meisten Fehler gespeichert werden, wird aber den Teilnehmer/innen nicht angezeigt.';
$string['languageproblemsexist'] = 'Ihre Frage enthält möglicherweise Probleme mit der Sprache.';
$string['languageproblemsextra'] = 'Das Feld {$a->field} hat die folgenden Sprachen, die nicht im Fragetext enthalten sind: {$a->langs}.';
$string['languageproblemsmissing'] = 'Das Sprach-Tag {$a->lang} fehlt im Folgenden: {$a->missing}.';
$string['logicsymbol'] = 'Darstellung logischer Ausdrücke';
$string['logicsymbol_help'] = 'Steuert, wie logische Symbole in der CAS-Ausgabe angezeigt werden sollen.';
$string['logicsymbol_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_options.md#logicsymbol';
$string['logicsymbollang'] = 'Wörter';
$string['logicsymbolsymbol'] = 'Symbole';
$string['matrixparens'] = 'Standard Form der Matrix-Klammern';
$string['matrixparens_help'] = 'Steuert die Standard-Form von Matrixklammern, wenn sie in der CAS-Ausgabe angezeigt werden.';
$string['matrixparens_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Matrix.md#matrixparens';
$string['mbstringrequired'] = 'Für STACK ist die Installation der MBSTRING-Bibliothek erforderlich.';
$string['multcross'] = 'Kreuz';
$string['multdot'] = 'Punkt';
$string['multiplicationsign'] = 'Multiplikationszeichen';
$string['multiplicationsign_help'] = 'Steuert, wie Multiplikationszeichen angezeigt werden.';
$string['multiplicationsign_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_options.md#multiplication';
$string['multonlynumbers'] = 'Nur Zahlen';
$string['multspace'] = 'Leerzeichen';
$string['mustverify'] = 'Teilnehmende müssen validieren lassen';
$string['mustverify_help'] = 'Steuert, ob die Eingabe der Studierenden als erzwungener Prozess in zwei Schritten nochmals in validierter Form angezeigt  wird, bevor diese Eingabe dem Bewertungsmechanismus zur Verfügung gestellt wird. Syntaxfehler werden immer zurückgemeldet.';
$string['mustverify_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs/index.md#Student_must_verify';
$string['mustverifyshowvalidation'] = 'Sie können die Validierung nicht in zwei Schritten erforderlich machen, aber die Ergebnisse der Validierung nicht nach dem ersten Schritt anzeigen. Dies versetzt die Teilnehmer/innen in eine unmögliche Position.';
$string['namealreadyused'] = 'Sie haben diesen Namen bereits verwendet.';
$string['newnameforx'] = 'Neuer Name für \'{$a}\'';
$string['next'] = 'Nächster';
$string['nodeaddnum'] = 'Zu addierende Zahl (max. 9)';
$string['nodehelp'] = 'Knoten des Rückmeldebaums';
$string['nodehelp_help'] = '### Antwortüberprüfung
Eine Antwortüberprüfung ist ein Test um zwei Ausdrücke dahingehend zu vergleichen, ob sie bestimmte mathematische Eigenschaften erfüllen.

### SAns
Dies ist das erste Argument der Antwortüberprüfungsfunktion. In asymmetrischen Tests wird dies als die Teilnehmendenantwort angesehen, obwohl es jeder gültige CAS Ausdruck sein könnte. Es können darin auch Variablen aus der Aufgabe oder dem Feedback benutzt werden.

### TAns
Dies ist das zweite Argument der Antwortüberprüfungsfunktion. In asymmetrischen Tests wird dies als die Dozentenantwort angesehen, obwohl es jeder gültige CAS Ausdruck sein könnte. Es können darin auch Variablen aus der Aufgabe oder dem Feedback benutzt werden.

### Test-Optionen
Dieses Feld erlaubt Antwortüberprüfung eine Option zu verwenden, z.B. eine Variable oder eine bestimmte numerische Präzision.

### Feedback unterdrücken
Falls JA, wird jedes von der Antwortüberprüfung automatisch generierte Feedback unterdrückt und dem Studierenden nicht angezeigt. Dies betrifft aber nicht die Feedback-Felder der Verzweigungen des Rückmeldebaums.';
$string['nodeloopdetected'] = 'Ein Kreis wurde in diesem Rückmeldebaum (PRT) entdeckt.';
$string['nodenotused'] = 'Kein anderer Knoten des Rückmeldebaums (PRT) verweist auf diesen Knoten.';
$string['nodex'] = 'Knoten {$a}';
$string['nodexdelete'] = 'Lösche Knoten {$a}';
$string['nodexfalsefeedback'] = 'Knoten {$a} FALSCH Feedback';
$string['nodextruefeedback'] = 'Knoten {$a} WAHR feedback';
$string['nodexwhenfalse'] = 'Knoten {$a} wenn FALSCH';
$string['nodexwhentrue'] = 'Knoten {$a} wenn WAHR';
$string['nonempty'] = 'Dies darf nicht leer sein.';
$string['noprtsifnoinputs'] = 'Eine Frage ohne Eingaben kann keinen Rückmeldebaum (PRT) haben.';
$string['nosemicolon'] = 'Sie dürfen den Maximaausdruck nicht mit einem Semikolon beenden.';
$string['notanswered'] = '(Meine Auswahl zurücksetzen)';
$string['notavalidname'] = 'Kein gültiger Name';
$string['notestcasesyet'] = 'Es wurden bisher keine Testfälle hinzugefügt.';
$string['notsaved'] = '** FRAGE WURDE NICHT GESPEICHERT **';
$string['numericalinputdp'] = 'Sie müssen genau \\( {$a} \\) Dezimalstellen angeben.';
$string['numericalinputmaxdp'] = 'Sie dürfen höchstens \\( {$a} \\) Dezimalstellen angeben.';
$string['numericalinputmaxsf'] = 'Sie dürfen höchstens \\( {$a} \\) signifikante Stellen angeben.';
$string['numericalinputmindp'] = 'Sie müssen mindestens \\( {$a} \\) Dezimalstellen angeben.';
$string['numericalinputminmaxerr'] = 'Die geforderte Mindestanzahl an numerischen Stellen übersteigt die maximale Anzahl an Stellen!';
$string['numericalinputminsf'] = 'Sie müssen mindestens \\( {$a} \\) signifikante Stellen angeben.';
$string['numericalinputminsfmaxdperr'] = 'Geben Sie keine Anforderungen für Dezimalstellen und signifikante Stellen in derselben Eingabe an.';
$string['numericalinputmustfloat'] = 'Für diese Eingabe wird eine Fließkommazahl erwartet.';
$string['numericalinputmustint'] = 'Für diese Eingabe wird eine eindeutige, ganze Zahl erwartet.';
$string['numericalinputmustnumber'] = 'Für diese Eingabe wird eine Zahl erwartet.';
$string['numericalinputmustrational'] = 'Für diese Eingabe wird ein Bruch oder eine rationale Zahl erwartet.';
$string['numericalinputoptboolerr'] = 'Der Wert der Option <code>{$a->opt}</code> sollte boolesch sein, ist aber <code>{$a->val}</code>.';
$string['numericalinputoptinterr'] = 'Der Wert der Option <code>{$a->opt}</code> sollte eine ganze Zahl sein, ist aber <code>{$a->val}</code>.';
$string['numericalinputsf'] = 'Sie müssen genau \\( {$a} \\) signifikante Stellen angeben.';
$string['numericalinputvarsforbidden'] = 'Diese Eingabe erwartet eine Zahl und darf daher keine Variablen enthalten.';
$string['options'] = 'Optionen';
$string['overallresult'] = 'Gesamtergebnis';
$string['parsons_got_unrecognised_value'] = 'Die Eingabe von Parson ist ungültig.';
$string['penalty'] = 'Abzüge';
$string['penalty_help'] = 'Das Punktabzugsschema ermittels diesen Wert für jeden Rückmeldebaum (PRT) aus den verschiedenen gültigen Antwortversuchen, die nicht vollständig korrekt waren.';
$string['penalty_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Feedback.md';
$string['penaltyerror'] = 'Der Punktabzug muss eine numerischer Wert zwischen 0 und 1 sein, oder eine Variable (diese wird nicht überprüft).';
$string['penaltyerror2'] = 'Der Punktabzug muss leer oder ein numerischer Wert zwischen 0 und 1 sein.';
$string['phpcasstring'] = 'PHP Ausgabe';
$string['phpsuitecolerror'] = 'PHP Fehler';
$string['phpvalid'] = 'V1';
$string['phpvalidatemismatch'] = '[PHP validate mismatch]';
$string['pleaseananswerallparts'] = 'Bitte beantworten Sie alle Teile der Aufgabe.';
$string['pleasecheckyourinputs'] = 'Bitte überprüfen Sie, ob ihre Eingabe wie gewünscht interpretiert wurde.';
$string['pluginname'] = 'STACK';
$string['pluginname_help'] = 'STACK ist ein Assessmentsystem für Mathematik.';
$string['pluginnameadding'] = 'STACK-Frage hinzufügen';
$string['pluginnameediting'] = 'STACK-Frage bearbeiten';
$string['pluginnamesummary'] = 'STACK ermöglicht es mathematische Fragestellungen in Moodle-Tests zu verwenden. Es bedient sich dabei eines Computeralgebrasystems um mathematische Eigenschaften der eingegebenen Antworten zu ermitteln und diese dann zu bewerten.';
$string['privacy:metadata'] = 'Der STACK-Fragentyp speichert keine personenbezogenen Daten.';
$string['prtcorrectfeedback'] = 'Standard Feedback für richtige Antworten';
$string['prtheading'] = 'Rückmeldebaum (PRT): {$a}';
$string['prtincorrectfeedback'] = 'Standard Feedback für falsche Antworten';
$string['prtmustbesetup'] = 'Ein Rückmeldebaum (PRT) muss aufgesetzt werden, bevor die Frage gespeichert werden kann.';
$string['prtname'] = 'PRT Name';
$string['prtnamelength'] = 'Namen von Rückmeldebäumen (PRT) können nicht länger als 18 Zeichen sein. \'{$a}\' ist zu lang.';
$string['prtnodesheading'] = 'Potentielle Rückmeldebaum-Knoten ({$a})';
$string['prtpartiallycorrectfeedback'] = 'Standard Feedback für teilweise richtige Antworten';
$string['prtremovedconfirm'] = 'Ich bestätige, dass ich diesen potenziellen Rückmeldebaum aus dieser Frage entfernen möchte.';
$string['prtremovedconfirmbelow'] = 'Der potentielle Rückmeldebaum \'{$a}\' wurde entfernt. Bitte bestätigen Sie dies unten.';
$string['prtruntimeerror'] = 'Der Knoten {$a->node} hat den folgenden Laufzeitfehler generiert: {$a->error}';
$string['prtruntimepenalty'] = 'Der Punktabzug wurde nicht vollständig zu einem numerischen Wert ausgewertet (prüfen Sie die Variablennamen).';
$string['prtruntimescore'] = 'Die Punktzahl wurde nicht vollständig zu einem numerischen Wert ausgewertet (überprüfen Sie die Variablennamen).';
$string['prts'] = 'Potentielle Rückmeldebäume';
$string['prtwillbecomeactivewhen'] = 'Dieser potenzielle Rückmeldebaum wird aktiv, wenn Teilnehmer/innen folgendes geantwortet haben: {$a}';
$string['prtwillberemoved'] = 'Auf diesen Rückmeldebaum wird im Fragetext oder im spezifischen Feedback nicht mehr Bezug genommen. Wenn Sie die Frage jetzt speichern, gehen die Daten zu diesem potentiellen Rückmeldebaum verloren. Bitte bestätigen Sie, dass Sie dies tun möchten. Alternativ können Sie den Fragetext oder das spezifische Feedback bearbeiten, um den Platzhalter "[[feedback:{$a}]]" zurückzusetzen.';
$string['pslash'] = 'Schrägstriche innerhalb von Maxima-String-Variablen schützen:';
$string['qm_error'] = 'Ihre Antwort enthält das Fragezeichen "?", welches in Antworten nicht erlaubt ist. Bitte ersetzen Sie es mit konkreten Werten.';
$string['questionbroken'] = 'Diese Frage wurde während der Bearbeitung als beschädigt markiert.';
$string['questiondescription'] = 'Aufgabenbeschreibung';
$string['questiondescription_help'] = 'Die Aufgabenbeschreibung ist CASText.  Der Zweck einer Aufgabenbeschreibung ist es, einen sinnvollen Platz für die Diskussion der Frage zu bieten. Der Inhalt ist für die Studierenden nicht verfügbar.';
$string['questiondoesnotuserandomisation'] = 'Diese Frage verwendet keine Randomisierung.';
$string['questionnotdeployedyet'] = 'Keine Varianten dieser Frage wurden bisher eingesetzt.';
$string['questionnote'] = 'Aufgabenhinweis';
$string['questionnote_help'] = 'Der Aufgabenhinweis ist ein CASText. Der Zweck des Aufgabenhinweises liegt darin, dass zwischen verschiedenen zufälligen Versionen einer Frage unterschieden werden kann. Zwei Fragen sind genau dann gleich, wenn die Aufgabenhinweise gleich sind. Für die spätere Analyse ist es sehr hilfreich, aussagekräftige Antworthinweise zu verwenden. (Vermeiden Sie Bilder und Dateien - diese werden in den meisten Ausgaben nicht angezeigt).';
$string['questionnote_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_note.md';
$string['questionnote_missing'] = 'Der Aufgabenhinweis ist leer.  Bitte fügen Sie einen aussagekräftigen Aufgabenhinweis (Zusammenfassung) hinzu.';
$string['questionnotempty'] = 'Der Antworthinweis kann nicht leer sein, wenn rand() bei der Definition der Aufgabenvariablen verwendet wird. Der Aufgabenhinweis wird verwendet, um zwischen verschiedenen zufälligen Versionen der Aufgabe zu unterscheiden.';
$string['questionpreview'] = 'Vorschau der Frage';
$string['questionsimplify'] = 'Aufgabenweites Simplify';
$string['questionsimplify_help'] = 'Setzt innerhalb Maxima die globale Variable "simp" für die gesamte Aufgabe.';
$string['questionsimplify_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Simplification.md';
$string['questiontestempty'] = 'Leere Fragetests sind nicht zulässig!';
$string['questiontests'] = 'Test der Frage';
$string['questiontestsdefault'] = '(Standard)';
$string['questiontestsfor'] = 'Fragetests für Seed {$a}';
$string['questiontestspass'] = 'Alle Fragetests wurden bestanden.';
$string['questiontext'] = 'Aufgabentext';
$string['questiontext_help'] = 'Der Aufgabentext ist ein CASText. Dies ist die "Aufgabe", die der Studierenden konkret sieht. Sie müssen Eingabe- und Validierungsfelder in diesem Feld (und nur in diesem) unterbringen. Verwenden sie zum Beispiel `[[input:ans1]] [[validation:ans1]]`.';
$string['questiontext_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/CASText.md#question_text';
$string['questiontextfeedbacklanguageproblems'] = 'Es gibt Unstimmigkeiten in den Feedback-Tags zwischen den Sprachen.';
$string['questiontextfeedbackonlycontain'] = 'Der Aufgabentext zusammen mit spezifischen Feedback sollte das Token \'{$a}\' nur einmal enthalten.';
$string['questiontextlanguages'] = 'Die in Ihrer Frage gefundenen Sprach-Tags sind: {$a}.';
$string['questiontextmustcontain'] = 'Der Aufgabentext muss das Token \'{$a}\' enthalten.';
$string['questiontextnonempty'] = 'Der Aufgabentext darf nicht leer sein.';
$string['questiontextonlycontain'] = 'Der Aufgabentext sollte das Token \'{$a}\' nur einmal enthalten.';
$string['questiontextplaceholderswhitespace'] = 'Platzhalter dürfen keine Leerzeichen enthalten. Dieser scheint zu enthalten: \'{$a}\'.';
$string['questionvalue'] = 'Aufgabenwert';
$string['questionvaluepostive'] = 'Der Aufgabenwert muss nicht-negativ sein.';
$string['questionvariables'] = 'Aufgabenvariablen';
$string['questionvariables_help'] = 'Dieses Feld erlaubt es CAS Variablen zu definieren und zu verändern, z.B. um Zufallsversionen zu ermöglichen. Diese Variablen sind in allen anderen Teilen der Aufgabe verfügbar.';
$string['questionvariables_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Variables.md#Question_variables';
$string['questionvariablevalues'] = 'Fragevariabeln-Werte';
$string['questionwarnings'] = 'Fragewarnungen';
$string['questionwarnings_help'] = 'Fragewarnungen sind Hinweise auf mögliche Probleme, die Sie eventuell angehen möchten, die aber keine direkten Fehler sind.';
$string['quiet'] = 'Feedback unterdrücken';
$string['quiet_help'] = 'Falls JA, wird jedes von der Antwortüberprüfung automatisch generierte Feedback unterdrückt und dem Studierenden nicht angezeigt. Dies betrifft aber nicht die Feedback-Felder der Verzweigungen des Rückmeldebaums.';
$string['quiet_icon_false'] = '<span title ="Quiet off" alt="Quiet Off Microphone icon" "style="font-size: 1.25em; color:blue;"><i class="fa fa-commenting-o"></i></span>';
$string['quiet_icon_true'] = '<span title ="Quiet on" alt="Quiet On Microphone icon" style="font-size: 1.25em; color:red;"><i class="fa fa-microphone-slash" aria-hidden="true"></i></span>';
$string['rawdata'] = 'Rohdaten';
$string['renamequestionparts'] = 'Teile der Frage umbenennen';
$string['replacedollarscount'] = 'Diese Kategorie enthält {$a} STACK-Fragen.';
$string['replacedollarsin'] = 'Korrigierte Maths-Trennzeichen in Feld {$a}';
$string['replacedollarsindex'] = 'Kontexte mit STACK-Fragen';
$string['replacedollarsindexintro'] = 'Ein Klick auf einen, der Links leitet Sie auf eine Seite, auf der Sie die Fragen auf alte Maths-Trennzeichen überprüfen und automatisch korrigieren können. Wenn Sie zu viele Fragen (Tausende) in einem Kontext haben, wird die Menge der Ausgabe wahrscheinlich Ihren Webbrowser überfordern. In diesem Fall fügen Sie der URL einen Parameter preview=0 hinzu und versuchen es erneut.';
$string['replacedollarsindextitle'] = '$s in Fragetexten ersetzen';
$string['replacedollarsnoproblems'] = 'Keine problematische Trennzeichen gefunden';
$string['replacedollarstitle'] = 'Ersetze $s in Fragetexten in {$a}';
$string['requirelowestterms'] = 'Verlange vollständige Kürzung';
$string['requirelowestterms_help'] = 'Falls JA, muss jeder Koeffizient oder andere rationale Zahlen in Ausdrücken vollständig gekürzt eingegeben werden. Andernfalls wird die Eingabe als ungültig zurückgewiesen.';
$string['requirelowestterms_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs/index.md#Require_lowest_terms';
$string['runquestiontests'] = 'STACK Frage Dashboard';
$string['runquestiontests_alert'] = 'Es fehlen Tests oder Varianten.';
$string['runquestiontests_auto'] = 'Automatisch einen Test in der Annahme, dass die Beispielantwort die vollen Punkte gibt, hinzufügen. Bitte prüfen Sie den Antworttext sorgfältig.';
$string['runquestiontests_autoprompt'] = 'Test in der Annahme, dass die Beispielantwort die vollen Punkte gibt, hinzufügen.';
$string['runquestiontests_example'] = 'Beispiel';
$string['runquestiontests_explanation'] = 'Wenn Sie den Test hinzufügen, wird die Ausgabe folgendermaßen aussehen:';
$string['runquestiontests_help'] = 'Das Dashboard führt Fragetests durch, die die Fragen einem Unit-Test unterziehen. Dadurch kann sichergestellt werden, dass das Verhalten der Fragen mit dem Verhalten übereinstimmt, das von dem/der Trainer/in erwartet wird. Zudem stellen eingesetzte Varianten sicher, dass zufällige Versionen, die ein/e Teilnehmer/in sieht, im Voraus mit den Fragetests getestet werden. Diese Werkzeuge helfen Ihnen, zuverlässige Fragen zu erstellen und zu testen. Sie sollten diese in allen Fällen verwenden, in denen eine Frage von Teilnehmer/innen bearbeitet wird.  Das Dashboard bietet auch viele weitere STACK-spezifische Funktionen.';
$string['runtimeerror'] = 'Diese Frage führte zu einem unerwarteten internen Fehler. Bitte suchen Sie Rat, z.B. bei einem Dozenten.';
$string['runtimefielderr'] = 'Das Feld ""{$a->field}"" erzeugte den folgenden Fehler: {$a->err}';
$string['sans'] = 'SAns';
$string['sans_help'] = 'Dies ist das erste Argument der Antwortüberprüfungsfunktion. In asymmetrischen Tests wird dies als die Studierendenantwort angesehen, obwohl es jeder gültige CAS Ausdruck sein könnte. Es können darin auch Variablen aus der Aufgabe oder dem Feedback benutzt werden.';
$string['sans_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_Tests/index.md';
$string['sansrequired'] = 'SAns darf nicht leer sein.';
$string['savechat'] = 'Änderungen in der Frage speichern';
$string['savechatexp'] = 'Damit gelangen Sie zum Bearbeitungsformular der Frage. Von dort aus können Sie Ihre Änderungen als neue Version der Frage speichern.';
$string['savechatmsg'] = 'Die Aufgabenvariablen und das allgemeine Feedback wurden in die Frage zurückgespeichert.';
$string['savechatnew'] = 'Zum Bearbeitungsformular senden';
$string['scientificnotation'] = 'Wissenschaftliche Schreibweise';
$string['scientificnotation_10'] = 'n * 10^m';
$string['scientificnotation_E'] = 'n E m';
$string['scientificnotation_help'] = 'Wählen Sie, wie genau Ausdrücke in der wissenschaftlichen Schreibweise angezeigt werden.';
$string['scientificnotation_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_options.md#scientificnotation';
$string['score'] = 'Score';
$string['scoreerror'] = 'Die Punkte müssen ein numerischer Wert zwischen 0 und 1 sein, oder eine Variable (diese wird nicht überprüft).';
$string['scoremode'] = 'Mod';
$string['seedx'] = 'Seed {$a}';
$string['seekhelp'] = 'Bitte fragen Sie Ihre/n Trainer/in.';
$string['seethisquestioninthequestionbank'] = '<i class="fa fa-list-alt"></i> Diese Frage in der Fragensammlung ansehen';
$string['seetodolist'] = '<i class="fa fa-exclamation-triangle"></i> Finde <tt>[[todo]]</tt> Blöcke';
$string['seetodolist_desc'] = 'Auf dieser Seite sollen alle Fragen, die <tt>[[todo]]</tt> Blöcke enthalten, angezeigt und nach Tags gruppiert werden.';
$string['seetodolist_help'] = 'Wenn Sie auf den Namen einer Frage klicken, gelangen Sie zum Dashboard der Frage.  Sie können auch eine Vorschau der Frage aufrufen.';
$string['selectquiz'] = 'Wählen Sie einen Test, um die Ergebnisse zu analysieren.';
$string['sendgeneralfeedback'] = '<i class="fa fa-file-text"></i> Allgemeines Feedback an das CAS weitergeben';
$string['settingajaxvalidation'] = 'Sofortige Validierung';
$string['settingajaxvalidation_desc'] = 'Wenn diese Einstellung aktiviert ist, werden die aktuellen Eingaben der Studierenden validiert, sobald sie ihre Eingabe unterbrechen. Dies führt zu einer besseren Benutzererfahrung, erhöht jedoch vermutlich die Serverlast.';
$string['settingcasdebugging'] = 'CAS Debugging';
$string['settingcasdebugging_desc'] = 'Sollen Debugging Informationen über die CAS-Verbindung gespeichert werden?';
$string['settingcasmaximaversion'] = 'Maxima-Version';
$string['settingcasmaximaversion_desc'] = 'Die Version des verwendeten Maxima.';
$string['settingcasresultscache'] = 'CAS Ergebnis Caching';
$string['settingcasresultscache_db'] = 'Cache in der Datenbank';
$string['settingcasresultscache_desc'] = 'Diese Einstellung bestimmt, ob Aufrufe zum CAS gecached werden. Dies sollte im Normalfall eingeschaltet sein. Außnahmen betreffen die Weiterentwicklung des Maxima Codes.
Der aktuelle Status des Cache wird auf der Funktionstests-Seite angezeigt. Wenn Sie Einstellungen ändern, z.B. das GNUPlot-Kommando, so muss der Cache erst geleert werden, damit die Änderungen Wirkung zeigen.';
$string['settingcasresultscache_none'] = 'Kein Cache';
$string['settingcastimeout'] = 'Zeitüberschreitung der CAS-Verbindung';
$string['settingcastimeout_desc'] = 'Timeout für die Verbindungsversuche zu Maxima.';
$string['settingdefaultinputoptions'] = 'Standard Eingabeoptionen';
$string['settingdefaultinputoptions_desc'] = 'Werden beim Erstellen einer neuen Frage oder beim Hinzufügen einer neuen Eingabe zu einer vorhandenen Frage verwendet.';
$string['settingdefaultquestionoptions'] = 'Standard Eingabeoptionen';
$string['settingdefaultquestionoptions_desc'] = 'Beim Anlegen einer neuen Frage verwendet.';
$string['settingmathsdisplay'] = 'Maths-Filter';
$string['settingmathsdisplay_desc'] = 'Die Methode, die zum Anzeigen von Maths verwendet wird. Wenn Sie MathJax auswählen, müssen Sie die Anweisungen auf der Seite Healthcheck befolgen, um es einzurichten. Wenn Sie einen Filter auswählen, müssen Sie sicherstellen, dass der Filter auf der Filter-Übersichtsseite in der Administration aktiviert ist.';
$string['settingmathsdisplay_mathjax'] = 'MathJax';
$string['settingmathsdisplay_maths'] = 'Alter OU Maths-Filter';
$string['settingmathsdisplay_oumaths'] = 'Neuer OU Maths-Filter';
$string['settingmathsdisplay_tex'] = 'Moodle TeX-Filter';
$string['settingmaximalibraries'] = 'Optionale Maxima-Bibliotheken laden:';
$string['settingmaximalibraries_desc'] = 'Dies ist eine kommagetrennte Liste von Namen von Maxima-Bibliotheken, die automatisch in Maxima geladen werden. Es können nur unterstützte Bibliotheken verwendet werden: "stats, distrib, descriptive, simplex". Diese Bibliotheken werden nicht geladen, wenn Sie ein Maxima-Image zur Optimierung der Leistung gespeichert haben.';
$string['settingmaximalibraries_error'] = 'Das folgende Paket ist nicht unterstützt: {$a}';
$string['settingmaximalibraries_failed'] = 'Anscheinend wurden einige der Maxima-Pakete, die Sie angefordert haben, nicht geladen.';
$string['settingplatformmaximacommand'] = 'Maxima Kommando';
$string['settingplatformmaximacommand_desc'] = 'Wenn das Feld leer ist, wird STACK sinnvoll raten, wo Maxima zu finden ist. Wenn dies fehlschlägt, sollte hier der vollständige Pfad der Maxima- oder Maxima-optimierten ausführbaren Datei angegeben werden. Dies ist nur zur Entwicklung und Fehlersuche zu verwenden. Verwenden Sie es nicht auf einem Produktivsystem: Verwenden Sie die optimierte Version oder besser die Maxima-Pool-Option.';
$string['settingplatformmaximacommandopt'] = 'Optimierter Maximabefehl';
$string['settingplatformplotcommand'] = 'Plot Kommando';
$string['settingplatformplotcommand_desc'] = 'Normalerweise kann dies leer gelassen werden, aber wenn das Plotten von Graphen nicht funktioniert, müssen Sie hier möglicherweise den vollständigen Pfad zum Befehl gnuplot angeben.';
$string['settingplatformtype'] = 'Plattform';
$string['settingplatformtype_desc'] = 'STACK muss wissen, auf welchem Betriebssystem es läuft. Die Option "Server" bietet eine bessere Leistung, allerdings auf Kosten der Einrichtung eines zusätzlichen Servers. Die Option "Linux (optimiert)" wird auf der Seite "Maxima optimieren" in der Dokumentation erläutert.';
$string['settingplatformtypelinux'] = 'Linux';
$string['settingplatformtypelinuxoptimised'] = 'Linux (optimiert)';
$string['settingplatformtypeserver'] = 'Server';
$string['settingplatformtypeserverproxy'] = 'Server (via Proxy)';
$string['settingreplacedollars'] = '<code>$</code> und <code>$$</code> ersetzen';
$string['settingreplacedollars_desc'] = 'Ersetze <code>$...$</code> und <code>$$...$$</code> Trennzeichen in Fragetexten, zusätzlich zu <code>\\\\[...\\\\]</code> und <code>\\\\(...\\\\)</code>. Eine bessere Option ist die Verwendung des Skripts \'Maths-Trennzeichen korrigieren\', auf das im Folgenden Bezug genommen wird.';
$string['settingserveruserpass'] = 'Server username:password';
$string['settingserveruserpass_desc'] = 'Wenn Sie bei Plattform: Server verwenden und Ihren Maxima-Pool-Server mit HTTP-Authentifizierung eingerichtet haben, können Sie hier den Benutzernamen und das Passwort eingeben. Das ist etwas sicherer, als sie in die URL zu schreiben. Das Format ist username:password.';
$string['settingsmathsdisplayheading'] = 'Maths Anzeige-Optionen';
$string['settingsmaximasettings'] = 'Zu Maxima verbinden';
$string['settingusefullinks'] = 'Nützliche Links';
$string['showingundeployedvariant'] = 'Zeige nicht eingesetzte Varianten: {$a}';
$string['showvalidation'] = 'Zeige die Validierung';
$string['showvalidation_help'] = 'Zeigt eine validierte Darstellung der Eingabe an. Dies schließt auch die traditionelle zweidimensionale Darstellung ein. Syntaxfehler werden immer zurückgemeldet.';
$string['showvalidation_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs/index.md#Show_validation';
$string['showvalidationcompact'] = 'Ja, kompakt';
$string['showvalidationno'] = 'Nein';
$string['showvalidationyes'] = 'Ja, mit Variabelliste';
$string['showvalidationyesnovars'] = 'Ja, aber ohne Variabelliste';
$string['singlechargotmorethanone'] = 'Sie können hier nur ein Zeichen eingeben.';
$string['specificfeedback'] = 'Spezifisches Feedback';
$string['specificfeedback_help'] = 'Standardgemäß wird Feedback für jeden potenziellen Rückmeldebaum in diesem Block angezeigt.  Er kann auch in den Fragetext verschoben werden. In diesem Fall hat Moodle weniger Kontrolle darüber, wann er bei verschieden Frageverhalten angezeigt wird.';
$string['sqrtsign'] = 'Wurzeln';
$string['sqrtsign_help'] = 'Steuert wie irrationale Zahlen angezeigt werden.';
$string['sqrtsign_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_options.md#surd';
$string['stack:usediagnostictools'] = 'Verwende die STACK Tools';
$string['stackBlock_elif_after_else'] = '"elif" nach einem "else" in einem if-Block.';
$string['stackBlock_elif_out_of_an_if'] = '"elif" kann nicht außerhalb eines if-Blocks existieren.';
$string['stackBlock_else_out_of_an_if'] = '"else" kann nicht außerhalb eines if-Blocks existieren.';
$string['stackBlock_escapeNeedsValue'] = 'escape-Block braucht ein Testattribut.';
$string['stackBlock_geogebra_heading'] = 'GeoGebra Materialien';
$string['stackBlock_geogebra_height'] = 'Die Höhe eines GeoGebra Applets muss eine bekannte CSS Längeneineheit verwenden.';
$string['stackBlock_geogebra_link'] = 'Link zum referenzierten GeoGebra Material';
$string['stackBlock_geogebra_settingdefaultoptions'] = 'GeoGebra Einstellungen in STACK';
$string['stackBlock_ifNeedsCondition'] = 'if-Block braucht ein Testattribut.';
$string['stackBlock_jsxgraph_height'] = 'Die Höhe eines JSXGraphen muss eine bekannte CSS-Längeneinheit sein.';
$string['stackBlock_jsxgraph_height_num'] = 'Der numerische Teil der Höhe eines JSXGraphen muss eine reine Zahl sein und darf keine zusätzlichen Zeichen enthalten.';
$string['stackBlock_jsxgraph_overdefined_dimension'] = 'Bei der Definition des Seitenverhältnisses für JSXGraph sollte man entweder Breite oder Höhe definieren, nicht beides.';
$string['stackBlock_jsxgraph_param'] = 'Der jsxgraph-Block unterstützt in diesem Zusammenhang nur diese Parameter: {$a->param}.';
$string['stackBlock_jsxgraph_underdefined_dimension'] = 'Bei der Definition des Seitenverhältnisses für den JSXGraph muss man entweder die Breite oder die Höhe des Graphen festlegen.';
$string['stackBlock_jsxgraph_width'] = 'Die Breite eines JSXGraphen muss eine bekannte CSS-Längeneinheit sein.';
$string['stackBlock_jsxgraph_width_num'] = 'Der numerische Teil der Breite eines JSXGraphen muss eine reine Zahl sein und darf keine zusätzlichen Zeichen enthalten.';
$string['stackBlock_missmatch'] = 'hat keine Übereinstimmung.';
$string['stackBlock_multiple_else'] = 'Mehrere else-Verzweigungen in einem if-Block.';
$string['stackBlock_overwritten_sortable_option'] = 'Unveränderliche Sortable Optionen gefunden, die folgenden werden ignoriert:';
$string['stackBlock_parsons_available_header'] = 'Von hier ziehen:';
$string['stackBlock_parsons_contents'] = 'Der Inhalt eines Parsons-Blocks muss ein JSON der Form {#stackjson_stringify(proof_steps)#} sein. Wenn Sie benutzerdefinierte Objekte übergeben, dann sollte der Inhalt des Parsons-Blocks ein JSON der Form {steps: {#stackjson_stringify(proof_steps)#}, options: {JSON mit Sortable Optionen}}. Alternativ kann der Inhalt des Parsons-Blocks auch rohe JSON-Äquivalente enthalten. Stellen Sie sicher, dass die Maxima-Variable proof_steps das richtige Format hat. Beachten Sie, dass alle Beweisschritte Strings sein müssen. Sehen Sie sich für Details dazu die Dokumentation an.';
$string['stackBlock_parsons_height'] = 'Die Höhe eines Parsons-Blocks muss eine bekannte CSS-Längeneinheit verwenden.';
$string['stackBlock_parsons_height_num'] = 'Der numerische Teil der Höhe eines Parsons-Blocks muss eine reine Zahl sein und darf keine zusätzlichen Zeichen enthalten.';
$string['stackBlock_parsons_length_num'] = 'Der numerische Wert der Länge muss eine positive ganze Zahl sein und darf keine zusätzlichen Zeichen oder numerischen Typen enthalten.';
$string['stackBlock_parsons_overdefined_dimension'] = 'Bei der Definition des Seitenverhältnisses mittels aspect-ratio für einen Parsons-Block sollte nur die Breite oder die Höhe definiert werden, nicht beides.';
$string['stackBlock_parsons_param'] = 'Der Parsons-Block unterstützt in diesem Zusammenhang nur diese Parameter: "{$a->param}".';
$string['stackBlock_parsons_ref'] = 'Der Parsons-Block unterstützt nur die Referenzierung von Inputs, die im selben CASText-Abschnitt vorhanden sind, \'{$a->var}\' existiert hier nicht.';
$string['stackBlock_parsons_underdefined_dimension'] = 'Bei der Definition des Seitenverhältnisses mittels aspect-ratio für einen Parsons-Block muss entweder die Breite oder die Höhe der Listen festgelegt werden.';
$string['stackBlock_parsons_unknown_mathjax_version'] = 'Der Parsons-Block unterstützt nur MathJax-Versionen {$a->mjversion} für den Parameter mathjax.';
$string['stackBlock_parsons_unknown_named_version'] = 'Der Parsons-Block unterstützt nur Versionen mit dem Namen: {$a->version}.';
$string['stackBlock_parsons_used_header'] = 'Erstellen Sie hier Ihre Lösung:';
$string['stackBlock_parsons_width'] = 'Die Breite eines Parsons-Blocks muss eine bekannte CSS-Längeneinheit verwenden.';
$string['stackBlock_parsons_width_num'] = 'Der numerische Teil der Breite eines Parsons-Blocks muss eine reine Zahl sein und darf keine zusätzlichen Zeichen enthalten.';
$string['stackBlock_unknownBlock'] = 'Der folgende Block ist unbekannt:';
$string['stackBlock_unknown_sortable_option'] = 'Unbekannte Sortable Optionen gefunden, die folgenden werden ignoriert:';
$string['stackCas_CASError'] = 'Das CAS lieferte folgende Fehler zurück:';
$string['stackCas_CASErrorCaused'] = 'verursacht durch den folgenden Fehler:';
$string['stackCas_MissingAt'] = 'Es fehlt ein <code>@</code>-Zeichen.';
$string['stackCas_MissingCloseDisplay'] = 'Fehlende <code>\\]</code>.';
$string['stackCas_MissingCloseHTML'] = 'Fehlender schließender HTML Tag.';
$string['stackCas_MissingCloseInline'] = 'Fehlende <code>\\)</code>.';
$string['stackCas_MissingClosingRawCAS'] = 'Fehlende <code>#}</code>.';
$string['stackCas_MissingClosingTeXCAS'] = 'Fehlende <code>@}</code>.';
$string['stackCas_MissingDollar'] = 'Es fehlt ein <code>$</code>-Zeichen';
$string['stackCas_MissingOpenDisplay'] = 'Fehlende <code>\\[</code>.';
$string['stackCas_MissingOpenHTML'] = 'Fehlender öffnender HTML Tag.';
$string['stackCas_MissingOpenInline'] = 'Fehlende <code>\\(</code>.';
$string['stackCas_MissingOpenRawCAS'] = 'Fehlende <code>{#</code>.';
$string['stackCas_MissingOpenTeXCAS'] = 'Fehlende <code>{@</code>.';
$string['stackCas_MissingStars'] = 'Anscheinend fehlen "*" Zeichen. Vielleicht meinten Sie {$a->cmd}.';
$string['stackCas_MissingString'] = 'Sie haben ein Anführungszeichen <code>"</code> vergessen.';
$string['stackCas_StringOperation'] = 'Eine Zeichenfolge scheint an der falschen Stelle zu sein. Dies ist das Problem: <code>{$a->issue}</code>.';
$string['stackCas_allFailed'] = 'Das CAS lieferte keine ausgewerteten Ausdrücke zurück. Bitte überprüfen sie die Verbindung zum CAS.';
$string['stackCas_apostrophe'] = 'Apostroph-Zeichen sind in Rückmeldungen nicht erlaubt.';
$string['stackCas_applyfunmakestring'] = 'Der Name der Funktion kann keine Zeichenkette in <code>{$a->type}</code> sein.';
$string['stackCas_applyingnonobviousfunction'] = 'Dieser Funktionsaufruf {$a->problem} scheint keinen leicht erkennbaren Funktionsnamen zu haben. Aus Sicherheitsgründen müssen Sie den Aufruf möglicherweise vereinfachen, damit der Validator den Funktionsnamen sehen kann.';
$string['stackCas_backward_inequalities'] = 'Nicht-strikte Ungleichheiten z.B. \\( \\leq \\) oder \\( \\geq \\), müssen als <= oder >= eingegeben werden. Sie haben {$a->cmd} in Ihrem Ausdruck, der rückwärts gerichtet ist.';
$string['stackCas_badLogIn'] = 'Sie haben den Ausdruck <tt>In</tt> eingegeben.  Der natürliche Logarithmus wird als <tt>ln</tt> mit kleinen Buchstaben eingegeben.  ("Lima November", nicht "Indien November")';
$string['stackCas_bracketsdontmatch'] = 'Die Klammern im Ausdruck sind falsch verschachtelt: {$a->cmd}.';
$string['stackCas_chained_inequalities'] = 'Sie scheinen "verkettete Ungleichheiten" zu haben, z.B. \\(a < b < c\\).  Sie müssen einzelne Ungleichungen mit logischen Operationen wie \\(und\\) oder \\(oder\\) verbinden.';
$string['stackCas_decimal_usedcomma'] = 'Sie haben einen Punkt <code>.</code> genutzt, es ist aber ein Komma <code>,</code> als Dezimaltrennzeichen zu verwenden.';
$string['stackCas_decimal_usedthreesep'] = 'Sie haben einen Punkt <code>.</code>, ein Komma <code>,</code> und ein Semikolon <code>;</code> in Ihrem Ausdruck genutzt. Nutzen Sie bitte konsequent ein Dezimaltrennzeichen (<code>.</code> oder <code>,</code>) und ein Trennzeichen für Listen (<code>,</code> oder <code>;</code>). Ihre Antwort ist ambivalent.';
$string['stackCas_errorpos'] = 'Etwa in Zeile {$a->line}, Zeichen {$a->col}.';
$string['stackCas_failedReturn'] = 'Das CAS lieferte keine Daten zurück.';
$string['stackCas_failedReturnOne'] = 'Das CAS lieferte einige Daten nicht zurück.';
$string['stackCas_failedValidation'] = 'fehlgeschlagene CASText Validierung.';
$string['stackCas_failedtimeout'] = 'CAS konnte aufgrund einer Zeitüberschreitung keine Daten zurückgeben.';
$string['stackCas_finalChar'] = '\'{$a->char}\' ist ein ungültiges Endzeichen in {$a->cmd}';
$string['stackCas_forbiddenChar'] = 'CAS-Befehle dürfen die folgenden Zeichen nicht enthalten: {$a->char}.';
$string['stackCas_forbiddenFunction'] = 'Verbotene Funktion: {$a->forbid}.';
$string['stackCas_forbiddenOperator'] = 'Verbotener Operator: {$a->forbid}.';
$string['stackCas_forbiddenVariable'] = 'Verbotene Variable oder Konstante: {$a->forbid}.';
$string['stackCas_forbiddenWord'] = 'Der Ausdruck {$a->forbid} ist verboten.';
$string['stackCas_forbiddenntuple'] = 'In dieser Eingabe sind Koordinaten nicht erlaubt.';
$string['stackCas_inputsdefined'] = 'Sie dürfen keine Eingabenamen als Variablen verwenden. Sie haben versucht, <code>{$a}</code> zu definieren.';
$string['stackCas_invalidCommand'] = 'CAS Befehle sind ungültig.';
$string['stackCas_missingLeftBracket'] = 'Es fehlt eine linke Klammer <span class="stacksyntaxexample">{$a->bracket}</span> in dem Ausdruck: {$a->cmd}.';
$string['stackCas_missingRightBracket'] = 'Es fehlt eine rechte Klammer <span class="stacksyntaxexample">{$a->bracket}</span> in dem Ausdruck: {$a->cmd}.';
$string['stackCas_newline'] = 'Zeilenvorschub-Zeichen sind in Rückmeldungen nicht erlaubt.';
$string['stackCas_noFunction'] = 'Die Verwendung der Funktion {$a->forbid} in dem Term {$a->term} ist in diesem Zusammenhang nicht zulässig.';
$string['stackCas_operatorAsVariable'] = 'Der Operator {$a->op} wurde als Variable interpretiert. Überprüfen Sie den Syntax.';
$string['stackCas_percent'] = '&#037; gefunden im Ausdruck {$a->expr}.';
$string['stackCas_qmarkoperators'] = 'Fragezeichen sind in Antworten nicht erlaubt.';
$string['stackCas_redefine_built_in'] = 'Die Neudefinition einer eingebauten Funktion „{$a->name}“ ist verboten.';
$string['stackCas_redefinitionOfConstant'] = 'Die Neudefinition von Schlüsselkonstanten ist verboten: {$a->constant}.';
$string['stackCas_reserved_function'] = 'Der Funktionsname „{$a->name}“ ist in dieser Frage nicht erlaubt. Bitte kontaktieren Sie Ihre/n Trainer/in.';
$string['stackCas_spaces'] = 'Verbotene Leerzeichen gefunden im Ausdruck {$a->expr}.';
$string['stackCas_spuriousop'] = 'Unbekannter Operator: {$a->cmd}.';
$string['stackCas_studentInputAsFunction'] = 'Die Verwendung von Eingabe Teilnehmender als Name einer Funktion ist nicht zulässig.';
$string['stackCas_trigexp'] = 'Sie können eine trigonometrische Funktion nicht potenzieren, indem Sie {$a->forbid} schreiben. Das Quadrat des Wertes von \\(\\{$a->identifier}(x)\\) wird als <tt>{$a->identifier}(x)^2</tt> eingegeben. Das Inverse von \\(\\{$a->identifier}(x)\\) wird als <tt>a{$a->identifier}(x)</tt> und nicht als \\(\\{$a->identifier}^{-1}(x)\\) eingegeben.';
$string['stackCas_triginv'] = 'Inverse trigonometrische Funktionen werden {$a->goodinv}, und nicht {$a->badinv} geschrieben.';
$string['stackCas_trigop'] = 'Sie müssen {$a->trig} auf ein Argument anwenden.  Sie scheinen {$a->forbid} zu haben, was so aussieht, als hätten Sie versucht, {$a->trig} als Variablennamen zu verwenden.';
$string['stackCas_trigparens'] = 'Wenn Sie eine trigonometrische Funktion auf ihre Argumente anwenden, müssen Sie runde Klammern und keine eckigen Klammern verwenden, z.B. {$a->forbid}.';
$string['stackCas_trigspace'] = 'Um eine trigonometrische Funktion auf ihre Argumente anzuwenden, müssen Sie Klammern verwenden, keine Leerzeichen. Verwenden Sie zum Beispiel stattdessen {$a->trig}.';
$string['stackCas_underscores'] = 'Die folgende Verwendung von Unterstrichen ist nicht zulässig: {$a}.';
$string['stackCas_unencpsulated_comma'] = 'Ein Komma in Ihrem Ausdruck erscheint auf seltsame Weise. Kommas werden verwendet, um Elemente in Listen, Mengen usw. zu trennen. Bei Gleitkommazahlen müssen Sie einen Dezimalpunkt verwenden, kein Komma.';
$string['stackCas_unencpsulated_semicolon'] = 'Ein Semikolon <code>;</code> in Ihrem Ausdruck ist an einer komischen Stelle. Semikola werden als Trennzeichen in Listen, Mengen und Ähnlichem verwendet.';
$string['stackCas_unencpsulated_varmatrix'] = 'In einer Matrix mit variabler Größe <b>verwendet man Leerzeichen zur Trennung der Elemente</b>, nicht ein Komma.';
$string['stackCas_unitssynonym'] = 'Sie scheinen Einheiten {$a->forbid} zu haben. Meinten Sie {$a->unit}?';
$string['stackCas_unknownFunction'] = 'Unbekannte Funktion: {$a->forbid} im Term {$a->term}.';
$string['stackCas_unknownFunctionCase'] = 'Bei der Eingabe wird zwischen Groß- und Kleinschreibung unterschieden: {$a->forbid} ist eine unbekannte Funktion. Meinten Sie {$a->lower}?';
$string['stackCas_unknownUnitsCase'] = 'Bei der Eingabe von Einheiten wird zwischen Groß- und Kleinschreibung unterschieden: {$a->forbid} ist eine unbekannte Einheit. Meinten Sie eine aus der folgenden Liste {$a->unit}?';
$string['stackCas_unknownVariableCase'] = 'Bei der Eingabe wird zwischen Groß- und Kleinschreibung unterschieden: {$a->forbid} ist eine unbekannte Varriable. Meinten Sie {$a->lower}?';
$string['stackCas_useinsteadChar'] = 'Bitte ersetzen Sie <span class="stacksyntaxexample">{$a->bad}</span> durch \'<span class="stacksyntaxexample">{$a->char}</span>\'.';
$string['stackCas_varmatrix_eg'] = 'Z.B. {$a->good} statt {$a->bad}.';
$string['stackDoc_404'] = 'Fehler 404';
$string['stackDoc_404message'] = 'Datei nicht gefunden.';
$string['stackDoc_community'] = 'Community-Website';
$string['stackDoc_directoryStructure'] = 'Verzeichnisstruktur';
$string['stackDoc_docs'] = 'STACK Dokumentation';
$string['stackDoc_docs_desc'] = '<a href="{$a->link}">Dokumentation von STACK</a>: Lokales (unveränderliches) Wiki.';
$string['stackDoc_home'] = 'Dokumentation Anfang';
$string['stackDoc_index'] = 'Kategorieindex';
$string['stackDoc_licence'] = 'Die STACK-Dokumentation ist lizenziert unter <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.';
$string['stackDoc_licence_alt'] = 'Creative Commons Lizenz';
$string['stackDoc_siteMap'] = 'Sitemap';
$string['stackDoc_version'] = 'Ihre Seite verwendet die STACK-Version {$a}.';
$string['stackInstall_input_intro'] = 'Auf dieser Seite lässt sich testen, wie STACK die verschiedenen Studierendeneingaben interpretiert. Momentan geschieht dies nur mit sehr großzügigen Einstellungen, um eine möglichst informale Syntax anzuwenden und Sternchen einzufügen. <br />\'V\' Spalten zeigen die Validität bzgl. PHP und dem CAS an.  V1 = PHP valid, V2 = CAS valid.';
$string['stackInstall_input_title'] = 'Eine Testumgebung für die Validierung von Studierendeneingaben';
$string['stackInstall_input_title_desc'] = 'Das <a href="{$a->link}">Input-Test-Skript</a> bietet Testfälle an, wie STACK mathematische Ausdrücke interpretiert. Diese sind auch nützlich, um an diesen Beispielen zu lernen.';
$string['stackInstall_replace_dollars_desc'] = 'Das <a href="{$a->link}">Maths-Trennzeichen korrigieren-Skript</a> kann verwendet werden, um Trennzeichen im alten Stil wie <code>@...@</code>, <code>$...$</code> und <code>$$...$$</code> in Ihren Fragen mit dem neuen empfohlenen <code>{@...@}</code>, <code>\\(...\\)</code> und <code>\\[...\\]</code> zu ersetzen.';
$string['stackInstall_testsuite_choose'] = 'Bitte wählen Sie eine Antwortüberprüfung.';
$string['stackInstall_testsuite_errors'] = 'Diese Frage hat zur Laufzeit die folgenden Fehler generiert.';
$string['stackInstall_testsuite_fail'] = 'Nicht alle Tests bestanden!';
$string['stackInstall_testsuite_failingtests'] = 'Tests die fehlschlugen';
$string['stackInstall_testsuite_failingupgrades'] = 'Fragen, die beim Upgrade fehlgeschlagen sind.';
$string['stackInstall_testsuite_intro'] = 'Diese Seite erlaubt einen Korrektheitstest von STACKs Antwortüberprüfungen. Beachten Sie, dass nur Antwortüberprüfungen über das Webinterface getestet werden können. Andere Maxima Befehle müssen separat per Kommandozeile getestet werden: siehe unittests.mac.';
$string['stackInstall_testsuite_nogeneralfeedback'] = 'Fragen ohne allgemeines Feedback: Studierende schätzen ausgearbeitete Lösungen sehr!';
$string['stackInstall_testsuite_notests'] = 'Fragen ohne Tests: bitte fügen Sie einige hinzu!';
$string['stackInstall_testsuite_pass'] = 'Alle Tests bestanden!';
$string['stackInstall_testsuite_title'] = 'Eine Testumgebung für STACK Antwortüberprüfungen';
$string['stackInstall_testsuite_title_desc'] = 'Das <a href="{$a->link}">Antwortüberprüfungsskript</a> überprüft, dass die Antwortüberprüfungen korrekt funktionieren. Diese sind auch als Praxisbeispiele für eigene Anpassungen sehr hilfreich.';
$string['stackOptions_AnsTest_values_AddConst'] = 'AddConst';
$string['stackOptions_AnsTest_values_AlgEquiv'] = 'AlgEquiv';
$string['stackOptions_AnsTest_values_AlgEquivNouns'] = 'AlgEquivNouns';
$string['stackOptions_AnsTest_values_Antidiff'] = 'Antidiff';
$string['stackOptions_AnsTest_values_CasEqual'] = 'CasEqual';
$string['stackOptions_AnsTest_values_CompSquare'] = 'CompletedSquare';
$string['stackOptions_AnsTest_values_Diff'] = 'Diff';
$string['stackOptions_AnsTest_values_EqualComAss'] = 'EqualComAss';
$string['stackOptions_AnsTest_values_EqualComAssRules'] = 'EqualComAssRules';
$string['stackOptions_AnsTest_values_Equiv'] = 'EquivReasoning';
$string['stackOptions_AnsTest_values_EquivFirst'] = 'EquivFirst';
$string['stackOptions_AnsTest_values_Expanded'] = 'Expanded';
$string['stackOptions_AnsTest_values_FacForm'] = 'FacForm';
$string['stackOptions_AnsTest_values_GT'] = 'Num-GT';
$string['stackOptions_AnsTest_values_GTE'] = 'Num-GTE';
$string['stackOptions_AnsTest_values_Int'] = 'Int';
$string['stackOptions_AnsTest_values_Levenshtein'] = 'Levenshtein';
$string['stackOptions_AnsTest_values_LowestTerms'] = 'LowestTerms';
$string['stackOptions_AnsTest_values_NumAbsolute'] = 'NumAbsolute';
$string['stackOptions_AnsTest_values_NumDecPlaces'] = 'NumDecPlaces';
$string['stackOptions_AnsTest_values_NumDecPlacesWrong'] = 'NumDecPlacesWrong';
$string['stackOptions_AnsTest_values_NumRelative'] = 'NumRelative';
$string['stackOptions_AnsTest_values_NumSigFigs'] = 'NumSigFigs';
$string['stackOptions_AnsTest_values_PartFrac'] = 'PartFrac';
$string['stackOptions_AnsTest_values_PropLogic'] = 'PropositionalLogic';
$string['stackOptions_AnsTest_values_SRegExp'] = 'SRegExp';
$string['stackOptions_AnsTest_values_SameType'] = 'SameType';
$string['stackOptions_AnsTest_values_Sets'] = 'Mengen';
$string['stackOptions_AnsTest_values_SigFigsStrict'] = 'SigFigsStrict';
$string['stackOptions_AnsTest_values_SingleFrac'] = 'SingleFrac';
$string['stackOptions_AnsTest_values_String'] = 'String';
$string['stackOptions_AnsTest_values_StringSloppy'] = 'StringSloppy';
$string['stackOptions_AnsTest_values_SubstEquiv'] = 'SubstEquiv';
$string['stackOptions_AnsTest_values_SysEquiv'] = 'SysEquiv';
$string['stackOptions_AnsTest_values_UnitsAbsolute'] = 'UnitsAbsolute';
$string['stackOptions_AnsTest_values_UnitsRelative'] = 'UnitsRelative';
$string['stackOptions_AnsTest_values_UnitsSigFigs'] = 'UnitsSigFigs';
$string['stackOptions_AnsTest_values_UnitsStrictAbsolute'] = 'UnitsStrictAbsolute';
$string['stackOptions_AnsTest_values_UnitsStrictRelative'] = 'UnitsStrictRelative';
$string['stackOptions_AnsTest_values_UnitsStrictSigFigs'] = 'UnitsStrictSigFigs';
$string['stackOptions_AnsTest_values_Validator'] = 'Validator';
$string['stack_library'] = 'STACK Fragebibliothek';
$string['stack_library_destination'] = 'Fragen werden in folgende Kategorie importiert:';
$string['stack_library_error'] = 'Etwas ist schief gegangen. Bitte laden Sie die Seite neu und versuchen Sie es noch einmal.';
$string['stack_library_failure'] = 'Fehlgeschlagener Import von:';
$string['stack_library_help'] = 'Anstatt eine eigene Frage zu erstellen, können Sie über diesen Link zur STACK Fragebibliothek gelangen. Die Fragebibliothek enthält viele vorgefertigte STACK-Fragen, die Sie in Moodle importieren können. Sie können die Fragen dann so verwenden, wie sie sind, oder sie an Ihre Bedürfnisse anpassen.';
$string['stack_library_import'] = 'Importieren';
$string['stack_library_import_folder'] = 'Ordner importieren';
$string['stack_library_importlist'] = 'Importierte Fragen';
$string['stack_library_instructions_five'] = 'Testfragen können zum aktuellen Kurs hinzugefügt werden, indem eine .json-Datei importiert wird.';
$string['stack_library_instructions_four'] = 'Verwenden Sie die Dropdown-Liste, um die Kategorie zu ändern.';
$string['stack_library_instructions_one'] = 'Wählen Sie eine Frage aus untenstehender Liste, um sie hier anzuzeigen.';
$string['stack_library_instructions_three'] = 'Nutzen Sie das DropDown Feld, um die Kategorie zu ändern.';
$string['stack_library_instructions_two'] = 'Klicken Sie auf \'Importieren\', um die Frage in Ihre aktuelle Fragenkategorie zu importieren.';
$string['stack_library_not_stack'] = 'Dies ist keine STACK-Frage, daher kann sie hier nicht vollständig angezeigt werden. Sie können sie aber dennoch importieren.';
$string['stack_library_qb_return'] = 'Zurück zur Fragensammlung';
$string['stack_library_quiz'] = 'Das ist ein Test:';
$string['stack_library_quiz_course'] = 'Der Test wird in den Kurs importiert:';
$string['stack_library_quiz_prefix'] = 'Test:';
$string['stack_library_quiz_return'] = 'Zurück zum Test';
$string['stack_library_selected'] = 'Angezeigte Fragen:';
$string['stack_library_success'] = 'Erfolgreicher Import von:';
$string['stackerrors'] = 'Ihre Frage enthält Fehler.';
$string['stackfilesizeerror'] = 'Eine oder mehrere Dateien (z. B. Bilder) sind mehr als 1 MB groß.';
$string['stackfileuseerror'] = 'Eine oder mehrere Dateien (z.B. Bilder) sind intern mit {$a} verbunden, aber keine scheint im aktuellen Text selbst verwendet zu werden.';
$string['stackversioncomment'] = 'Diese Frage scheint im Feld {$a->qfield} Kommentare im Stil /*...*/ zu verwenden, die nicht mehr unterstützt werden.';
$string['stackversionedited'] = 'Diese Frage wurde mit STACK Version {$a} erstellt.';
$string['stackversionerror'] = 'Diese Frage verwendet {$a->pat} im {$a->qfield}, was sich in der STACK-Version {$a->ver} geändert hat und nicht mehr unterstützt wird.';
$string['stackversionerroralt'] = 'Eine Alternative ist {$a}.';
$string['stackversionmulerror'] = 'Diese Frage hat eine Eingabe, die die Option "mul" verwendet, die nach STACK Version 4.2 nicht unterstützt wird. Bitte bearbeiten Sie diese Frage.';
$string['stackversionnone'] = 'Diese Frage wurde nicht bearbeitet, seit die Frageversionsnummerierung in STACK 4.2 eingeführt wurde. Bitte überprüfen Sie Ihre Frage sorgfältig.';
$string['stackversionnow'] = 'Die aktuelle Version von STACK ist {$a}.';
$string['stackversionregexp'] = 'Die Antwortüberprüfung "RegExp" wird ab STACK Version 4.3 nicht mehr unterstützt.  Bitte verwenden Sie stattdessen die neue Antwortüberprüfung "SRegExp".';
$string['stop'] = '[stop]';
$string['strictsyntax'] = 'Strenge Syntax';
$string['strictsyntax_help'] = 'Diese Option wird nicht mehr verwendet und wird entfernt.';
$string['strictsyntax_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/';
$string['strlengtherror'] = 'Diese Zeichenkette darf nicht mehr als 255 Zeichen beinhalten.';
$string['studentValidation_invalidAnswer'] = 'Diese Antwort ist ungültig.';
$string['studentValidation_listofunits'] = 'In Ihrer Antwort wurden die folgenden Einheiten gefunden: {$a}';
$string['studentValidation_listofvariables'] = 'In Ihrer Antwort wurden die folgenden Variablen gefunden: {$a}';
$string['studentValidation_notes'] = '(Diese Eingabe wird von STACK nicht automatisch bewertet).';
$string['studentValidation_yourLastAnswer'] = 'Ihre letzte Antwort wurde folgendermaßen interpretiert: {$a}';
$string['studentanswer'] = 'Studierendeneingabe';
$string['studentinputtoolong'] = 'Ihre Eingabe ist länger als von STACK erlaubt.';
$string['switchtovariant'] = 'Wechseln zu Variante:';
$string['symbolicprtcorrectfeedback'] = '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span>';
$string['symbolicprtincorrectfeedback'] = '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span>';
$string['symbolicprtpartiallycorrectfeedback'] = '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>';
$string['syntaxattribute'] = 'Hinweis-Attribut';
$string['syntaxattribute_help'] = 'Der Syntax-Hinweis erscheint entweder als veränderbarer *Wert* oder als nicht-veränderbarer *Platzhalter*.';
$string['syntaxattribute_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Syntax_Hint';
$string['syntaxattributeplaceholder'] = 'Platzhalter';
$string['syntaxattributevalue'] = 'Wert';
$string['syntaxhint'] = 'Syntax-Hinweis';
$string['syntaxhint_help'] = 'Der Syntax-Hinweis erscheint, wenn das Antwortfeld von Studierenden leer gelassen wird.';
$string['syntaxhint_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs/index.md#Syntax_Hint';
$string['syntaxhint_toolong'] = 'Das Feld für den Syntax-Hinweis ist auf 255 Zeichen begrenzt. Definieren Sie eine String-Variable (z.B. <code>sh: „Langer Hinweis“;</code>) in den Frage-Variablen, und betten Sie sie hier ein, z.B. mit <code>{@sh@}</code>.';
$string['tans'] = 'TAns';
$string['tans_help'] = 'Dies ist das zweite Argument der Antwortüberprüfungsfunktion. In asymmetrischen Tests wird dies als die Dozentenantwort angesehen, obwohl es jeder gültige CAS Ausdruck sein könnte. Es können darin auch Variablen aus der Aufgabe oder dem Feedback benutzt werden.';
$string['tans_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_Tests/index.md';
$string['tansrequired'] = 'TAns darf nicht leer sein.';
$string['teacheranswer'] = 'Musterlösung';
$string['teacheranswercase'] = 'Die Antwort der Trainer/innen als Testfall nutzen.';
$string['teacheranswerempty'] = 'Diese Eingabe kann leer gelassen werden.';
$string['teacheranswershow'] = 'Eine richtige Antwort ist \\( {$a->display} \\). Sie kann so eingegeben werden: {$a->value}';
$string['teacheranswershow_disp'] = 'Eine richtige Antwort ist {$a->display} .';
$string['teacheranswershow_mcq'] = 'Eine richtige Antwort ist: {$a->display}';
$string['teacheranswershownotes'] = 'Für diese Eingabe wurde keine korrekte Antwort geliefert.';
$string['teachersanswer'] = 'Musterlösung';
$string['teachersanswer_help'] = 'Der Dozent muss eine Musterlösung für jedes Eingabefeld angeben. Dies muss eine gültige Maxima-Zeichenkette sein. Sie kann Variablen aus dem Aufgabentext enthalten.';
$string['teachersanswer_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs/index.md#model_answer';
$string['testallincategory'] = 'Alle Fragen in dieser Kategorie überprüfen';
$string['testalltitle'] = 'Alle Fragen in diesem Kontext überprüfen';
$string['testcasexresult'] = 'Testfall {$a->no} {$a->result}';
$string['testingquestion'] = 'Teste Frage {$a}';
$string['testingquestionvariants'] = 'Fragevarianten vorbereiten';
$string['testinputs'] = 'Test-Eingaben';
$string['testoptions'] = 'Test-Optionen';
$string['testoptions_help'] = 'Dieses Feld erlaubt Antwortüberprüfung eine Option zu verwenden, z.B. eine Variable oder eine bestimmte numerische Präzision.';
$string['testoptions_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Potential_response_trees.md';
$string['testoptionsinvalid'] = 'Die Testoptionen sind ungültig: {$a}';
$string['testoptionsrequired'] = 'Testoptionen werden für diesen Test benötigt.';
$string['testpassesandfails'] = '{$a->passes} Bestandene und {$a->fails} Fehlgeschlagene.';
$string['testsuitecolerror'] = 'CAS Fehler';
$string['testsuitecolmark'] = 'Bewertung';
$string['testsuitecolpassed'] = '?';
$string['testsuitefail'] = '<span style="color:red;"><i class="fa fa-times"></i></span>';
$string['testsuitefeedback'] = 'Feedback';
$string['testsuiteknownfail'] = '<span style="color:orange;"><i class="fa fa-adjust"></i></span>';
$string['testsuiteknownfailmaths'] = '<span style="color:orange;"><i class="fa fa-adjust"></i>!</span>';
$string['testsuitenotests'] = 'Anzahl der Tests: {$a->no}.';
$string['testsuitepass'] = '<span style="color:green;"><i class="fa fa-check"></i></span>';
$string['testsuiteteststook'] = 'Tests dauerten {$a->time} Sekunden.';
$string['testsuiteteststookeach'] = 'Durchschnitt pro Test: {$a->time} Sekunden.';
$string['testthisvariant'] = 'Wechsle um diese Variante zu testen';
$string['texdisplaystyle'] = 'Gleichung im "Anzeige"-Stil';
$string['texinlinestyle'] = 'Gleichung im "Inline"-Stil';
$string['tidyquestion'] = '<i class="fa fa-sort-amount-asc"></i> Tool zum Nachbessern der Frage';
$string['tidyquestionx'] = 'Teile der Frage {$a} umbenennen';
$string['todo_desc'] = 'Die <a href="{$a->link}">"to do"</a>-Seite findet Fragen, die <tt>[[todo]]</tt>-Blöcke beinhalten.';
$string['todowarning'] = 'Sie haben nicht gelöste todo-Blöcke in "{$a->field}".';
$string['trig_degrees_radians_fact'] = '\\[
360^\\circ= 2\\pi \\hbox{ radians},\\quad
1^\\circ={2\\pi\\over 360}={\\pi\\over 180}\\hbox{ radians}
\\]
\\[
1 \\hbox{ radian} = {180\\over \\pi} \\hbox{ degrees}
\\approx 57.3^\\circ
\\]';
$string['trig_degrees_radians_name'] = 'Grade und Radianten';
$string['trig_standard_identities_fact'] = '\\[\\sin(a\\pm b)\\ = \\ \\sin(a)\\cos(b)\\ \\pm\\ \\cos(a)\\sin(b)\\]
\\[\\cos(a\\ \\pm\\ b)\\ = \\ \\cos(a)\\cos(b)\\ \\mp \\sin(a)\\sin(b)\\]
\\[\\tan (a\\ \\pm\\ b)\\ = \\ {\\tan (a)\\ \\pm\\ \\tan (b)\\over1\\ \\mp\\ \\tan (a)\\tan (b)}\\]
\\[ 2\\sin(a)\\cos(b)\\ = \\ \\sin(a+b)\\ +\\ \\sin(a-b)\\]
\\[ 2\\cos(a)\\cos(b)\\ = \\ \\cos(a-b)\\ +\\ \\cos(a+b)\\]
\\[ 2\\sin(a)\\sin(b) \\ = \\ \\cos(a-b)\\ -\\ \\cos(a+b)\\]
\\[ \\sin^2(a)+\\cos^2(a)\\ = \\ 1\\]
\\[ 1+{\\rm cot}^2(a)\\ = \\ {\\rm cosec}^2(a),\\quad \\tan^2(a) +1 \\ = \\ \\sec^2(a)\\]
\\[ \\cos(2a)\\ = \\ \\cos^2(a)-\\sin^2(a)\\ = \\ 2\\cos^2(a)-1\\ = \\ 1-2\\sin^2(a)\\]
\\[ \\sin(2a)\\ = \\ 2\\sin(a)\\cos(a)\\]
\\[ \\sin^2(a) \\ = \\ {1-\\cos (2a)\\over 2}, \\qquad \\cos^2(a)\\ = \\ {1+\\cos(2a)\\over 2}\\]';
$string['trig_standard_identities_name'] = 'Identitäten der trigonometrischen Funktionen';
$string['trig_standard_values_fact'] = '\\[\\sin(45^\\circ)={1\\over \\sqrt{2}}, \\qquad \\cos(45^\\circ) = {1\\over \\sqrt{2}},\\qquad
\\tan( 45^\\circ)=1
\\]
\\[
\\sin (30^\\circ)={1\\over 2}, \\qquad \\cos (30^\\circ)={\\sqrt{3}\\over 2},\\qquad
\\tan (30^\\circ)={1\\over \\sqrt{3}}
\\]
\\[
\\sin (60^\\circ)={\\sqrt{3}\\over 2}, \\qquad \\cos (60^\\circ)={1\\over 2},\\qquad
\\tan (60^\\circ)={ \\sqrt{3}}
\\]';
$string['trig_standard_values_name'] = 'Standardwerte der trigonometrischen Funktionen';
$string['true'] = 'Wahr';
$string['truebranch'] = 'WAHR-Zweig';
$string['truebranch_help'] = 'Diese Felder kontrollieren was passiert, wenn die Antwortüberprüfung positiv ausfällt
### Mod und Punkte
Wie die Bepunktung angepasst wird. "=" setzt die Punkte auf einen bestimmten Wert. "+/-" addieren oder subtrahieren Punkte von der aktuellen Summe.

### Abzüge
Im adaptiven oder interaktiven Modus, ziehe so viele Punkte ab.

### Nächster
Soll zu einem nächsten Knoten gesprungen werden, falls ja zu welchen, ansonsten stoppe.

### Antworthinweis
Dieses Tag dient zur Berichterstattung. Es bestimmt den eindeutigen Pfad durch den Baum und das Ergebnis jeder Antwort. Es wird automatisch erzeugt, kann aber auch manuell zu etwas Sinnvollem geändert werden.';
$string['unauthorisedbulktest'] = 'Sie haben keinen entsprechenden Zugang zu den Fragen von STACK.';
$string['undeploy'] = 'Einsatz zurücknehmen';
$string['unknown_block'] = 'Unbekannter Block vom Typ {$a->type} angefordert!';
$string['unrecognisedfactstags'] = 'Die folgende(n) Angabe(n) wurden nicht erkannt: {$a->tags}.';
$string['usetextarea'] = 'Wir empfehlen Ihnen dringend, für STACK-Fragen den Editor "Einfacher Text" zu verwenden.  Andere Editoren können Inhalte ändern, was dazu führen kann, dass Fragen mit JavaScript und anderem Code beim Speichern Ihrer Frage nicht mehr funktionieren.  Klicken Sie auf Einstellungen -> Texteditor wählen und wählen Sie die Option "Einfacher Text".';
$string['variant'] = 'Variante';
$string['variants'] = 'Varianten';
$string['variantsselectionseed'] = 'Zufallsgruppe';
$string['variantsselectionseed_help'] = 'Normalerweise kann dies leer gelassen werden. Falls Sie aber für zwei verschiedene Aufgaben in einem Test den gleichen Random Seed verwenden wollen, schreiben Sie in dieses Feld für beide Aufgaben die gleiche Zeichenkette (und setzen Sie den gleichen Satz Random Seeds ein, wenn Sie eingesetzte Versionen verwenden) und die Random Seeds der beiden Aufgaben werden damit dann synchronisiert.';
$string['verifyquestionandupdate'] = 'Überprüfe den Aufgabentext und aktualisiere die Felder';
$string['version'] = 'Version';
$string['yamlrecommended'] = 'Für STACK wird die Installation der YAML-Bibliothek empfohlen.';
$string['youmustconfirm'] = 'Bitte bestätigen Sie hier.';
