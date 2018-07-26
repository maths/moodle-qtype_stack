# Notación usada en trabajo actuarial

Hay varias notaciones particulares al trabajo actuarial. Por ejemplo, 

\[\require{enclose} EPV = a _{[25]+5:\enclose{actuarial}{30}} ^ {\space 1},\]

lo cual se compila usando `\[\require{enclose} EPV = a _{[25]+5:\enclose{actuarial}{30}} ^ {\space 1}\]`. Abajo se dan más ejemplos.

## Contingencias Vitales' Símbolos ##

Los ejemplos siguientes están adaptados de [http://maths.dur.ac.uk/stats/courses/AMII/LifeConSymbolsGuide.pdf](http://maths.dur.ac.uk/stats/courses/AMII/LifeConSymbolsGuide.pdf)

| Inline                                                                  | Código (sin delimitadores LaTeX)                                      | 
| ----------------------------------------------------------------------- | ----------------------------------------------------------------------- | 
| \( a \)                                                                 |  `a`                                                                    | 
| \( \require{enclose} a_{\enclose{actuarial}{K}} \)                      |  `\require{enclose} a_{\enclose{actuarial}{K}}`                         | 
| \( \require{enclose} \bar{a}_{\enclose{actuarial}{T}} \)                |  `\require{enclose} \bar{a}_{\enclose{actuarial}{T}}`                   | 
| \( \require{enclose} \ddot{a}_x \)                                      |  `\require{enclose} \ddot{a}_x`                                         | 
| \( \require{enclose} \bar{a}_{h}^{r} \)                                 |  `\require{enclose} \bar{a}_{h}^{r}`                                    | 
| \( \require{enclose} \ddot{a}_{x}^{\{m\}} \)                            |  `\require{enclose} \ddot{a}_{x}^{\{m\}}`                               | 
| \( \require{enclose} a_{x:\enclose{actuarial}{n}} \)                    |  `\require{enclose} a_{x:\enclose{actuarial}{n}}`                       | 
| \( \require{enclose} \bar{a}_{x:\enclose{actuarial}{n}} \)              |  `\require{enclose} \bar{a}_{x:\enclose{actuarial}{n}}`                 | 
| \( \require{enclose} \ddot{a}_{x:\enclose{actuarial}{n}} \)             |  `\require{enclose} \ddot{a}_{x:\enclose{actuarial}{n}}`                | 
| \( \require{enclose} \ddot{a}_{x:\enclose{actuarial}{n}}^{(m)} \)       |  `\require{enclose} \ddot{a}_{x:\enclose{actuarial}{n}}^{(m)}`          | 
| \( \require{enclose} \mathring{a}_{x:\enclose{actuarial}{n}}^{(m)} \)   |  `\require{enclose} \mathring{a}_{x:\enclose{actuarial}{n}}^{(m)}`      | 
| \( \require{enclose} \ddot{a}_{x:\enclose{actuarial}{n}}^{\{m\}} \)     |  `\require{enclose} \ddot{a}_{x:\enclose{actuarial}{n}}^{\{m\}}`        | 
| \( \require{enclose} \bar{a}_{\overline{x:\enclose{actuarial}{n}}} \)   |  `\require{enclose} \bar{a}_{\overline{x:\enclose{actuarial}{n}}}`      | 
| \( \require{enclose} {}^{2}\bar{a}_{x:\enclose{actuarial}{n}} \)        |  `\require{enclose} {}^{2}\bar{a}_{x:\enclose{actuarial}{n}}`           | 
| \( \require{enclose} {}^{2}\ddot{a}_{xy:\enclose{actuarial}{n}} \)      |  `\require{enclose} {}^{2}\ddot{a}_{xy:\enclose{actuarial}{n}}`         | 

STACK proporciona un poco de soporte para funciones estadísticas vía paquetes opcionales de maxima.  Vea las páginas específicas en [estadísticas](../CAS/Statistics.md).
