(mapc #'tex-setup
  '(
     (%acos "\\arccos ") ; CJS, changed!
     (%asin "\\arcsin ") ; CJS, changed!
     (%atan "\\arctan ") ; CJS, changed!
     ; Latex's arg(x) is ... ?
     (%cos "\\cos ")
     (%cosh "\\cosh ")
     (%cot "\\cot ")
     (%coth "\\coth ")
     (%csc "\\csc ")
     ; Latex's "deg" is ... ?
     (%determinant "\\det ")
     (%dim "\\dim ")
     (%exp "\\exp ")
     (%gcd "\\gcd ")
     ; Latex's "hom" is ... ?
     (%inf "\\infty ") ; many will prefer "\\infty". Hmmm.
     ; Latex's "ker" is ... ?
     ; Latex's "lg" is ... ?
     ; lim is handled by tex-limit.
     ; Latex's "liminf" ... ?
     ; Latex's "limsup" ... ?
     (%ln "\\ln ")
     (%log "\\ln ") ; CJS, changed!
     (%max "\\max ")
     (%min "\\min ")
     ; Latex's "Pr" ... ?
     (%sec "\\sec ")
     (%sin "\\sin ")
     (%sinh "\\sinh ")
     ; Latex's "sup" ... ?
     (%tan "\\tan ")
     (%tanh "\\tanh ")
    ;; (%erf "{\\rm erf}") this would tend to set erf(x) as erf x. Unusual
     ;(%laplace "{\\cal L}")
     )) ;; etc
