;; Custom version of erromsg() to collect the error as 
;; a string after it has been formatted
;; Matti Harjula 2019

(defmfun $errormsgtostring ()
  "errormsgtostring() returns the maxima-error message as string."
  (apply #'aformat nil (cadr $error) (caddr (process-error-argl (cddr $error))))
)

;; *************************************************************************************************
;; Added 12 Nov 2024.
;; 
;; Define these in LISP to remove compile warnings when starting the sandbox.

(defvar $stackintfmt '"~d")
(defvar $stackfltfmt '"~a")
(defvar $stackfltsep '".")


