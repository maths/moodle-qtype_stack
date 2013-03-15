(mapc #'tex-setup
    '(
	(%acos "\\cos^{-1}")
	(%asin "\\sin^{-1}")
	(%atan "\\tan^{-1}")
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
	(%inf "\\inf ")		   ; many will prefer "\\infty". Hmmm.
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
    (%asec "{\\rm sec}^{-1}")
    (%acsc "{\\rm csc}^{-1} ")
    (%acot "{\\rm cot}^{-1}")
    (%sech "{\\rm sech}")
    (%csch "{\\rm csch}")
    (%asinh "{\\rm sinh}^{-1}")
    (%acosh "{\\rm cosh}^{-1}")
    (%atanh "{\\rm tanh}^{-1}")
    (%asech "{\\rm sech}^{-1}")
    (%acsch "{\\rm csch}^{-1}")
    (%acoth "{\\rm coth}^{-1}")
)) ;; etc

