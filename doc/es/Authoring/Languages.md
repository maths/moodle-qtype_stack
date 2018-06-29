# How to produce multilingual content?



The STACK assignments can be easily localized for different languages using the multi-language content filter. [http://docs.moodle.org/22/en/Multi-language_content_filter](http://docs.moodle.org/22/en/Multi-language_content_filter).  The filter works by searching the document for all multilang blocks and displaying the block that matches the selected language.

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
