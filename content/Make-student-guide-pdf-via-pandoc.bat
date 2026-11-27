:: Script to convert given markdown files to PDF
:: Uses pandoc, with option
::
:: -f markdown+tex_math_single_backslash >>>  for unescaped backslashes, support of \(...\)
:: -V linkcolor=blue                     >>>> needed to colour links
::
:: Tim Lowe, November 2026

:: Need to run from doc\en\Students so that image locations needed for markdown resolve
cd "..\en\Students"

pandoc ..\..\content\student_guide_pandoc_header.txt  index.md   Answer_input.md Free_text_markdown.md Free_text_AsciiMath_intro.md -f markdown+tex_math_single_backslash -V linkcolor=blue -V geometry:"textwidth=15cm, textheight=25cm" -V fontsize=12pt --number-sections --shift-heading-level-by=0 --toc -o ..\..\content\STACK-student-guide.pdf


