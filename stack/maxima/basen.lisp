;; parsebasen.lisp -- parse a string as a single number token.
;;
;; Copyright (C) 2017 Stephen Parry, adopted from work by Robert Dodier
;; Copyright (C) 2005 Robert Dodier
;;
;; This program is free software; you can redistribute it and/or modify
;; it under the terms of the GNU General Public License as published by
;; the Free Software Foundation; either version 2 of the License, or
;; (at your option) any later version.
;;
;; This program is distributed in the hope that it will be useful,
;; but WITHOUT ANY WARRANTY; without even the implied warranty of
;; MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
;; GNU General Public License for more details.

(in-package :maxima)

(defun split-number-string(s b m)
    (cond 
      ((or (eql m #\S) (eql m #\_))
        (let* (
            (sp (position #\_ s :from-end t))
            (suff (cond (sp (subseq s (+ sp 1))) (t "")))
            (b2 (cond ((and (<= 1 (length suff) 2) (digit-char-p (elt suff 0)) (or (= 1 (length suff)) (digit-char-p (elt suff 1))) ) (parse-integer suff)) (t nil)))
            (body (cond (sp (subseq s 0 sp)) (t s))))
          (list "" body suff b2)))
      (t
        (let* (
            (prefs
              (cond
                ((eql m #\M) (cond ((> b 10) (list (cons "0" b) (cons "" -1))) (t (list (cons "" b)))))
                ((eql m #\C) '(("0b". 2) ("0x" . 16) ("0" . 8) ("" . 10)))
                ((eql m #\B) '(("&b". 2) ("&o" . 8) ("&h" . 16) ("" . 10)))
                (t (list (cons ""  b)))))
            (prefp (assoc s prefs :test (lambda(s1 s2) (and ( >= (length s1) (length s2) ) (string-equal (subseq s1 0 (length s2)) s2)) )))
            (pref (car prefp))
            (b2 (cdr prefp))
            (body (subseq s (length pref))))
          (list pref body "" b2)))))

;; This function is designed for converting base n numbers.                                 
;; frombasen(s, base, mindigits, mode) converts s from basen to an integer                  
;; base is the radix (base) of the number; must be 2 <= base <= 36 or 0, which
;;   means the base is determined by a suffix or prefix, according to mode.
;; digits is the exact number of figures to expect. 0 or nil here means any number.
;; mode is a string controlling the format:                                                 
;;    D STACK compatible syntax; does not work for bases 11+ and base cannot be 0.
;;    M Maxima syntax: number should be 0 prefixed if base 11+; the default. base cannot be 0.
;;    G Greedy syntax: means number can start with any alphanumeric; this is the most       
;;      convenient for entry of literal value answer but will seriously hamper use of       
;;      expressions containing variables or functions in student answers. base cannot be 0.
;;    B Visual Basic number syntax: &HFF &o77 &b11. If base is 0 any of the three
;;      prefixes can be used (or none for base 10); otherwise only the correct one can be used.
;;    C C/C++/Java number syntax: 0xff 077 0b11. If base is 0 any of the three
;;      prefixes can be used (or none for base 10); otherwise only the correct one can be used.
;;    S Suffix syntax; number should appear as a subscripted suffix, typed using an _ char. 
;;      If base is 0 any valid suffix can be used (or none for base 10);
;;      otherwise only the correct one can be used.

(defun $frombasen (s b &optional digits mode)
  (let*
    ((mode-letters "DMGBCS")
      (star-mode-letters "BCS_")
      (alt-mode-letters "_")
      (max-mode (- (length mode-letters) 1))
      (m (cond
        ((not mode) #\D)
        ((integerp mode) (cond ((<= 0 mode max-mode) (elt mode-letters mode)) (t nil)))
        ((or (characterp mode) (stringp mode)) (char-upcase (coerce mode 'character)))
        (t nil))))
    (cond
      ((stringp s)
        (cond
          ((and (integerp b) (or ( <= 2 b 36 ) (= b 0)))
            (cond 
              ((or (> b 0) (find m star-mode-letters))
                (cond
                  ((or (not (eql m #\D)) (<= b 10))
                    (cond
                      ((or (not digits) (and (integerp digits) ( >= digits 0 )))
                        (cond
                          ((and m (find m mode-letters))
                            (destructuring-bind (pref body suff b2) (split-number-string s b m)
                              (cond
                                ((or (eql b 0) (eql b2 b)) 
                                  (cond
                                    ((and (integerp b2) (<= 2 b2 36) )
                                      (cond
                                        ((or (eq digits 0) (eq digits (length body)))
                                          (let 
                                            ((n 
                                              (cond
                                                ((> (length body) 0)
                                                  (catch 'macsyma-quit (parse-basen-string (cond ((and (integerp b2) (> b2 10)) (concatenate 'string "0" body)) (t body)) b2)))
                                                (t (merror "frombasen: Empty value.")))))
                                            (cond
                                              ((integerp n) n)
                                              (t
                                                (merror "frombasen: ~M does not convert to a base ~M integer." s b2)))))
                                        (t
                                          (merror "frombasen: ~M is in incorrect format for ~M digit base ~M integer." s digits (cond ((> b 0) b) (t b2))))))
                                    (t
                                      (merror "frombasen: base \"~M\" is invalid." suff))))
                                (t
                                  (merror "frombasen: ~M~M is incorrect format for base ~M integer." pref body (cond ((> b 0) b) (t b2)))))))
                          (t
                            (let ((ml (format nil "~{~A~^, ~}" (coerce (concatenate 'string mode-letters alt-mode-letters) 'list))))
                              (merror "frombasen: ~M is not valid; must be one of ~M or an integer 0-~M" mode ml (format nil "~d" max-mode))))))
                      (t
                        (merror "frombasen: ~M is not an integer 0 or above" digits))))
                  (t
                    (merror "frombasen: base must be 10 or less for default mode."))))
              (t
                (let ((sml (format nil "~{~A~^, ~}" (coerce star-mode-letters 'list))))
                (merror "frombasen: cannot combine mode ~M and base 0, mode must be one of ~M" mode sml)))))
          (t
            (merror "frombasen: base of ~M is not an integer between 2 and 36 or 0." b))))
      (t
        (merror "frombasen: ~M is not a string." s)))))

;; 
;; (PARSE-STRING S)  --  parse the string as a Maxima expression.
;; Do not evaluate the parsed expression.

(defun parse-basen-string (s b)
  (declare (special *mread-prompt*))
  (let (( *read-base* b))
  (with-input-from-string
    (ss (ens-term s))
    (third (let ((*mread-prompt*)) (mread ss))))))

;; (ENS-TERM S)  -- if the string S does not contain dollar sign `$' or semicolon `;'
;; then append a dollar sign to the end of S.

(defun ens-term (s)
  (cond
    ((or (search "$" s :test #'char-equal) (search ";" s :test #'char-equal))
     s)
    (t
      (concatenate 'string s "$"))))

