;; Customize Maxima's tex() function.  
;; Chris Sangwin 1 may 2015.

(defprop $nounand tex-nary tex)
(defprop $nounand ("\\land ") texsym)
(defprop $nounand 69. tex-lbp)
(defprop $nounand 69. tex-rbp)

(defprop $nounor tex-nary tex)
(defprop $nounor ("\\lor ") texsym)
(defprop $nounor 70. tex-lbp)
(defprop $nounor 70. tex-rbp)

