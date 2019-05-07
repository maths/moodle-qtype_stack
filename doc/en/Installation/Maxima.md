# Compiling Maxima from source.

You are strongly advised to read the installation instructions for the various components you need!

As of 21st Dec 2015 the following has been used to compile Maxima from source.

### You will need the following, and GNU autotools 

    sudo apt-get install texinfo

### Download and compile SBCL (Lisp)

    cd /home/sangwinc/src
    wget http://downloads.sourceforge.net/project/sbcl/sbcl/1.3.1/sbcl-1.3.1-source.tar.bz2
    tar -xf sbcl-1.3.1-source.tar.bz2
    cd sbcl-1.3.1/
    ./make-config.sh
    ./make.sh

    sudo ./install.sh

### Download and compile Maxima

    cd /home/sangwinc/src
    wget http://kent.dl.sourceforge.net/project/maxima/Maxima-source/5.36.1-source/maxima-5.36.1.tar.gz
    tar -zxf maxima-5.36.1.tar.gz
    cd maxima-5.36.1/

    ./configure  --with-sbcl

    make
    sudo make install


