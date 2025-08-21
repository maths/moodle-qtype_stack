# Accessibility​ for Question Authors

It is important that we make course materials and content as accessible as possible. Higher education institutions have a legal responsibility to ensure their content meets accessibility standards. This page gives guidelines and resources on what we can do to improve accessibility.
Authoring accessible STACK questions is simpler than writing accessible materials from scratch, particularly in comparison to PDF LaTeX documents.

* When authoring STACK questions, we are able to take advantage of the work done by the Moodle community, detailed in their [policies page](http://docs.moodle.org/dev/Accessibility).
* When displaying mathematics in STACK, we use LaTeX rendered using MathJax. Details of what accessibility features this support are given on the [MathJax website](https://docs.mathjax.org/en/latest/basic/accessibility.html).
* STACK itself uses very simple CSS and HTML form fields, which is considered a gold standard for accessibility, due to user adaptability.

However, this is not the whole picture. Colleagues must still consider accessibility when authoring questions.

Please consider/check the following key points:
* Images:
  * If inserting a screenshot, consider if you could format the information  you wish to communicate using HTML, e.g. code snippets and mathematical equations.
  * Add meaningful alternative text to all non-text objects, in particular images. This can be done using `<img src="/path/to.img.jpg" alt="Alt text">` in HTML, or `![Alt text](/path/to/img.jpg)` in markdown.
  * It is important that this text conveys the information that the figure would have if viewed. If there is text in the image then this must be given. Do not paraphrase this text. See [useful guidance on alternative text](https://accessibility.huit.harvard.edu/describe-content-images).
* Links: Please add text to links rather than a full `https://` link, as it slows down screen reader users and makes navigation using verbal commands very difficult. However, avoid link text such as 'here' or 'read more', make each link disenable from other links on the page, and make it as clear as you can where the link leads just from the text. See [useful link text guidance](https://www.norfolk.gov.uk/article/44520/Links-and-link-text).
* Colour:
  * do not use colour alone to convey meaning e.g. in a plot.
  * Ensure your text has contrast levels of at least 4:5:1, this can be easily checked with the [WebAIM contrast checker](https://webaim.org/resources/contrastchecker/).
* JSXGraphs:
  * JSXGraphs are figures so must have alternative text. To add a description to a JSXGraph, use the `description` attribute in the board setup. For example:
  `description: 'Graph of the function f(x) = x squared'`.
  * Pay specific attention to refering exclusively to colour in a graph.
  * Consider users may be using keyboard navigation, be careful with interactive assesseed tasks requiring the user to click on the board to add points. Add an `add point` button.
  
Detailed guidance on digital accessibility is given by the WCAG 2.1 Accessibility Guidelines.
