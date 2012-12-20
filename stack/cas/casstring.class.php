<?php
// This file is part of Stack - http://stack.bham.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

/**
 * CAS strings and related functions.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../locallib.php');
require_once(dirname(__FILE__) . '/../utils.class.php');

class stack_cas_casstring {

    /** @var string as typed in by the user. */
    private $rawcasstring;

    /** @var string as modified by the validation. */
    private $casstring;

    /** @var bool if the string has passed validation. */
    private $valid;

    /** @var bool */
    private $key;

    /** @var string any error messages to display to the user. */
    private $errors;

    /**
     * @var string the value of the CAS string, in Maxima syntax. Only gets set
     *             after the casstring has been processed by the CAS.
     */
    private $value;

    /**
     * @var string how to display the CAS string, e.g. LaTeX. Only gets set
     *             after the casstring has been processed by the CAS.
     */
    private $display;

    /**
     * @var string how to display the CAS string, e.g. LaTeX. Only gets set
     *              after the casstring has been processed by the CAS, and the
     *              CAS function is an answertest.
     */
    private $answernote;

    /**
     * @var string how to display the CAS string, e.g. LaTeX. Only gets set
     *              after the casstring has been processed by the CAS, and the
     *              CAS function is an answertest.
     */
    private $feedback;

    /** @var array blacklist of globally forbidden CAS keywords. */
    private static $globalforbid    = array('%TH', 'ADAPTH_DEPTH', 'ALIAS', 'ALIASES',
            'ALPHABETIC', 'APPENDFILE', 'APROPOS', 'ASSUME_EXTERNAL_BYTE_ORDER', 'BACKTRACE',
            'BATCH', 'BATCHLOAD', 'BOX', 'BOXCHAR', 'BREAK', 'BUG_REPORT', 'BUILD_INFO',
            'CATCH', 'CLOSE', 'CLOSEFILE', 'COMPFILE', 'COMPILE', 'COMPILE_FILE',
            'CURRENT_LET_RULE_PACKAGE', 'DATA_FILE_NAME', 'DEACTIVATE', 'DEBUGMODE',
            'DEFINE', 'DEFINE_VARIABLE', 'DEMO', 'DEPENDENCIES', 'DESCRIBE', 'DIMACS_EXPORT',
            'DIMACS_IMPORT', 'ENTERMATRIX', 'ERRCATCH', 'ERROR', 'ERROR_SIZE', 'ERROR_SYMS', 'ERRORMSG',
            'EVAL_STRING', 'EXAMPLE', 'FEATURE', 'FEATUREP', 'FEATURES', 'FILE_NAME',
            'FILE_OUTPUT_APPEND', 'FILE_SEARCH', 'FILE_SEARCH_DEMO', 'FILE_SEARCH_LISP',
            'FILE_SEARCH_MAXIMA', 'FILE_TYPE', 'FILENAME_MERGE', 'FLENGTH', 'FORTINDENT',
            'FORTRAN', 'FORTSPACES', 'FPOSITION', 'FRESHLINE', 'FUNCTIONS', 'FUNDEF',
            'FUNMAKE', 'GNUPLOT_FILE_NAME', 'GNUPLOT_OUT_FILE', 'GNUPLOT_PREAMBLE',
            'GNUPLOT_PS_TERM_COMMAND', 'GNUPLOT_TERM', 'INCHAR', 'INFEVAL', 'INFOLISTS',
            'KILL', 'KILLCONTEXT', 'LABELS', 'LDISP', 'LDISPLAY', 'LINECHAR', 'LINEL',
            'LINENUM', 'LINSOLVEWARN', 'LMXCHAR', 'LOAD', 'LOADFILE', 'LOADPRINT',
            'MACROEXPAND', 'MACROEXPAND1', 'MACROEXPANSION', 'MACROS', 'MANUAL_DEMO',
            'MAXIMA_TEMPDIR', 'MAXIMA_USERDIR', 'MULTIPLOT_MODE', 'MYOPTIONS', 'NEWLINE',
            'NOLABELS', 'OPENA', 'OPENA_BINARY', 'OPENR', 'OPENR_BINARY', 'OPENW',
            'OPENW_BINARY', 'OUTCHAR', 'PACKAGEFILE', 'PARSE_STRING', 'PICKAPART', 'PIECE',
            'PLAYBACK', 'PLOTDF', 'PRINT_GRAPH', 'PRINTF', 'PRINTFILE', 'PROMPT', 'PSFILE',
            'QUIT', 'READ', 'READ_ARRAY', 'READ_BINARY_ARRAY', 'READ_BINARY_LIST',
            'READ_BINARY_MATRIX', 'READ_HASHED_ARRAY', 'READ_LIST', 'READ_MATRIX',
            'READ_NESTED_LIST', 'READ_XPM', 'READLINE', 'READONLY', 'REFCHECK', 'REMBOX',
            'REMFUNCTION', 'RESET', 'RMXCHAR', 'ROOM', 'RUN_TESTSUITE', 'RUN_VIEWER', 'SAVE',
            'SAVEDEF', 'SET_PLOT_OPTION', 'SETCHECK', 'SETCHECKBREAK', 'SETVAL', 'SHOWTIME',
            'SPARSE6_EXPORT', 'SPARSE6_IMPORT', 'SPLICE', 'SPRINT', 'STATUS', 'STRINGOUT',
            'SUPCONTEXT', 'SYSTEM', 'TCL_OUTPUT', 'TERMINAL', 'TESTSUITE_FILES', 'THROW',
            'TIME', 'TIMER', 'TIMER_DEVALUE', 'TIMER_INFO', 'TO_LISP', 'TRACE', 'TRACE_OPTIONS',
            'TRANSCOMPILE', 'TRANSLATE', 'TRANSLATE_FILE', 'TRANSRUN', 'TTYOFF', 'UNTIMER',
            'UNTRACE', 'USER_PREAMBLE', 'VALUES', 'WITH_STDOUT', 'WRITE_BINARY_DATA',
            'WRITE_DATA', 'WRITEFILE');

    /** @var array blacklist of CAS keywords forbidden to teachers. */
    // note we allow RANDOM_PERMUTATION.
    private static $teachernotallow = array('%UNITEXPAND', 'ABASEP', 'ABSBOXCHAR', 'ACTIVATE',
            'ACTIVECONTEXTS', 'ADDITIVE', 'ADIM', 'AF', 'AFORM', 'AGD', 'ALG_TYPE',
            'ALL_DOTSIMP_DENOMS', 'ALLSYM', 'ANTID', 'ANTIDIFF', 'ANTIDIFFERENCE', 'ANTISYMMETRIC',
            'ARITHMETIC', 'ARITHSUM', 'ARRAY', 'ARRAYAPPLY', 'ARRAYINFO', 'ARRAYMAKE', 'ARRAYS',
            'ASSOC_LEGENDRE_P', 'ASSOC_LEGENDRE_Q', 'ASYMBOL', 'ATENSIMP', 'ATOMGRAD', 'ATRIG1',
            'ATVALUE', 'AUGMENTED_LAGRANGIAN_METHOD', 'AV', 'AXES', 'AXIS_3D', 'AXIS_BOTTOM',
            'AXIS_LEFT', 'AXIS_RIGHT', 'AXIS_TOP', 'AZIMUT', 'BACKSUBST', 'BARS', 'BARSPLOT',
            'BASHINDICES', 'BDVAC', 'BERLEFACT', 'BFPSI', 'BFPSI0', 'BIMETRIC', 'BODE_GAIN',
            'BODE_PHASE', 'BORDER', 'BOUNDARIES_ARRAY', 'BOXPLOT', 'CANFORM', 'CANTEN', 'CBFFAC',
            'CBRANGE', 'CBTICS', 'CDF_BERNOULLI', 'CDF_BETA', 'CDF_BINOMIAL', 'CDF_CAUCHY',
            'CDF_CHI2', 'CDF_CONTINUOUS_UNIFORM', 'CDF_DISCRETE_UNIFORM', 'CDF_EXP', 'CDF_F',
            'CDF_GAMMA', 'CDF_GEOMETRIC', 'CDF_GUMBEL', 'CDF_HYPERGEOMETRIC', 'CDF_LAPLACE',
            'CDF_LOGISTIC', 'CDF_LOGNORMAL', 'CDF_NEGATIVE_BINOMIAL', 'CDF_NONCENTRAL_CHI2',
            'CDF_NONCENTRAL_STUDENT_T', 'CDF_NORMAL', 'CDF_PARETO', 'CDF_POISSON', 'CDF_RANK_SUM',
            'CDF_RAYLEIGH', 'CDF_SIGNED_RANK', 'CDF_STUDENT_T', 'CDF_WEIBULL', 'CDISPLAY',
            'CENTRAL_MOMENT', 'CFRAME_FLAG', 'CGEODESIC', 'CHANGENAME', 'CHAOSGAME', 'CHEBYSHEV_T',
            'CHEBYSHEV_U', 'CHECK_OVERLAPS', 'CHECKDIV', 'CHRISTOF', 'CLEAR_RULES', 'CMETRIC',
            'CNONMET_FLAG', 'COGRAD', 'COLLAPSE', 'COLOR', 'COLORBOX', 'COLUMNS', 'COMBINATION',
            'COMP2PUI', 'COMPONENTS', 'CONCAN', 'CONMETDERIV', 'CONSTVALUE', 'CONT2PART', 'CONTEXT',
            'CONTEXTS', 'CONTINUOUS_FREQ', 'CONTORTION', 'CONTOUR', 'CONTOUR_LEVELS', 'CONTOUR_PLOT',
            'CONTRACT_EDGE', 'CONTRAGRAD', 'CONTRIB_ODE', 'CONVERT', 'COORD', 'COPY_GRAPH', 'COR',
            'COV', 'COV1', 'COVDIFF', 'COVERS', 'CREATE_LIST', 'CSETUP', 'CT_COORDS', 'CT_COORDSYS',
            'CTAYLOR', 'CTAYPOV', 'CTAYPT', 'CTAYSWITCH', 'CTAYVAR', 'CTORSION_FLAG', 'CTRANSFORM',
            'CTRGSIMP', 'CUNLISP', 'CV', 'DECLARE_CONSTVALUE', 'DECLARE_DIMENSIONS',
            'DECLARE_FUNDAMENTAL_DIMENSIONS', 'DECLARE_FUNDAMENTAL_UNITS', 'DECLARE_QTY',
            'DECLARE_TRANSLATED', 'DECLARE_UNIT_CONVERSION', 'DECLARE_UNITS', 'DECLARE_WEIGHTS',
            'DECSYM', 'DEFAULT_LET_RULE_PACKAGE', 'DEFCON', 'DEFMATCH', 'DEFRULE', 'DELAY', 'DELETEN',
            'DIAG', 'DIAGMATRIXP', 'DIAGMETRIC', 'DIM', 'DIMENSION', 'DIMENSIONLESS', 'DIMENSIONS',
            'DIMENSIONS_AS_LIST', 'DIRECT', 'DISCRETE_FREQ', 'DISP', 'DISPCON', 'DISPFLAG',
            'DISPFORM', 'DISPFUN', 'DISPJORDAN', 'DISPLAY', 'DISPLAY2D', 'DISPLAY_FORMAT_INTERNAL',
            'DISPRULE', 'DISPTERMS', 'DISTRIB', 'DOMXEXPT', 'DOMXMXOPS', 'DOMXNCTIMES', 'DOTSIMP',
            'DRAW', 'DRAW2D', 'DRAW3D', 'DRAW_FILE', 'DRAW_GRAPH', 'DRAW_GRAPH_PROGRAM', 'DSCALAR',
            'EINSTEIN', 'ELAPSED_REAL_TIME', 'ELAPSED_RUN_TIME', 'ELE2COMP', 'ELE2POLYNOME',
            'ELE2PUI', 'ELEM', 'ELEVATION', 'ELLIPSE', 'ENHANCED3D', 'ENTERMATRIX', 'ENTERTENSOR',
            'ENTIER', 'EPS_HEIGHT', 'EPS_WIDTH', 'EV_POINT', 'EVFLAG', 'EVFUN', 'EVOLUTION',
            'EVOLUTION2D', 'EVUNDIFF', 'EXPLICIT', 'EXPLOSE', 'EXPON', 'EXPOP', 'EXPT', 'EXSEC',
            'EXTDIFF', 'EXTRACT_LINEAR_EQUATIONS', 'F90', 'FACTS', 'FAST_CENTRAL_ELEMENTS',
            'FAST_LINSOLVE', 'FB', 'FILE_BGCOLOR', 'FILL_COLOR', 'FILL_DENSITY', 'FILLARRAY',
            'FILLED_FUNC', 'FINDDE', 'FIX', 'FLIPFLAG', 'FLUSH', 'FLUSH1DERIV', 'FLUSHD', 'FLUSHND',
            'FONT', 'FONT_SIZE', 'FORGET', 'FRAME_BRACKET', 'FUNDAMENTAL_DIMENSIONS',
            'FUNDAMENTAL_UNITS', 'GAUSSPROB', 'GCDIVIDE', 'GCFAC', 'GD', 'GDET', 'GEN_LAGUERRE',
            'GENSUMNUM', 'GEOMAP', 'GEOMETRIC', 'GEOMETRIC_MEAN', 'GEOSUM', 'GET', 'GET_PIXEL',
            'GET_PLOT_OPTION', 'GET_TEX_ENVIRONMENT', 'GET_TEX_ENVIRONMENT_DEFAULT', 'GGF',
            'GGFCFMAX', 'GGFINFINITY', 'GLOBAL_VARIANCES', 'GLOBALSOLVE', 'GNUPLOT_CLOSE',
            'GNUPLOT_CURVE_STYLES', 'GNUPLOT_CURVE_TITLES', 'GNUPLOT_DEFAULT_TERM_COMMAND',
            'GNUPLOT_DUMB_TERM_COMMAND', 'GNUPLOT_PM3D', 'GNUPLOT_REPLOT', 'GNUPLOT_RESET',
            'GNUPLOT_RESTART', 'GNUPLOT_START', 'GOSPER', 'GOSPER_IN_ZEILBERGER', 'GOSPERSUM',
            'GR2D', 'GR3D', 'GRADEF', 'GRADEFS', 'GRAPH6_DECODE', 'GRAPH6_ENCODE', 'GRAPH6_EXPORT',
            'GRAPH6_IMPORT', 'GRID', 'GROBNER_BASIS', 'HARMONIC', 'HARMONIC_MEAN', 'HAV',
            'HEAD_ANGLE', 'HEAD_BOTH', 'HEAD_LENGTH', 'HEAD_TYPE', 'HERMITE', 'HISTOGRAM', 'HODGE',
            'IC_CONVERT', 'ICC1', 'ICC2', 'ICHR1', 'ICHR2', 'ICOUNTER', 'ICURVATURE', 'IDIFF',
            'IDIM', 'IDUMMY', 'IDUMMYX', 'IEQN', 'IEQNPRINT', 'IFB', 'IFC1', 'IFC2', 'IFG', 'IFGI',
            'IFR', 'IFRAME_BRACKET_FORM', 'IFRAMES', 'IFRI', 'IFS', 'IGEODESIC_COORDS',
            'IGEOWEDGE_FLAG', 'IKT1', 'IKT2', 'IMAGE', 'IMETRIC', 'IMPLICIT', 'IMPLICIT_DERIVATIVE',
            'INDEXED_TENSOR', 'INDICES', 'INFERENCE_RESULT', 'INFERENCEP', 'INFIX', 'INIT_ATENSOR',
            'INIT_CTENSOR', 'INM', 'INMC1', 'INMC2', 'INPROD', 'INTERVALP', 'INTOPOIS', 'INVARIANT1',
            'INVARIANT2', 'INVERT_BY_LU', 'IP_GRID', 'IP_GRID_IN', 'ISHOW', 'ISOLATE',
            'ISOLATE_WRT_TIMES', 'ITEMS_INFERENCE', 'ITR', 'JACOBI_P', 'JF', 'JORDAN', 'JULIA',
            'KDELS', 'KDELTA', 'KEY', 'KINVARIANT', 'KOSTKA', 'KT', 'KURTOSIS', 'KURTOSIS_BERNOULLI',
            'KURTOSIS_BETA', 'KURTOSIS_BINOMIAL', 'KURTOSIS_CHI2', 'KURTOSIS_CONTINUOUS_UNIFORM',
            'KURTOSIS_DISCRETE_UNIFORM', 'KURTOSIS_EXP', 'KURTOSIS_F', 'KURTOSIS_GAMMA',
            'KURTOSIS_GEOMETRIC', 'KURTOSIS_GUMBEL', 'KURTOSIS_HYPERGEOMETRIC', 'KURTOSIS_LAPLACE',
            'KURTOSIS_LOGISTIC', 'KURTOSIS_LOGNORMAL', 'KURTOSIS_NEGATIVE_BINOMIAL',
            'KURTOSIS_NONCENTRAL_CHI2', 'KURTOSIS_NONCENTRAL_STUDENT_T', 'KURTOSIS_NORMAL',
            'KURTOSIS_PARETO', 'KURTOSIS_POISSON', 'KURTOSIS_RAYLEIGH', 'KURTOSIS_STUDENT_T',
            'KURTOSIS_WEIBULL', 'LABEL', 'LABEL_ALIGNMENT', 'LABEL_ORIENTATION', 'LAGUERRE',
            'LASSOCIATIVE', 'LBFGS', 'LBFGS_NCORRECTIONS', 'LBFGS_NFEVAL_MAX', 'LC2KDT', 'LC_L',
            'LC_U', 'LCHARP', 'LEGEND', 'LEGENDRE_P', 'LEGENDRE_Q', 'LEINSTEIN', 'LET',
            'LET_RULE_PACKAGES', 'LETRAT', 'LETRULES', 'LETSIMP', 'LEVI_CIVITA', 'LFG', 'LG',
            'LGTREILLIS', 'LI', 'LIEDIFF', 'LINDSTEDT', 'LINE_TYPE', 'LINE_WIDTH', 'LINEAR',
            'LINEAR_PROGRAM', 'LINEAR_SOLVER', 'LISPDISP', 'LIST_CORRELATIONS', 'LIST_NC_MONOMIALS',
            'LISTARRAY', 'LISTOFTENS', 'LOGAND', 'LOGCB', 'LOGOR', 'LOGX', 'LOGXOR', 'LOGY', 'LOGZ',
            'LORENTZ_GAUGE', 'LPART', 'LRIEM', 'LRIEMANN', 'LSQUARES_ESTIMATES',
            'LSQUARES_ESTIMATES_APPROXIMATE', 'LSQUARES_ESTIMATES_EXACT', 'LSQUARES_MSE',
            'LSQUARES_RESIDUAL_MSE', 'LSQUARES_RESIDUALS', 'LTREILLIS', 'M1PBRANCH', 'MAINVAR',
            'MAKE_ARRAY', 'MAKE_LEVEL_PICTURE', 'MAKE_POLY_CONTINENT', 'MAKE_POLY_COUNTRY',
            'MAKE_POLYGON', 'MAKE_RANDOM_STATE', 'MAKE_RGB_PICTURE', 'MAKEBOX', 'MAKEORDERS',
            'MANDELBROT', 'MAPERROR', 'MAT_FUNCTION', 'MAX_ORD', 'MAXAPPLYDEPTH', 'MAXAPPLYHEIGHT',
            'MAXI', 'MAXIMIZE_LP', 'MAXNEGEX', 'MAXPOSEX', 'MAXPSIFRACDENOM', 'MAXPSIFRACNUM',
            'MAXPSINEGINT', 'MAXPSIPOSINT', 'MAXTAYORDER', 'MAYBE', 'MEAN', 'MEAN_BERNOULLI',
            'MEAN_BETA', 'MEAN_BINOMIAL', 'MEAN_CHI2', 'MEAN_CONTINUOUS_UNIFORM', 'MEAN_DEVIATION',
            'MEAN_DISCRETE_UNIFORM', 'MEAN_EXP', 'MEAN_F', 'MEAN_GAMMA', 'MEAN_GEOMETRIC',
            'MEAN_GUMBEL', 'MEAN_HYPERGEOMETRIC', 'MEAN_LAPLACE', 'MEAN_LOGISTIC', 'MEAN_LOGNORMAL',
            'MEAN_NEGATIVE_BINOMIAL', 'MEAN_NONCENTRAL_CHI2', 'MEAN_NONCENTRAL_STUDENT_T',
            'MEAN_NORMAL', 'MEAN_PARETO', 'MEAN_POISSON', 'MEAN_RAYLEIGH', 'MEAN_STUDENT_T',
            'MEAN_WEIBULL', 'MEDIAN', 'MEDIAN_DEVIATION', 'MESH', 'MESH_LINES_COLOR',
            'METRICEXPANDALL', 'MINI', 'MINIMALPOLY', 'MINIMIZE_LP', 'MINOR', 'MNEWTON',
            'MOD_BIG_PRIME', 'MOD_TEST', 'MOD_THRESHOLD', 'MODE_CHECK_ERRORP', 'MODE_CHECK_WARNP',
            'MODE_CHECKP', 'MODE_DECLARE', 'MODE_IDENTITY', 'MODEMATRIX', 'MODULAR_LINEAR_SOLVER',
            'MON2SCHUR', 'MONO', 'MONOMIAL_DIMENSIONS', 'MULTI_ELEM', 'MULTI_ORBIT', 'MULTI_PUI',
            'MULTINOMIAL', 'MULTSYM', 'NATURAL_UNIT', 'NC_DEGREE', 'NEGATIVE_PICTURE', 'NEWCONTEXT',
            'NEWTON', 'NEWTONEPSILON', 'NEWTONMAXITER', 'NEXTLAYERFACTOR', 'NICEINDICES',
            'NICEINDICESPREF', 'NM', 'NMC', 'NONCENTRAL_MOMENT', 'NONEGATIVE_LP', 'NONMETRICITY',
            'NONZEROANDFREEOF', 'NOUNDISP', 'NP', 'NPI', 'NPTETRAD', 'NTERMST', 'NTICKS', 'NTRIG',
            'NUMBERED_BOUNDARIES', 'ODE2', 'ODE_CHECK', 'ODELIN', 'OPTIMIZE', 'OPTIMPREFIX',
            'OPTIONSET', 'ORBIT', 'ORBITS', 'ORTHOPOLY_RECUR', 'ORTHOPOLY_RETURNS_INTERVALS',
            'ORTHOPOLY_WEIGHT', 'OUTOFPOIS', 'PALETTE', 'PARAMETRIC', 'PARAMETRIC_SURFACE',
            'PARGOSPER', 'PARTPOL', 'PDF_BERNOULLI', 'PDF_BETA', 'PDF_BINOMIAL', 'PDF_CAUCHY',
            'PDF_CHI2', 'PDF_CONTINUOUS_UNIFORM', 'PDF_DISCRETE_UNIFORM', 'PDF_EXP', 'PDF_F',
            'PDF_GAMMA', 'PDF_GEOMETRIC', 'PDF_GUMBEL', 'PDF_HEIGHT', 'PDF_HYPERGEOMETRIC',
            'PDF_LAPLACE', 'PDF_LOGISTIC', 'PDF_LOGNORMAL', 'PDF_NEGATIVE_BINOMIAL',
            'PDF_NONCENTRAL_CHI2', 'PDF_NONCENTRAL_STUDENT_T', 'PDF_NORMAL', 'PDF_PARETO',
            'PDF_POISSON', 'PDF_RANK_SUM', 'PDF_RAYLEIGH', 'PDF_SIGNED_RANK', 'PDF_STUDENT_T',
            'PDF_WEIBULL', 'PDF_WIDTH', 'PEARSON_SKEWNESS', 'PERMUT', 'PERMUTATION', 'PETROV',
            'PIC_HEIGHT', 'PIC_WIDTH', 'PICTURE_EQUALP', 'PICTUREP', 'PIECHART', 'PLOT2D',
            'PLOT3D', 'PLOT_FORMAT', 'PLOT_OPTIONS', 'PLOT_REAL_PART', 'PLSQUARES', 'POCHHAMMER',
            'POCHHAMMER_MAX_INDEX', 'POINT_SIZE', 'POINT_TYPE', 'POINTS', 'POINTS_JOINED',
            'POLAR', 'POLAR_TO_XY', 'POLYGON', 'PREDERROR', 'PRIMEP_NUMBER_OF_TESTS', 'PRINTPROPS',
            'PRODRAC', 'PRODUCT', 'PRODUCT_USE_GAMMA', 'PROGRAMMODE', 'PROPORTIONAL_AXES', 'PROPS',
            'PROPVARS', 'PSEXPAND', 'PSI', 'PUI', 'PUI2COMP', 'PUI2ELE', 'PUI2POLYNOME',
            'PUI_DIRECT', 'PUIREDUC', 'QRANGE', 'QTY', 'QUANTILE', 'QUANTILE_BERNOULLI',
            'QUANTILE_BETA', 'QUANTILE_BINOMIAL', 'QUANTILE_CAUCHY', 'QUANTILE_CHI2',
            'QUANTILE_CONTINUOUS_UNIFORM', 'QUANTILE_DISCRETE_UNIFORM', 'QUANTILE_EXP',
            'QUANTILE_F', 'QUANTILE_GAMMA', 'QUANTILE_GEOMETRIC', 'QUANTILE_GUMBEL',
            'QUANTILE_HYPERGEOMETRIC', 'QUANTILE_LAPLACE', 'QUANTILE_LOGISTIC',
            'QUANTILE_LOGNORMAL', 'QUANTILE_NEGATIVE_BINOMIAL', 'QUANTILE_NONCENTRAL_CHI2',
            'QUANTILE_NONCENTRAL_STUDENT_T', 'QUANTILE_NORMAL', 'QUANTILE_PARETO',
            'QUANTILE_POISSON', 'QUANTILE_RAYLEIGH', 'QUANTILE_STUDENT_T', 'QUANTILE_WEIBULL',
            'QUARTILE_SKEWNESS', 'RANDOM', 'RANDOM_BERNOULLI', 'RANDOM_BETA', 'RANDOM_BINOMIAL',
            'RANDOM_BIPARTITE_GRAPH', 'RANDOM_CAUCHY', 'RANDOM_CHI2', 'RANDOM_CONTINUOUS_UNIFORM',
            'RANDOM_DIGRAPH', 'RANDOM_DISCRETE_UNIFORM', 'RANDOM_EXP', 'RANDOM_F', 'RANDOM_GAMMA',
            'RANDOM_GEOMETRIC', 'RANDOM_GRAPH', 'RANDOM_GRAPH1', 'RANDOM_GUMBEL',
            'RANDOM_HYPERGEOMETRIC', 'RANDOM_LAPLACE', 'RANDOM_LOGISTIC', 'RANDOM_LOGNORMAL',
            'RANDOM_NEGATIVE_BINOMIAL', 'RANDOM_NETWORK', 'RANDOM_NONCENTRAL_CHI2',
            'RANDOM_NONCENTRAL_STUDENT_T', 'RANDOM_NORMAL', 'RANDOM_PARETO',
            'RANDOM_POISSON', 'RANDOM_RAYLEIGH', 'RANDOM_REGULAR_GRAPH', 'RANDOM_STUDENT_T',
            'RANDOM_TOURNAMENT', 'RANDOM_TREE', 'RANDOM_WEIBULL', 'RANGE', 'RATCHRISTOF',
            'RATEINSTEIN', 'RATIONAL', 'RATPRINT', 'RATRIEMANN', 'RATWEYL', 'RATWTLVL',
            'REARRAY', 'RECTANGLE', 'REDIFF', 'REDRAW', 'REDUCE_CONSTS', 'REDUCE_ORDER',
            'REGION_BOUNDARIES', 'REGION_BOUNDARIES_PLUS', 'REMARRAY', 'REMCOMPS', 'REMCON',
            'REMCOORD', 'REMLET', 'REMOVE_DIMENSIONS', 'REMOVE_FUNDAMENTAL_DIMENSIONS',
            'REMOVE_FUNDAMENTAL_UNITS', 'REMPART', 'REMSYM', 'RENAME', 'RESOLVANTE',
            'RESOLVANTE_ALTERNEE1', 'RESOLVANTE_BIPARTITE', 'RESOLVANTE_DIEDRALE',
            'RESOLVANTE_KLEIN', 'RESOLVANTE_KLEIN3', 'RESOLVANTE_PRODUIT_SYM',
            'RESOLVANTE_UNITAIRE', 'RESOLVANTE_VIERER', 'REVERT', 'REVERT2', 'RGB2LEVEL',
            'RIC', 'RICCI', 'RIEM', 'RIEMANN', 'RINVARIANT', 'RK', 'ROT_HORIZONTAL',
            'ROT_VERTICAL', 'SAVEFACTORS', 'SCATTERPLOT', 'SCURVATURE', 'SET_DRAW_DEFAULTS',
            'SET_RANDOM_STATE', 'SET_TEX_ENVIRONMENT', 'SET_TEX_ENVIRONMENT_DEFAULT',
            'SET_UP_DOT_SIMPLIFICATIONS', 'SETUNITS', 'SETUP_AUTOLOAD', 'SF', 'SHOWCOMPS',
            'SIMILARITYTRANSFORM', 'SIMPLE_LINEAR_REGRESSION', 'SIMPLIFIED_OUTPUT',
            'SIMPLIFY_PRODUCTS', 'SIMPLIFY_SUM', 'SIMPLODE', 'SIMPMETDERIV', 'SIMTRAN',
            'SKEWNESS', 'SKEWNESS_BERNOULLI', 'SKEWNESS_BETA', 'SKEWNESS_BINOMIAL',
            'SKEWNESS_CHI2', 'SKEWNESS_CONTINUOUS_UNIFORM', 'SKEWNESS_DISCRETE_UNIFORM',
            'SKEWNESS_EXP', 'SKEWNESS_F', 'SKEWNESS_GAMMA', 'SKEWNESS_GEOMETRIC',
            'SKEWNESS_GUMBEL', 'SKEWNESS_HYPERGEOMETRIC', 'SKEWNESS_LAPLACE', 'SKEWNESS_LOGISTIC',
            'SKEWNESS_LOGNORMAL', 'SKEWNESS_NEGATIVE_BINOMIAL', 'SKEWNESS_NONCENTRAL_CHI2',
            'SKEWNESS_NONCENTRAL_STUDENT_T', 'SKEWNESS_NORMAL', 'SKEWNESS_PARETO', 'SKEWNESS_POISSON',
            'SKEWNESS_RAYLEIGH', 'SKEWNESS_STUDENT_T', 'SKEWNESS_WEIBULL', 'SOLVE_REC',
            'SOLVE_REC_RAT', 'SOMRAC', 'SPARSE6_DECODE', 'SPARSE6_ENCODE', 'SPHERICAL_BESSEL_J',
            'SPHERICAL_BESSEL_Y', 'SPHERICAL_HANKEL1', 'SPHERICAL_HANKEL2', 'SPHERICAL_HARMONIC',
            'SPLIT', 'SQRTDENEST', 'SSTATUS', 'STAIRCASE', 'STARDISP', 'STATS_NUMER', 'STD', 'STD1',
            'STD_BERNOULLI', 'STD_BETA', 'STD_BINOMIAL', 'STD_CHI2', 'STD_CONTINUOUS_UNIFORM',
            'STD_DISCRETE_UNIFORM', 'STD_EXP', 'STD_F', 'STD_GAMMA', 'STD_GEOMETRIC', 'STD_GUMBEL',
            'STD_HYPERGEOMETRIC', 'STD_LAPLACE', 'STD_LOGISTIC', 'STD_LOGNORMAL',
            'STD_NEGATIVE_BINOMIAL', 'STD_NONCENTRAL_CHI2', 'STD_NONCENTRAL_STUDENT_T',
            'STD_NORMAL', 'STD_PARETO', 'STD_POISSON', 'STD_RAYLEIGH', 'STD_STUDENT_T',
            'STD_WEIBULL', 'STIRLING', 'STIRLING1', 'STIRLING2', 'STRINGDISP', 'STYLE',
            'SUBSAMPLE', 'SUMMAND_TO_REC', 'SURFACE_HIDE', 'SYMMETRICP', 'TAB', 'TAKE_CHANNEL',
            'TAKE_INFERENCE', 'TCONTRACT', 'TENSORKILL', 'TENTEX', 'TEST_MEAN',
            'TEST_MEANS_DIFFERENCE', 'TEST_NORMALITY', 'TEST_PROPORTION',
            'TEST_PROPORTIONS_DIFFERENCE', 'TEST_RANK_SUM', 'TEST_SIGN', 'TEST_SIGNED_RANK',
            'TEST_VARIANCE', 'TEST_VARIANCE_RATIO', 'TEXPUT', 'TITLE', 'TOTALDISREP', 'TOTIENT',
            'TPARTPOL', 'TR', 'TR_ARRAY_AS_REF', 'TR_BOUND_FUNCTION_APPLYP', 'TR_FILE_TTY_MESSAGESP',
            'TR_FLOAT_CAN_BRANCH_COMPLEX', 'TR_FUNCTION_CALL_DEFAULT', 'TR_NUMER',
            'TR_OPTIMIZE_MAX_LOOP', 'TR_SEMICOMPILE', 'TR_STATE_VARS', 'TR_WARN_BAD_FUNCTION_CALLS',
            'TR_WARN_FEXPR', 'TR_WARN_MEVAL', 'TR_WARN_MODE', 'TR_WARN_UNDECLARED',
            'TR_WARN_UNDEFINED_VARIABLE', 'TR_WARNINGS_GET', 'TR_WINDY', 'TRACEMATRIX',
            'TRANSFORM_XY', 'TRANSPARENT', 'TREILLIS', 'TREINAT', 'TRIVIAL_SOLUTIONS', 'TUBE',
            'TUBE_EXTREMES', 'TUTTE_GRAPH', 'UEIVECTS', 'UFG', 'UFORGET', 'UG', 'ULTRASPHERICAL',
            'UNDIFF', 'UNIT_STEP', 'UNIT_VECTORS', 'UNITEIGENVECTORS', 'UNITP', 'UNITS',
            'UNITVECTOR', 'UNKNOWN', 'UNORDER', 'URIC', 'URICCI', 'URIEM', 'URIEMANN',
            'USE_FAST_ARRAYS', 'USERSETUNITS', 'UVECT', 'VAR', 'VAR1', 'VAR_BERNOULLI', 'VAR_BETA',
            'VAR_BINOMIAL', 'VAR_CHI2', 'VAR_CONTINUOUS_UNIFORM', 'VAR_DISCRETE_UNIFORM', 'VAR_EXP',
            'VAR_F', 'VAR_GAMMA', 'VAR_GEOMETRIC', 'VAR_GUMBEL', 'VAR_HYPERGEOMETRIC', 'VAR_LAPLACE',
            'VAR_LOGISTIC', 'VAR_LOGNORMAL', 'VAR_NEGATIVE_BINOMIAL', 'VAR_NONCENTRAL_CHI2',
            'VAR_NONCENTRAL_STUDENT_T', 'VAR_NORMAL', 'VAR_PARETO', 'VAR_POISSON', 'VAR_RAYLEIGH',
            'VAR_STUDENT_T', 'VAR_WEIBULL', 'VECTOR', 'VERBOSE', 'VERS', 'WARNINGS', 'WEYL',
            'WRONSKIAN', 'X_VOXEL', 'XAXIS', 'XAXIS_COLOR', 'XAXIS_SECONDARY', 'XAXIS_TYPE',
            'XAXIS_WIDTH', 'XLABEL', 'XRANGE', 'XRANGE_SECONDARY', 'XTICS', 'XTICS_AXIS',
            'XTICS_ROTATE', 'XTICS_ROTATE_SECONDARY', 'XTICS_SECONDARY', 'XTICS_SECONDARY_AXIS',
            'XU_GRID', 'XY_FILE', 'XYPLANE', 'Y_VOXEL', 'YAXIS', 'YAXIS_COLOR', 'YAXIS_SECONDARY',
            'YAXIS_TYPE', 'YAXIS_WIDTH', 'YLABEL', 'YRANGE', 'YRANGE_SECONDARY', 'YTICS',
            'YTICS_AXIS', 'YTICS_ROTATE', 'YTICS_ROTATE_SECONDARY', 'YTICS_SECONDARY',
            'YTICS_SECONDARY_AXIS', 'YV_GRID', 'Z_VOXEL', 'ZAXIS', 'ZAXIS_COLOR', 'ZAXIS_TYPE',
            'ZAXIS_WIDTH', 'ZEILBERGER', 'ZEROA', 'ZEROB', 'ZLABEL', 'ZLANGE', 'ZRANGE', 'ZTICS',
            'ZTICS_AXIS', 'ZTICS_ROTATE' );

    /** @var array CAS keywords ALLOWED by students. */
    private static $studentallow    = array('%C', '%E', '%GAMMA', '%I', '%K1', '%K2',
            '%PHI', '%PI', 'ABS', 'ABSINT', 'ACOS', 'ACOSH', 'ACOT', 'ACOTH', 'ACSC', 'ACSCH',
            'ADDMATRICES', 'ADJOIN', 'AND', 'ASCII', 'ASEC', 'ASECH', 'ASIN', 'ASINH', 'ATAN',
            'ATAN2', 'ATANH', 'AUGCOEFMATRIX', 'BELLN', 'BESSEL_I', 'BESSEL_J', 'BESSEL_K',
            'BESSEL_Y', 'BESSELEXPAND', 'BETA', 'BEZOUT', 'BFFAC', 'BFHZETA', 'BFLOAT',
            'BFLOATP', 'BINOMIAL', 'BLOCKMATRIXP', 'BURN', 'CABS', 'CARDINALITY', 'CARG',
            'CARTAN', 'CARTESIAN_PRODUCT', 'CEILING', 'CEQUAL', 'CEQUALIGNORE', 'CF',
            'CFDISREP', 'CFEXPAND', 'CFLENGTH', 'CGREATERP', 'CGREATERPIGNORE', 'CHARAT',
            'CHARFUN', 'CHARFUN2', 'CHARLIST', 'CHARP', 'CHARPOLY', 'CINT', 'CLESSP',
            'CLESSPIGNORE', 'COEFF', 'COEFMATRIX', 'COL', 'COLUMNOP', 'COLUMNSPACE',
            'COLUMNSWAP', 'COMBINE', 'COMPARE', 'CONCAT', 'CONJUGATE', 'CONS', 'CONSTITUENT',
            'COPY', 'COS', 'COSH', 'COT', 'COTH', 'COVECT', 'CSC', 'CSCH', 'CSPLINE',
            'CTRANSPOSE', 'DBLINT', 'DEFINT', 'DEL', 'DELETE', 'DELTA', 'DENOM', 'DESOLVE',
            'DETERMINANT', 'DETOUT', 'DGAUSS_A', 'DGAUSS_B', 'DIAG_MATRIX', 'DIAGMATRIX',
            'DIFF', 'DIGITCHARP', 'DISJOIN', 'DISJOINTP', 'DISOLATE', 'DIVIDE', 'DIVISORS',
            'DIVSUM', 'DKUMMER_M', 'DKUMMER_U', 'DOTPRODUCT', 'ECHELON', 'EIGENVALUES',
            'EIGENVECTORS', 'EIGHTH', 'EIVALS', 'EIVECTS', 'ELEMENTP', 'ELIMINATE',
            'ELLIPTIC_E', 'ELLIPTIC_EC', 'ELLIPTIC_EU', 'ELLIPTIC_F', 'ELLIPTIC_KC',
            'ELLIPTIC_PI', 'EMATRIX', 'EMPTYP', 'ENDCONS', 'EPSILON_LP', 'EQUAL', 'EQUALP',
            'EQUIV_CLASSES', 'ERF', 'EULER', 'EV', 'EVAL', 'EVENP', 'EVERY', 'EXP', 'EXPAND',
            'EXPANDWRT', 'EXPANDWRT_DENOM', 'EXPANDWRT_FACTORED', 'EXPRESS', 'EXTREMAL_SUBSET',
            'EZGCD', 'FACSUM', 'FACSUM_COMBINE', 'FACTCOMB', 'FACTLIM', 'FACTOR',
            'FACTORFACSUM', 'FACTORIAL', 'FACTOROUT', 'FACTORSUM', 'FALSE', 'FASTTIMES', 'FFT',
            'FIB', 'FIBTOPHI', 'FIFTH', 'FIND_ROOT', 'FIND_ROOT_ABS', 'FIND_ROOT_ERROR',
            'FIND_ROOT_REL', 'FIRST', 'FLATTEN', 'FLOAT', 'FLOAT2BF', 'FLOOR', 'FOURCOS',
            'FOUREXPAND', 'FOURIER', 'FOURINT', 'FOURINTCOS', 'FOURINTSIN', 'FOURSIMP',
            'FOURSIN', 'FOURTH', 'FREEOF', 'FULL_LISTIFY', 'FULLMAP', 'FULLMAPL', 'FULLRATSIMP',
            'FULLRATSUBST', 'FULLSETIFY', 'FUNCSOLVE', 'FUNP', 'GAMMA', 'GAMMA_INCOMPLETE',
            'GAMMA_INCOMPLETE_GENERALIZED', 'GAMMA_INCOMPLETE_REGULARIZED', 'GAUSS_A',
            'GAUSS_B', 'GCD', 'GCDEX', 'GCFACTOR', 'GENMATRIX', 'GET_LU_FACTORS', 'GFACTOR',
            'GFACTORSUM', 'GRAMSCHMIDT', 'HANKEL', 'HESSIAN', 'HGFRED', 'HILBERT_MATRIX',
            'HIPOW', 'HORNER', 'HYPERGEOMETRIC', 'HYPERGEOMETRIC_REPRESENTATION', 'IDENT',
            'IDENTFOR', 'IDENTITY', 'IFACTORS', 'IMAGPART', 'IND', 'INF', 'INFINITY',
            'INNERPRODUCT', 'INRT', 'INTEGER_PARTITIONS', 'INTEGRATE', 'INTERSECT',
            'INTERSECTION', 'INTOSUM', 'INV_MOD', 'INVERSE_JACOBI_CD', 'INVERSE_JACOBI_CN',
            'INVERSE_JACOBI_CS', 'INVERSE_JACOBI_DC', 'INVERSE_JACOBI_DN', 'INVERSE_JACOBI_DS',
            'INVERSE_JACOBI_NC', 'INVERSE_JACOBI_ND', 'INVERSE_JACOBI_NS', 'INVERSE_JACOBI_SC',
            'INVERSE_JACOBI_SD', 'INVERSE_JACOBI_SN', 'INVERT', 'ISQRT', 'JACOBI', 'JACOBI_CD',
            'JACOBI_CN', 'JACOBI_CS', 'JACOBI_DC', 'JACOBI_DN', 'JACOBI_DS', 'JACOBI_NC',
            'JACOBI_ND', 'JACOBI_NS', 'JACOBI_SC', 'JACOBI_SD', 'JACOBI_SN', 'JACOBIAN', 'JOIN',
            'KRON_DELTA', 'KRONECKER_PRODUCT', 'KUMMER_M', 'KUMMER_U', 'LAGRANGE', 'LAMBDA',
            'LAMBERT_W', 'LAPLACE', 'LAST', 'LCM', 'LDEFINT', 'LENGTH', 'LHS', 'LIMIT',
            'LINEARINTERPOL', 'LINSOLVE', 'LINSOLVE_PARAMS', 'LISTIFY', 'LMAX', 'LMIN',
            'LOCATE_MATRIX_ENTRY', 'LOG', 'LOG10', 'LOG_GAMMA', 'LOGABS', 'LOGARC',
            'LOGCONTRACT', 'LOGEXPAND', 'LOGNEGINT', 'LOGNUMER', 'LOGSIMP', 'LOPOW',
            'LOWERCASEP', 'LRATSUBST', 'LREDUCE', 'LSUM', 'LU_BACKSUB', 'LU_FACTOR',
            'MAKE_TRANSFORM', 'MAKEFACT', 'MAKEGAMMA', 'MAKELIST', 'MAKESET', 'MAP',
            'MAPATOM', 'MAPLIST', 'MAT_COND', 'MAT_FULLUNBLOCKER', 'MAT_NORM', 'MAT_TRACE',
            'MAT_UNBLOCKER', 'MATRIX', 'MATRIX_ELEMENT_ADD', 'MATRIX_ELEMENT_MULT',
            'MATRIX_ELEMENT_TRANSPOSE', 'MATRIX_SIZE', 'MATRIXMAP', 'MATRIXP', 'MATTRACE',
            'MAX', 'MEMBER', 'MIN', 'MINF', 'MINFACTORIAL', 'MOD', 'MOEBIUS',
            'MULTINOMIAL_COEFF', 'MULTTHRU', 'NCEXPT', 'NCHARPOLY', 'NEWDET', 'NINTH',
            'NOEVAL', 'NONNEGINTEGERP', 'NOT', 'NOTEQUAL', 'NROOTS', 'NTERMS', 'NTHROOT',
            'NULLITY', 'NULLSPACE', 'NUM', 'NUM_DISTINCT_PARTITIONS', 'NUM_PARTITIONS',
            'NUMBERP', 'NUMER', 'NUMERVAL', 'NUMFACTOR', 'NUSUM', 'NZETA', 'NZETAI', 'NZETAR',
            'ODDP', 'OP', 'OPERATORP', 'OR', 'ORDERGREAT', 'ORDERGREATP', 'ORDERLESS',
            'ORDERLESSP', 'ORTHOGONAL_COMPLEMENT', 'OUTERMAP', 'PADE', 'PARABOLIC_CYLINDER_D',
            'PART', 'PART2CONT', 'PARTFRAC', 'PARTITION', 'PARTITION_SET', 'PERMANENT',
            'PERMUTATIONS', 'PLOG', 'POISDIFF', 'POISEXPT', 'POISINT', 'POISLIM', 'POISMAP',
            'POISPLUS', 'POISSIMP', 'POISSON', 'POISSUBST', 'POISTIMES', 'POISTRIM',
            'POLARFORM', 'POLARTORECT', 'POLYMOD', 'POLYNOME2ELE', 'POLYNOMIALP',
            'POLYTOCOMPANION', 'POSFUN', 'POTENTIAL', 'POWER_MOD', 'POWERDISP', 'POWERS',
            'POWERSERIES', 'POWERSET', 'PRIMEP', 'PRINTPOIS', 'QUAD_QAG', 'QUAD_QAGI',
            'QUAD_QAGS', 'QUAD_QAWC', 'QUAD_QAWF', 'QUAD_QAWO', 'QUAD_QAWS', 'QUNIT',
            'QUOTIENT', 'RADCAN', 'RADEXPAND', 'RADSUBSTFLAG', 'RANK', 'RASSOCIATIVE', 'RAT',
            'RATALGDENOM', 'RATCOEF', 'RATDENOM', 'RATDENOMDIVIDE', 'RATDIFF', 'RATDISREP',
            'RATEPSILON', 'RATEXPAND', 'RATFAC', 'RATIONALIZE', 'RATMX', 'RATNUMER', 'RATNUMP',
            'RATP', 'RATSIMP', 'RATSIMPEXPONS', 'RATSUBST', 'RATVARS', 'RATWEIGHT',
            'RATWEIGHTS', 'REALONLY', 'REALPART', 'REALROOTS', 'RECTFORM', 'RECTTOPOLAR',
            'REMAINDER', 'REMFUN', 'RESIDUE', 'REST', 'RESULTANT', 'REVERSE', 'RHS', 'RISCH',
            'RNCOMBINE', 'ROMBERG', 'ROMBERGABS', 'ROMBERGIT', 'ROMBERGMIN', 'ROMBERGTOL',
            'ROOTSCONMODE', 'ROOTSCONTRACT', 'ROOTSEPSILON', 'ROUND', 'ROW', 'ROWOP', 'ROWSWAP',
            'RREDUCE', 'SCALARMATRIXP', 'SCALARP', 'SCALED_BESSEL_I', 'SCALED_BESSEL_I0',
            'SCALED_BESSEL_I1', 'SCALEFACTORS', 'SCANMAP', 'SCHUR2COMP', 'SCONCAT', 'SCOPY',
            'SCSIMP', 'SDOWNCASE', 'SEC', 'SECH', 'SECOND', 'SEQUAL', 'SEQUALIGNORE',
            'SET_PARTITIONS', 'SETDIFFERENCE', 'SETEQUALP', 'SETIFY', 'SETP', 'SEVENTH',
            'SEXPLODE', 'SIGN', 'SIGNUM', 'SIMPSUM', 'SIN', 'SINH', 'SINNPIFLAG', 'SINSERT',
            'SINVERTCASE', 'SIXTH', 'SLENGTH', 'SMAKE', 'SMISMATCH', 'SOLVE', 'SOLVEDECOMPOSES',
            'SOLVEEXPLICIT', 'SOLVEFACTORS', 'SOLVENULLWARN', 'SOLVERADCAN', 'SOLVETRIGWARN',
            'SOME', 'SORT', 'SPACE', 'SPARSE', 'SPECINT', 'SPOSITION', 'SQFR', 'SQRT',
            'SQRTDISPFLAG', 'SREMOVE', 'SREMOVEFIRST', 'SREVERSE', 'SSEARCH', 'SSORT', 'SSUBST',
            'SSUBSTFIRST', 'STRIM', 'STRIML', 'STRIMR', 'STRINGP', 'STRUVE_H', 'STRUVE_L',
            'SUBLIS', 'SUBLIS_APPLY_LAMBDA', 'SUBLIST', 'SUBLIST_INDICES', 'SUBMATRIX',
            'SUBSET', 'SUBSETP', 'SUBST', 'SUBSTINPART', 'SUBSTPART', 'SUBSTRING', 'SUBVARP',
            'SUM', 'SUMCONTRACT', 'SUMEXPAND', 'SUPCASE', 'SYMBOLP', 'SYMMDIFFERENCE', 'TAN',
            'TANH', 'TAYLOR', 'TAYLOR_LOGEXPAND', 'TAYLOR_ORDER_COEFFICIENTS',
            'TAYLOR_SIMPLIFIER', 'TAYLOR_TRUNCATE_POLYNOMIALS', 'TAYLORDEPTH', 'TAYLORINFO',
            'TAYLORP', 'TAYTORAT', 'TELLSIMP', 'TELLSIMPAFTER', 'TENTH', 'THIRD', 'TLIMIT',
            'TLIMSWITCH', 'TODD_COXETER', 'TOEPLITZ', 'TRANSPOSE', 'TREE_REDUCE',
            'TRIANGULARIZE', 'TRIGEXPAND', 'TRIGEXPANDPLUS', 'TRIGEXPANDTIMES', 'TRIGINVERSES',
            'TRIGRAT', 'TRIGREDUCE', 'TRIGSIGN', 'TRIGSIMP', 'TRUE', 'TRUNC', 'UND', 'UNION',
            'UNIQUE', 'UNSUM', 'UNTELLRAT', 'UPPERCASEP', 'VANDERMONDE_MATRIX', 'VECT_CROSS',
            'VECTORPOTENTIAL', 'VECTORSIMP', 'XREDUCE', 'XTHRU', 'ZEROBERN', 'ZEROEQUIV',
            'ZEROFOR', 'ZEROMATRIX', 'ZEROMATRIXP', 'ZETA', 'ZETA%PI', 'PI', 'E', 'I', 'FLOAT',
            'ROUND', 'TRUNCATE', 'DECIMALPLACES', 'ANYFLOAT', 'ANYFLOATEX', 'EXPAND', 'EXPANDP',
            'SIMPLIFY', 'DIVTHRU', 'FACTOR', 'FACTORP', 'DIFF', 'INT', 'RAND', 'PLOT',
            'PLOT_IMPLICIT', 'STACK_VALIDATE_TYPELESS', 'STACK_VALIDATE', 'ALPHA', 'NU', 'BETA',
            'XI', 'GAMMA', 'OMICRON', 'DELTA', 'PI', 'EPSILON', 'RHO', 'ZETA', 'SIGMA', 'ETA',
            'TAU', 'THETA', 'UPSILON', 'IOTA', 'PHI', 'KAPPA', 'CHI', 'LAMBDA', 'PSI', 'MU',
            'OMEGA');

    /**
     * @var all the characters permitted in responses.
     * Note, these are used in regular expression ranges, so - must be at the end, and ^ may not be first.
     */
    private static $allowedchars =
            '0123456789,./\%&{}[]()$£@!"\'?`^~*_+qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM;:=><|: -';

    /**
     * @var all the permitted which are not allowed to be the final character.
     * Note, these are used in regular expression ranges, so - must be at the end, and ^ may not be first.
     */
    private static $disallowedfinalchars = '/+*^£#~=,_&`¬;:$-';

    public function __construct($rawstring) {
        $this->rawcasstring   = $rawstring;

        $this->valid          =  null;  // If NULL then the validate command has not yet been run....

        if (!is_string($this->rawcasstring)) {
            throw new stack_exception('stack_cas_casstring: rawstring must be a string.');
        }

    }

    /*********************************************************/
    /* Validation functions                                  */
    /*********************************************************/

    // We may need to use this function more than once to validate with different options.
    public function validate($security='s', $syntax=true, $insertstars=false) {

        if (!('s'===$security || 't'===$security)) {
            throw new stack_exception('stack_cas_casstring: security level, must be "s" or "t" only.');
        }

        if (!is_bool($syntax)) {
            throw new stack_exception('stack_cas_casstring: syntax, must be Boolean.');
        }

        if (!is_bool($insertstars)) {
            throw new stack_exception('stack_cas_casstring: insertstars, must be Boolean.');
        }

        $this->valid     = true;
        $cmd             = $this->rawcasstring;
        $this->casstring = $this->rawcasstring;

        // CAS strings must be non-empty.
        if (trim($this->casstring) == '') {
            $this->valid = false;
            return false;
        }

        // CAS strings may not contain @ or $.
        if (strpos($cmd, '@') !== false || strpos($cmd, '$') !== false) {
            $this->add_error(stack_string('illegalcaschars'));
            $this->valid = false;
            return false;
        }

        // Search for HTML fragments.  This is hard to do because < is an infix operator!
        // We cannot search for arbitrary closing tags, e.g. for the pattern '</' because
        // we pass back strings with HTML in when we have already evaluated plots!
        $htmlfragments = array('<span', '</span>', '<p>', '</p>');
        foreach ($htmlfragments as $frag) {
            if (strpos($cmd, $frag) !== false) {
                $this->add_error(stack_string('htmlfragment').' <pre>'.$cmd.'</pre>');
                $this->valid = false;
                return false;
            }
        }

        // If student, check for spaces between letters or numbers in expressions.
        if ($security != 't') {
            $pat = "|([A-Za-z0-9\(\)]+) ([A-Za-z0-9\(\)]+)|";
            // Special case - allow students to type in expressions such as "x>1 and x<4".
            $cmdmod = str_replace(' or ', '', $cmd);
            $cmdmod = str_replace(' and ', '', $cmdmod);
            $cmdmod = str_replace('not ', '', $cmdmod);
            if (preg_match($pat, $cmdmod)) {
                $this->valid = false;
                $cmds = str_replace(' ', '<font color="red">_</font>', $cmd);
                $this->add_error(stack_string("stackCas_spaces", array('expr'=>stack_maxima_format_casstring($cmds))));
            }
        }

        // Check for % signs, allow %pi %e, %i, %gamma, %phi but nothing else.
        if (strstr($cmd, '%') !== false) {
            $cmdl = strtolower($cmd);
            preg_match_all("(\%.*)", $cmdl, $found);

            foreach ($found[0] as $match) {
                if (!((strpos($match, '%e') !== false) || (strpos($match, '%pi') !== false)
                    || (strpos($match, '%i') !== false) || (strpos($match, '%j') !== false)
                    || (strpos($match, '%gamma') !== false) || (strpos($match, '%phi') !== false))) {
                    // Constants %e and %pi are allowed. Any other percentages dissallowed.
                    $this->valid   = false;
                    $this->add_error(stack_string('stackCas_percent', array('expr' => stack_maxima_format_casstring($cmd))));
                }
            }
        }

        $inline = stack_utils::check_bookends($cmd, '(', ')');
        if ($inline !== true) { // The method check_bookends does not return false.
            $this->valid = false;
            if ($inline == 'left') {
                $this->add_error(stack_string('stackCas_missingLeftBracket',
                    array('bracket'=>'(', 'cmd' => stack_maxima_format_casstring($cmd))));
            } else {
                $this->add_error(stack_string('stackCas_missingRightBracket',
                    array('bracket'=>')', 'cmd' => stack_maxima_format_casstring($cmd))));
            }
        }
        $inline = stack_utils::check_bookends($cmd, '{', '}');
        if ($inline !== true) { // The method check_bookends does not return false.
            $this->valid = false;
            if ($inline == 'left') {
                $this->add_error(stack_string('stackCas_missingLeftBracket',
                 array('bracket'=>'{', 'cmd' => stack_maxima_format_casstring($cmd))));
            } else {
                $this->add_error(stack_string('stackCas_missingRightBracket',
                 array('bracket'=>'}', 'cmd' => stack_maxima_format_casstring($cmd))));
            }
        }
        $inline = stack_utils::check_bookends($cmd, '[', ']');
        if ($inline !== true) { // The method check_bookends does not return false.
            $this->valid = false;
            if ($inline == 'left') {
                $this->add_error(stack_string('stackCas_missingLeftBracket',
                 array('bracket'=>'[', 'cmd' => stack_maxima_format_casstring($cmd))));
            } else {
                $this->add_error(stack_string('stackCas_missingRightBracket',
                 array('bracket'=>']', 'cmd' => stack_maxima_format_casstring($cmd))));
            }
        }

        if (!stack_utils::check_nested_bookends($cmd)) {
            $this->valid = false;
            $this->add_error(stack_string('stackCas_bracketsdontmatch',
                     array('cmd' => stack_maxima_format_casstring($cmd))));
        }

        if ($security == 's') {
            // Check for apostrophes if a student.
            if (strpos($cmd, "'") !== false) {
                $this->valid = false;
                $this->add_error(stack_string('stackCas_apostrophe'));
            }
            // Check new lines.
            if (strpos($cmd, "\n") !== false) {
                $this->valid = false;
                $this->add_error(stack_string('stackCas_newline'));
            }
        }

        // Only permit the following characters to be sent to the CAS.
        $cmd = trim($cmd);
        $allowedcharsregex = '~[^' . preg_quote(self::$allowedchars, '~') . ']~u';

        // Check for permitted characters.
        if (preg_match_all($allowedcharsregex, $cmd, $matches)) {
            $invalidchars = array();
            foreach ($matches as $match) {
                $badchar = $match[0];
                if (!array_key_exists($badchar, $invalidchars)) {
                    $invalidchars[$badchar] = $badchar;
                }
            }
            $this->valid = false;
            $this->add_error(stack_string('stackCas_forbiddenChar', array( 'char' => implode(", ", array_unique($invalidchars)))));
        }

        // Check for disallowed final characters,  / * + - ^ £ # = & ~ |, ? : ;.
        $disallowedfinalcharsregex = '~[' . preg_quote(self::$disallowedfinalchars, '~') . ']$~u';
        if (preg_match($disallowedfinalcharsregex, $cmd, $match)) {
            $this->valid = false;
            $a = array();
            $a['char'] = $match[0];
            $a['cmd']  = stack_maxima_format_casstring($cmd);
            $this->add_error(stack_string('stackCas_finalChar', $a));
        }

        // Check for empty parentheses `()`.
        if (strpos($cmd, '()') !== false) {
            $this->valid = false;
            $this->add_error(stack_string('stackCas_forbiddenWord', array('forbid'=>stack_maxima_format_casstring('()'))));
        }

        // Check for spurious operators.
        $spuriousops = array('<>', '||', '&', '..');
        foreach ($spuriousops as $op) {
            if (substr_count($cmd, $op)>0) {
                $this->valid = false;
                $a = array();
                $a['cmd']  = stack_maxima_format_casstring($op);
                $this->add_error(stack_string('stackCas_spuriousop', $a));
            }
        }

        // CAS strings may not contain @ or $.
        if (strpos($cmd, '=<') !== false || strpos($cmd, '=>') !== false) {
            if (strpos($cmd, '=<') !== false) {
                $a['cmd'] = stack_maxima_format_casstring('=<');
            } else {
                $a['cmd'] = stack_maxima_format_casstring('=>');
            }
            $this->add_error(stack_string('stackCas_backward_inequalities', $a));
            $this->valid = false;
        } else if (!($this->check_chained_inequalities($cmd))) {
            $this->valid = false;
            $this->add_error(stack_string('stackCas_chained_inequalities'));
        }

        $this->check_stars($security, $syntax, $insertstars);

        $this->check_security($security);

        $this->key_val_split();
        return $this->valid;
    }

    /**
     * Checks that there are no *s missing from expressions, eg 2x should be 2*x
     *
     * @return bool|string true if no missing *s, false if missing stars but automatically added
     * if stack is set to not add stars automatically, a string indicating the missing stars is returned.
     */
    private function check_stars($security, $syntax, $insertstars) {

        // Some patterns are always invalid syntax, and must have stars.
        $patterns[] = "|(\))(\()|";                   // Simply the pattern ")(".  Must be wrong!
        $patterns[] = "|(\))([0-9A-Za-z])|";          // E.g. )a, or )3.
        // We assume f and g are single letter functions.
        // 'E' is used to denote scientific notation.    E.g. 3E2 = 300.0.
        if ($syntax) {
            $patterns[] = "|([0-9]+)([A-DF-Za-z])|";  // E.g. 3x.
            $patterns[] = "|([0-9])([A-DF-Za-z]\()|"; // E.g. 3 x (.
        } else {
            $patterns[] = "|([0-9]+)([A-Za-z])|";     // E.g. 3x.
            $patterns[] = "|([0-9])([A-Za-z]\()|";    // E.g. 3 x (.
        }

        if ($security == 's') {
            $patterns[] = "|([0-9]+)(\()|";           // E.g. 3212 (.
            if (!$syntax) {
                $patterns[] = "|(^[A-Za-z])(\()|";    // E.g. a( , that is a single letter.
                $patterns[] = "|(\*[A-Za-z])(\()|";
                $patterns[] = "|([A-Za-z])([0-9]+)|"; // E.g. x3.
            }
        }

        // Loop over every CAS command checking for missing stars.
        $missingstar     = false;
        $missingstring   = '';

        // Prevent ? characters calling LISP or the Maxima help file.  Instead, these pass through and are displayed as normal.
        $cmd = str_replace('?', 'QMCHAR', $this->rawcasstring);

        foreach ($patterns as $pat) {
            if (preg_match($pat, $cmd)) {
                // Found a missing star.
                $missingstar = true;
                if ($insertstars) {
                    // Then we automatically add stars.
                    $cmd = preg_replace($pat, "\${1}*\${2}", $cmd);
                } else {
                    // Flag up the error.
                    $missingstring = stack_maxima_format_casstring(preg_replace($pat,
                        "\${1}<font color=\"red\">*</font>\${2}", $cmd));
                }
            }
        }

        if (false == $missingstar) {
            // If no missing stars return true.
            return true;
        }
        // Guard clause above - we have missing stars detected.
        if ($insertstars) {
            // If we are going to quietly insert them.
            $this->casstring = str_replace('QMCHAR', '?', $cmd);
            return true;
        } else {
            // If missing stars & strict syntax is on return errors.
            $a['cmd']  = str_replace('QMCHAR', '?', $missingstring);
            $this->add_error(stack_string('stackCas_MissingStars', $a));
            $this->valid = false;
            return false;
        }
    }


    /**
     * Check for forbidden CAS commands, based on security level
     *
     * @return bool|string true if passes checks if fails, returns string of forbidden commands
     */
    private function check_security($security) {

        $cmd = $this->casstring;
        $strin_keywords = array();
        $pat = "|[\?_A-Za-z0-9]+|";
        preg_match_all($pat, $cmd, $out, PREG_PATTERN_ORDER);

        // Filter out some of these matches.
        foreach ($out[0] as $key) {
            // Do we have only numbers, or only 2 characters?
            // These strings are fine.
            preg_match("|[0-9]+|", $key, $justnum);

            if (empty($justnum) and strlen($key)>2) {
                $upkey = strtoupper($key);
                array_push($strin_keywords, $upkey);
            }
        }
        $strin_keywords = array_unique($strin_keywords);
        // Check for global forbidden words.
        foreach ($strin_keywords as $key) {
            if (in_array($key, self::$globalforbid)) {
                // Very bad!
                $this->valid = false;
                $this->add_error(stack_string('stackCas_forbiddenWord', array('forbid'=>stack_maxima_format_casstring($key))));
            } else {
                if ($security == 't') {
                    if (in_array($key, self::$teachernotallow)) {
                        // If a teacher check against forbidden commands.
                        $this->valid = false;
                        $this->add_error(stack_string('stackCas_unsupportedKeyword',
                            array('forbid'=>stack_maxima_format_casstring($key))));
                    }
                } else {
                    // Only allow the student to use set commands.
                    if (!in_array($key, self::$studentallow)) {
                        $this->valid = false;
                        $this->add_error(stack_string('stackCas_unknownFunction',
                            array('forbid'=>stack_maxima_format_casstring($key))));
                    }
                    // Else we have not found any security problems with keywords.
                }
            }
        }
        return null;
    }

    /**
     * This function checks chained inequalities
     * If we have two or more inequality symbols then we must have a logical connection {or/and} between each pair.
     * First we need to split over commas to break up lists etc.
     */
    private function check_chained_inequalities($ex) {

        if (substr_count($ex, '<') + substr_count($ex, '>')<2) {
            return true;
        }

        // Plots, and HTML elements are protected within strings when they come back through the CAS.
        $found = stack_utils::substring_between($ex, '<html>', '</html>');
        if ($found[1]>0) {
            $ex = str_replace($found[0], '', $ex);
        }

        // Separate out lists, sets, etc.
        $ex_split = explode(',', $ex);
        $bits = array();
        $ok = true;
        foreach ($ex_split as $bit) {
            $ok = $ok && $this->check_chained_inequalities_ind($bit);
        }

        return $ok;
    }

    private function check_chained_inequalities_ind($ex) {

        if (substr_count($ex, '<') + substr_count($ex, '>')<2) {
            return true;
        }

        // Split over characters '<>', '<=', '>=', '<', '>', '=',
        // Note the order in splits:  this is important.
        $splits = array( '<>', '<=', '>=', '<', '>', '=');
        $bits = array($ex);
        foreach ($splits as $split) {
            $newbits = array();
            foreach ($bits as $bit) {
                $newbits = array_merge($newbits, explode($split, $bit));
            }
            $bits = $newbits;
        }
        // Remove first and last entries.
        unset($bits[count($bits)-1]);
        unset($bits[0]);

        // Now check each "middle bit" has one of the following.
        // Note the space before, but not afterwards....
        $connectives = array(' and', ' or', ' else', ' then', ' do');
        $ok = true;
        foreach ($bits as $bit) {
            $onefound = false;
            foreach ($connectives as $con) {
                if (!(false === strpos($bit, $con))) {
                    $onefound = true;
                }
            }
            $ok = $ok && $onefound;
        }
        return $ok;
    }

    /**
     * Check for CAS commands which appear in the $keywords array, which are not just single variables
     * Notes, (i)  this is case insensitive.
     *        (ii) returns true if we find the element of the array.
     * @return bool|string true if an element of array is found in the casstring.
     */
    public function check_external_forbidden_words($keywords) {
        if (null===$this->valid) {
            $this->validate();
        }
        // Ensure all $keywords are upper case.
        foreach ($keywords as $key => $val) {
            $keywords[$key] = trim(strtoupper($val));
        }

        $found          = false;
        $strin_keywords = array();
        $pat = "|[\?_A-Za-z0-9]+|";
        preg_match_all($pat, $this->casstring, $out, PREG_PATTERN_ORDER);

        // Filter out some of these matches.
        foreach ($out[0] as $key) {
            if (strlen($key)>1) {
                $upkey = strtoupper($key);
                array_push($strin_keywords, $upkey);
            }
        }
        $strin_keywords = array_unique($strin_keywords);

        foreach ($strin_keywords as $key) {
            if (in_array($key, $keywords)) {
                $found = true;
                $this->valid = false;
                $this->add_error(stack_string('stackCas_forbiddenWord', array('forbid'=>stack_maxima_format_casstring($key))));
            }
        }
        return $found;
    }

    /*********************************************************/
    /* Internal utility functions                            */
    /*********************************************************/

    private function add_error($err) {
        $this->errors = trim(trim($this->errors).' '.trim($err));
    }

    private function key_val_split() {
        $i = strpos($this->casstring, ':');
        if (false === $i) {
            $this->key   = '';
        } else {
            // Need to check we don't have a function definition...
            if ('='===substr($this->casstring, $i+1, 1)) {
                $this->key   = '';
            } else {
                $this->key       = trim(substr($this->casstring, 0, $i));
                $this->casstring = trim(substr($this->casstring, $i+1));
            }
        }
    }

    /*********************************************************/
    /* Return and modify information                         */
    /*********************************************************/

    public function get_valid($security='s', $syntax=true, $insertstars=false) {
        if (null===$this->valid) {
            $this->validate($security, $syntax, $insertstars);
        }
        return $this->valid;
    }

    public function set_valid($val) {
        $this->valid=$val;
    }

    public function get_errors() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->errors;
    }

    public function get_raw_casstring() {
        return $this->rawcasstring;
    }

    public function get_casstring() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->casstring;
    }

    public function get_key() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->key;
    }

    public function get_value() {
        return $this->value;
    }

    public function get_display() {
        return $this->display;
    }

    public function set_key($key, $append_key=true) {
        if (null===$this->valid) {
            $this->validate();
        }
        if (''!=$this->key && $append_key) {
            $this->casstring = $this->key.':'.$this->casstring;
            $this->key=$key;
        } else {
            $this->key=$key;
        }
    }

    public function set_value($val) {
        $this->value=$val;
    }

    public function set_display($val) {
        $this->display=$val;
    }

    public function get_answernote() {
        return $this->answernote;
    }

    public function set_answernote($val) {
        $this->answernote=$val;
    }

    public function get_feedback() {
        return $this->feedback;
    }

    public function set_feedback($val) {
        $this->feedback=$val;
    }

    public function add_errors($err) {
        if (''==trim($err)) {
            return false;
        } else {
            return $this->errors.=$err;
        }
    }

    // If we "CAS validate" this string, then we need to set various options.
    // If the teacher's answer is NULL then we use typeless validation, otherwise we check type.
    public function set_cas_validation_casstring($key, $forbidfloats=true, $lowestterms=true, $tans=null) {
        if (null===$this->valid) {
            $this->validate();
        }
        if (false === $this->valid) {
            return false;
        }

        $this->key = $key;
        $starredanswer = $this->casstring;

        // Turn PHP Booleans into Maxima true & false.
        if ($forbidfloats) {
            $forbidfloats='true';
        } else {
            $forbidfloats='false';
        }
        if ($lowestterms) {
            $lowestterms='true';
        } else {
            $lowestterms='false';
        }

        if (null===$tans) {
            $this->casstring = 'stack_validate_typeless(['.$starredanswer.'],'.$forbidfloats.','.$lowestterms.')';
        } else {
            $this->casstring = 'stack_validate(['.$starredanswer.'],'.$forbidfloats.','.$lowestterms.','.$tans.')';
        }
        return true;
    }

}
