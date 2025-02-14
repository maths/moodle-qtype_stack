# Compiling Maxima from source.

As of 12th April 2024 the following has been used to compile Maxima from source on Linux Ubuntu 22.04.3.

If you compile Maxima from source you _must_ include unicode support. This is essential even if you only use Maxima in English.  Students' answers, and teacher's content, increasingly uses unicode which inevitably passes through Maxima.

### You will need the following, and GNU autotools 

    sudo apt-get install texinfo

### Download and compile SBCL (Lisp)

    wget https://sourceforge.net/projects/sbcl/files/sbcl/2.3.2/sbcl-2.3.2-source.tar.bz2
    tar -xf sbcl-2.3.2-source.tar.bz2
    cd sbcl-2.3.2/
    ./make-config.sh
    ./make.sh

    sudo ./install.sh

### Download and compile Maxima with SBCL

    wget https://sourceforge.net/projects/maxima/files/Maxima-source/5.47.0-source/maxima-5.47.0.tar.gz 
    tar -xzf maxima-5.47.0.tar.gz
    cd maxima-5.47.0/

    ./configure --enable-sbcl

    make
    sudo make install

By default the above will install Maxima to `/usr/local/bin/maxima`. You can use the `--prefix` flag in the call to `./configure` to
change this. For example, `./configure --prefix=/usr/bin --enable-sbcl`, followed by `make` and `sudo make install` will install
Maxima to `/usr/bin/maxima`. Optionally, you can use the `make check` command after `make` and before `sudo make install` to 
check for any issues found from the configuration and build procedures.


