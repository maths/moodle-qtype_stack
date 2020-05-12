#|
; logic.mac--Logic algebra package for Maxima CAS.
; Copyright (c) 2008--2009 Alexey Beshenov <al@beshenov.ru>.
;
; Version 2.11. Last modified 2009-01-07.
;
; logic.mac is free software; you can redistribute it and/or modify it
; under the terms of the GNU Lesser General Public License as published
; by the Free Software Foundation; either version 2.1 of the License,
; or (at your option) any later version.
;
; logic.mac is distributed in the hope that it will be useful, but
; WITHOUT ANY WARRANTY; without even the implied warranty of
; MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
; General Public License for more details.
;
; You should have received a copy of the GNU General Public License
; along with the logic.mac; see the file COPYING. If not, write to the
; Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
; Boston, MA 02110-1301, USA.
|#


(defvar $logic_mac_version 2.11)

(defvar use-maxima-logic-operators t)

(if use-maxima-logic-operators
  (progn
    (defvar *not-op* 'mnot)
    ($texput "not" " \\neg " '$prefix)
    (defvar *and-op* 'mand)
    ($texput "and" " \\wedge " '$nary)
    (defvar *or-op* 'mor)
    ($texput "or" " \\vee " '$nary))
  (progn
    ($prefix "log-not" 70)
    (defvar *not-op* '$log-not)
    ($texput "log-not" " \\neg " '$prefix)
    ($nary "log-and" 65)
    (defvar *and-op* '$log-and)
    ($texput "log-and" " \\wedge " '$nary)
    ($nary "log-or" 60)
    (defvar *or-op* '$log-or)
    ($texput "log-or" " \\vee " '$nary)))

($nary "nand" 62)
(defvar *nand-op* '$nand)
($texput "nand" " \\mid " '$nary)

($nary "nor" 61)
(defvar *nor-op* '$nor)
($texput "nor" " \\downarrow " '$nary)

($infix "implies" 59)
(defvar *implies-op* '$implies)
($texput "implies" " \\rightarrow " '$infix)

($nary "xnor" 58)
(defvar *eq-op* '$xnor)
($texput "xnor" " \leftrightarrow " '$nary)

($nary "xor" 58)
(defvar *xor-op* '$xor)
($texput "xor" " \\oplus " '$nary)

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

(defun get-maxima-operator (expr)
  (if (and (listp expr) expr (listp (car expr)) (car expr))
    (caar expr)
    nil))

(defun contains-operator (expr op)
  (let
    ((o (get-maxima-operator expr)) args)
    (setf args (if o (cdr expr) nil))
    (if
      (eq o op)
      t
      (member t (mapcar #'(lambda (e) (contains-operator e op)) args)))))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

(defun cartesian-product (l1 l2)
  (if l1
    (append
      (mapcar #'(lambda (e) (cons (car l1) e)) l2)
      (cartesian-product (cdr l1) l2))
    nil))

(defun replicate (n e)
  (if (and (integerp n) (>= n 0))
    (if (= n 0) nil (cons e (replicate (1- n) e)))
    (error "Invalid arguments to 'replicate'")))

(defun zip (l1 l2)
  (if (or (not (listp l1)) (not (listp l2)) (/= (length l1) (length l2)))
    (error "Invalid arguments to 'zip'"))
  (if (null l1)
    l1
    (cons (cons (car l1) (car l2)) (zip (cdr l1) (cdr l2)))))

(defun remove-nth (n l)
  (cond
    ((or (not (integerp n)) (< n 0)) (error "Invalid argumet to 'remove-nth'"))
    ((= n 0) (cdr l))
    (t (cons (car l) (remove-nth (1- n) (cdr l))))))

(defun multiset-to-hash (l)
  (mapcar
    #'(lambda (e) (list e (count e l :test 'equal)))
    (remove-duplicates l :test 'equal)))

(defun hash-to-multiset (h)
  (mapcan (lambda (he) (replicate (second he) (first he))) h))

(defun cancel-pairs-in-hash (h)
  (mapcar (lambda (he) (list (first he) (mod (second he) 2))) h))

(defun cancel-pairs (l)
  (hash-to-multiset (cancel-pairs-in-hash (multiset-to-hash l))))

(defun subst-recursive (expr pairs)
  (if pairs
    (let ((p (car pairs)))
      (subst (cdr p) (car p) (subst-recursive expr (cdr pairs))))
    expr))

(defun disjoin-list (pred lst)
  (if (null lst)
    '(nil nil)
    (let ((dl (disjoin-list pred (cdr lst))))
      (if (funcall pred (car lst))
        (list (cons (car lst) (first dl)) (second dl))
        (list (first dl) (cons (car lst) (second dl)))))))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

; t or nil
(defun booleanp (x)
  (or (eq x t) (eq x nil)))

(defun logic-sort-comparator (x y)
  (cond
    ((and (not (booleanp x)) (booleanp y)) t)
    ((and (booleanp x) (not (booleanp y))) nil)
    ((and (not (listp x)) (listp y)) nil)
    ((and (listp x) (not (listp y))) t)
    ((and (listp x) (listp y) (< (length x) (length y))) nil)
    ((and (listp x) (listp y) (> (length x) (length y))) t)
    (t ($orderlessp x y))))

(defun sort-symbols (seq)
  (sort seq 'logic-sort-comparator))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

; op (x_1, ..., f(y_1, ..., y_m), ..., x_n) =>
;                                         op (x_1, ..., y_1, ..., y_m, ..., x_n)
(defun flatten-nested (args op)
  (let
    ((nested-exprs nil)
     (other nil))
    (loop while args do
      (if
        (eq (get-maxima-operator (car args)) op)
        (setq nested-exprs (cons (car args) nested-exprs))
        (setq other (cons (car args) other)))
      (setq args (cdr args)))
    (setq
      nested-exprs
      (mapcar #'(lambda (e) (flatten-nested (cdr e) op)) nested-exprs))
    (if nested-exprs
      (append other (apply 'append nested-exprs))
      other)))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

; Logic functions

; Implication
(defun simp-implies (x y)
  (cond
    ((eq x nil) t)
    ((and (eq x t) (eq y t)) t)
    ((and (eq x t) (eq y nil)) nil)
    (t (list (list *implies-op* 'simp) x y))))

; Webb-operation or Peirce arrow (Quine's dagger, NOR)
(defun simp-nor (&rest args)
  (if
    (member t args)
    (return-from simp-nor nil))
  (setf args (remove-duplicates (remove nil args) :test 'equal))
  (cond
    ((null args) t)
    ((eq (length args) 1) (simp-not (car args)))
    (t (cons (list *nor-op* 'simp) (sort-symbols args)))))

; Sheffer stroke (alternative denial, NAND)
(defun simp-nand (&rest args)
  (if
    (member nil args)
    (return-from simp-nand t))
  (setf args (remove-duplicates (remove t args) :test 'equal))
  (cond
    ((null args) nil)
    ((eq (length args) 1) (simp-not (car args)))
    (t (cons (list *nand-op* 'simp) (sort-symbols args)))))

; Equivalence
(defun simp-eq (&rest args)
  (setf args (cancel-pairs (remove t (flatten-nested args *eq-op*))))
  (cond
    ((null args) t)
    ((eq (length args) 1) (car args))
    (t (cons (list *eq-op* 'simp) (sort-symbols args)))))

; Sum modulo 2 (exclusive or)
(defun simp-xor (&rest args)
  (setf args (cancel-pairs (remove nil (flatten-nested args *xor-op*))))
  (cond
    ((null args) nil)
    ((eq (length args) 1) (car args))
    (t (cons (list *xor-op* 'simp) (sort-symbols args)))))

; returns t if args = (... x ... not x ...)
; used in simp-and and simp-or
(defun x-not-x (args)
  (let
    ((neg
       (disjoin-list
         #'(lambda (e) (eq (get-maxima-operator e) *not-op*)) args)))
    (not
      (null
        (intersection
          (mapcar 'cadr (first neg)) (second neg) :test 'equal)))))

; Logical AND (conjunction)
(defun simp-and (&rest args)
  (setf args (flatten-nested args *and-op*))
  (if
    (member nil args)
    (return-from simp-and nil))
  (setf args (remove-duplicates (remove t args) :test 'equal))
  (cond
    ((null args) t)
    ((eq (length args) 1) (car args))
    (t
      (if (x-not-x args)
        nil
        (cons (list *and-op* 'simp) (sort-symbols args))))))

; Logical OR (disjunction)
(defun simp-or (&rest args)
  (setf args (flatten-nested args *or-op*))
  (if
    (member t args)
    (return-from simp-or t))
  (setf args (remove-duplicates (remove nil args) :test 'equal))
  (cond
    ((null args) nil)
    ((eq (length args) 1) (car args))
    (t
      (if (x-not-x args)
        t
        (cons (list *or-op* 'simp) (sort-symbols args))))))

; Logical NOT (negation)
(defun simp-not (x)
  (cond
    ((eq (get-maxima-operator x) *not-op*) (cadr x))
    ((eq x nil) t)
    ((eq x t) nil)
    (t (list (list *not-op* 'simp) x))))

(defun apply-op (op args)
  (cond
    ((eq op *and-op*) (apply 'simp-and args))
    ((eq op *xor-op*) (apply 'simp-xor args))
    ((eq op *not-op*) (apply 'simp-not args))
    ((eq op *or-op*) (apply 'simp-or args))
    ((eq op *nor-op*) (apply 'simp-nor args))
    ((eq op *nand-op*) (apply 'simp-nand args))
    ((eq op *eq-op*) (apply 'simp-eq args))
    ((eq op *implies-op*) (apply 'simp-implies args))
    (t (cons (list op) args))))

(defun logic-simp (expr)
  (let
    ((op (get-maxima-operator expr)) args)
    (setf args (if op (mapcar 'logic-simp (cdr expr)) nil))
    (if op
      (apply-op op args)
      expr)))

(defun $logic_simp (expr) (logic-simp expr))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

#|
;
; (all-charfuns 1) => ((nil) (t))
;
; (all-charfuns 2) => ((nil nil) (nil t) (t nil) (t t))
;
; (all-charfuns 3) => ((nil nil nil) (nil nil t) (nil t nil) (nil t t)
;                      (t nil nil) (t nil t) (t t nil) (t t t))
;
; ...
;
|#

(defun all-charfuns (n)
  (if (not (and (integerp n) (>= n 1)))
    (error "Invalid argument to 'all-charfuns'"))
  (cond
    ((= n 1) '((nil) (t)))
    (t
      (let
        ((pre (all-charfuns (1- n))))
        (append
          (mapcar (lambda (l) (cons nil l)) pre)
          (mapcar (lambda (l) (cons t l)) pre))))))

; List of values for all-charfuns, 2^n elements
(defun characteristic-vector (expr &rest args)
  (if (null args)
    (setf args (list-of-variables expr)))
  (if (null args)
    (list expr)
    (let (vals (n (length args)))
      (setf vals (mapcar #'(lambda (l) (zip args l)) (all-charfuns n)))
      (mapcar #'(lambda (v) (logic-simp (subst-recursive expr v))) vals))))

(defun list-of-variables (expr)
  (sort-symbols (cdr ($listofvars expr))))

(defun $characteristic_vector (expr &rest args)
  (cons '(mlist simp) (apply 'characteristic-vector (cons expr args))))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

; Conversion to the Zhegalkin basis {and, xor}
(defun zhegalkin-basis-substitute (expr)
  (let
    ((op (get-maxima-operator expr)) args)
    (setf args (if op (mapcar 'zhegalkin-basis-substitute (cdr expr)) nil))
    (cond
      ; not x => x xor t
      ((eq op *not-op*) (simp-xor (car args) t))
      ; x implies y => (x and y) xor x xor t
      ((eq op *implies-op*)
        (simp-xor (apply 'simp-and args) (first args) t))
      ; x1 nand x2 nand x3 ... nand xn => (x1 and x2 and x3 ... and xn) xor t
      ((eq op *nand-op*) (simp-xor (apply 'simp-and args) t))
      ; x nor y => (x or y) xor t
      ((eq op *nor-op*)
        (simp-xor
          (zhegalkin-basis-substitute (simp-or (first args) (second args)))
          t))
      ; x or y => (x and y) xor x xor y
      ((eq op *or-op*)
        (let (zhegform)
          (setf zhegform
            (simp-xor
              (simp-and (first args) (second args))
              (first args) (second args)))
          (setf args (cddr args))
          (loop while args do
            (setf zhegform
              (simp-xor
                (simp-and zhegform (car args))
                zhegform
                (car args)))
            (setf args (cdr args)))
          zhegform))
      ; a eq b => a xor b xor t
      ; a eq b eq c => a xor b xor c
      ; a eq b eq c eq d => a xor b xor c xor d xor t
      ; a eq b eq c eq d eq e => a xor b xor c xor d xor e
      ; ...
      ((eq op *eq-op*)
        (apply 'simp-xor
          (if (evenp (length args)) (cons t args) args)))
      (op (apply-op op args))
      (t expr))))

; acts like Maxima "expand" on ordinary polynomial ring,
; but on Zhegalkin polynomials
(defun zhegalkin-basis-expand (expr)
  (let
    ((op (get-maxima-operator expr)) args)
    (setf args (if op (mapcar 'zhegalkin-basis-expand (cdr expr)) nil))
    (cond
      ((eq op *and-op*)
        (let
          ((xor-expression
             (find-if
               (lambda (e) (eq (get-maxima-operator e) *xor-op*))
               (cdr expr))))
          (if xor-expression
            (let
              ((xor-args (cdr xor-expression))
               (and-args
                 (remove xor-expression (cdr expr) :test 'equal)))
              (zhegalkin-basis-expand
                (apply 'simp-xor
                  (mapcar
                    (lambda (e) (apply 'simp-and (cons e and-args)))
                    xor-args))))
            expr)))
      ((eq op *xor-op*) (apply 'simp-xor args))
      (t expr))))

(defun $zhegalkin_form (expr)
  (zhegalkin-basis-expand (zhegalkin-basis-substitute expr)))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

(defun $logic_equiv (expr1 expr2)
  (equal
    ($zhegalkin_form expr1)
    ($zhegalkin_form expr2)))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

(defun subst-not (expr)
  (let
    ((op (get-maxima-operator expr)))
    (if op
      (cons (list op) (mapcar 'subst-not (cdr expr)))
      (simp-not expr))))

; f^* (x_1, ..., x_n) = not f (not x_1, ..., not x_n)
(defun $dual_function (expr)
  (logic-simp (simp-not (subst-not expr))))

; f = f^*
(defun $self_dual (expr)
  ($logic_equiv expr ($dual_function expr)))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

(defun closed-under (expr x)
  (let
    (val n (args (list-of-variables expr)))
    (setf n (length args))
    (setf val (zip args (replicate n x)))
    (eq (logic-simp (subst-recursive expr val)) x)))

; f (nil, ..., nil) = nil
(defun $closed_under_f (expr)
  (closed-under expr nil))

; f (t, ..., t) = t
(defun $closed_under_t (expr)
  (closed-under expr t))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

(defun $monotonic (expr &rest args)
  (let
    (prev-value (charvec (apply 'characteristic-vector (cons expr args))))
    (if charvec
      (progn
        (setf prev-value (car charvec))
        (setf charvec (cdr charvec))
        (loop while charvec do
          (if
            (and
              (eq (car charvec) nil)
              (eq prev-value t))
            (return-from $monotonic nil))
          (setf prev-value (car charvec))
          (setf charvec (cdr charvec)))
        t)
      t)))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

(defun $linear (expr)
  (not (contains-operator ($zhegalkin_form expr) *and-op*)))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

; Post's theorem

(defun post-table (&rest expressions)
  (mapcar
    (lambda (fn) (mapcar fn expressions))
    '($self_dual $closed_under_f $closed_under_t $linear $monotonic)))

(defun functionally-complete (table)
  (if
    (null table)
    (return-from functionally-complete nil))
  (loop while table do
    (if
      (not (member nil (car table)))
      (return-from functionally-complete nil))
    (setf table (cdr table)))
  t)

(defun $functionally_complete (&rest expressions)
  (functionally-complete (apply 'post-table expressions)))

; Basis is a complete system without redundant functions
(defun $logic_basis (&rest expressions)
  (let
    ((table (apply 'post-table expressions))
     (n (length expressions)))
    (if (functionally-complete table)
      (if (= n 1)
        (return-from $logic_basis t))
      (return-from $logic_basis nil))
    (loop for i from 0 to (1- n) do
      (if
        (functionally-complete
          (mapcar (lambda (e) (remove-nth i e)) table))
        (return-from $logic_basis nil)))
    t))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

;; Logic differentiation

#|
;
;                               dy
;   (1)                        --- = false
;                              dx
;
; where y is a variable which not depends on x.
;
;
;                               dx
;   (2)                        --- = true
;                              dx
;
;
;              d
;   (3)       --- [x  and ... and x ] = x  and x  ... and x
;             dx    1              n     2      3          n
;               1
;
;
;                     d             df     dg
;   (4)              -- [g xor f] = -- xor --
;                    dx             dx     dx
;
;
; TODO: higher orders / mixed
;
|#

(defun diff-zhegalkin-form (expr x)
  (let ((op (get-maxima-operator expr)))
    (cond
      ((null op) (eq expr x))
      ((eq op *xor-op*)
        (apply
          'simp-xor
          (mapcar #'(lambda (e) (diff-zhegalkin-form e x)) (cdr expr))))
      ((eq op *and-op*)
        (let ((args (cdr expr)))
          (if (member x args) (apply 'simp-and (remove x args)) nil)))
      (t (error "Not a Zhegalkin form in diff-zhegalkin-form: '~s'" expr)))))

(defun $logic_diff (expr x)
  (diff-zhegalkin-form ($zhegalkin_form expr) x))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

; Coversion to the Boolean basis {and, or, not}
(defun boolean-basis-substitute (expr)
  (let
    ((op (get-maxima-operator expr)) args)
    (setf args (if op (mapcar 'boolean-basis-substitute (cdr expr)) nil))
    (cond
      ; x implies y => (not x) or y
      ((eq op *implies-op*) (simp-or (simp-not (first args)) (second args)))
      ; x1 nand ... nand xn => not (x1 and ... and xn)
      ((eq op *nand-op*) (simp-not (apply 'simp-and args)))
      ; x1 nor ... not xn => not (x1 or ... or xn)
      ((eq op *nor-op*) (simp-not (apply 'simp-or args)))
      ; x eq b => ((not x) or y) and ((not y) or x)
      ((eq op *eq-op*)
        (let (boolform)
          (setf boolform
            (simp-and
              (simp-or (simp-not (first args)) (second args))
              (simp-or (simp-not (second args)) (first args))))
          (setf args (cddr args))
          (loop while args do
            (setf boolform
              (simp-and
                (simp-or (simp-not boolform) (car args))
                (simp-or (simp-not (car args)) boolform)))
            (setf args (cdr args)))
          boolform))
      ; x xor y => ((not x) and y) or ((not y) and x)
      ((eq op *xor-op*)
        (let (boolform)
          (setf boolform
            (simp-or
              (simp-and (simp-not (first args)) (second args))
              (simp-and (simp-not (second args)) (first args))))
          (setf args (cddr args))
          (loop while args do
            (setf boolform
              (simp-or
                (simp-and (simp-not boolform) (car args))
                (simp-and (simp-not (car args)) boolform)))
            (setf args (cdr args)))
          boolform))
      (op (apply-op op args))
      (t expr))))

(defun $boolean_form (expr)
  (boolean-basis-substitute expr))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

; De Morgan's rules
(defun $demorgan (expr)
  (let
    ((op (get-maxima-operator expr)) args)
    (setf args (if op (mapcar '$demorgan (cdr expr)) nil))
    (cond
      ((eq op *not-op*)
        (let ((op-op (get-maxima-operator (car args))))
          (cond
            ((eq op-op *and-op*) (apply 'simp-or (mapcar 'simp-not (cdar args))))
            ((eq op-op *or-op*) (apply 'simp-and (mapcar 'simp-not (cdar args))))
            (t (apply 'simp-not args)))))
      ((null op) expr)
      (t (apply-op op args)))))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;

; Perfect disjunctive normal forms

(defun elementary-conjunct-disjunct (vars-vals b)
  (if (null vars-vals)
    nil
    (cons
      (if (eq (cdar vars-vals) b)
        (caar vars-vals)
        (list (list *not-op* 'simp) (caar vars-vals)))
      (elementary-conjunct-disjunct (cdr vars-vals) b))))

(defun pdnf-pcnf (expr b)
  (let ((args (list-of-variables expr)))
    (if (null args)
      expr
      (let (vals (n (length args)) (result nil))
        (setf vals (mapcar #'(lambda (l) (zip args l)) (all-charfuns n)))
        (loop while vals do
          (if (eq (logic-simp (subst-recursive expr (car vals))) b)
            (setf result
              (cons
                (apply (if b 'simp-and 'simp-or)
                  (elementary-conjunct-disjunct (car vals) b))
                result)))
          (setf vals (cdr vals)))
        (apply  (if b 'simp-or 'simp-and) result)))))

; Perfect disjunctive normal form
(defun $pdnf (expr)
  (pdnf-pcnf expr t))

; Perfect conjunctive normal form
(defun $pcnf (expr)
  (pdnf-pcnf expr nil))

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
