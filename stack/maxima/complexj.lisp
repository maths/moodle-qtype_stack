;; Customize Maxima's TEX() function.  
;; Make %i print at a "j"
;; Chris Sangwin 19 August Jan 2005.
;; Useful files: 
;; \Maxima-5.9.0\share\maxima\5.9.0\share\utils\mactex-utilities.lisp
;; \Maxima-5.9.0\share\maxima\5.9.0\src\mactex.lisp

(defprop $%i "\\mathrm{j}" texword)

(defprop $%i "<mi>j</mi> " mathmlword)
