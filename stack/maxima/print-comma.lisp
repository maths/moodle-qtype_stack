(in-package :cl-user)

(setq stackdecimalsep #\,)

(defun inject-comma (string comma-char comma-interval)
  (let* ((len (length string))
         (offset (mod len comma-interval)))
    (with-output-to-string (out)
               (write-string string out :start 0 :end offset)
               (do ((i offset (+ i comma-interval)))
                   ((>= i len))
                 (unless (zerop i)
                   (write-char comma-char out))
                 (write-string string out :start i :end (+ i comma-interval))))))


(defun print-float (stream arg colonp atp
               &optional
               (point-char #\.)
               (comma-char #\,)
               (comma-interval 3))
  "A function for printing floating point numbers, with an interface
suitable for use with the tilde-slash FORMAT directive.  The full form
is

    ~point-char,comma-char,comma-interval/print-float/

The point-char is used in place of the decimal point, and defaults to
#\\.  If : is specified, then the whole part of the number will be
grouped in the same manner as ~D, using COMMA-CHAR and COMMA-INTERVAL.
If @ is specified, then the sign is always printed."
  (let* ((sign (if (minusp arg) "-" (if (and atp (plusp arg)) "+" "")))
         (output (format nil "~F" arg))
         (point (position #\. output :test 'char=))
         (whole (subseq output (if (minusp arg) 1 0) point))
         (fractional (subseq output (1+ point))))
    (when colonp
      (setf whole (inject-comma whole comma-char comma-interval)))
    (format stream "~A~A~C~A"
            sign whole point-char fractional)))

;; Basic usage examples.
;; colonp decides if we group digits or not.
;; atp controls if we print an initial + sign
;; The next arguments are point-char, comma-char and comma interval.
;; printf_float(false, %pi*10^6, true, false, ",", " ", 3);
;; printf_float(false, -%pi*10^6, true, false, ",", ".", 3);

(defun maxima::$printf_float (stream arg &optional
         (colonp t) (atp t)
                     (point-char #\.)
                     (comma-char #\,)
                     (comma-interval 3))
  (flet ((coerce-to-char (s)
             (cond ((characterp s) s)
                   ((and (stringp s) (equal s ""))
                (code-char 0))
                   ((stringp s)
                (car (coerce s 'list)))
                   ((symbolp s)
                (cadr (coerce (format nil "~a" s) 'list)))
                   (t
                ;; fix me
                (error "Input needs to be a character or string, found ~a." s)))))
    (let ((point-char (coerce-to-char point-char))
      (comma-char (coerce-to-char comma-char))
      (arg (maxima::$float arg)))
      (print-float stream arg colonp atp point-char comma-char comma-interval))))
