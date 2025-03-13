# The Greek Alphabet

Greek letters are transliterated using their English names.  I.e.

    [alpha,beta,gamma,delta,epsilon,zeta,eta,theta,iota,kappa,lambda,mu,nu,xi,omicron,pi,rho,sigma,tau,upsilon,phi,chi,psi,omega]
    
Upper case Greek letters have an upper-case English first letter.  I.e.

    [Alpha,Beta,Gamma,Delta,Epsilon,Zeta,Eta,Theta,Iota,Kappa,Lambda,Mu,Nu,Xi,Omicron,Pi,Rho,Sigma,Tau,Upsilon,Phi,Chi,Psi,Omega]
    
Many of the Greek letters already have a meaning in Maxima.

* `beta`:  The beta function is defined as \(\gamma(a) \gamma(b)/\gamma(a+b)\).
* `gamma`:  The gamma function.
* `delta`: This is the Dirac Delta function (only defined in Laplace).
* `zeta`: This is the Riemann zeta function.
* `lambda`: Defines and returns a lambda expression, i.e. an unnamed function.
* `psi`: The derivative of 'log (gamma (<x>))' of order '<n>+1', which has a strange syntax `psi[n](x)`.  It is also defined in the tensor package.

Note that by default, `psi` requires arguments and any attempt to use this variable name without arguments will result in an error.  For this reason we delete this function in STACK, and `psi` becomes an unnamed variable.


The following are given a specific value by STACK.

* `pi` is defined to be the numeric constant which is the ratio of the diameter to the circumference of a circle.  In Maxima this is normally `%pi`, but STACK also defines the letter `pi` to have this value.
* In Maxima the numeric constant which represents the so-called golden mean, \((1 + \sqrt{5})/2\) is `%phi`.

## "Undefine" Maxima defaults

It is currently not possible to "undefine" function names and return them to variables.


