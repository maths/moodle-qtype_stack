;; Customize Maxima's tex() function.  
;; Chris Sangwin 21 Oct 2005.
;; Useful files: 
;; \Maxima-5.9.0\share\maxima\5.9.0\share\utils\mactex-utilities.lisp
;; \Maxima-5.9.0\share\maxima\5.9.0\src\mactex.lisp

(defprop $nounadd tex-mplus tex)
(defprop $nounadd ("+") texsym)
(defprop $nounadd 100. tex-lbp)
(defprop $nounadd 100. tex-rbp)

(defprop $nounsub tex-prefix tex)
(defprop $nounsub ("-") texsym)
(defprop $nounsub 100. tex-rbp)
(defprop $nounsub 100. tex-lbp)

(defprop $nounmul tex-nary tex)
(defprop $nounmul "\\," texsym)
(defprop $nounmul 120. tex-lbp)
(defprop $nounmul 120. tex-rbp)

(defprop $noundiv tex-mquotient tex)
(defprop $noundiv 122. tex-lbp) ;;dunno about this
(defprop $noundiv 123. tex-rbp)

(defprop $nounpow tex-mexpt tex)
(defprop $nounpow 140. tex-lbp)
(defprop $nounpow 139. tex-rbp)

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