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
 * Finnish language strings for the Stack question type.
 *
 * @package   qtype_stack
 * @copyright 2012 Matti Pauna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'STACK';
$string['pluginname_help'] = 'STACK matematiikan tehtävien arviointijärjestelmä.';
$string['pluginnameadding'] = 'Lisätään STACK tehtävä';
$string['pluginnameediting'] = 'Muokataan STACK tehtävää';
$string['pluginnamesummary'] = 'STACK tarjoaa matemaattisia tehtäviä Moodlen tentteihin.  Ne käyttävät symbolisen laskennan ohjelmistoa opiskelijoiden vastauksien tarkistuksessa.';

// General strings.
$string['errors'] = 'Virheitä';
$string['debuginfo'] = 'Virheenjäljitystietoja';
$string['exceptionmessage'] = '{$a}';

// Strings used on the editing form.
$string['addanothernode'] = 'Lisää solmu';
$string['answernote'] = 'Vastauksen tunnus';
$string['answernote_err'] = 'Vastauksen tunnus ei saa sisältää merkkiä |.  STACK lisää sen vastauksen tunnuksiin erottaakseen ne automaattisesti.';
$string['answernote_help'] = 'Tämä on tunnus jota käytetään raportointiin.  Se jäljittää vastauspuun polun sekä kunkin vastaustestin tuloksen.  Tämä on generoitu automaattisesti mutta se voidaan muuttaa tarvittaessa.';
$string['answernote_link'] = 'question/type/stack/doc/doc.php/Authoring/Potential_response_trees.md#Answer_note';
$string['assumepositive'] = 'Oletetaan positiiviseksi';
$string['assumepositive_help'] = 'Asettaa Maximan assume_pos muuttujan arvon.';
$string['assumepositive_link'] = 'question/type/stack/doc/doc.php/Authoring/Options.md#Assume_Positive';
$string['autosimplify'] = 'Automaattinen sievennys';
$string['autosimplify_help'] = 'Asettaa muuttujan "simp" Maximassa tälle vastauspuulle.';
$string['autosimplify_link'] = 'question/type/stack/doc/doc.php/CAS/Maxima.md#Simplification';
$string['boxsize'] = 'Vastauskentän pituus';
$string['boxsize_help'] = 'Opiskelijalle tarjotun vastauskentän pituus.';
$string['boxsize_link'] = 'question/type/stack/doc/doc.php/Authoring/Inputs.md#Box_Size';
$string['checkanswertype'] = 'Tarkista vastauksen tyyppi';
$string['checkanswertype_help'] = 'Jos kyllä, vastaukset, jotka ovat eri tyyppiä (esim. lauseke, yhtälö, matriisi, lista, joukko) hylätään automaattisesti.';
$string['checkanswertype_link'] = '/question/type/stack/doc/doc.php/Authoring/Inputs.md#Check_Type';
$string['complexno'] = 'Ilmaisun sqrt(-1) tulkinta ja esitys';
$string['complexno_help'] = 'Asettaa symbolien i and sqrt(-1) tulkinnan';
$string['complexno_link'] = 'question/type/stack/doc/doc.php/Authoring/Options.md#sqrt_minus_one.';
$string['defaultprtcorrectfeedback'] = 'Vastaus on oikein.';
$string['defaultprtincorrectfeedback'] = 'Vastaus on väärin.';
$string['defaultprtpartiallycorrectfeedback'] = 'Vastaus on osittain oikein.';
$string['feedback'] = 'Palaute';
$string['feedback_help'] = 'Tämä teksti voi sisältää tehtävän muuttujia, syötettyjä arvoja tai palautteen muuttujia.  Teksti näytetään opiskelijalle, jos hänen vastauksensa toteuttavat tämän vastauspuun.';
$string['inputtest'] ='Syötteen tarkistus';
$string['feedbackvariables'] = 'Palautteen muuttujat';
$string['feedbackvariables_help'] = 'Palautteen muuttujien avulla voidaan suorittaa laskuja syötettyjen arvojen sekä tehtävän muuttujien avulla.  Näitä muuttujia voidaan käyttää missä tahansa tämän vastauspuun käsittelyn vaiheissa.';
$string['feedbackvariables_link'] = 'question/type/stack/doc/doc.php/Authoring/KeyVals.md#Feedback_variables';
$string['forbidfloat'] = 'Liukuluvut kielletään';
$string['forbidfloat_help'] = 'Jos kyllä, opiskelijan liukulukuina syöttämät vastaukset hylätään väärän tyyppisinä.';
$string['forbidfloat_link'] = '/question/type/stack/doc/doc.php/Authoring/Inputs.md#Forbid_Floats';
$string['forbidwords'] = 'Kielletyt merkkijonot ';
$string['forbidwords_help'] = 'Tämä on pilkuin erotettu lista merkkijonoista, joita ei sallita opiskelijan vastauksessa.';
$string['forbidwords_link'] = 'question/type/stack/doc/doc.php/Authoring/CASText.md#Forbidden_Words';
$string['generalfeedback'] = 'Mallivastaus';
$string['generalfeedback_help'] = 'Mallivastaus näytetään opiskelijalle tehtävän yrityksen jälkeen. Mallivastauksessa voidaan käyttää tehtävän muuttujia. Mallivastaus ei riipu opiskelijan antamista vastauksista.';
$string['generalfeedback_link'] = 'question/type/stack/doc/doc.php/Authoring/CASText.md#general_feedback';
$string['generalfeedbacktags'] = 'Mallivastauksessa ei sallita merkkijonoja \'{$a}\'.';
$string['showvalidation'] = 'Näytä validointi';
$string['showvalidation_help'] = 'Jos kyllä, kaikki validointiin liittyvä palaute näytetään mukaan lukien lausekkeiden kaksiulotteiset esitykset.';
$string['showvalidation_link'] = 'Näytä validointi';
$string['inputheading'] = 'Vastaus: {$a}';
$string['inputtype'] = 'Vastauksen tyyppi';
$string['inputtype_help'] = 'Asettaa vastauksen tyypin, esim. tekstikenttä, oikein/väärin, useampirivinen tekstialue.';
$string['inputtype_link'] = 'question/type/stack/doc/doc.php/Authoring/Inputs.md';
$string['inputtypealgebraic'] = 'Algebrallinen lauseke';
$string['inputtypeboolean'] = 'Oikein/väärin';
$string['inputtypedropdown'] = 'Pudotusvalikko';
$string['inputtypesinglechar'] = 'Yksittäinen merkki';
$string['inputtypetextarea'] = 'Tekstialue';
$string['inputtypematrix'] = 'Matriisi';
$string['insertstars'] = 'Lisätäänkö tähdet';
$string['insertstars_help'] = 'Jos kyllä, järjestelmä yrittää automaattisesti lisätä *-merkit kirjainten ja numeroiden väliin.';
$string['insertstars_link'] = '/question/type/stack/doc/doc.php/Authoring/Inputs.md#Insert_Stars';
$string['multiplicationsign'] = 'Kertomerkki';
$string['multiplicationsign_help'] = 'Asettaa kertomerkin esitystavan.';
$string['multiplicationsign_link'] = 'question/type/stack/doc/doc.php/Authoring/Options.md#multiplication';
$string['multcross'] = 'Risti';
$string['multdot'] = 'Piste';
$string['mustverify'] = 'Esikatselu';
$string['mustverify_help'] = 'Näytetäänkö opiskelijan vastaus esikatselussa ennen arvostelua.';
$string['mustverify_link'] = 'question/type/stack/doc/doc.php/Authoring/Inputs.md#Student_must_verify';
$string['next'] = 'Seuraava';
$string['nodexfalsefeedback'] = 'Solmun {no} palaute jos väärin';
$string['nodextruefeedback'] = 'Solmun {no} palaute jos väärin';
$string['nodexwhenfalse'] = 'Seuraava solmu {no} jos väärin';
$string['nodexwhentrue'] = 'Seuraava solmu {no} jos oikein';
$string['nodex'] = 'Solmu {$a}';
$string['nonempty'] = 'Ei saa olla tyhjä.';
$string['penalty'] = 'Rangaistus';
$string['penalty_help'] = 'Tämä arvo vähennetään kokonaispisteistä jokaisen väärän yrityskerran jälkeen.';
$string['penalty_link'] = 'question/type/stack/doc/doc.php/Authoring/Feedback.md';
$string['penaltyerror'] = 'Rangaistus pitää olla välillä 0-1.';
$string['penaltyerror2'] = 'Rangaistus ei saa olla tyhjä tai sen pitää olla 0:n ja 1:n välillä oleva lukuarvo.';
$string['prtcorrectfeedback'] = 'Yleinen palaute oikean vastauksen jälkeen';
$string['prtheading'] = 'Vastauspuu: {$a}';
$string['prtincorrectfeedback'] = 'Yleinen palaute väärän vastauksen jälkeen';
$string['prtpartiallycorrectfeedback'] = 'Yleinen palaute osittain oikean vastauksen jälkeen';
$string['prtwillbecomeactivewhen'] = 'Tämä vastauspuu käsitellään, jos opiskelija vastannut kenttään {$a}';
$string['questionnote'] = 'Tehtävän erotteluteksti';
$string['questionnote_help'] = 'Tämän tarkoituksena on erotella tehtävän satunnaistetut versiot toisistaan. Järjestelmä katsoo tehtävän kaksi versiota samaksi jos ja vain jos niiden erottelutekstit ovat samat. Satunnaistetuiden tehtävien erotustekstiin pitää laittaa tehtävän muuttujia.';
$string['questionnote_link'] = 'question/type/stack/doc/doc.php/Authoring/Question_note.md';
$string['questionnotetags'] = 'Tehtävän erotteluteksti ei saa sisältää merkkijonoa \'{$a}\'.';
$string['questionnotempty'] = 'Tehtävän erotteluteksti ei saa olla tyhjä, jos tehtävässä käytetään satunnaistettuja muuttujia. Järjestelmä käyttää erottelutekstiä erottamaan tehtävän eri versiot toisistaan.';
$string['questionsimplify'] = 'Tehtäväkohtainen sievennys';
$string['questionsimplify_help'] = 'Asettaa Maximan "simp"-muuttujan arvon tämän tehtävän käsittelyssä.';
$string['questionsimplify_link'] = 'question/type/stack/doc/doc.php/CAS/Maxima.md#Simplification';
$string['questiontext'] = 'Kysymysteksti';
$string['questiontext_help'] = 'Teksti, jonka opiskelija saa tehtävänantona. Tämän kentän tulee sisältää myös syötekenttiä ja syötteen tarkistuksen käsittelyelementtejä. Esimerkiksi: [[input:ans1]] [[validation:ans1]].';
$string['questiontext_link'] = 'question/type/stack/doc/doc.php/Authoring/CASText.md#question_text';
$string['questiontextnonempty'] = 'Kysymysteksti ei saa olla tyhjä.';
$string['questiontextmustcontain'] = 'Kysymystekstissä ei saa käyttää merkkijonoja \'{$a}\'.';
$string['questiontextonlycontain'] = 'Kysymystekstissä on liian monta kopiota merkkijonosta \'{$a}\'.  Näitä saa käyttää vain kerran.';
$string['questiontextfeedbackmustcontain'] = 'Kysymystekstin ja arvostelun jälkeisen palautteen pitää sisältää merkkijono \'{$a}\'.';
$string['questiontextfeedbackonlycontain'] = 'Kysymystekstin ja arvostelun jälkeisen palautteen pitää sisältää vain yksi kopio merkkijonosta \'{$a}\'.';
$string['questionvalue'] = 'Kysymyksen arvo';
$string['questionvariables'] = 'Tehtävän muuttujat';
$string['questionvariables_help'] = 'Tähän kenttään määritetään tehtävässä käytetyt muuttujat.  Niihin voidaan viitata muista tehtävän osista.';
$string['questionvariables_link'] = 'question/type/stack/doc/doc.php/Authoring/KeyVals.md';
$string['quiet'] = 'Hiljainen';
$string['quiet_help'] = 'Jos kyllä, mitään tehtävän tarkistustyypin antamaa palautetta ei näytetä opiskelijalle. Tämä ei vaikuta vastauspuun solmujen palautteeseen.';
$string['requiredfield'] = 'Tämä kenttä tarvitaan!';
$string['requirelowestterms'] = 'Vaaditaanko supistettu muoto';
$string['requirelowestterms_help'] = 'Jos kyllä, opiskelijan syöttämät murtoluvut vaaditaan supistetussa muodossa.';
$string['requirelowestterms_link'] = '/question/type/stack/doc/doc.php/Authoring/Inputs.md#Require_lowest_terms';
$string['sans'] = 'SAns';
$string['sans_help'] = 'Opiskelijan vastaus. Tyypillisesti vastauskentän tunnus mutta olla myös lauseke, joka riippuu tehtävän ja/tai palautteen muuttujista.';
$string['sans_link'] = 'question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['stop'] = '[stop]';
$string['score'] = 'Pisteet';
$string['scoreerror'] = 'Pisteiden arvon pitää olla luku väliltä 0-1.';
$string['scoremode'] = 'Mod';
$string['specificfeedback'] = 'Palaute';
$string['specificfeedback_help'] = 'Jokaisen vastauspuun palaute näytetään tässä osiossa.';
$string['specificfeedbacktags'] = 'Palaute ei saa sisältää merkkijonoa \'{$a}\'.';
$string['sqrtsign'] = 'Juurien esitystapa';
$string['sqrtsign_help'] = 'Esitetäänkö juurilausekkeet juurina.';
$string['sqrtsign_link'] = 'question/type/stack/doc/doc.php/Authoring/Options.md#surd';
$string['strictsyntax'] = 'Maxima-syntaksi';
$string['strictsyntax_help'] = 'Pitääkö syötteen olla Maxima-syntaksin mukaista?';
$string['strictsyntax_link'] = '/question/type/stack/doc/doc.php/Authoring/Inputs.md#Strict_Syntax';
$string['syntaxhint'] = 'Syntaksivihje';
$string['syntaxhint_help'] = 'Syntaksivihje tulee valmiiksi vastauksen syöttökenttään.';
$string['syntaxhint_link'] = '/question/type/stack/doc/doc.php/Authoring/Inputs.md#Syntax_Hint';
$string['tans'] = 'TAns';
$string['tans_help'] = 'Opettajan antama vastaus.  Voi olla myös muu lauseke, joka sisältää tehtävän tai palautteen muuttujia.';
$string['tans_link'] = 'question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['teachersanswer'] = 'Mallivastaus';
$string['teachersanswer_help'] = 'Jokaiselle syötteelle pitää asettaa mallivastaus.  Se voi sisältää tehtävän muuttujia.';
$string['teachersanswer_link'] = 'question/type/stack/doc/doc.php/Authoring/Inputs.md#model_answer';
$string['testoptions'] = 'Testin lisävalinnat';
$string['testoptions_help'] = 'Joidenkin tarkistustestien tarvitsemia parametreja, kuten muuttujia voidaan syöttää tähän kenttään.';
$string['testoptions_link'] = 'question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['testoptionsrequired'] = 'Tämä tarkistustesti vaatii lisäparametreja.';
$string['variantsselectionseed'] = 'Tehtäväryhmä';
$string['variantsselectionseed_help'] = 'Normaalisti tämä kenttä on tyhjä. Jos halutaan, että kaksi eri tentin kysymystä käyttää sama satunnaislukugeneraattorin siemen, tähän kenttään kirjoitetaan sama merkkijono molemmille tehtäville.';
$string['verifyquestionandupdate'] = 'Tarkista kysymysteksti ja päivitä lomake.';

// Error reporting in the feedback form.
$string['edit_form_error'] = 'Solmussa {$a->no} {$a->field} on seuraava virhe: ';

// Admin settings.
$string['settingcasdebugging'] = 'CAS virheenjäljitys';
$string['settingcasdebugging_desc'] = 'Tarkistetaanko virheenjäljitystietoja CAS yhteydestä.';
$string['settingcasmaximaversion'] = 'Maximan versio';
$string['settingcasmaximaversion_desc'] = 'Käytössä olevan Maximan versio.';
$string['settingcasresultscache'] = 'CAS tulosten välimuistiin tallennus';
$string['settingcasresultscache_db'] = 'Välimuisti tietokannassa';
$string['settingcasresultscache_desc'] = 'Tallennetaanko CAS kutsut välimuistiin? Tämän on syytä olla päällä, muutoin kuin ohjelmistokehitystilanteissa. Välimuistin tila näkyy kuntotarkistus-sivulla. Välimuisti pitää tyhjentää jos CAS tai gnuplot asetukset muuttuvat.';
$string['settingcasresultscache_none'] = 'Älä tallenna välimuistiin';
$string['settingcastimeout'] = 'CAS yhteyden aikaraja';
$string['settingcastimeout_desc'] = 'Kauanko aikaa sallitaan yhden CAS kutsun käsittelyyn.';
$string['settingplatformtype'] = 'Järjestelmän tyyppi';
$string['settingplatformtype_desc'] = 'Stack tarvitsee tiedon mikä ympäristön käyttöjärjestelmä on.';
$string['settingplatformtypeunix'] = 'Linux';
$string['settingplatformtypewin']  = 'Windows';
$string['settingplatformtypeserver'] = 'Palvelin';
$string['settingplatformmaximacommand'] = 'Maxima-komento';
$string['settingplatformmaximacommand_desc'] = 'Stack tarvitsee tiedon millä komennolla Maxima käynnistyy.  Jos tyhjä, Stack tekee valistuneen arvauksen.';
$string['settingplatformplotcommand'] = 'Piirtokomento';
$string['settingplatformplotcommand_desc'] = 'Stack tarvitsee tiedon millä komennolla gnuplot käynnistyy.  Jos tyhjä, Stack tekee valistuneen arvauksen.';

// Strings used by interaction elements.
$string['false'] = 'Epätosi';
$string['notanswered'] = 'Ei vastattu';
$string['true'] = 'Tosi';
$string['ddl_empty'] = 'Tälle pudotusvalikolle ei ole asetettu valintoja. Aseta valinnat.';

// Strings used by the question test script.
$string['addanothertestcase'] = 'Lisää uusi testitapaus...';
$string['addatestcase'] = 'Lisää testitapaus...';
$string['addingatestcase'] = 'Lisätään testitapaus kysymykseen {$a}';
$string['createtestcase'] = 'Luo testitapaus';
$string['currentlyselectedvariant'] = 'Tämä versio on esitetty alla';
$string['deletetestcase'] = 'Poista testitapaus {$a->no} tehtävästä {$a->question}';
$string['deletetestcaseareyousure'] = 'Oletko varma, että haluat poistaa testitapauksen {$a->no} tehtävästä {$a->question}?';
$string['deletethistestcase'] = 'Poista tämä testitapaus...';
$string['deploy'] = 'Luo versiot';
$string['deployedvariantoptions'] = 'Seuraavat versiot on luotu:';
$string['deployedvariants'] = 'Luodut versiot';
$string['editingtestcase'] = 'Muokkaa testitapausta {$a->no} tehtävälle {$a->question}';
$string['editthistestcase'] = 'Muokkaa tätä testitapausta...';
$string['expectedanswernote'] = 'Odotettu vastauksen tunnus';
$string['expectedoutcomes'] = 'Odotetut tulokset';
$string['expectedpenalty'] = 'Odotettu rangaistus';
$string['expectedscore'] = 'Odotetut pisteet';
$string['inputdisplayed'] = 'Näytetään';
$string['inputentered'] = 'Syötetty arvo';
$string['inputexpression'] = 'Testin syöte';
$string['inputname'] = 'Syötteen nimi';
$string['inputstatus'] = 'Status';
$string['inputstatusname'] = 'Tyhjä';
$string['inputstatusnameinvalid'] = 'Ei hyväksytty';
$string['inputstatusnamevalid'] = 'Hyväksytty';
$string['inputstatusnamescore'] = 'Pisteet';
$string['notestcasesyet'] = 'Testitapauksia ei ole vielä lisätty.';
$string['penalty'] = 'Rangaistus';
$string['prtname'] = 'PRT nimi';
$string['questiondoesnotuserandomisation'] = 'Tässä tehtävässä ei ole satunnaisuutta.';
$string['questionnotdeployedyet'] = 'Tälle tehtävälle ei ole vielä luotu versioita.';
$string['questionpreview'] = 'Tehtävän esikatselu';
$string['questiontests'] = 'Tehtävän testitapaukset';
$string['runquestiontests'] = 'Suorita testitapaukset...';
$string['showingundeployedvariant'] = 'Näytetään ei-tallennettu versio: {$a}';
$string['switchtovariant'] = 'Siirry satunnaiseen versioon';
$string['testcasexresult'] = 'Testitapaus {$a->no} {$a->result}';
$string['testingquestion'] = 'Testataan tehtävää {$a}';
$string['testinputs'] = 'Testin syötteet';
$string['testthisvariant'] = 'Testaa tätä versiota';
$string['undeploy'] = 'Poista versio';

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
$string['answertest'] = 'Answer test';
$string['answertest_help'] = 'An answer test is used to compare two expressions to establish whether they satisfy some mathematical criteria.';
$string['answertest_link'] = 'question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['testsuitecolpassed'] = 'Passed?';
$string['studentanswer'] = 'Student response';
$string['teacheranswer'] = 'Teacher answer';
$string['options'] = 'Options';
$string['testsuitecolerror'] = 'CAS errors';
$string['testsuitecolrawmark'] = 'Raw mark';
$string['testsuitecolexpectedscore'] = 'Expected mark';
$string['testsuitepass'] = 'Pass';
$string['testsuitefail'] = 'Fail';
$string['stackInstall_input_title'] = "A test suite for validation of student's input";
$string['stackInstall_input_title_desc'] = 'The <a href=\"{$a->link}\">input-tests script</a> provides test cases of how STACK interprests mathematical expressions.  They are also useful to learn by example.';
$string['stackInstall_input_intro'] = 'This page allows you to test how STACK interprets various inputs from a student.  This currently only checks with the most liberal settings, trying to adopt an informal syntax and insert stars.  <br />V columns record validity as judged by PHP and the CAS.  V1 = PHP valid, V2 = CAS valid.';
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
$string['stackCas_spaces']                  = 'Lauseke {$a->expr} ei saa sisältää välilyöntejä.';
$string['stackCas_percent']                 = 'Lauseke {$a->expr} ei saa sisältää merkkiä &#037;.';
$string['stackCas_missingLeftBracket']      = 'Vasen sulku <span class="stacksyntaxexample">{$a->bracket}</span> puuttuu lausekkeesta {$a->cmd}.';
$string['stackCas_missingRightBracket']     = 'Oikea sulku <span class="stacksyntaxexample">{$a->bracket}</span> puuttuu lausekkeesta {$a->cmd}.';
$string['stackCas_apostrophe']              = 'Heittomerkkejä ei sallita vastauksessa.';
$string['stackCas_newline']                 = 'Rivinvaihtoja ei sallita vastauksessa.';
$string['stackCas_forbiddenChar']           = 'Maxima komennossa ei sallita seuraavia merkkejä: {$a->char}.';
$string['stackCas_finalChar']               = 'Merkki \'{$a->char}\' ei ole sallittu lausekkeessa {$a->cmd}';
$string['stackCas_MissingStars']            = '*-merkkejä puuttuu. Tarkoititko: {$a->cmd}?';
$string['stackCas_unknownFunction']         = 'Tuntematon funktio: {$a->forbid}.';
$string['stackCas_unsupportedKeyword']      = 'Tätä avainsanaa ei sallita: {$a->forbid}.';
$string['stackCas_forbiddenWord']           = 'Lauseke {$a->forbid} on kielletty.';

// Used in cassession.class.php.
$string['stackCas_CASError']                = 'Maxima palautti seuraavat virheilmoitukset:';
$string['stackCas_allFailed']               = 'Maksima ei onnistunut komentojen suorittamisessa.  Tarkista Maxima-yhteys.';
$string['stackCas_failedReturn']            = 'Maxima ei palauttanut vastauksia.';

// Used in castext.class.php.
$string['stackCas_tooLong']                 = 'CASText komento on liian pitkä.';
$string['stackCas_MissingAt']               = '@-merkki puuttuu.';
$string['stackCas_MissingDollar']           = '$-merkki puuttuu';
$string['stackCas_MissingOpenHint']         = 'Missing opening hint';
$string['stackCas_MissingClosingHint']      = 'Missing closing /hint';
$string['stackCas_MissingOpenDisplay']      = '[-merkki puuttuu';
$string['stackCas_MissingCloseDisplay']     = ']-merkki puuttuu';
$string['stackCas_MissingOpenInline']       = '(-merkki puuttuu';
$string['stackCas_MissingCloseInline']      = ')-merkki puuttuu';
$string['stackCas_MissingOpenHTML']         = 'Aloittava html-tagi puuttuu';
$string['stackCas_MissingCloseHTML']        = 'Lopettava html-tagi puuttuu';
$string['stackCas_failedValidation']        = 'CASText ei kelpaa. ';
$string['stackCas_invalidCommand']          = 'CAS komennot eivät kelpaa. ';
$string['stackCas_CASErrorCaused']          = 'aiheutti seuraavan virheen:';

$string['Maxima_DivisionZero']  = 'Jako nollalla.';
$string['Lowest_Terms']   = 'Vastauksessasi olevat murtoluvut eivät ole supistetussa muodossa. Supista ne ja yritä uudestaan.';
$string['Illegal_floats'] = 'Vastauksessasi on desimaalilukuja, joita ei sallita tässä tehtävässä.  Muunna desimaaliluvut murtoluvuiksi.';
$string['qm_error'] = 'Vastauksessasi on kysymysmerkkejä. Korvaa ne arvoilla.';
// TODO add this to STACK...
// $string['CommaError']     = 'Your answer contains commas which are not part of a list, set or matrix.  <ul><li>If you meant to type in a list, please use <tt>{$a[0]}</tt>,</li><li>If you meant to type in a set, please use <tt>{$a[1]}</tt>.</li></ul>';

// Answer tests.
$string['stackOptions_AnsTest_values_AlgEquiv']           =  'AlgEquiv';
$string['stackOptions_AnsTest_values_EqualComAss']        =  'EqualComAss';
$string['stackOptions_AnsTest_values_CasEqual']           =  'CasEqual';
$string['stackOptions_AnsTest_values_SameType']           =  'SameType';
$string['stackOptions_AnsTest_values_SubstEquiv']         =  'SubstEquiv';
$string['stackOptions_AnsTest_values_SysEquiv']           =  'SysEquiv';
$string['stackOptions_AnsTest_values_Expanded']           =  'Expanded';
$string['stackOptions_AnsTest_values_FacForm']            =  'FacForm';
$string['stackOptions_AnsTest_values_SingleFrac']         =  'SingleFrac';
$string['stackOptions_AnsTest_values_PartFrac']           =  'PartFrac';
$string['stackOptions_AnsTest_values_CompSquare']         =  'CompletedSquare';
$string['stackOptions_AnsTest_values_NumRelative']        =  'NumRelative';
$string['stackOptions_AnsTest_values_NumAbsolute']        =  'NumAbsolute';
$string['stackOptions_AnsTest_values_NumSigFigs']         =  'NumSigFigs';
$string['stackOptions_AnsTest_values_GT']                 =  'Num-GT';
$string['stackOptions_AnsTest_values_GTE']                =  'Num-GTE';
$string['stackOptions_AnsTest_values_LowestTerms']        =  'LowestTerms';
$string['stackOptions_AnsTest_values_Diff']               =  'Diff';
$string['stackOptions_AnsTest_values_Int']                =  'Int';
$string['stackOptions_AnsTest_values_String']             =  'String';
$string['stackOptions_AnsTest_values_StringSloppy']       =  'StringSloppy';
$string['stackOptions_AnsTest_values_RegExp']             =  'RegExp';

$string['AT_NOTIMPLEMENTED']        = 'Tämä tarkistustestiä ei ole käytössä. ';
$string['TEST_FAILED']              = 'Tarkistustesti ei toiminut.  Ilmoita opettajalle tai järjestelmän ylläpitäjälle. {$a->errors}';
$string['AT_MissingOptions']        = 'CAS lisävalinnat-kentän muuttuja puuttuu.';
$string['AT_InvalidOptions']        = 'Lisävalinnat kenttä ei kelpaa. {$a->errors}';

$string['ATAlgEquiv_SA_not_expression'] = 'Vastauksesi pitäisi olla lauseke, ei yhtälö, epäyhtälö, lista, joukko tai matriisi. ';
$string['ATAlgEquiv_SA_not_matrix']     = 'Vastauksesi pitäisi olla matriisi mutta se ei ole. ';
$string['ATAlgEquiv_SA_not_list']       = 'Vastauksesi pitäisi olla lista mutta se ei ole. Listan alkiot syötetään hakasulkeiden väliin pilkuin eroteltuna, esim. [1, 2, 3]. ';
$string['ATAlgEquiv_SA_not_set']        = 'Vastauksesi pitäisi olla joukko mutta se ei ole. Joukon alkiot syötetään aaltosulkeiden väliin pilkuin eroteltuna. ';
$string['ATAlgEquiv_SA_not_equation']   = 'Vastauksesi pitäisi olla yhtälö mutta se ei ole. ';

$string['ATAlgEquiv_TA_not_equation']   = 'Vastauksesi on yhtälö mutta vastausta johon sitä verrataan ei ole. Jos esimerkiksi syötit y = 2, vastaukseksi riittää 2. ';
$string['ATAlgEquiv_SA_not_inequality'] = 'Vastauksesi pitäisi olla epäyhtälö mutta se ei ole. ';

$string['Subst']                        = 'Vastauksesi olisi oikein jos korvaisit muuttujan seuraavasti: {$a->m0} ';

$string['ATInequality_nonstrict']       = 'Syöttämäsi epäyhtälön pitäisi olla aito mutta se ei ole. ';
$string['ATInequality_strict']          = 'Syöttämäsi epäyhtälö ei pitäisi olla aito. ';
$string['ATInequality_backwards']       = 'Syöttämäsi epäyhtälön suunta pitäisi olla toisin päin. ';

$string['ATLowestTerms_wrong']          = 'Supista vielä syöttämäsi murtoluku. ';
$string['ATLowestTerms_entries']        = 'Seuraavat termit vastauksessasi eivät ole sievennettyjä. {$a->m0} Yritä uudelleen. ';

$string['ATList_wronglen']          = 'Syöttämässäsi listassa pitäisi olla {$a->m0} alkiota, mutta siinä on {$a->m1} alkiota. ';
$string['ATList_wrongentries']      = 'Alla olevat punaisella merkityt alkiot ovat väärin. {$a->m0} ';

$string['ATMatrix_wrongsz']         = 'Syöttämäsi matriisin pitäisi olla {$a->m0} x {$a->m1} mutta se on {$a->m2} x {$a->m3}. ';
$string['ATMatrix_wrongentries']    = 'Alla olevat punaisella merkityt alkiot ovat väärin. {$a->m0} ';

$string['ATSet_wrongsz']            = 'Syöttämässäsi joukossa pitäisi olla {$a->m0} eri alkiota mutta siinä on {$a->m1} alkiota. ';
$string['ATSet_wrongentries']       = 'Alla olevat punaisella merkityt alkiot ovat väärin (ne saattavat olla eri muodossa kuin syöttämäsi). {$a->m0} ';

$string['irred_Q_factored']         = 'Termi {$a->m0} pitäisi olla tekijöihin jakamattomassa muodossa mutta se ei ole. ';
$string['irred_Q_commonint']        = 'Lausekkeesta pitäisi ottaa yhteinen tekijä kertoimeksi. ';
$string['irred_Q_optional_fac']     = 'Lausekkeen {$a->m0} voi vielä jakaa tekijöihin. Voit halutessasi jatkaa työskentelyä. ';

$string['FacForm_UnPick_morework']  = 'Termiä {$a->m0} pitää vielä muokata. ';
$string['FacForm_UnPick_intfac']    = $string['irred_Q_commonint'];
$string['ATFacForm_error_list']     = 'Vastauksen tarkastuksessa tapahtui virhe. Ota yhteyttä järjestelmän ylläpitäjiin ';
$string['ATFacForm_error_degreeSA'] = 'Vastauskesi astetta ei pystytty määrittämään.';
$string['ATFacForm_isfactored']     = 'Vastauksesi on jaettu tekijöihin. ';  // Needs a space at the end.
$string['ATFacForm_notfactored']    = 'Vastaustasi ei ole jaettu tekijöihin. '; // Needs a space at the end.
$string['ATFacForm_notalgequiv']    = 'Vastauksesi ei ole algebrallisesti yhtä kuin oikean vastaus. Jokin virhe on tapahtunut laskussasi. ';


$string['ATPartFrac_error_list']        = $string['ATFacForm_error_list'];
$string['ATPartFrac_true']              = '';
$string['ATPartFrac_single_fraction']   ='Vastauksesi näyttäisi olevan yksi murtolauseke se pitäisi saattaa osittaismurtokehitelmään. ';
$string['ATPartFrac_diff_variables']    ='Vastauksessasi esiintyvät muuttujat ovat eri kuin kysymyksessä. Tarkista ja muuta. ';
$string['ATPartFrac_denom_ret']         ='Jos vastauksesi esitetään yksittäisenä murtolausekkeena, sen nimittäjä olisi {$a->m0}. Mutta sen pitäisi olla {$a->m1}. ';
$string['ATPartFrac_ret_expression']    ='Vastauksesi esitettynä yksittäisenä murtolausekkeena on {$a->m0}. ';

$string['ATSingleFrac_error_list']     = $string['ATFacForm_error_list'];
$string['ATSingleFrac_true']           = '';
$string['ATSingleFrac_part']           = 'Vastauksesi pitäisi olla yksi murtolauseke . ';
$string['ATSingleFrac_var']            = 'Vastauksessasi esiintyvät muuttujat ovat eri kuin kysymyksessä. Tarkista ja muuta. ';
$string['ATSingleFrac_ret_exp']        = 'Vastauksesi ei ole algebrallisesti yhtä kuin oikean vastaus. Jokin virhe on tapahtunut laskussasi. ';
$string['ATSingleFrac_div']            = 'Vastauksessasi esiintyy murtolausekkeita murtolausekkeiden sisällä. Muuta vastauksesi siten, että se on yksi murtolauseke. ';

$string['ATCompSquare_true']            = '';
$string['ATCompSquare_false']           = '';
$string['ATCompSquare_not_AlgEquiv']    = 'Vastauksesi näyttää olevan hyväkstyttävässä muodossa mutta se ei ole yhtäpitävä oikean vastauksen kanssa.';
$string['ATCompSquare_false_no_summands']     = 'Täydennetty neliö on muotoa ( a(...)^2 + b), missä a ja b eivät riipu muuttujasta. Enemmän kuin yksi termi näyttäisi riippuvan muuttujasta.';


$string['ATInt_error_list']         = $string['ATFacForm_error_list'];
$string['ATInt_const_int']          = 'Integrointivakio puuttuu. Se voi olla mielivaltainen vakio mutta ei luku. ';
$string['ATInt_const']              = 'Integrointivakio puuttuu muuten vastauksesi on oikein. ';
$string['ATInt_EqFormalDiff']       = 'Vastauksesi derivaatta ei ole yhtä kuin lauseke, joka pyydettiin integroimaan.';

$string['ATInt_wierdconst']         = 'Vastauksesi derivaatta ei ole yhtä kuin lauseke, joka pyydettiin integroimaan.  Integrointivakio on tunnistamattomassa muodossa.';
$string['ATInt_diff']               = 'Näyttää siltä, että ole derivoinut integroinnin sijasta. ';
$string['ATInt_generic']            = 'Vastauksesi derivaatta ei ole yhtä kuin lauseke, joka pyydettiin integroimaan, eli: {$a->m0}.  Vastauksesi derivaatta muuttujan {$a->m1} suhteen on: {$a->m2}.';
$string['ATDiff_error_list']        = $string['ATFacForm_error_list'];
$string['ATDiff_int']               = 'Näyttää siltä, että olet integroinut derivoinnin sijasta. ';

$string['ATNumSigFigs_error_list']        = $string['ATFacForm_error_list'];
$string['ATNumSigFigs_NotDecimal']  = 'Vastauksesi pitäisi olla desimaaliluku mutta se ei ole. ';
$string['ATNumSigFigs_Inaccurate']  = 'Vastauksesi tarkkuus ei ole oikein. ';
$string['ATNumSigFigs_WrongDigits'] = 'Vastauksessasi on väärä määrä merkitseviä numeroita. ';

$string['ATSysEquiv_SA_not_list']               = 'Vastauksesi pitäisi olla lista mutta se ei ole.';
$string['ATSysEquiv_SB_not_list']               = 'Opettajan antaman vastauksen pitäisi olla lista mutta se ei ole. Ilmoita opettajalle tai järjestelmän valvojille.';
$string['ATSysEquiv_SA_not_eq_list']            = 'Vastauksesi pitäisi olla yhtälö mutta se ei ole.';
$string['ATSysEquiv_SB_not_eq_list']            = 'Opettajan antaman vastauksen pitäisi olla lista yhtälöitä mutta se ei ole. Ilmoita opettajalle tai järjestelmän valvojille.';
$string['ATSysEquiv_SA_not_poly_eq_list']       = 'Yksi tai useampi yhtälöistäsi ei ole polynomi.';
$string['ATSysEquiv_SB_not_poly_eq_list']       = 'Opettajan antaman vastauksen pitäisi olla lista polynomiyhtälöitä mutta se ei ole. Ilmoita opettajalle tai järjestelmän valvojille.';
$string['ATSysEquiv_SA_missing_variables']      = 'Vastauksestasi puuttuu yksi tai useampia muuttujia.';
$string['ATSysEquiv_SA_extra_variables']        = 'Vastauksessasi on liikaa muuttujia.';
$string['ATSysEquiv_SA_system_underdetermined'] = 'Vastauksessasi olevat yhtälöt näyttävät oikeilta mutta niitä tarvitaan lisää.';
$string['ATSysEquiv_SA_system_overdetermined']  = 'Seuraavassa punaisella merkityt yhtälöt eivät ole oikein. {$a->m0} ';

$string['studentValidation_yourLastAnswer']  = 'Vastauksesi tulkittiin muodossa: {$a}';
$string['studentValidation_invalidAnswer']   = 'Tämä vastaus ei kelpaa. ';
$string['stackQuestion_noQuestionParts']        = 'Tässä tehtävässä ei ole osia, joihin voidaan vastata.';

// Documentation strings.
$string['stackDoc_404']                 = 'Error 404';
$string['stackDoc_docs']                = 'STACK Dokumentaatio';
$string['stackDoc_docs_desc']           = '<a href="{$a->link}">STACK Dokumentaatio</a>: paikallinen staattinen wiki.';
$string['stackDoc_home']                = 'Dokumentaation juuri';
$string['stackDoc_index']               = 'Kategoriahakemisto';
$string['stackDoc_parent']              = 'Ylätaso';
$string['stackDoc_siteMap']             = 'Sivukartta';
$string['stackDoc_404message']          = 'Tiedostoa ei löytynyt.';
$string['stackDoc_directoryStructure']  = 'Hakemistorakenne';