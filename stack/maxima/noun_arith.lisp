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

(defprop $nounand tex-nary tex)
;;(defprop $nounand ("\\land ") texsym)
(defprop $nounand ("\\,{\\mbox{ !AND! }}\\, ") texsym)
(defprop $nounand 65. tex-lbp)
(defprop $nounand 65. tex-rbp)
;;(defprop mand ("\\land ") texsym)
(defprop mand ("\\,{\\mbox{ !AND! }}\\, ")  texsym)

(defprop $nounor tex-nary tex)
;;(defprop $nounor ("\\lor ") texsym)
(defprop $nounor ("\\,{\\mbox{ !OR! }}\\, ") texsym)
(defprop $nounor 61. tex-lbp)
(defprop $nounor 61. tex-rbp)
;;(defprop mor ("\\lor ") texsym)
(defprop mor ("\\,{\\mbox{ !OR! }}\\, ")  texsym)

(defprop $nounnot tex-prefix tex)
;;(defprop $nounnot ("\\neg ") texsym)
(defprop $nounnot ("{\\rm !NOT!}") texsym)
(defprop $nounnot 70. tex-lbp)
(defprop $nounnot 70. tex-rbp)
(defprop mnot tex-prefix tex)
;;(defprop mnot ("\\neg ") texsym)
(defprop mnot ("{\\rm !NOT!}") texsym)