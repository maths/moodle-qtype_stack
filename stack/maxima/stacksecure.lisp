;; Disable Maxima's system(), eval_string_lisp() and cl_eval() family inside STACK.
;; Load this after every other library, so nothing can restore the originals.
;;
;; The PHP security map already forbids all of these in author code.  This file is
;; defence in depth: it removes the functions from the image itself.

(in-package :maxima)

;; Keep the real system() under a Lisp-only name for STACK's own plot().
;; It has no $ prefix, so it is not a Maxima function, and author code cannot
;; reach it because STACK's parser rejects ?-identifiers.
;;
;; defmfun makes $SYSTEM a wrapper that calls SYSTEM-IMPL by name, and the
;; defmfun below redefines SYSTEM-IMPL.  So save the implementation function
;; object (named by the IMPL-NAME property), not the $SYSTEM wrapper.
(unless (fboundp 'stack-system-original)
  (setf (symbol-function 'stack-system-original)
        (symbol-function (or (get '$system 'impl-name) '$system))))

(defmfun $system (&rest args)
  (declare (ignore args))
  (merror (intl:gettext "system: disabled in STACK.")))

(defmfun $eval_string_lisp (string)
  (declare (ignore string))
  (merror (intl:gettext "eval_string_lisp: disabled in STACK.")))

;; cl_eval, common_lisp and to_cl (share/contrib/tocl.lisp) are autoloaded on
;; first call.  Remove the autoload property, so the package is never loaded,
;; and define each one to raise an error.
(dolist (f '($cl_eval $common_lisp $to_cl))
  (remprop f 'autoload))

(defmfun $cl_eval (e)
  (declare (ignore e))
  (merror (intl:gettext "cl_eval: disabled in STACK.")))

(defmfun $common_lisp (e)
  (declare (ignore e))
  (merror (intl:gettext "common_lisp: disabled in STACK.")))

(defmfun $to_cl (e)
  (declare (ignore e))
  (merror (intl:gettext "to_cl: disabled in STACK.")))

;; oid_to_octets (share/stringproc/cryptools.lisp) applies the Lisp reader to
;; parts of its argument.  It is autoloaded with the rest of stringproc, and
;; loading stringproc later would restore the original.  So load stringproc
;; now if it is still pending, then redefine the function.
(when (get '$oid_to_octets 'autoload)
  ($load "stringproc"))
(remprop '$oid_to_octets 'autoload)

(defmfun $oid_to_octets (str)
  (declare (ignore str))
  (merror (intl:gettext "oid_to_octets: disabled in STACK.")))

;; Delete one temporary gnuplot file, replacing system(concat(DEL_CMD, ...)).
;; Only a .plt file inside maxima_tempdir is deleted; anything else is ignored.
;; probe-file canonicalises both paths, resolving ".." and symlinks.
;; maxima_tempdir must end in a directory separator, as plot() assumes.
(defun stack-delete-plot-file (file)
  (let ((path (probe-file file))
        (dir  (probe-file $maxima_tempdir)))
    (when (and path dir
               (equal (pathname-type path) "plt")
               (eql 0 (search (namestring dir) (namestring path))))
      (delete-file path))
    '$done))
