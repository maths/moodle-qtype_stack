(defun is-adjacent (form1 form2)
  "Checks if the two forms can be combined(are adjacent in the K-Map).
   Example : (is-adjacent '(0 0 1 0) '(0 0 1 1)) -> t
             (is-adjacent '(0 0 1 1) '(0 0 1 1)) -> nil"
  (loop
     for x on form1
     for y on form2
     do
       (if (equal (car x) (car y))
	   nil
	   (if (equal (cdr x) (cdr y))
	       (if (or (equal (car x) 2) (equal (car y) 2))
		   (return-from is-adjacent nil)
		   (return-from is-adjacent t))
	       (return-from is-adjacent nil))))
  (return-from is-adjacent nil))

(defun common-expression (minterm1 minterm2)
  "Returns the common-expression obtained by combining 2 minterms.
   Example : (common-expression '(1 1 0 1) '(1 1 1 1)) -> (1 1 2 1)"
  (loop
     for x in minterm1
     for y in minterm2
     collect
       (if (equal x y)
	   x
	   2)))
  
(defun combine-neighbours (minterm minterms)
  "Checks a minterm against a list of minterms to find adjacent pairs.
   Returns a list l such that,
     (car l) is a list of implicants formed by combining minterm with all possible neighbours in minterms.
     (cadr l) is a list of all minterms covered in the implicants in the first list.
   Example :  (combine-neighbours '(0 0) '((0 1) (1 1))) -> (((0 2)) ((0 0) (0 1)))"
  (let* ((pair_found nil)
	 (result (loop for minterm2 in minterms
		    if (is-adjacent minterm minterm2)
		    collect (progn
			      (setf pair_found t)
			      (common-expression minterm minterm2))
		    into comm_expr_ls and
		    collect minterm2 into to_remove
		    finally (return (list comm_expr_ls (cons minterm to_remove))))))
    (if pair_found
	result
	(list (cons minterm nil) ()))))
	     

(defun reduce-to-prime-implicants (numvar minterms)
  "Reduces a list of minterms, corresponding to numvar number of boolean variables, to it's prime-implicants, collectively covering all minterms.
   Returns a list of prime-impicants covering all minterms.
   Example : (reduce-to-prime-implicants 2 '((0 0) (0 1) (1 0))) -> ((0 2) (2 0))"
  (dotimes (counter numvar)
    (setf minterms (loop for minterms-left on minterms as result = (combine-neighbours (car minterms-left) (cdr minterms-left))
		      append (car result) into implicants ; Add the returned list of combined minterms to "implicants" list
		      append (cadr result) into covered   ; Add all the minterms covered in prime-implicants to list "covered"
		      finally (return
				(remove-duplicates
				 (remove-if (lambda (implicant) (member implicant covered)) implicants) :test 'equal)))))
  minterms)

(defun is-covered-by-implicant (minterm prime-implicant)
  "Returns prime-implicant if minterm is covered in given prime-implicant, else return nil.
   Example : (is-covered-by-implicant '(0 0 1 0) '(2 2 2 2)) -> (2 2 2 2)"
  (if (every
       (lambda (term) (not (null term)))
       (mapcar (lambda (bit_minterm bit_prime_implicant)
		 (or
		  (equal bit_minterm bit_prime_implicant) ; Check if bit is same as in prime-implicant
		  (equal bit_prime_implicant 2)))         ; OR check if bit is covered in prime-implicant
	       minterm prime-implicant))
      prime-implicant
      nil))

(defun is-covered-by-set-of-implicants (minterm prime-implicants)
  "Returns list of prime-implicants covering the minterm.
   Example : (is-covered-by-set-of-implicants '(0 0 1 0) '((0 0 2 0) (2 0 2 2) (2 2 0 2))) -> ((0 0 2 0) (2 0 2 2))"
  (remove-if 'null (mapcar (lambda (prime_implicant) (is-covered-by-implicant minterm prime_implicant)) prime-implicants)))

(defun select-maximum-reduced-prime-implicants (implicant-by-minterms)
  "Given a list of prime-implicants by minterms, returns a list of implicants by minterms having maximum level of reduction.
   Example : (select-maximum-reduced-prime-implicants '(((1 0 2 2) (1 2 2 2) (2 0 2 2)) ((0 0 0 2) (0 0 2 2) (2 2 0 0))))
          -> (((1 2 2 2) (2 0 2 2)) ((0 0 2 2) (2 2 0 0)))"
  (mapcar
   (lambda (minterms max-reduction) (remove-if (lambda (minterm) (/= (count 2 minterm) max-reduction)) minterms))
   implicant-by-minterms
   (mapcar (lambda (minterms) (apply #'max (cons 0 (mapcar (lambda (minterm) (count 2 minterm)) minterms)))) implicant-by-minterms)))
   
(defun reduce-to-minimum-cover (numvar minterms)
  "numvar : number of boolean variables
   minterms : list of minterms of the form (X X X X) where Xs are 0s or 1s.
   Returns a list of prime-implicants which is the minimum cover of the given minterms
   Example : (reduce-to-minimum-cover 2 '((0 0) (0 1) (1 0))) -> ((0 2) (2 0))"
  (let* ((prime-implicants (reduce-to-prime-implicants numvar minterms))
	 (implicant-by-minterms (mapcar
				 (lambda (minterm)
				   (is-covered-by-set-of-implicants minterm prime-implicants))
				 minterms)))
    (remove-duplicates (mapcar (lambda (minterms) (car minterms)) (select-maximum-reduced-prime-implicants implicant-by-minterms)))))

(defun decimal-to-binary (numvar number)
  "numvar : number of bits
   number : decimal number to be converted
   Returns the binary representation of number in binary.
   Example : (decimal-to-binary 2 3) -> (1 1)"
  (loop
     for x from (1- numvar) downto 0
     if (>= number (expt 2 x))
     collect (progn (setf number (- number (expt 2 x))) 1)
     else
     collect 0))

(defun maxima-expression (minimum-cover list-of-variables)
  "minimum-cover : list of prime-implicants forming a minimum cover
   list-of-variables : a sorted list of variable symbols in the input
   Returns the maxima-expression corresponding to the given minimum-cover
   Example : (maxima-expression '((0 2) (2 0)) '($x $y)) -> ((MOR SIMP) ((MNOT SIMP) $X) ((MNOT SIMP) $Y))"
  (cons '(mor simp)
		(mapcar
		 (lambda (prime-implicant)
		   (let* ((implicant-maxima-expr 
				   (remove-if
					'null (mapcar
						   (lambda (bit variable)
							 (cond ((equal bit 0) `((mnot simp) ,variable))
								   ((equal bit 1) variable)
								   ((equal bit 2) nil)))
						   prime-implicant
						   list-of-variables))))
			 (if (equal (list-length implicant-maxima-expr) 1)
				 (car implicant-maxima-expr)
			   (cons '(mand simp) implicant-maxima-expr))))
		 minimum-cover)))

(defun transform-to-intermediate (expr substitution-table)
  (cond ((and (consp expr)
			  (consp (car expr))
			  (member (caar expr) '(mand mor mnot)))
		 `(,(car expr) ,@(mapcar (lambda (x) (transform-to-intermediate x substitution-table)) (cdr expr))))
		((and (consp expr)
			  (consp (car expr)))
		 (let ((sym (gensym)))
		   (setf (gethash sym substitution-table) expr)
		   sym))
		(t expr)))

(defun substitute-intermediate (expr substitution-table)
  (cond ((and (consp expr)
			  (consp (car expr)))
		 `(,(car expr) ,@(mapcar (lambda (x) (substitute-intermediate x substitution-table)) (cdr expr))))
		((atom expr) (if (nth-value 1 (gethash expr substitution-table))
					   (gethash expr substitution-table)
					   expr))))

(defun $logic_simplify (expr)
  "Requisite : needs logic.lisp for charactristic_vector function and running maxima for listofvars function.
   Given a logic expression, reduce it to it's simplest form using the method of K-Map reduction.
   Example : logic_simplify(((not a) and (not b) and c) or ((not a) and b and c) or (a and (not b) and c) or (a and b and c) or ((not a) and b and (not c))); -> ((not a) and b) or c
             logic_simplify(((not a) and b) or (a and b)) -> b"
  (let* ((substitutions (make-hash-table))
		 (intermediate (transform-to-intermediate expr substitutions))
		 (characteristic-vector (cdr ($characteristic_vector intermediate)))
		 (list-of-variables (list-of-variables intermediate))
		 (numvar (list-length list-of-variables))
		 (list-of-minterms (loop
							for bit in characteristic-vector
							for counter from 0 to (1- (expt 2 numvar))
							if bit collect (decimal-to-binary numvar counter)))
		 (minimum-cover (reduce-to-minimum-cover numvar list-of-minterms)))
    (cond ((null minimum-cover) nil)
		  ((equal (car minimum-cover) (make-list numvar :initial-element 2)) t)
		  (t (let ((converted-expression (substitute-intermediate (maxima-expression minimum-cover list-of-variables) substitutions)))
			   (if (equal (list-length converted-expression) 2)
				   (cadr converted-expression)
				 converted-expression))))))
