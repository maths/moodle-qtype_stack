;; Customize Maxima's tex() function.  
;; Chris Sangwin 21 Oct 2005.
;; Useful files: 
;; \Maxima-5.9.0\share\maxima\5.9.0\share\utils\mactex-utilities.lisp
;; \Maxima-5.9.0\share\maxima\5.9.0\src\mactex.lisp

(defprop $noun+ tex-mplus tex)
(defprop $noun+ ("+") texsym)
(defprop $noun+ 100. tex-lbp)
(defprop $noun+ 100. tex-rbp)

(defprop $noun- tex-prefix tex)
(defprop $noun- ("-") texsym)
(defprop $noun- 100. tex-rbp)
(defprop $noun- 100. tex-lbp)

(defprop $noun* tex-nary tex)
(defprop $noun* "\\," texsym)
(defprop $noun* 120. tex-lbp)
(defprop $noun* 120. tex-rbp)

(defprop $noun/ tex-mquotient tex)
(defprop $noun/ 122. tex-lbp) ;;dunno about this
(defprop $noun/ 123. tex-rbp)

(defprop $noun^ tex-mexpt tex)
(defprop $noun^ 140. tex-lbp)
(defprop $noun^ 139. tex-rbp)
