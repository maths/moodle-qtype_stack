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

;; 26 Nov 2017.
;; Note, this commit in Maxmia changed (getcharn f) to (get-first-char).
;; https://sourceforge.net/p/maxima/code/ci/b27acfa194281f42ef6d2a4ef2434d8dea4705f1/

;; If you want LaTeX style quotients, first load mactex and second
;; define tex-mquotient as follows

(defun tex-mquotient (x l r)
  (if (or (null (cddr x)) (cdddr x)) (wna-err (caar x)))
  (setq l (tex (cadr x) (append l '("\\frac{")) nil 'mparen 'mparen)
    r (tex (caddr x) (list "}{") (append '("}") r) 'mparen 'mparen))
  (append l r))

;; Define an explicit multiplication
;;(defprop mtimes "\\times " texsym)
;;(defprop mtimes "\\cdot " texsym)


;; patch to tex-prefix to make sin(x) always like sin(x), and not the default sin x.
;; CJS 24 June 2004.

(defun tex-prefix (x l r)
  (tex (cadr x) (append l (texsym (caar x)) '("\\left( ") )  (append '(" \\right)") r) 'mparen 'mparen))

;; Fix the problem with -27 being printed -(27)
;; CJS 21 Jan 2009

(defprop mminus tex-prefix-blank tex)
;;(defprop mminus tex-prefix tex)
(defprop mminus ("-") texsym)

(defun tex-prefix-blank (x l r)
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
       (back (append '("{\\underline{")
                     (tex (caddr x) nil nil 'mparen 'mparen)
                     '("}}}"))))
    (append l front back r)))

(defprop $texcolor tex-texcolor tex)

;; Allow colour into TeX expressions from Maxima
;; Thanks to andrej.vodopivec@fmf.uni-lj.si Fri Jan 14 09:32:42 2005

(defun tex-texcolorplain (x l r)
  (let
      ((front (append '("{\\color{")
                      (list (stripdollar (cadr x)))
                      '("}")))
       (back (append '("{")
                     (tex (caddr x) nil nil 'mparen 'mparen)
                     '("}}"))))
    (append l front back r)))

(defprop $texcolorplain tex-texcolorplain tex)

;; Changed log to ln, and other things.
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
    (%inf "\\inf ")
    ; many will prefer "\\infty".
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



;; Remove un-needed {}s from string output.
;; Chris Sangwin, 28/10/2009

(defun tex-string (x)
  (cond ((equal x "") (concatenate 'string "\\mbox{ }"))
    ((eql (elt x 0) #\\) x)
    (t (concatenate 'string "\\mbox{" x "}"))))

;; Chris Sangwin, 21/9/2010

(defprop mlessp (" < ") texsym)
(defprop mgreaterp (" > ") texsym)

;; Change the display of derivatives, at the request of the OU.
;; Chris Sangwin, 1/4/2015.

(defprop %derivative tex-derivative tex)
(defun tex-derivative (x l r)
  (tex (if $derivabbrev
       (tex-dabbrev x)
       (tex-d x '"\\mathrm{d}")) l r lop rop))

(defun tex-d(x dsym)            ;dsym should be $d or "$\\partial"
  ;; format the macsyma derivative form so it looks
  ;; sort of like a quotient times the deriva-dand.
  (let*
      ((arg (cadr x)) ;; the function being differentiated
       (difflist (cddr x)) ;; list of derivs e.g. (x 1 y 2)
       (ords (if (null (odds difflist 0))
                 `(1)
                 (odds difflist 0)
              )) ;; e.g. (1 2), but not empty.
       (vars (odds difflist 1)) ;; e.g. (x y)
       (numer (mfuncall `$simplify `((mexpt) ,dsym ((mplus) ,@ords)))) ; d^n numerator
       (denom (cons '($blankmult)
            (mapcan #'(lambda(b e)
                `(,dsym ,(simplifya (mfuncall `$simplify `((mexpt) ,b ,(mfuncall `$simplify e))) nil)))
                vars ords))))
      (if (symbolp arg)
      `((mquotient) (($blankmult) ,(simplifya numer nil) ,arg) ,denom)
      `(($blankmult) ((mquotient) ,numer ,denom) ,arg)
      )
     ))


(defun tex-dabbrev (x)
  ;; Format diff(f,x,1,y,1) so that it looks like
  ;; f
  ;;  x y
  (let*
      ((arg (cadr x)) ;; the function being differentiated
       (difflist (cddr x)) ;; list of derivs e.g. (x 1 y 2)
       (ords (odds difflist 0)) ;; e.g. (1 2)
       (vars (odds difflist 1))) ;; e.g. (x y)
    (append
     (if (symbolp arg)
     `((,arg array))
     `((mqapply array) ,arg))
     (if (and (= (length vars) 1)
          (= (car ords) 1))
     vars
     `((($blankmult) ,@(mapcan #'(lambda (var ord)
                   (make-list ord :initial-element var))
                   vars ords)))))))


;; Change the display of integrals to be consistent with derivatives.
;; Chris Sangwin, 8/6/2015.
(defprop %integrate tex-int tex)
(defun tex-int (x l r)
  (let ((s1 (tex (cadr x) nil nil 'mparen 'mparen)) ;;integran, at the request of the OU delims / & d
    (var (tex (caddr x) nil nil 'mparen rop))) ;; variable
    (cond((= (length x) 3)
      (append l `("\\int {" ,@s1 "}{\\;\\mathrm{d}" ,@var "}") r))
     (t ;; presumably length 5
      (let ((low (tex (nth 3 x) nil nil 'mparen 'mparen))
        ;; 1st item is 0
        (hi (tex (nth 4 x) nil nil 'mparen 'mparen)))
        (append l `("\\int_{" ,@low "}^{" ,@hi "}{" ,@s1 "\\;\\mathrm{d}" ,@var "}") r))))))


;; Fine tune the display to enable us to print gamma07 as \gammma_{07},
;; Chris Sangwin 7/6/2016.
(defprop $texsub tex-texsub tex)
(defun tex-texsub (x l r)
  (let
      ((front (append '("{")
                      (tex (cadr x) nil nil 'mparen 'mparen)
                      '("}_")))
       (back (append '("{")
                      (tex (caddr x) nil nil 'mparen 'mparen)
                     '("}"))))
    (append l front back r)))

;; Powers of functions are displayed by tex as f^2(x), not f(x)^2.
;; This list is an exception, e.g. conjugate(x)^2.
;; We use this list because tex-mexpt is also defined in stacktex40.lisp for earlier versions of Maxima.
(defvar tex-mexpt-fnlist '(%sum %product %derivative %integrate %at $conjugate $texsub $lg $logbase
                                         %lsum %limit $pderivop $#pm#))

;; insert left-angle-brackets for mncexpt. a^<n> is how a^^n looks.
(defun tex-mexpt (x l r)
  (let((nc (eq (caar x) 'mncexpt))) ; true if a^^b rather than a^b
    ;; here is where we have to check for f(x)^b to be displayed
    ;; as f^b(x), as is the case for sin(x)^2 .
    ;; which should be sin^2 x rather than (sin x)^2 or (sin(x))^2.
    ;; yet we must not display (a+b)^2 as +^2(a,b)...
    ;; or (sin(x))^(-1) as sin^(-1)x, which would be arcsine x
    (cond ;; this whole clause
      ;; should be deleted if this hack is unwanted and/or the
      ;; time it takes is of concern.
      ;; it shouldn't be too expensive.
      ((and (eq (caar x) 'mexpt)      ; don't do this hack for mncexpt
            (let*
                ((fx (cadr x)) ; this is f(x)
                 (f (and (not (atom fx)) (atom (caar fx)) (caar fx))) ; this is f [or nil]
                 (bascdr (and f (cdr fx))) ; this is (x) [maybe (x,y..), or nil]
                 (expon (caddr x)) ;; this is the exponent
                 (doit (and
                        f ; there is such a function
                        (member (get-first-char f) '(#\% #\$)) ;; insist it is a % or $ function
                        (not (member 'array (cdar fx) :test #'eq)) ; fix for x[i]^2
                        ;; Unlike core Maxima we have alist of functions.
                        (not (member f tex-mexpt-fnlist :test #'eq))
                        (or (and (atom expon) (not (numberp expon))) ; f(x)^y is ok
                            (and (atom expon) (numberp expon) (> expon 0))))))
                                        ; f(x)^3 is ok, but not f(x)^-1, which could
                                        ; inverse of f, if written f^-1 x
                                        ; what else? f(x)^(1/2) is sqrt(f(x)), ??
              (cond (doit
                     (setq l (tex `((mexpt) ,f ,expon) l nil 'mparen 'mparen))
                     (if (and (null (cdr bascdr))
                              (eq (get f 'tex) 'tex-prefix))
                         (setq r (tex (car bascdr) nil r f 'mparen))
                         (setq r (tex (cons '(mprogn) bascdr) nil r 'mparen 'mparen))))
                    (t nil))))) ; won't doit. fall through
      (t (setq l (cond ((or ($bfloatp (cadr x))
                            (and (numberp (cadr x)) (numneedsparen (cadr x))))
                        ; ACTUALLY THIS TREATMENT IS NEEDED WHENEVER (CAAR X) HAS GREATER BINDING POWER THAN MTIMES ...
                        (tex (cadr x) (append l '("\\left(")) '("\\right)") lop (caar x)))
                       ((atom (cadr x)) (tex (cadr x) l nil lop (caar x)))
                       (t (tex (cadr x) (append l '("{")) '("}") lop (caar x))))
               r (if (mmminusp (setq x (nformat (caddr x))))
                     ;; the change in base-line makes parens unnecessary
                     (if nc
                         (tex (cadr x) '("^ {-\\langle ") (cons "\\rangle }" r) 'mparen 'mparen)
                         (tex (cadr x) '("^ {- ") (cons " }" r) 'mminus 'mparen))
                     (if nc
                         (tex x (list "^{\\langle ") (cons "\\rangle}" r) 'mparen 'mparen)
                         (if (and (integerp x) (< x 10))
                             (tex x (list "^")(cons "" r) 'mparen 'mparen)
                             (tex x (list "^{")(cons "}" r) 'mparen 'mparen)))))))
    (append l r)))

;; Added by CJS, 10-9-16.  Display an argument.
(defprop $argument tex-argument tex)

(defun tex-argument(x l r) ;;matrix looks like ((mmatrix)((mlist) a b) ...)
  (append l `("\\begin{array}{lll}")
      (mapcan #'(lambda(y)
              (tex-list (cdr y) nil (list "\\cr ") "&"))
          (cdr x))
      '("\\end{array}") r))

;; Added by CJS, 15-5-17.  Display a list as a group with a single curly bracket on the left.
(defprop $argumentand tex-argumentand tex)
(defun tex-argumentand(x l r)
  (append l `("\\left\\{\\begin{array}{l}")
      (mapcan #'(lambda(y)
              (tex y nil (list "\\cr ") 'mparen 'mparen))
          (cdr x))
      '("\\end{array}\\right.") r))

;; *************************************************************************************************
;; The following code does not affect TeX output, but rather are general functions needed for STACK.
;;

;; Added 13 Nov 2016.  Try to better display trailing zeros.
;; Based on the "grind function". See src/grind.lisp

;; This function has grind (and hence "string") output the number according to the format template.
;; floatgrind(number, template).
;; DANGER: no error checking on the type of arguments.
(defprop $floatgrind msz-floatgrind grind)
(defun msz-floatgrind (x l r)
  (msz (mapcar #'(lambda (l) (get-first-char l)) (makestring (concatenate 'string "floatgrind(" (format nil (cadr (cdr x)) (cadr x)) ",\"" (cadr (cdr x)) "\")"))) l r)
)

;; This function has grind (and hence "string") output the number with the following number of decimal places.
;; displaydp(number, ndps).
;; DO NOT USE: no error checking on the types of the arguments.
;;(defprop $dispdp msz-dispdp grind)
;;(defun msz-dispdp (x l r)
;;  (msz (mapcar #'(lambda (l) (get-first-char l)) (makestring (concatenate 'string "dispdp(" (format nil (concatenate 'string "~," (format nil "~d" (cadr (cdr x))) "f" ) (cadr x)) "," (format nil "~d" (cadr (cdr x))) ")" ))) l r)
;;)

;; This function has grind (and hence "string") output the number with the following number of decimal places.
;; displaydp(number, ndps).
(defprop $dispdpvalue msz-dispdpvalue grind)
(defun msz-dispdpvalue (x l r)
 (msz (mapcar #'(lambda (l) (get-first-char l)) (makestring (format nil (concatenate 'string "~," (format nil "~d" (cadr (cdr x))) "f" ) (cadr x)) )) l r)
)

;; Define an "arrayp" function to check if we have a Maxima array.
(defmfun $arrayp (x) (and (not (atom x)) (cond ((member 'array (car x) :test #'eq) $true) (T $false))))

;; *************************************************************************************************
;; Added 19 Dec 2018.
;; Based src/mformat.lisp

;; Suppress warnings printed by mtell, e.g. by solve, rat and other functions.
;; Use the Maxima variable stack_mtell_quiet.
(defun mtell (&rest l) (cond ((eq $stack_mtell_quiet $true) (values)) (t (apply #'mformat nil l))));

;; *************************************************************************************************
;; Added 31 Oct 2019.
;;
;; catchable-syntax-error.lisp
;; copyright 2019 by Robert Dodier
;; I release this work under terms of the GNU General Public License v2

;; Helper for MREAD-SYNERR.
;; Adapted from local function PRINTER in built-in MREAD-SYNERR.

(defun mread-synerr-printer (x)
  (cond ((symbolp x)
         (print-invert-case (stripdollar x)))
        ((stringp x)
         (maybe-invert-string-case x))
        (t x)))

;; Punt to Maxima function 'error' so that syntax errors can be caught by 'errcatch'.
;; This definition replaces the built-in MREAD-SYNERR
;; which throws to the top level of the interpreter in a way which cannot
;; be intercepted by 'errcatch'.
;;
;; After a syntax error is detected, the global variable 'error'
;; contains the error message (which is also printed on the console
;; when the error occurs).
;;
;; Aside from punting to 'error', this implementation doesn't try to
;; do anything else which the built-in MREAD-SYNERR does. In particular
;; this implementation doesn't try to output any input-line information.

(defun mread-synerr (format-string &rest l)
  (let*
    ((format-string-1 (concatenate 'string "syntax error: " format-string))
     (format-string-args (mapcar #'mread-synerr-printer l))
     (message-string (apply #'format nil format-string-1 format-string-args)))
    (declare (special *parse-stream*))
    (when (eql *parse-stream* *standard-input*)
      (read-line *parse-stream* nil nil))
    ($error message-string)))

;; *************************************************************************************************
;; Added 08 Jan 2020.
;; Based src/grind.lisp

;; Up the binding power of mminus, so that -(a/b) outputs exactly this way and not -a/b = (-a)/b.
;; Subtle differences.

;; In a maxima session type
;; :lisp (defprop mminus 120. rbp);

;; We provide just two specific functions here, and do not allow users to set an arbitrary binding power.

;; *************************************************************************************************

(defmspec $mminusbp120 (x)
  (setq x (car x))
  (defprop mminus 120. rbp)
  (defprop mminus 120. lbp)
  '$done
)

(defmspec $mminusbp100 (x)
  (setq x (car x))
  (defprop mminus 100. rbp)
  (defprop mminus 100. lbp)
  '$done
)

;; *************************************************************************************************
;; Added 08 Jan 2020.
;; Needed for %union, etc, where we don't display unions of just one item as unions.

(defprop $%union tex-nary2 tex)
(defprop $%union (" \\cup ") texsym)
;; Sort out binding power of %union to display correctly.
;; tex-support is defined in to_poly_solve_extra.lisp.
(defprop $%union 114. tex-rbp)
(defprop $%union 115. tex-lbp)

(defprop $%intersection tex-nary2 tex)
(defprop $%intersection (" \\cap ") texsym)
(defprop $%intersection 114. tex-lbp)
(defprop $%intersection 115. tex-rbp)


(defun tex-nary2 (x l r)
  (let* ((op (caar x)) (sym (texsym op)) (y (cdr x)) (ext-lop lop) (ext-rop rop))
    (cond ((null y)       (tex-function x l r t)) ; this should not happen
          ((null (cdr y)) (tex (car y) l r lop rop)) ; Single elements in the argument.
          (t (do ((nl) (lop ext-lop op) (rop op (if (null (cdr y)) ext-rop op)))
                 ((null (cdr y)) (setq nl (append nl (tex (car y)  l r lop rop))) nl)
           (setq nl (append nl (tex (car y) l sym lop rop))
             y (cdr y)
             l nil))))))

;; *************************************************************************************************
;; Added 27 June 2020.
;; Localise some Maxmia-generated strings

(defprop $true  "\\mathbf{!BOOLTRUE!}"  texword)
(defprop $false "\\mathbf{!BOOLFALSE!}" texword)
