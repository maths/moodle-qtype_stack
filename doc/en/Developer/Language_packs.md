# Translation of the STACK Project

## Translation of questions

This page will deal with translations of the STACK interface. Individual question text can also be translated. See this page in the [multi-language question](../Authoring/Languages.md) authoring section.

## STACK 4 is released and being used with the following languages


STACK is designed to support the straightforward addition of other language packs. If you have translated STACK into other languages - or would be interested in doing so - please [let us know](mailto:C.J.Sangwin@ed.ac.uk).

As of May 2023, substantial translations exist for the following languages.

* Afrikaans [af]
* Arabic [ar]
* Catalan [ca]
* Chinese (simplified) [zh_cn]
* Dansk [da]
* Dutch [nl]
* Estonian [et]
* English [en]
* Finnish [fi]
* French [fr]
* Galego [gl]
* German [de]
* Greek [el]
* Hebrew [he]
* Italian [it]
* Japanese [ja]
* Polish [pl]
* Portuguese - Brazil [pt_br]
* Romanian [ro]
* Romanian for workplace [ro_wp]
* Silesian [szl]
* Slovak [sk] Slovenian [sl]
* Spanish (international) [es]
* Spanish - Mexico [es_mx]
* Swedish [sv]
* Swedish - Finland [sv_fi]

Details of some colleagues who translated STACK are given under [credits](../About/Credits.md).  If you need these languages please contact the developers for more details of the status of this work.

## Installing a language pack

To allow users of your server to see interface in a certain language, the language pack has to be installed on your server. In Moodle, to install a language pack on your server, goo to `Site adminstration`, `Language`, `Language pakcs`. Here you can see a list of installed language packs, as well as a list of available languages. This is a list of all translations of Moodle, but note only some of them correspond to translations of STACK as well (see list above). To install a language pack, click on it and click `Install selected language pack`. 

Once a language pack is installed, a user can change the preferred language for Moodle and STACK by clicking on their profile, and then going to `Preferences`, ` Preferred language` and selecting the language.

## How to translate STACK

STACK is part of Moodle.  Hence, we have used the Moodle translation management system.  

Please check if STACK has already been translated into your language.  We are using [Moodle's AMOS system](http://docs.moodle.org/en/AMOS) to do this.  

_ALL_ strings which appear to the user should be found in the single file

You need to translate the strings for the Components `qtype_stack`,
`qbehaviour_adaptivemultipart`, `qbehaviour_dfcbmexplicitvaildate`,
`qbehaviour_dfexplicitvaildate` and `qformat_stack`.

It is convenient to translate the strings online.  To translate STACK into another language please use Moodle's AMOS system. See [Moodle online documentation](http://docs.moodle.org/en/AMOS) for specific details.

Apologies to those people who have translated STACK through AMOS, but who do not appear in the [credits](../About/Credits.md). Please contact [Chris Sangwin](mailto:C.J.Sangwin@ed.ac.uk) to add your name.

## Finding language packs for an API setup

1. Create a new full Moodle server and install the language pack you want.
2. Look on your server in `$CFG->langlocalroot.'/'.$lang;`
3. Find the file "qtype_stack.php".

This contains all the language strings.

## Further reading

Applying a language pack will change the language on the Moodle and STACK interface. It will not change question text unless this has been translated by the question author. For information on writing multilingual question text, see the page on [multilingual question authoring](../Authoring/Languages.md) in authoring section.


