;; basen.lisp -- functions for dealing with base N numbers in STACK Maxima.
;;
;; Copyright (C) 2017 Stephen Parry, adapted from work by Robert Dodier
;; Copyright (C) 2005 Robert Dodier and Chris Sangwin
;; Copyright (C) 2015 Chris Sangwin
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

(defun split-number-string(s base m)
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
                ((eql m #\M) (cond ((> base 10) (list (cons "0" base) (cons "" -1))) (t (list (cons "" base)))))
                ((eql m #\C) '(("0b". 2) ("0x" . 16) ("0" . 8) ("" . 10)))
                ((eql m #\B) '(("&b". 2) ("&o" . 8) ("&h" . 16) ("" . 10)))
                (t (list (cons ""  base)))))
            (prefp (assoc s prefs :test (lambda(s1 s2) (and ( >= (length s1) (length s2) ) (string-equal (subseq s1 0 (length s2)) s2)) )))
            (pref (car prefp))
            (b2 (cdr prefp))
            (body (subseq s (length pref))))
          (list pref body "" b2)))))

(defconstant basen-mode-list (list
        '("D" . 0)
        '("M" . 1)
        '("G" . 2)
        '("B" . 3) (cons '"B*" (+ 64 3)) '("C" . 4) (cons '"C*" (+ 64 4))
        '("S" . 5) '("_" . 5) (cons '"S*" ( + 64 5)) (cons '"_*" (+ 64 5))))

(defun lookup-basen-mode(mode)
  (let* (
      (mode-entry (cond 
        ((not mode) (assoc '"M" basen-mode-list :test #'string-equal))
        ((integerp mode) (rassoc mode basen-mode-list :test #'eql))
        ((or (charp mode) (stringp mode)) (assoc mode basen-mode-list :test #'string-equal))
        (t nil)))
      (mn (cdr mode-entry)))
    (and mode-entry (list (elt (car mode-entry) 0) (cond ((> mn 64) (- mn 64)) (t mn)) (> mn 64)))))

(defun summarize-basen-mode-list()
  (format nil "~{ ~A~^, ~}" (map 'list #'(lambda(e) (format nil "~A[~d]" (car e) (cdr e))) basen-mode-list)))

(defun $lookup_basen_mode(mode)
  (let ((rv (lookup-basen-mode mode))) (cons '(mlist) (cond (rv (cons (string (car rv)) (cdr rv))) (t nil )) )))

(defun summarize_basen_mode_list()
  (summarize-basen-mode-list))

;; This function is designed for converting base n numbers.                                 
;; frombasen(s, base, mode, mindigits) converts s from a base n format string to an integer                  
;; base is the radix of the number; must be 2 <= base <= 36.
;; digits is the exact number of figures to expect. 0 or nil here means any number.
;; mode is a string controlling the format:                                                 
;;    D    STACK compatible syntax; does not work for bases 11+.
;;    M    Maxima syntax: number should be 0 prefixed if base 11+; the default.
;;    G    Greedy syntax: means number can start with any alphanumeric; this is the most       
;;         convenient for entry of literal value answer but will seriously hamper use of       
;;         expressions containing variables or functions in student answers.
;;    B/B* Visual Basic number syntax: &HFF &o77 &b11. If B* any of the three
;;         prefixes can be used (or none for base 10) and base parameter is effectively ignored;
;;         otherwise only the prefix matching the base parameter can be used.
;;    C/C* C/C++/Java number syntax: 0xff 077 0b11. If C* any of the three
;;         prefixes can be used (or none for base 10) and base parameter is effectively ignored;
;;         otherwise only the prefix matching the base parameter can be used.
;;    S/S* Suffix syntax; number should appear with the radix as a subscripted suffix, typed using
;;         an _ char. If S* or _* any valid suffix can be used; otherwise only one matching the
;;         base parameter passed can be used.

(defun $frombasen (s base &optional mode digits)
  (destructuring-bind (ml mn choice) (lookup-basen-mode mode)
    (declare (ignore mn))
    (cond
      ((stringp s)
        (cond
          ((and (integerp base) ( <= 2 base 36 ))
            (cond
              (ml
                (cond
                  ((or (not (eql ml #\D)) (<= base 10))
                    (cond
                      ((or (not digits) (and (integerp digits) ( >= digits 0 )))
                        (destructuring-bind (pref body suff b2) (split-number-string s base ml)
                          (declare (ignore pref))

                          (cond
                            ((or choice (eql b2 base))

                              (cond
                                ((and (integerp b2) (<= 2 b2 36) )

                                  (cond
                                    ((or (eq digits 0) (eq digits (length body)))

                                      (let 
                                        ((n 
                                          (cond
                                            ((> (length body) 0)
                                              (catch 'macsyma-quit (parse-basen-string (cond ((and (integerp b2) (> b2 10)) (concatenate 'string "0" body)) (t body)) b2)) )
                                            (t (merror "frombasen: Empty value.")) )))
                                        (cond
                                          ((integerp n) n)
                                          (t
                                            (merror "frombasen: ~M does not convert to a base ~M integer." s b2) ) )))

                                    (t
                                      (merror "frombasen: ~M contains wrong number of digits for ~M digit base ~M integer." s digits (cond ((> base 0) base) (t b2))) ) ))

                                (t
                                  (merror "frombasen: base \"~M\" should be an integer between 2 and 36." suff) ) ))

                            (t
                              (merror "frombasen: ~M~M is incorrect format for base ~M integer." s (cond ((> base 0) base) (t b2))) ) )))

                      (t
                        (merror "frombasen: no of digits ~M should be an integer 0 or above" digits) ) ))

                  (t
                    (merror "frombasen: base must be 10 or less for default mode.") ) ))

              (t
                (merror "frombasen: ~M is not valid; must be one of ~M" mode (summarize-basen-mode-list) ) ) ))
          (t
            (merror "frombasen: base of ~M is not an integer between 2 and 36." base) ) ))
      (t
        (merror "frombasen: ~M is not a string." s) ) )))

;; 
;; (PARSE-STRING S)  --  parse the string as a Maxima expression.
;; Do not evaluate the parsed expression.

(defun parse-basen-string (s base)
  (declare (special *mread-prompt*))
  (let (( *read-base* base))
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

;; This function is designed for converting base n numbers.                                 
;; tobasen(n, base, mode, mindigits) converts n from an integer to a base n format string                  
;; base is the radix of the number; must be 2 <= base <= 36.
;; digits is the minimum number of figures outputted.
;; mode is a string controlling the format:                                                 
;;    D    STACK compatible format; base must be < 11.
;;    M    Maxima syntax: number will be 0 prefixed if base is 11+; This is the default.
;;    G    Greedy format: number is output without any prefix regardless of base. The simplest
;;         format for literal value base conversion questions but does not play nicely with
;;         others; i.e. base 11+ numbers will be confused with variables and some floats.
;;         e.g. abcd or 1e0 in base 16.
;;    B/B* Visual Basic number syntax: &HFF &o77 &b11. Only bases 2,8,10 and 16 are valid.
;;    C/C* C/C++/Java number syntax: 0xff 077 0b11. Only bases 2,8,10 and 16 are valid.
;;    S    Suffix syntax; number will appear with the radix as a subscripted suffix (123_8).

(defun $tobasen(n base &optional mode (mindigits 0))
  (let* (
      (m (first (lookup-basen-mode mode))))
    (format nil
      (concatenate
        'string 
        (cond 
          ((char-equal m #\M) "0")
          ((char-equal m #\C)
            (list '#\0 (cond ((= base 2) #\b) ((= base 8) #\o) ((= base 16) #\x) (t #\?))))
          ((char-equal m #\B)
            (list #\& (cond ((= base 2) #\B) ((= base 8) #\O) ((= base 16) #\H) (t #\?))))
          (t ""))
        "~" (format nil "~d" base) "," (format nil "~d" mindigits) ",'0R" 
        (cond 
          ((or (char-equal m #\_) (char-equal m #\S))
            (format nil "_~d" base))
          (t "")))
      n)))

;; This function has grind (and hence "string") output the number in the following base.
;; basen(number, base, mode, mindigits).
(defprop $basenvalue msz-basenvalue grind)
(defun msz-basenvalue (x l r)
  (msz (mapcar #'(lambda (l) (getcharn l 1)) 
    (let* (
        (value (second x)) (base (third x)) (mode (fourth x)) (mindigits (fifth x)) (m (first (lookup-basen-mode mode))))
    (makestring
      (format nil
        (concatenate
          'string 
          (cond 
            ((string-equal m "M") "0")
            ((string-equal m "C")
              (list #\0 (cond ((= base 2) #\b) ((= base 8) #\o) ((= base 16) #\x) (t #\?))))
            ((string-equal m "B")
              (list #\& (cond ((= base 2) #\B) ((= base 8) #\O) ((= base 16) #\H) (t #\?))))
            (t ""))
          "~" (format nil "~d" base) "," (format nil "~d" mindigits) ",'0R" 
          (cond 
            ((or (string-equal m "_") (string-equal m "S"))
              (format nil "_~d" base))
            (t "")))
        value))  ) ) l r))
