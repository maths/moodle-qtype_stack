# Producing multilingual content

Multilingual content can be developed using two systems.

1. The multilang-filter that comes with Moodle.
2. STACK's own language blocks (recommended).

Please note that the TinyMCE editor considers paragraph tags (`<p>..</p>`) inside any span tags (e.g. `<span lang="en" class="multilang">...</span>`) to be invalud. The TinyMCE editor will potentially "clean up" (i.e. ruin) your multilingual content if using both the Moodle multilang filter and TinyMCE(as of June 2024).  This is a known issue, and one reason we recommend against using TinyMCE for editing STACK content. (It does nasty things to Javascript content as well).

## Using the built-in castext block in STACK

In STACK 4.4 we made it simpler to mix languages in the CAS logic, for example MCQ-labels.

The logic includes a special language-code `other` which will be used if no matching language can be found. We also support defining multiple language codes as comma separated lists. The order of languages is defined by the order they appear in the question-text. 

The block is used like this:

    [[lang code='fi']]...Teksti suomeksi...[[/lang]]
    [[lang code="en,other"]]...Text in English...[[/lang]]

It is also usable in inline CASText2:

    lbls: [castext("[[lang code='en']]Like this {@...@}[[/lang]]..."),...];

Nested language blocks will not get displayed unless they have matching codes, so you may not use `en` wrapper and `en_gb`, `en_us` within it to fine tune things. Only one code will match in the whole question
so if the VLE uses `en_us` and it is found in the question at any place then that is the only code that matches.

If you are dealing with localisation in JSXGraph or MCQ-label style situations you might want to
use the new `lang`-blocks, but do note that while they do not require any additional filters to be installed they still require that the system has language-packs and the users have the ability to select the language.

## Translating text in pictures

The STACK built-in castext block will translate tags embedded into inline SVG images in documents.  Here is a very basic SVG example with language blocks embedded.

    <svg height="210" width="500">
      <title>[[lang code='en,other']]Circles and triangles[[/lang]][[lang code='no']]Sirkler og trekanter[[/lang]]</title>
      <polygon points="200,10 350,90 160,210" style="fill:white;stroke:black;stroke-width:2" />
      <circle cx="239" cy="94" r="55" stroke="red" stroke-width="1" fill="white" />
      <text x="200" y="80" fill="red">[[lang code='en,other']]Circumference[[/lang]][[lang code='no']]Omkrets[[/lang]]</text>
    </svg>

Remember to add alternative text as the title or description for the inline SVG using the `<title>` and `<desc>` tags.  The title or description can also be translated.

## Using the Moodle filter multilang

STACK questions can be easily localized for different languages using the multi-language content filter. [http://docs.moodle.org/en/Multi-language_content_filter](http://docs.moodle.org/en/Multi-language_content_filter).  That is to say, a single question can exist in multiple languages and the user can choose the language they use.

This page deals with translating text within individual questions. It does not deal with translating the Moodle and STACK interface. See the page on [Language packs and translating STACK](../Developer/Language_packs.md).

### Installation of the Moodle multilang filter

1. Your site administrator must enable the [Moodle multi-language content filter](http://docs.moodle.org/en/Multi-language_content_filter).
2. The multi-language content filter must be applied before the MathJax filter, otherwise strange results will occur.

## Authoring questions

The filter works by searching the document for all multilang blocks and displaying the block that matches the selected language.

    <span lang="en" class="multilang">...Text in English...</span>
    <span lang="fi" class="multilang">...Text in Finnish...</span>

If no block matching the user's language can be found, the block encountered first is displayed. In the above example the English block would be displayed if the user's language was not either English or Finnish. The English block should be the first block in the documents in order for it to be the default language in case no matching language can be found.

When translating STACK assignments into different languages, it should be noted that the question text field may not contain multiple instances of input or validation fields of the same name. This means that the input and validations fields must be placed outside multilang blocks in order for them to be visible in all the available languages. The following example will illustrate this.

STACK would not accept the following question text:

    <span lang="en" class="multilang">
       <p>
          Let \( {\bf a} = ({@a@}, {@b@}) \). Find a vector \({\bf b}\neq {\bf 0}\) such that it is perpendicular to \(\mathbf{a}\)
       </p>
       <p>
          \({\bf b} = \Big(\)[[input:ans1]]\(,\) [[input:ans2]]\(\Big)\)
       </p>
       <div>
          [[validation:ans1]]
       </div>
       <div>
          [[validation:ans2]]
       </div>
    </span>
    
    <span lang="fi" class="multilang">
       <p>
          Olkoon vektori \( {\bf a} = ({@a@}, {@b@}) \). Anna vektori \({\bf b}\neq {\bf 0}\) siten, että vektorit ovat kohtisuorassa toisiaan vastaan.
       </p>
       <p>
          \({\bf b} = \Big(\)[[input:ans1]]\(,\) [[input:ans2]]\(\Big)\)
       </p>
       <div>
          [[validation:ans1]]
       </div>
       <div>
          [[validation:ans2]]
       </div>
    </span>

But this question text causes no issues:

    <span lang="en" class="multilang">
       <p>
          Let \( {\bf a} = ({@a@}, {@b@}) \). Find a vector \({\bf b}\neq {\bf 0}\) such that it is perpendicular to \(\mathbf{a}\)
       </p>
    </span>
    <span lang="fi" class="multilang">
       <p>
          Olkoon vektori \( {\bf a} = ({@a@}, {@b@}) \). Anna vektori \({\bf b}\neq {\bf 0}\) siten, että vektorit ovat kohtisuorassa toisiaan vastaan.
       </p>
    </span>
    
    <p>
       \({\bf b} = \Big(\)[[input:ans1]]\(,\) [[input:ans2]]\(\Big)\)
    </p>
    <div>[[validation:ans1]]</div>
    <div>[[validation:ans2]]</div>

### multilang in PRTs with several nodes

The multilang filter also works for the feedback texts in PRT nodes. However, as reported in [Issue #940](https://github.com/maths/moodle-qtype_stack/issues/940), STACK simply concatenates these texts, such that multiple multilang instructions from different PRT nodes can end up right next to each other. In such a situation, the multilang filter cannot determine that the author meant all of them to be printed. For the Moodle multilang filter, what STACK returns will look like this:

    <span lang="en" class="multilang">English feedback from PRT node 1</span>
    <span lang="de" class="multilang">German feedback from PRT node 1</span>
    <span lang="en" class="multilang">English feedback from PRT node 2</span>
    <span lang="de" class="multilang">German feedback from PRT node 2</span>

The multilang filter will assume all of these belong together and will only display the feedback from PRT node 2. To fix this, you can use anything which tells the multilang filter to start a new block, like enclosing the `<span>` tag in a paragraph (`<p>`), using a line break (`<br>`), a non-breaking space (`&nbsp;`), or a zero-width space (`&#x200b0;`).

### Changing STACK's language

If you have written a multilingual question, and a student wants to see it in a certain language, they have to change their preferred language. In Moodle, this is done by clicking on their profile, and then going to `Preferences`, ` Preferred language` and selecting the language. This will not only change the language of all question text (where multilingual blocks have been used), but also the language of the default Moodle and STACK interface (if this translation is available). The specific language pack has to be installed on your server by the server administrator to allow this. More information is available in the developer docs under [Translating STACK](../Developer/Language_packs.md).

### Language within the CAS.

STACK defines a variable `%_STACK_LANG`, which should be a string reflecting the language preference of the user of the question.  The default is `"en"`.  We also have a predicate function `is_lang(code)` which returns `true` if the current language set is `code`.

A related issue is decimal separators.  An internal variable `stackfltsep` holds either `"."` or `","` to reflect the current option for the decimal separator option.

### Further reading

The STACK project has been translated to many languages. Information on specific languages, installing language packs and how to contribute to translations can be found [here](../Developer/Language_packs.md).
