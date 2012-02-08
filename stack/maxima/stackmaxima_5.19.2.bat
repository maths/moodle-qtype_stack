@echo off

set arg0=%0
set arg1=%1
set arg2=%2
set arg3=%3
set arg4=%4
set arg5=%5
set arg6=%6
set arg7=%7
set arg8=%8
set arg9=%9

rem Uncomment the line below and set required value to MAXIMA_LANG_SUBDIR
rem to get localized describe in command line Maxima
rem set MAXIMA_LANG_SUBDIR=es

set lisp=gcl
set version=5.19.2
set prefix=C:/maxima
set maxima_prefix=C:\PROGRA~1\MAXIMA~1.1
set package=maxima
set verbose=false
set mingw_gccver=3.3.1
set path=%maxima_prefix%\bin;%maxima_prefix%\lib\gcc-lib\mingw32\3.3.1;%path%

if "%USERPROFILE%" == "" goto win9x
if "%MAXIMA_USERDIR%" == "" set MAXIMA_USERDIR=%USERPROFILE%\maxima
if "%MAXIMA_TEMPDIR%" == "" set MAXIMA_TEMPDIR=%USERPROFILE%
goto startparseargs
:win9x
if "%MAXIMA_USERDIR%" == "" set MAXIMA_USERDIR=%maxima_prefix%\user
if "%MAXIMA_TEMPDIR%" == "" set MAXIMA_TEMPDIR=%maxima_prefix%

:startparseargs
if x%1 == x-l goto foundlisp
if x%1 == x--lisp goto foundlisp
if x%1 == x-u goto foundversion
if x%1 == x--use-version goto foundversion
if x%1 == x-v goto foundverbose
if x%1 == x--verbose goto foundverbose

:continueparseargs
shift
if not x%1 == x goto startparseargs
goto endparseargs

:foundlisp
set lisp=%2
shift
goto continueparseargs

:foundversion
set version=%2
shift
goto continueparseargs

:foundverbose
set verbose=true
goto continueparseargs

:endparseargs

if "%MAXIMA_LAYOUT_AUTOTOOLS%" == "" goto defaultlayout
set layout_autotools=true
goto endlayout

:defaultlayout
set layout_autotools=true

:endlayout

if "%MAXIMA_PREFIX%" == "" goto defaultvars
if "%layout_autotools%" == "true" goto maxim_autotools
set maxima_imagesdir=%MAXIMA_PREFIX%\src
goto endsetupvars

:maxim_autotools
set maxima_imagesdir=%MAXIMA_PREFIX%\lib\%package%\%version%
goto endsetupvars

:defaultvars
if "%layout_autotools%" == "true" goto defmaxim_autotools
set maxima_imagesdir=%prefix%\src
goto endsetupvars

:defmaxim_autotools
set maxima_imagesdir=%prefix%\lib\%package%\%version%
goto endsetupvars

:endsetupvars

set maxima_image_base=%maxima_imagesdir%\binary-%lisp%\maxima

if "%verbose%" == "true" @echo on
if "%lisp%" == "gcl" goto dogcl
if "%lisp%" == "clisp" goto doclisp
if "%lisp%" == "ecl" goto doecl

@echo Maxima error: lisp %lisp% not known.
goto end

:dogcl
%maxima_imagesdir%\binary-gcl\maxima.exe -eval "(cl-user::run)" -f -- %arg1% %arg2% %arg3% %arg4% %arg5% %arg6% %arg7% %arg8% %arg9%
goto end

:doclisp
if "%layout_autotools%" == "true" goto clisp_autotools
clisp -q -M %maxima_image_base%.mem "" -- %arg1% %arg2% %arg3% %arg4% %arg5% %arg6% %arg7% %arg8% %arg9%
goto end

:clisp_autotools
%maxima_imagesdir%\binary-clisp\lisp.exe -q -M %maxima_image_base%.mem "" -- %arg1% %arg2% %arg3% %arg4% %arg5% %arg6% %arg7% %arg8% %arg9%
goto end

:doecl
ecl -load %maxima_image_base%.fas -eval "(user::run)" -- "%arg1%" "%arg2%" "%arg3%" "%arg4%" "%arg5%" "%arg6%" "%arg7%" "%arg8%" "%arg9%"
goto end

:end


