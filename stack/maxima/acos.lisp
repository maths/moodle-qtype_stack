(mapc #'tex-setup
      '(
    (%acos "{\\rm acos}")
    (%asin "{\\rm asin}")
    (%atan "{\\rm atan}")

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
    (%inf "\\inf ")
    ; many will prefer "\\infty".
    ; Latex's "ker" is ... ?
    ; Latex's "lg" is ... ?
    ; lim is handled by tex-limit.
    ; Latex's "liminf" ... ?
    ; Latex's "limsup" ... ?
    (%ln "\\ln ")
    (%log "\\ln ")
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

    ; Maxima built-in functions which do not have corresponding TeX symbols.

    (%asec "{\\rm asec}")
    (%acsc "{\\rm acsc}")
    (%acot "{\\rm acot}")

    (%sech "{\\rm sech}")
    (%csch "{\\rm csch}")

    (%asinh "{\\rm asinh}")
    (%acosh "{\\rm acosh}")
    (%atanh "{\\rm atanh}")

    (%asech "{\\rm asech}")
    (%acsch "{\\rm acsch}")
    (%acoth "{\\rm acoth}")

)) ;; etc
