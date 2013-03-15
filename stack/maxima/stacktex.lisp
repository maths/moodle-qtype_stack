;; Customize Maxima's TEX() function.  To give better control to the output.
;; Chris Sangwin 27 Sept 2010.
;; Useful files:
;; \Maxima-5.21.1\share\maxima\5.21.1\share\utils\mactex-utilities.lisp
;; \Maxima-5.21.1\share\maxima\5.21.1\src\mactex.lisp

;; Additional mactex utilities taken from the distributed file
;; mactex-utilities.lisp
;; Based on code by Richard J. Fateman,  copyright 1987.
;; Fateman's code was ported to Common Lisp by William
;; Schelter.

;; If you want LaTeX style quotients, first load mactex and second
;; define tex-mquotient as follows

(defun tex-mquotient (x l r)
  (if (or (null (cddr x)) (cdddr x)) (wna-err (caar x)))
  (setq l (tex (cadr x) (append l '("\\frac{")) nil 'mparen 'mparen)
    r (tex (caddr x) (list "}{") (append '("}") r) 'mparen 'mparen))
  (append l r))

;; Define an explicit multipliction
;;(defprop mtimes "\\times " texsym)
;;(defprop mtimes "\\cdot " texsym)

;; To use the LaTeX matrix style using the array environment, define tex-matrix as
;; Chris Sangwin 24/1/2004
;; This is a hack, sorry.

(defun tex-matrix-col-count (x csym)
;; Replaces everything with a csym
  (if (null (cdr (car (cdr x)))) (list csym)  ; Empty rows
  ; (cdr x)              - a list of rows
  ; (car (cdr x))        - first row
  ; (cdr (car (cdr x)))  - first row without (mlist)
  (mapcon #'(lambda(y) (list csym)) (cdr (car (cdr x)))) ; replace each item with a csym
  )
)

(defun tex-matrix (x l r) ;; matrix looks like ((mmatrix)((mlist) a b) ...)
  (append l `("\\left[\\begin{array}{")
             (tex-matrix-col-count x "c") ; Replace every column with a "c"
            `("} ")
     ; Below is the bit we need - forms the array
     (mapcan #'(lambda(y) (tex-list (cdr y) nil (list " \\\\ ") " & ")) (cdr x))
     '("\\end{array}\\right]") r)
)


;; patch to tex-prefix to make sin(x) always like sin(x), and not the default sin x.
;; CJS 24 June 2004

(defun tex-prefix (x l r)
  (tex (cadr x) (append l (texsym (caar x)) '("\\left( ") )  (append '(" \\right)") r) 'mparen 'mparen))

;; Fix the problem with -27 being printed -(27)
;; CJS 21 Jan 2009

(defprop mminus tex-prefix-unaryminus tex)
;;(defprop mminus tex-prefix tex)
(defprop mminus ("-") texsym)

(defun tex-prefix-unaryminus (x l r)
  (tex (cadr x) (append l (texsym (caar x))) r (caar x) rop))



;; Display question marks correctly
(defprop &? ("?") texsym)

;; Allow colour into TeX expressions from Maxima
;; Thanks to andrej.vodopivec@fmf.uni-lj.si Fri Jan 14 09:32:42 2005

(defun tex-texcolor (x l r)
  (let
      ((front (append '("{\\color{")
                      (list (stripdollar (cadr x)))
                      '("}")))
       (back (append '("{")
                     (tex (caddr x) nil nil 'mparen 'mparen)
                     '("}}"))))
    (append l front back r)))

(defprop $texcolor tex-texcolor tex)

(defun tex-texdecorate (x l r)
  (let
      ((front (append '("{")
                      (list (stripdollar (cadr x)))
                      '("")))
       (back (append '("{")
                     (tex (caddr x) nil nil 'mparen 'mparen)
                     '("}}"))))
    (append l front back r)))

(defprop $texdecorate tex-texdecorate tex)

;; Changed log to ln, and other things
;; If changes are made here, then we also need to update arccos.lisp

(mapc #'tex-setup
      '(
    (%acos "{\\rm acos}")
    (%asin "{\\rm asin}")
    (%atan "{\\rm atan}")

					; Latex's arg(x) is ... ?
	(%cos "\\cos ")
	(%cosh "\\cosh ")
	(%cot "\\cot ")
	(%coth "\\coth ")
	(%csc "\\csc ")
					; Latex's "deg" is ... ?
	(%determinant "\\det ")
	(%dim "\\dim ")
	(%exp "\\exp ")
	(%gcd "\\gcd ")
					; Latex's "hom" is ... ?
	(%inf "\\inf ")		   ; many will prefer "\\infty". Hmmm.
					; Latex's "ker" is ... ?
					; Latex's "lg" is ... ?
					; lim is handled by tex-limit.
					; Latex's "liminf" ... ?
					; Latex's "limsup" ... ?
	(%ln "\\ln ")
	(%log "\\ln ")
	(%max "\\max ")
	(%min "\\min ")
					; Latex's "Pr" ... ?
	(%sec "\\sec ")
	(%sin "\\sin ")
	(%sinh "\\sinh ")
					; Latex's "sup" ... ?
	(%tan "\\tan ")
	(%tanh "\\tanh ")
	;; (%erf "{\\rm erf}") this would tend to set erf(x) as erf x. Unusual
					;(%laplace "{\\cal L}")

    ; Maxima built-in functions which do not have corresponding TeX symbols.

    (%asec "{\\rm asec}")
    (%acsc "{\\rm acsc}")
    (%acot "{\\rm acot}")

    (%sech "{\\rm sech}")
    (%csch "{\\rm csch}")
    
    (%asinh "{\\rm asinh}")
    (%acosh "{\\rm acosh}")
    (%atanh "{\\rm atanh}")

    (%asech "{\\rm asech}")
    (%acsch "{\\rm acsch}")
    (%acoth "{\\rm acoth}")

)) ;; etc



;; Remove un-needed {}s from string output
;; Chris Sangwin, 28/10/2009

(defun tex-string (x)
  (cond ((equal x "") "")
	((eql (elt x 0) #\\) x)
	(t (concatenate 'string "\\mbox{" x "}"))))


;; Sort out display on inequalitis
;; Chris Sangwin, 21/9/2010

(defprop mlessp (" < ") texsym)
(defprop mgreaterp (" > ") texsym)


