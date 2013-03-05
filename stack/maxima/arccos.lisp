(mapc #'tex-setup
      '(
	(%acos "\\arccos ")
	(%asin "\\arcsin ")
	(%atan "\\arctan ")
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
    (%asec "{\\rm arcsec}")
    (%acsc "{\\rm arccsc}")
    (%acot "{\\rm arccot}")
    (%sech "{\\rm sech}")
    (%csch "{\\rm csch}")
    (%asinh "{\\rm arcsinh}")
    (%acosh "{\\rm arccosh}")
    (%atanh "{\\rm arctanh}")
    (%asech "{\\rm arcsech}")
    (%acsch "{\\rm arccsch}")
    (%acoth "{\\rm arccoth}")
)) ;; etc

