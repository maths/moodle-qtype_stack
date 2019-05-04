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
  (if (or (eql m #\S) (eql m #\_))
      (let* (
             (sp (position #\_ s :from-end t))
             (suff (if sp (subseq s (+ sp 1)) "" ))
             (b2 (if (and (<= 1 (length suff) 2) (digit-char-p (elt suff 0)) (or (= 1 (length suff)) (digit-char-p (elt suff 1))) ) (parse-integer suff) -1))
             (body (cond (sp (subseq s 0 sp)) (t s))))
	
        (if (and (eql m #\_) ( > b2 10)) (if (eql (char s 0) #\0) (list "0" (subseq body 1) suff b2) (list "" body suff -2)) (list "" body suff b2)) )

    (let* (
           (prefs
            (cond
             ((eql m #\M)
              (if (> base 10)
                  (list (cons "0" base) (cons "" -2))
                (list (cons "" base))))
             ((eql m #\C) '(("0b". 2) ("0x" . 16) ("0" . 8) ("" . 10)))
             ((eql m #\B) '(("&b". 2) ("&o" . 8) ("&h" . 16) ("" . 10)))
             (t (list (cons ""  base)))))
           (prefp (assoc s prefs :test (lambda(s1 s2) (and ( >= (length s1) (length s2) ) (string-equal (subseq s1 0 (length s2)) s2)) )))
           (pref (car prefp))
           (b2 (cdr prefp))
           (body (subseq s (length pref))))
      (list pref body "" b2))))

(defconstant basen-mode-list (list
			      '("D" . 0) (cons '"D<" (+ 128 0)) 
			      '("M" . 1) (cons '"M<" (+ 128 1)) 
			      '("G" . 2) (cons '"G<" (+ 128 2)) 
			      '("B" . 3) (cons '"B*" (+ 128 3)) '("C" . 4) (cons '"C*" (+ 64 4))
			      '("_" . 5) '("S" . 6) (cons '"_*" ( + 64 5)) (cons '"S*" (+ 64 6))))

(defun lookup-basen-mode(mode)
  (let* (
	 (mode-entry (cond 
		      ((not mode) (assoc '"M" basen-mode-list :test #'string-equal))
		      ((integerp mode) (rassoc mode basen-mode-list :test #'eql))
		      ((or (charp mode) (stringp mode)) (assoc mode basen-mode-list :test #'string-equal))
		      (t nil)))
	 (mn (cdr mode-entry)))
    (and mode-entry (list (elt (car mode-entry) 0) (cond ((> mn 128) (- mn 128)) ((> mn 64) (- mn 64)) (t mn)) (> mn 64) (> mn 128)))))

(defun summarize-basen-mode-list()
  (format nil "~{ ~A~^, ~}" (map 'list #'(lambda(e) (format nil "~A[~d]" (car e) (cdr e))) basen-mode-list)))

(defun $lookup_basen_mode(mode)
  (let ((rv (lookup-basen-mode mode))) (cons '(mlist) (cond (rv (cons (string (car rv)) (cdr rv))) (t nil )) )))

(defun $summarize_basen_mode_list()
  (summarize-basen-mode-list))

(defun validate-basen-params (func base mode mindigits)
  (let ((mode-entry (lookup-basen-mode mode)))
    (if (and (integerp base) (or ( <= 2 base 36 ) ( = -2 base ) (and mode-entry (third mode-entry) (= base 0))))
	(if mode-entry
            (if (or (not (or (eql (first mode-entry) #\B) (eql (first mode-entry) #\C) )) (third mode-entry) (= base 2) (= base 8) (= base 10) (= base 16) )
		(if (or (not (eql (first mode-entry) #\D)) (<= base 10))
		    (if (and (integerp mindigits) ( >= mindigits 0 ))
			mode-entry
		      (merror "~M: minimum number of digits ~M should be an integer 0 or greater." func mindigits) )
		  (merror "~M: base must be 10 or less for default mode." func ) )
              (merror "~M: base must be 2, 8, 10 or 16 for mode ~M." func mode ) )
          (merror "~M: ~M is not valid; must be one of ~M." func mode (summarize-basen-mode-list) ) )
      (merror "~M: base of ~M is not an integer between 2 and 36." func base) ) ) )


;; This function is designed for converting from a base n representation string to an integer.
;; frombasen(s, base, mode, mindigits) converts s from a base n format string to an integer
;; s         is the string to convert.
;; base      is the radix of the number; must be 2 <= base <= 36.
;; mindigits is the exact number of figures to expect. 0 or nil here means any number.
;; mode      is a string controlling the format:                                                 
;;    D    STACK compatible syntax; does not work for bases 11+.
;;    D<   Variation of D syntax where the number reads as if padded from the right with
;;         zeroes, i.e. the most significant digit is fixed as maximum value. Useful
;;         for processing fixed point numbers.
;;    M    Maxima syntax: number should be 0 prefixed if base 11+; the default.
;;    M<   Variation of M syntax where the number reads as if padded from the right with
;;         zeroes, i.e. the most significant digit is fixed as maximum value. Useful
;;         for processing fixed point numbers.
;;    G    Greedy syntax: means number can start with any alphanumeric; this is the most       
;;         convenient for entry of literal value answer but will seriously hamper use of       
;;         expressions containing variables or functions in student answers.
;;    G<   Variation of G syntax where the number reads as if padded from the right with
;;         zeroes, i.e. the most significant digit is fixed as maximum value. Useful
;;         for processing fixed point numbers.
;;    B/B* Visual Basic number syntax: &HFF &o77 &b11. If B* any of the three
;;         prefixes can be used (or none for base 10) and base parameter is effectively ignored;
;;         otherwise only the prefix matching the base parameter can be used.
;;    C/C* C/C++/Java number syntax: 0xff 077 0b11. If C* any of the three
;;         prefixes can be used (or none for base 10) and base parameter is effectively ignored;
;;         otherwise only the prefix matching the base parameter can be used.
;;    _/_* Suffix syntax; number should appear with the radix as a subscripted suffix, typed using
;;         an _ char. If  _* any valid suffix can be used; otherwise only one matching the
;;         base parameter passed can be used. Numbers base 11+ must be prefixed with a zero.
;;    S/S* Greedy Suffix syntax; as _/_*, but no prefix required for base 11+

(defun $frombasen (s base &optional (mode "M") (mindigits 0))
  (if (stringp s)
      (if (> (length (string-trim '(#\Space #\Tab #\Newline) s)) 0)
	  (let 
              ((mode-entry (validate-basen-params "frombasen" base mode mindigits))
               (absbase (abs base)))
            (destructuring-bind (ml mn choice leftj) mode-entry
				(declare (ignore mn))
				(destructuring-bind (pref body suff b2) (split-number-string s absbase ml)
						    
						    (declare (ignore pref))
						    
						    (if (> b2 -2) 
							(if (or choice (eql b2 absbase))
							    (if (and (integerp b2) (<= 2 b2 36) )
								(if (or (> (length (string-trim '(#\Space #\Tab #\Newline) body)) 0) (and (eq ml #\M) (> absbase 10)) )
								    
								    (let* (
									   (body-padded (cond-left-pad body mindigits leftj))
									   (n 
									    (catch 'macsyma-quit 
									      (parse-basen-string (if (and (integerp b2) (> b2 10)) (concatenate 'string "0" body-padded) body-padded) (if (= base -2) -2 b2))) ))
								      (declare (special $report_synerr_info))
								      (if (integerp n)
									  (if (or (eq mindigits 0) (eq mindigits (length body)) (and leftj (>= mindigits (length body))) )
									      n
									    (merror "~M: ~M contains wrong number of digits for ~M digit base ~M integer." "frombasen" s mindigits (cond ((> base 0) base) (t b2))) )

									(merror "~M: ~M is not a valid base ~M integer." "frombasen" s b2) ))
								  
								  (merror "~M: Empty value." "frombasen") )
							      
							      (merror "~M: base \"~M\" should be an integer between 2 and 36." "frombasen" suff) )
							  
							  (merror "~M: ~M is incorrect format for base ~M integer." "frombasen" s (if (> base 0) base b2) ) )
						      
						      (merror "~M: Prefix 0 missing from ~M." "frombasen" s) ) ) ) )
	
	(merror "~M: Empty string." "frombasen" ) )

    (merror "~M: ~M is not a string." "frombasen" s) ) ) 

(defun cond-left-pad (s mindigits leftj)
  (if (and leftj (< (length s) mindigits))
      (concatenate 'string s (make-string (- mindigits (length s)) :initial-element #\0))
    s ) )

;; 
;; (PARSE-BASEN-STRING S BASE)  --  parse the string as a Maxima expression in base N.
;; Do not evaluate the parsed expression.

(defun parse-basen-string (s base)
  (declare (special *mread-prompt*))
  (let*
      ((absbase (abs base))
       s1
       adj
       ( *read-base* absbase )
       ( fstr (
               make-array '(0) :element-type 'base-char :fill-pointer 0 :adjustable t)))
    (cond ((and (= base -2) (and (> (length s) 0) (not (eq (elt s 0) #\0))))
	   (setq s1 (subseq s 1)) (setq adj (- (expt 2 (- (length s) 1)))))
	  (t (setq s1 s) (setq adj 0)))
    (+ (with-output-to-string (*standard-output* fstr)
			      (with-input-from-string
			       (ss (ens-term s1))
			       (third (let ((*mread-prompt*)) (mread ss))))) adj )))

;; (ENS-TERM S)  -- if the string S does not contain dollar sign `$' or semicolon `;'
;; then append a dollar sign to the end of S.

(defun ens-term (s)
  (cond
   ((or (search "$" s :test #'char-equal) (search ";" s :test #'char-equal))
    s)
   (t
    (concatenate 'string s "$"))))

(defun min-twos-comp-digits(n)
  (+ (ceiling (log (if (< n 0) (- n) (+ n 1)) 2)) 1 ) )

(defun twos-comp-of(n m)
  (if (< n 0) (+ n (expt 2 m)) n ) )

;; This function is designed for converting from a number to a base n representation string.
;; tobasen(n, base, mode, mindigits) converts n from an integer to a base n format string
;; n        is the integer number to convert.
;; base     is the radix of the number; must be 2 <= base <= 36 or -2
;;          -2 indicates that the number should be treated as two's complement;
;;          any negative number n is first converted to 2^m + n - where m is either
;;          mindigits or the minimum required to represent the number (eg, for
;;          -128..127 this is 8 digits), whichever is greater.
;; mindigitsis the minimum number of figures outputted.
;; mode     is a string controlling the format:                                                 
;;    D    STACK compatible format; base must be < 11.
;;    D<   Variation of D syntax where the number reads as if padded from the right with
;;         zeroes, i.e. the most significant digit is fixed as maximum value. Useful
;;         for processing fixed point numbers.
;;    M    Maxima syntax: number will be 0 prefixed if base is 11+; This is the default.
;;    M<   Variation of M syntax where the number reads as if padded from the right with
;;         zeroes, i.e. the most significant digit is fixed as maximum value. Useful
;;         for processing fixed point numbers.
;;    G    Greedy format: number is output without any prefix regardless of base. The simplest
;;         format for literal value base conversion questions but does not play nicely with
;;         others; i.e. base 11+ numbers will be confused with variables and some floats.
;;         e.g. abcd or 1e0 in base 16.
;;    G<   Variation of G syntax where the number reads as if padded from the right with
;;         zeroes, i.e. the most significant digit is fixed as maximum value. Useful
;;         for processing fixed point numbers.
;;    B/B* Visual Basic number syntax: &HFF &o77 &b11. Only bases 2,8,10 and 16 are valid.
;;    C/C* C/C++/Java number syntax: 0xff 077 0b11. Only bases 2,8,10 and 16 are valid.
;;    S    Suffix syntax; number will appear with the radix as a subscripted suffix (123_8).
;;    _/_* Suffix syntax; number will appear with the radix as a subscripted suffix (123_8).
;;         Numbers base 11+ will be prefixed with a zero.
;;    S/S* Greedy Suffix syntax; as _/_*, but no prefix generated for base 11+

(defun tobasen(n base &optional (mode "M") (mindigits 0))
  (if (integerp n)
      (let
          ((mode-entry (validate-basen-params "tobasen" base mode mindigits)) (absbase (abs base))
           (md (if (= base -2) (max (min-twos-comp-digits n) mindigits) mindigits ) ) )
	(destructuring-bind (ml mn choice leftj) mode-entry
			    (declare (ignore mn))
			    (format nil
				    (concatenate
				     'string 
				     (cond 
				      ((or (and (char-equal ml #\C) (/= absbase 10)) (and (or (char-equal ml #\_) (char-equal ml #\M)) (> absbase 10))) "0")
				      ((and (char-equal ml #\B) (/= absbase 10)) "&")
				      (t ""))
				     (cond 
				      ((char-equal ml #\C)
				       (cond ((= absbase 2) "b") ((= absbase 8) "") ((= absbase 10) "") ((= absbase 16) "x") (t "?")))
				      ((char-equal ml #\B)
				       (cond ((= absbase 2) "B") ((= absbase 8) "o") ((= absbase 10) "") ((= absbase 16) "H") (t "?")))
				      (t ""))
				     "~" (format nil "~d" absbase) "," (format nil "~d" md) ",'0R" 
				     (cond 
				      ((or (char-equal ml #\_) (char-equal ml #\S))
				       (format nil "_~d" absbase))
				      (t "")))
				    (if (or (>= n 0) (/= base -2)) n (+ n (expt 2 md)) ) ) ) ) 
    (merror "~M: ~M is not an integer." "tobasen" n ) ) )

(defun $tobasen(n base &optional (mode "M") (mindigits 0))
  (tobasen n base mode mindigits))

;; This function has grind (and hence "string") output the number in the following base.
;; basen(number, base, mode, mindigits).
(defprop $basenvalue msz-basenvalue grind)
(defun msz-basenvalue (x l r)
  (msz (mapcar #'(lambda (l) (char (string l) 0)) 
	       (let* (
		      (value (second x)) (base (third x)) (mode (fourth x)) (mindigits (fifth x)) (m (first (lookup-basen-mode mode))))
		 (makestring (tobasen value base mode mindigits) )  ) ) l r))

;; This function calculates the two's complement of a number, with mindigits or more bits.

(defun $twoscompof(n &optional (mindigits (min-twos-comp-digits n)))
  (twos-comp-of n mindigits))

;; This function calculates the minimum number of digits required to represent a number 
;; in two's complement representation.
(defun $mintwoscompdigits(n) (min-twos-comp-digits n))

