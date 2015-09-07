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
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');

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
     * @var array Records logical informataion about the string, used for statistical
     *             anaysis of students' answers.
     */
    private $answernote;

    /**
     * @var string how to display the CAS string, e.g. LaTeX. Only gets set
     *              after the casstring has been processed by the CAS, and the
     *              CAS function is an answertest.
     */
    private $feedback;

    /** @var array blacklist of globally forbidden CAS keywords. */
    private static $globalforbid    = array('%th', 'adapth_depth', 'alias', 'aliases',
            'alphabetic', 'appendfile', 'apropos', 'assume_external_byte_order', 'backtrace',
            'batch', 'barsplot', 'batchload', 'boxchar', 'boxplot', 'bug_report', 'build_info',
            'catch', 'close', 'closefile', 'compfile', 'compile', 'compile_file',
            'current_let_rule_package', 'data_file_name', 'deactivate', 'debugmode',
            'define', 'define_variable', 'demo', 'dependencies', 'describe', 'dimacs_export',
            'dimacs_import', 'entermatrix', 'errcatch', 'error', 'error_size', 'error_syms', 'errormsg',
            'eval_string', 'example', 'feature', 'featurep', 'features', 'file_name',
            'file_output_append', 'file_search', 'file_search_demo', 'file_search_lisp',
            'file_search_maxima', 'file_search_tests', 'file_search_usage', 'file_type',
            'filename_merge', 'flength', 'fortindent', 'fortran', 'fortspaces', 'fposition', 'freshline',
            'functions', 'fundef', 'funmake', 'grind', 'gnuplot_file_name', 'gnuplot_out_file',
            'gnuplot_preamble', 'gnuplot_ps_term_command', 'gnuplot_term', 'inchar', 'infeval',
            'infolists', 'kill', 'killcontext', 'labels', 'leftjust', 'ldisp', 'ldisplay', 'linechar',
            'linel', 'linenum', 'linsolvewarn', 'load', 'load_pathname', 'loadfile',
            'loadprint', 'macroexpand', 'macroexpand1', 'macroexpansion', 'macros', 'manual_demo',
            'maxima_tempdir', 'maxima_userdir', 'multiplot_mode', 'myoptions', 'newline',
            'nolabels', 'opena', 'opena_binary', 'openr', 'openr_binary', 'openw',
            'openw_binary', 'outchar', 'packagefile', 'parse_string', 'pathname_directory', 'pathname_name',
            'pathname_type', 'pickapart', 'piece', 'playback', 'plotdf', 'print', 'print_graph',
            'printf', 'printfile', 'prompt', 'psfile', 'quit', 'read', 'read_array',
            'read_binary_array', 'read_binary_list', 'read_binary_matrix', 'read_hashed_array', 'read_list', 'read_matrix',
            'read_nested_list', 'read_xpm', 'readline', 'readonly', 'refcheck', 'rembox', 'remvalue',
            'remfunction', 'reset', 'rmxchar', 'room', 'run_testsuite', 'run_viewer', 'save',
            'savedef', 'scatterplot', 'starplot', 'stemplot', 'set_plot_option', 'setup_autoload',
            'setcheck', 'setcheckbreak', 'setval', 'showtime',
            'sparse6_export', 'sparse6_import', 'splice', 'sprint', 'status', 'stringout',
            'supcontext', 'system', 'tcl_output', 'terminal', 'tex', 'testsuite_files', 'throw',
            'time', 'timer', 'timer_devalue', 'timer_info', 'to_lisp', 'trace', 'trace_options',
            'transcompile', 'translate', 'translate_file', 'transrun', 'ttyoff', 'untimer',
            'untrace', 'user_preamble', 'values', 'with_stdout', 'write_binary_data',
            'write_data', 'writefile');

    /** @var array blacklist of CAS keywords forbidden to teachers. */
    // Note we allow RANDOM_PERMUTATION.
    private static $teachernotallow = array('%unitexpand', 'abasep', 'absboxchar', 'absolute_real_time', 'activate',
            'activecontexts', 'additive', 'adim', 'af', 'aform', 'agd', 'alg_type',
            'all_dotsimp_denoms', 'allsym', 'antid', 'antidiff', 'antidifference', 'antisymmetric',
            'arithmetic', 'arithsum', 'array', 'arrayapply', 'arrayinfo', 'arraymake', 'arrays',
            'assoc_legendre_p', 'assoc_legendre_q', 'asymbol', 'atensimp', 'atomgrad', 'atrig1',
            'atvalue', 'augmented_lagrangian_method', 'av', 'axis_3d', 'axis_bottom',
            'axis_left', 'axis_right', 'axis_top', 'azimut', 'backsubst', 'bars',
            'bashindices', 'bdvac', 'berlefact', 'bfpsi', 'bfpsi0', 'bimetric', 'bode_gain',
            'bode_phase', 'border', 'boundaries_array', 'canform', 'canten', 'cbffac',
            'cbrange', 'cbtics', 'cdisplay',
            'cframe_flag', 'cgeodesic', 'changename', 'chaosgame', 'chebyshev_t',
            'chebyshev_u', 'check_overlaps', 'checkdiv', 'christof', 'clear_rules', 'cmetric',
            'cnonmet_flag', 'cograd', 'collapse', 'colorbox', 'columns', 'combination',
            'comp2pui', 'components', 'concan', 'conmetderiv', 'constvalue', 'cont2part', 'context',
            'contexts', 'contortion', 'contour', 'contour_levels', 'contour_plot',
            'contract_edge', 'contragrad', 'contrib_ode', 'convert', 'coord', 'copy_graph',
            'covdiff', 'covers', 'create_list', 'csetup', 'ct_coords', 'ct_coordsys',
            'ctaylor', 'ctaypov', 'ctaypt', 'ctayswitch', 'ctayvar', 'ctorsion_flag', 'ctransform',
            'ctrgsimp', 'cunlisp', 'declare_constvalue', 'declare_dimensions',
            'declare_fundamental_dimensions', 'declare_fundamental_units', 'declare_qty',
            'declare_translated', 'declare_unit_conversion', 'declare_units', 'declare_weights',
            'decsym', 'default_let_rule_package', 'defcon', 'defmatch', 'defrule', 'delay', 'deleten',
            'diag', 'diagmatrixp', 'diagmetric', 'dim', 'dimension', 'dimensionless', 'dimensions',
            'dimensions_as_list', 'direct', 'disp', 'dispcon', 'dispflag',
            'dispform', 'dispfun', 'dispjordan', 'display', 'display2d', 'display_format_internal',
            'disprule', 'dispterms', 'distrib', 'domxexpt', 'domxmxops', 'domxnctimes', 'dotsimp',
            'draw', 'draw2d', 'draw3d', 'draw_file', 'draw_graph', 'draw_graph_program', 'dscalar',
            'einstein', 'elapsed_real_time', 'elapsed_run_time', 'ele2comp', 'ele2polynome',
            'ele2pui', 'elem', 'elevation', 'ellipse', 'enhanced3d', 'entermatrix', 'entertensor',
            'entier', 'eps_height', 'eps_width', 'ev_point', 'evflag', 'evfun', 'evolution',
            'evolution2d', 'evundiff', 'explicit', 'explose', 'expon', 'expop', 'expt', 'exsec',
            'extdiff', 'extract_linear_equations', 'f90', 'facts', 'fast_central_elements',
            'fast_linsolve', 'fb', 'file_bgcolor', 'fill_color', 'fill_density', 'fillarray',
            'filled_func', 'findde', 'fix', 'flipflag', 'flush', 'flush1deriv', 'flushd', 'flushnd',
            'font', 'font_size', 'forget', 'frame_bracket', 'fundamental_dimensions',
            'fundamental_units', 'gaussprob', 'gcdivide', 'gcfac', 'gd', 'gdet', 'gen_laguerre',
            'gensumnum', 'geomap', 'geometric', 'geosum', 'get', 'get_pixel',
            'get_plot_option', 'get_tex_environment', 'get_tex_environment_default', 'ggf',
            'ggfcfmax', 'ggfinfinity', 'globalsolve', 'gnuplot_close',
            'gnuplot_curve_styles', 'gnuplot_curve_titles', 'gnuplot_default_term_command',
            'gnuplot_dumb_term_command', 'gnuplot_pm3d', 'gnuplot_replot', 'gnuplot_reset',
            'gnuplot_restart', 'gnuplot_start', 'gosper', 'gosper_in_zeilberger', 'gospersum',
            'gr2d', 'gr3d', 'gradef', 'gradefs', 'graph6_decode', 'graph6_encode', 'graph6_export',
            'graph6_import', 'grid', 'grobner_basis', 'harmonic', 'hav',
            'head_angle', 'head_both', 'head_length', 'head_type', 'hermite', 'histogram', 'hodge',
            'ic_convert', 'icc1', 'icc2', 'ichr1', 'ichr2', 'icounter', 'icurvature', 'idiff',
            'idim', 'idummy', 'idummyx', 'ieqn', 'ieqnprint', 'ifb', 'ifc1', 'ifc2', 'ifg', 'ifgi',
            'ifr', 'iframe_bracket_form', 'iframes', 'ifri', 'ifs', 'igeodesic_coords',
            'igeowedge_flag', 'ikt1', 'ikt2', 'image', 'imetric', 'implicit', 'implicit_plot', 'implicit_derivative',
            'indexed_tensor', 'indices', 'infix', 'init_atensor',
            'init_ctensor', 'inm', 'inmc1', 'inmc2', 'inprod', 'intervalp', 'intopois', 'invariant1',
            'invariant2', 'invert_by_lu', 'ip_grid', 'ip_grid_in', 'ishow', 'isolate',
            'isolate_wrt_times', 'itr', 'jacobi_p', 'jf', 'jordan', 'julia',
            'kdels', 'kdelta', 'key', 'kinvariant', 'kostka', 'kt', 'label', 'label_alignment', 'label_orientation', 'laguerre',
            'lassociative', 'lbfgs', 'lbfgs_ncorrections', 'lbfgs_nfeval_max', 'lc2kdt', 'lc_l',
            'lc_u', 'lcharp', 'legendre_p', 'legendre_q', 'leinstein', 'let',
            'let_rule_packages', 'letrat', 'letrules', 'letsimp', 'levi_civita', 'lfg', 'lg',
            'lgtreillis', 'li', 'liediff', 'lindstedt', 'line_type', 'line_width', 'linear',
            'linear_program', 'linear_solver', 'lispdisp', 'list_nc_monomials',
            'listarray', 'listoftens', 'logand', 'logcb', 'logor', 'logxor', 'logz',
            'lorentz_gauge', 'lpart', 'lriem', 'lriemann', 'lsquares_estimates',
            'lsquares_estimates_approximate', 'lsquares_estimates_exact', 'lsquares_mse',
            'lsquares_residual_mse', 'lsquares_residuals', 'ltreillis', 'm1pbranch', 'mainvar',
            'make_array', 'make_level_picture', 'make_poly_continent', 'make_poly_country',
            'make_polygon', 'make_random_state', 'make_rgb_picture', 'makebox', 'makeorders',
            'mandelbrot', 'maperror', 'mat_function', 'max_ord', 'maxapplydepth', 'maxapplyheight',
            'maxi', 'maximize_lp', 'maxnegex', 'maxposex', 'maxpsifracdenom', 'maxpsifracnum',
            'maxpsinegint', 'maxpsiposint', 'maxtayorder', 'maybe', 'mesh', 'mesh_lines_color',
            'metricexpandall', 'mini', 'minimalpoly', 'minimize_lp', 'minor', 'mnewton',
            'mod_big_prime', 'mod_test', 'mod_threshold', 'mode_check_errorp', 'mode_check_warnp',
            'mode_checkp', 'mode_declare', 'mode_identity', 'modematrix', 'modular_linear_solver',
            'mon2schur', 'mono', 'monomial_dimensions', 'multi_elem', 'multi_orbit', 'multi_pui',
            'multinomial', 'multsym', 'natural_unit', 'nc_degree', 'negative_picture', 'newcontext',
            'newton', 'newtonepsilon', 'newtonmaxiter', 'nextlayerfactor', 'niceindices',
            'niceindicespref', 'nm', 'nmc', 'nonegative_lp', 'nonmetricity',
            'nonzeroandfreeof', 'noundisp', 'np', 'npi', 'nptetrad', 'ntermst', 'ntrig',
            'numbered_boundaries', 'ode2', 'ode_check', 'odelin', 'optimize', 'optimprefix',
            'optionset', 'orbit', 'orbits', 'orthopoly_recur', 'orthopoly_returns_intervals',
            'orthopoly_weight', 'outofpois', 'palette', 'parametric_surface',
            'pargosper', 'partpol', 'pdf_width', 'permut', 'permutation', 'petrov',
            'pic_height', 'pic_width', 'picture_equalp', 'picturep', 'piechart', 'plot2d',
            'plot3d', 'ploteq', 'plot_format', 'plot_options', 'plot_real_part', 'plsquares', 'pochhammer',
            'pochhammer_max_index', 'points_joined',
            'polar', 'polar_to_xy', 'polygon', 'prederror', 'primep_number_of_tests', 'printprops',
            'prodrac', 'product', 'product_use_gamma', 'programmode', 'proportional_axes', 'props',
            'propvars', 'psexpand', 'psi', 'pui', 'pui2comp', 'pui2ele', 'pui2polynome',
            'pui_direct', 'puireduc', 'qty', 'random', 'ratchristof',
            'rateinstein', 'rational', 'ratprint', 'ratriemann', 'ratweyl', 'ratwtlvl',
            'rearray', 'rectangle', 'rediff', 'redraw', 'reduce_consts', 'reduce_order',
            'region_boundaries', 'region_boundaries_plus', 'remarray', 'remcomps', 'remcon',
            'remcoord', 'remlet', 'remove_dimensions', 'remove_fundamental_dimensions',
            'remove_fundamental_units', 'rempart', 'remsym', 'rename', 'resolvante',
            'resolvante_alternee1', 'resolvante_bipartite', 'resolvante_diedrale',
            'resolvante_klein', 'resolvante_klein3', 'resolvante_produit_sym',
            'resolvante_unitaire', 'resolvante_vierer', 'revert', 'revert2', 'rgb2level',
            'ric', 'ricci', 'riem', 'riemann', 'rinvariant', 'rk', 'rot_horizontal',
            'rot_vertical', 'savefactors', 'scurvature', 'set_draw_defaults',
            'set_random_state', 'set_tex_environment', 'set_tex_environment_default',
            'set_up_dot_simplifications', 'setunits', 'setup_autoload', 'sf', 'showcomps',
            'similaritytransform', 'simplified_output',
            'simplify_products', 'simplify_sum', 'simplode', 'simpmetderiv', 'simtran',
            'solve_rec', 'solve_rec_rat', 'somrac', 'sparse6_decode', 'sparse6_encode', 'spherical_bessel_j',
            'spherical_bessel_y', 'spherical_hankel1', 'spherical_hankel2', 'spherical_harmonic',
            'split', 'sqrtdenest', 'sstatus', 'staircase', 'stardisp',
            'stirling', 'stirling1', 'stirling2', 'stringdisp',
            'summand_to_rec', 'surface_hide', 'symmetricp', 'tab', 'take_channel',
            'tcontract', 'tensorkill', 'tentex', 'timedate', 'title', 'totaldisrep', 'totient',
            'tpartpol', 'tr', 'tr_array_as_ref', 'tr_bound_function_applyp', 'tr_file_tty_messagesp',
            'tr_float_can_branch_complex', 'tr_function_call_default', 'tr_numer',
            'tr_optimize_max_loop', 'tr_semicompile', 'tr_state_vars', 'tr_warn_bad_function_calls',
            'tr_warn_fexpr', 'tr_warn_meval', 'tr_warn_mode', 'tr_warn_undeclared',
            'tr_warn_undefined_variable', 'tr_warnings_get', 'tr_windy', 'tracematrix',
            'transform_xy', 'transparent', 'treillis', 'treinat', 'trivial_solutions', 'tube',
            'tube_extremes', 'tutte_graph', 'ueivects', 'ufg', 'uforget', 'ug', 'ultraspherical',
            'undiff', 'unit_step', 'unit_vectors', 'uniteigenvectors', 'unitp', 'units',
            'unitvector', 'unknown', 'unorder', 'uric', 'uricci', 'uriem', 'uriemann',
            'use_fast_arrays', 'usersetunits', 'uvect', 'vector', 'verbose', 'vers', 'warnings',
            'weyl', 'wronskian', 'x_voxel', 'xaxis', 'xaxis_color', 'xaxis_secondary', 'xaxis_type',
            'xaxis_width', 'xrange', 'xrange_secondary', 'xtics', 'xtics_axis',
            'xtics_rotate', 'xtics_rotate_secondary', 'xtics_secondary', 'xtics_secondary_axis',
            'xu_grid', 'xy_file', 'xyplane', 'y_voxel', 'yaxis', 'yaxis_color', 'yaxis_secondary',
            'yaxis_type', 'yaxis_width', 'yrange', 'yrange_secondary', 'ytics',
            'ytics_axis', 'ytics_rotate', 'ytics_rotate_secondary', 'ytics_secondary',
            'ytics_secondary_axis', 'yv_grid', 'z_voxel', 'zaxis', 'zaxis_color', 'zaxis_type',
            'zaxis_width', 'zeilberger', 'zeroa', 'zerob', 'zlabel', 'zlange', 'zrange', 'ztics',
            'ztics_axis', 'ztics_rotate' );

    /** @var array CAS keywords defined by the distrib package.  These are ALLOWED by students. */
    private static $distrib    = array('cdf_bernoulli', 'cdf_beta', 'cdf_binomial', 'cdf_cauchy', 'cdf_chi2',
            'cdf_continuous_uniform', 'cdf_discrete_uniform', 'cdf_exp', 'cdf_f', 'cdf_gamma', 'cdf_general_finite_discrete',
            'cdf_geometric', 'cdf_gumbel', 'cdf_hypergeometric', 'cdf_laplace', 'cdf_logistic', 'cdf_lognormal',
            'cdf_negative_binomial', 'cdf_noncentral_chi2', 'cdf_noncentral_student_t', 'cdf_normal', 'cdf_pareto',
            'cdf_poisson', 'cdf_rayleigh', 'cdf_student_t', 'cdf_weibull', 'kurtosis_bernoulli', 'kurtosis_beta',
            'kurtosis_binomial', 'kurtosis_chi2', 'kurtosis_continuous_uniform', 'kurtosis_discrete_uniform',
            'kurtosis_exp', 'kurtosis_f', 'kurtosis_gamma', 'kurtosis_general_finite_discrete', 'kurtosis_geometric',
            'kurtosis_gumbel', 'kurtosis_gumbel', 'kurtosis_hypergeometric', 'kurtosis_laplace', 'kurtosis_logistic',
            'kurtosis_lognormal', 'kurtosis_negative_binomial', 'kurtosis_noncentral_chi2',
            'kurtosis_noncentral_student_t', 'kurtosis_normal', 'kurtosis_pareto', 'kurtosis_poisson', 'kurtosis_rayleigh',
            'kurtosis_student_t', 'kurtosis_weibull', 'mean_bernoulli', 'mean_beta', 'mean_binomial', 'mean_chi2',
            'mean_continuous_uniform', 'mean_discrete_uniform', 'mean_exp', 'mean_f', 'mean_gamma', 'mean_general_finite_discrete',
            'mean_geometric', 'mean_gumbel', 'mean_hypergeometric', 'mean_laplace', 'mean_logistic', 'mean_lognormal',
            'mean_negative_binomial', 'mean_noncentral_chi2', 'mean_noncentral_student_t', 'mean_normal', 'mean_pareto',
            'mean_poisson', 'mean_rayleigh', 'mean_student_t', 'mean_weibull', 'pdf_bernoulli', 'pdf_beta', 'pdf_binomial',
            'pdf_cauchy', 'pdf_chi2', 'pdf_continuous_uniform',
            'pdf_discrete_uniform', 'pdf_exp', 'pdf_f', 'pdf_gamma', 'pdf_general_finite_discrete', 'pdf_geometric',
            'pdf_gumbel', 'pdf_hypergeometric', 'pdf_laplace', 'pdf_logistic', 'pdf_lognormal', 'pdf_negative_binomial',
            'pdf_noncentral_chi2', 'pdf_noncentral_student_t', 'pdf_normal', 'pdf_pareto', 'pdf_poisson', 'pdf_rayleigh',
            'pdf_student_t', 'pdf_weibull', 'quantile_bernoulli', 'quantile_beta', 'quantile_binomial', 'quantile_cauchy',
            'quantile_chi2', 'quantile_continuous_uniform', 'quantile_discrete_uniform', 'quantile_exp', 'quantile_f',
            'quantile_gamma', 'quantile_general_finite_discrete', 'quantile_geometric', 'quantile_gumbel',
            'quantile_hypergeometric', 'quantile_laplace', 'quantile_logistic', 'quantile_lognormal',
            'quantile_negative_binomial', 'quantile_noncentral_chi2', 'quantile_noncentral_student_t', 'quantile_normal',
            'quantile_pareto', 'quantile_poisson', 'quantile_rayleigh',
            'quantile_student_t', 'quantile_weibull', 'random_bernoulli', 'random_beta', 'random_binomial', 'random_cauchy',
            'random_chi2', 'random_continuous_uniform', 'random_discrete_uniform', 'random_exp', 'random_f', 'random_gamma',
            'random_general_finite_discrete', 'random_geometric', 'random_gumbel', 'random_hypergeometric',
            'random_laplace', 'random_logistic', 'random_lognormal', 'random_negative_binomial', 'random_noncentral_chi2',
            'random_noncentral_student_t', 'random_normal', 'random_pareto', 'random_poisson', 'random_rayleigh',
            'random_student_t', 'random_weibull', 'skewness_bernoulli', 'skewness_beta', 'skewness_binomial',
            'skewness_chi2', 'skewness_continuous_uniform', 'skewness_discrete_uniform', 'skewness_exp',
            'skewness_f', 'skewness_gamma', 'skewness_general_finite_discrete', 'skewness_geometric',
            'skewness_gumbel', 'skewness_hypergeometric', 'skewness_laplace', 'skewness_logistic',
            'skewness_lognormal', 'skewness_negative_binomial', 'skewness_noncentral_chi2', 'skewness_noncentral_student_t',
            'skewness_normal', 'skewness_pareto', 'skewness_poisson', 'skewness_rayleigh',
            'skewness_student_t', 'skewness_weibull', 'std_bernoulli', 'std_beta', 'std_binomial', 'std_chi2',
            'std_continuous_uniform', 'std_discrete_uniform', 'std_exp', 'std_f', 'std_gamma', 'std_general_finite_discrete',
            'std_geometric', 'std_gumbel', 'std_hypergeometric', 'std_laplace', 'std_logistic',
            'std_lognormal', 'std_negative_binomial', 'std_noncentral_chi2', 'std_noncentral_student_t', 'std_normal',
            'std_pareto', 'std_poisson', 'std_rayleigh', 'std_student_t', 'std_weibull', 'var_bernoulli',
            'var_beta', 'var_binomial', 'var_chi2', 'var_continuous_uniform', 'var_discrete_uniform', 'var_exp', 'var_f',
            'var_gamma', 'var_general_finite_discrete', 'var_geometric', 'var_gumbel', 'var_hypergeometric',
            'var_laplace', 'var_logistic', 'var_lognormal', 'var_negative_binomial', 'var_noncentral_chi2',
            'var_noncentral_student_t', 'var_normal', 'var_pareto', 'var_poisson',
            'var_rayleigh', 'var_student_t', 'var_weibull');

    /** @var array CAS keywords ALLOWED by students. */
    private static $studentallow    = array('%c', '%e', '%gamma', '%i', '%k1', '%k2',
            '%phi', '%pi', 'abs', 'absint', 'acos', 'acosh', 'acot', 'acoth', 'acsc', 'acsch',
            'addmatrices', 'adjoin', 'and', 'ascii', 'asec', 'asech', 'asin', 'asinh', 'atan',
            'atan2', 'atanh', 'augcoefmatrix', 'axes', 'belln', 'bessel_i', 'bessel_j', 'bessel_k',
            'bessel_y', 'besselexpand', 'beta', 'bezout', 'bffac', 'bfhzeta', 'bfloat',
            'bfloatp', 'binomial', 'black', 'blockmatrixp', 'blue', 'box', 'burn', 'cabs', 'cardinality', 'carg',
            'cartan', 'cartesian_product', 'ceiling', 'cequal', 'cequalignore', 'cf',
            'cfdisrep', 'cfexpand', 'cflength', 'cgreaterp', 'cgreaterpignore', 'charat',
            'charfun', 'charfun2', 'charlist', 'charp', 'charpoly', 'cint', 'clessp',
            'clesspignore', 'coeff', 'coefmatrix', 'col', 'columnop', 'columnspace',
            'columnswap', 'combine', 'compare', 'concat', 'conjugate', 'cons', 'constituent',
            'copy', 'cos', 'cosh', 'cot', 'coth', 'color', 'covect', 'csc', 'csch', 'cspline', 'cyan', 'cosec',
            'ctranspose', 'dblint', 'defint', 'del', 'delete', 'delta', 'denom', 'desolve',
            'determinant', 'detout', 'dgauss_a', 'dgauss_b', 'diag_matrix', 'diagmatrix',
            'diff', 'digitcharp', 'disjoin', 'disjointp', 'disolate', 'divide', 'divisors',
            'divsum', 'dkummer_m', 'dkummer_u', 'dotproduct', 'echelon', 'eigenvalues',
            'eigenvectors', 'eighth', 'eivals', 'eivects', 'elementp', 'eliminate',
            'elliptic_e', 'elliptic_ec', 'elliptic_eu', 'elliptic_f', 'elliptic_kc',
            'elliptic_pi', 'ematrix', 'emptyp', 'endcons', 'epsilon_lp', 'equal', 'equalp',
            'equiv_classes', 'erf', 'euler', 'ev', 'eval', 'evenp', 'every', 'exp', 'expand',
            'expandwrt', 'expandwrt_denom', 'expandwrt_factored', 'express', 'extremal_subset',
            'ezgcd', 'facsum', 'facsum_combine', 'factcomb', 'factlim', 'factor',
            'factorfacsum', 'factorial', 'factorout', 'factorsum', 'false', 'fasttimes', 'fft',
            'fib', 'fibtophi', 'fifth', 'find_root', 'find_root_abs', 'find_root_error',
            'find_root_rel', 'first', 'flatten', 'float', 'float2bf', 'floor', 'fourcos',
            'fourexpand', 'fourier', 'fourint', 'fourintcos', 'fourintsin', 'foursimp',
            'foursin', 'fourth', 'freeof', 'full_listify', 'fullmap', 'fullmapl', 'fullratsimp',
            'fullratsubst', 'fullsetify', 'funcsolve', 'funp', 'gamma', 'gamma_incomplete',
            'gamma_incomplete_generalized', 'gamma_incomplete_regularized', 'gauss_a',
            'gauss_b', 'gcd', 'gcdex', 'gcfactor', 'genmatrix', 'get_lu_factors', 'gfactor',
            'gfactorsum', 'gramschmidt', 'green', 'hankel', 'hessian', 'hgfred', 'hilbert_matrix',
            'hipow', 'horner', 'hypergeometric', 'hypergeometric_representation', 'ident',
            'identfor', 'identity', 'ifactors', 'imagpart', 'ind', 'inf', 'infinity',
            'innerproduct', 'inrt', 'integer_partitions', 'integrate', 'intersect',
            'intersection', 'intosum', 'inv_mod', 'inverse_jacobi_cd', 'inverse_jacobi_cn',
            'inverse_jacobi_cs', 'inverse_jacobi_dc', 'inverse_jacobi_dn', 'inverse_jacobi_ds',
            'inverse_jacobi_nc', 'inverse_jacobi_nd', 'inverse_jacobi_ns', 'inverse_jacobi_sc',
            'inverse_jacobi_sd', 'inverse_jacobi_sn', 'invert', 'isqrt', 'jacobi', 'jacobi_cd',
            'jacobi_cn', 'jacobi_cs', 'jacobi_dc', 'jacobi_dn', 'jacobi_ds', 'jacobi_nc',
            'jacobi_nd', 'jacobi_ns', 'jacobi_sc', 'jacobi_sd', 'jacobi_sn', 'jacobian', 'join',
            'kron_delta', 'kronecker_product', 'kummer_m', 'kummer_u', 'lagrange', 'lambda',
            'lambert_w', 'laplace', 'last', 'lcm', 'ldefint', 'legend', 'length', 'lhs', 'limit',
            'linearinterpol', 'linsolve', 'linsolve_params', 'listify', 'lmax', 'lmin',
            'locate_matrix_entry', 'log', 'logy', 'logx', 'log10', 'log_gamma', 'logabs', 'logarc',
            'logcontract', 'logexpand', 'lognegint', 'lognumer', 'logy', 'logsimp', 'lopow',
            'lowercasep', 'lratsubst', 'lreduce', 'lsum', 'lu_backsub', 'lu_factor', 'magenta',
            'make_transform', 'makefact', 'makegamma', 'makelist', 'makeset', 'map',
            'mapatom', 'maplist', 'mat_cond', 'mat_fullunblocker', 'mat_norm', 'mat_trace',
            'mat_unblocker', 'matrix', 'matrix_element_add', 'matrix_element_mult',
            'matrix_element_transpose', 'matrix_size', 'matrixmap', 'matrixp', 'mattrace',
            'max', 'member', 'min', 'minf', 'minfactorial', 'mod', 'moebius',
            'multinomial_coeff', 'multthru', 'ncexpt', 'ncharpoly', 'newdet', 'ninth',
            'noeval', 'nonnegintegerp', 'not', 'notequal', 'nroots', 'nterms', 'nthroot', 'nticks',
            'nullity', 'nullspace', 'num', 'num_distinct_partitions', 'num_partitions',
            'numberp', 'numer', 'numerval', 'numfactor', 'nusum', 'nzeta', 'nzetai', 'nzetar',
            'oddp', 'op', 'operatorp', 'or', 'ordergreat', 'ordergreatp', 'orderless',
            'orderlessp', 'orthogonal_complement', 'outermap', 'pade', 'parabolic_cylinder_d',
            'part', 'part2cont', 'partfrac', 'partition', 'partition_set', 'permanent',
            'permutations', 'plog', 'plot_realpart', 'point_type', 'point_size', 'points',
            'poisdiff', 'poisexpt', 'poisint', 'poislim', 'poismap',
            'poisplus', 'poissimp', 'poisson', 'poissubst', 'poistimes', 'poistrim',
            'polarform', 'polartorect', 'polymod', 'polynome2ele', 'polynomialp',
            'polytocompanion', 'posfun', 'potential', 'power_mod', 'powerdisp', 'powers',
            'powerseries', 'powerset', 'primep', 'printpois', 'quad_qag', 'quad_qagi',
            'quad_qags', 'quad_qawc', 'quad_qawf', 'quad_qawo', 'quad_qaws', 'qunit',
            'quotient', 'radcan', 'radexpand', 'radsubstflag', 'rank', 'rassociative', 'rat',
            'ratalgdenom', 'ratcoef', 'ratdenom', 'ratdenomdivide', 'ratdiff', 'ratdisrep',
            'ratepsilon', 'ratexpand', 'ratfac', 'rationalize', 'ratmx', 'ratnumer', 'ratnump',
            'ratp', 'ratsimp', 'ratsimpexpons', 'ratsubst', 'ratvars', 'ratweight',
            'ratweights', 'realonly', 'realpart', 'realroots', 'rectform', 'recttopolar', 'red',
            'remainder', 'remfun', 'residue', 'rest', 'resultant', 'reverse', 'rhs', 'risch',
            'rncombine', 'romberg', 'rombergabs', 'rombergit', 'rombergmin', 'rombergtol',
            'rootsconmode', 'rootscontract', 'rootsepsilon', 'round', 'row', 'rowop', 'rowswap',
            'rreduce', 'scalarmatrixp', 'scalarp', 'scaled_bessel_i', 'scaled_bessel_i0',
            'scaled_bessel_i1', 'scalefactors', 'scanmap', 'schur2comp', 'sconcat', 'scopy',
            'scsimp', 'sdowncase', 'sec', 'sech', 'second', 'sequal', 'sequalignore',
            'set_partitions', 'setdifference', 'setequalp', 'setify', 'setp', 'seventh',
            'sexplode', 'sign', 'signum', 'simpsum', 'sin', 'sinh', 'sinnpiflag', 'sinsert',
            'sinvertcase', 'sixth', 'slength', 'smake', 'smismatch', 'solve', 'solvedecomposes',
            'solveexplicit', 'solvefactors', 'solvenullwarn', 'solveradcan', 'solvetrigwarn',
            'some', 'sort', 'space', 'sparse', 'specint', 'sposition', 'sqfr', 'sqrt',
            'sqrtdispflag', 'sremove', 'sremovefirst', 'sreverse', 'ssearch', 'ssort', 'ssubst',
            'ssubstfirst', 'strim', 'striml', 'strimr', 'stringp', 'struve_h', 'struve_l', 'style',
            'sublis', 'sublis_apply_lambda', 'sublist', 'sublist_indices', 'submatrix',
            'subset', 'subsetp', 'subst', 'substinpart', 'substpart', 'substring', 'subvarp',
            'sum', 'sumcontract', 'sumexpand', 'supcase', 'symbolp', 'symmdifference', 'tan',
            'tanh', 'taylor', 'taylor_logexpand', 'taylor_order_coefficients',
            'taylor_simplifier', 'taylor_truncate_polynomials', 'taylordepth', 'taylorinfo',
            'taylorp', 'taytorat', 'tellsimp', 'tellsimpafter', 'tenth', 'third', 'tlimit',
            'tlimswitch', 'todd_coxeter', 'toeplitz', 'transpose', 'tree_reduce',
            'triangularize', 'trigexpand', 'trigexpandplus', 'trigexpandtimes', 'triginverses',
            'trigrat', 'trigreduce', 'trigsign', 'trigsimp', 'true', 'trunc', 'und', 'union',
            'unique', 'unsum', 'untellrat', 'uppercasep', 'vandermonde_matrix', 'vect_cross',
            'vectorpotential', 'vectorsimp', 'xreduce', 'xthru', 'zerobern', 'zeroequiv',
            'zerofor', 'zeromatrix', 'zeromatrixp', 'zeta', 'zeta%pi', 'pi', 'e', 'i', 'float',
            'round', 'truncate', 'decimalplaces', 'anyfloat', 'anyfloatex', 'expand', 'expandp',
            'simplify', 'divthru', 'factor', 'factorp', 'diff', 'int', 'rand', 'plot',
            'plot_implicit', 'stack_validate_typeless', 'stack_validate', 'alpha', 'nu', 'beta',
            'xi', 'gamma', 'omicron', 'delta', 'pi', 'epsilon', 'rho', 'zeta', 'sigma', 'eta',
            'tau', 'theta', 'upsilon', 'iota', 'phi', 'kappa', 'chi', 'lambda', 'psi', 'mu',
            'omega', 'parametric', 'discrete', 'xlabel', 'ylabel');

    /**
     * These lists are used by question authors for groups of words.
     * They should be lower case, because Maxima is lower case, and these correspond to Maxima names.
     */
    private static $keywordlists = array(
            '[[basic-algebra]]' => array('coeff', 'concat', 'conjugate', 'cspline', 'disjoin', 'divisors',
                    'ev', 'eliminate', 'equiv_classes', 'expand', 'expandwrt', 'facsum', 'factor', 'find_root',
                    'fullratsimp', 'gcd', 'gfactor', 'imagpart', 'intersection', 'lcm', 'logcontract', 'logexpand',
                    'member', 'nroots', 'nthroot', 'numer', 'partfrac', 'polarform', 'polartorect', 'ratexpand',
                    'ratsimp', 'realpart', 'round', 'radcan', 'num', 'denom', 'trigsimp', 'trigreduce', 'solve',
                    'allroots', 'simp', 'setdifference', 'sort', 'subst', 'trigexpand', 'trigexpandplus',
                    'trigexpandtimes', 'triginverses', 'trigrat', 'trigreduce', 'trigsign', 'trigsimp',
                    'truncate', 'decimalplaces', 'simplify'),
            '[[basic-calculus]]' => array('defint', 'diff', 'int', 'integrate', 'limit', 'partial', 'desolve',
                    'express', 'taylor'),
            '[[basic-matrix]]' => array('addmatrices', 'adjoin', 'augcoefmatrix', 'blockmatrixp', 'charpoly',
                    'coefmatrix', 'col', 'columnop', 'columnspace', 'columnswap', 'covect', 'ctranspose',
                    'determinant', ' diag_matrix', 'diagmatrix', 'dotproduct', 'echelon', 'eigenvalues',
                    'eigenvectors', 'eivals', 'eivects', 'ematrix', 'invert', 'matrix_element_add',
                    'matrix_element_mult', 'matrix_element_transpose', 'nullspace', 'resultant',
                    'rowop', 'rowswap', 'transpose')
    );

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
        $this->answernote = array();

        $this->valid          = null;  // If null then the validate command has not yet been run.

        if (!is_string($this->rawcasstring)) {
            throw new stack_exception('stack_cas_casstring: rawstring must be a string.');
        }

    }

    /*********************************************************/
    /* Validation functions                                  */
    /*********************************************************/

    /* We may need to use this function more than once to validate with different options.
     * $secutrity must either be 's' for student, or 't' for teacher.
     * $syntax is whether we enforce a "strict syntax".
     * $insertstars is whether we actually put stars into the places we expect them to go.
     *              0 - don't insert stars
     *              1 - insert stars
     *              2 - assume single letter variables only.
     * $allowwords enables specific function names (but never those from $globalforbid)
     */
    private function validate($security='s', $syntax=true, $insertstars=0, $allowwords='') {

        if (!('s' === $security || 't' === $security)) {
            throw new stack_exception('stack_cas_casstring: security level, must be "s" or "t" only.');
        }

        if (!is_bool($syntax)) {
            throw new stack_exception('stack_cas_casstring: syntax, must be Boolean.');
        }

        if (!is_int($insertstars)) {
            throw new stack_exception('stack_cas_casstring: insertstars, must be an integer.');
        }

        $this->valid     = true;
        $this->casstring = $this->rawcasstring;
        $cmd             = $this->rawcasstring;

        // CAS strings must be non-empty.
        if (trim($this->casstring) == '') {
            $this->answernote[] = 'empty';
            $this->valid = false;
            return false;
        }

        // CAS strings may not contain @ or $.
        if (strpos($cmd, '@') !== false || strpos($cmd, '$') !== false) {
            $this->add_error(stack_string('illegalcaschars'));
            $this->answernote[] = 'illegalcaschars';
            $this->valid = false;
            return false;
        }

        // Check for matching string delimiters.
        $cmdsafe = str_replace('\"', '', $cmd);
        if (stack_utils::check_matching_pairs($cmdsafe, '"') == false) {
            $this->errors .= stack_string('stackCas_MissingString');
            $this->answernote[] = 'MissingString';
            $this->valid = false;
        }

        // Now remove any strings from the $cmd.
        list($cmd, $strings) = $this->strings_remove($cmd);

        // Search for HTML fragments.  This is hard to do because < is an infix operator!
        // We cannot search for arbitrary closing tags, e.g. for the pattern '</' because
        // we pass back strings with HTML in when we have already evaluated plots!
        $htmlfragments = array('<span', '</span>', '<p>', '</p>');
        foreach ($htmlfragments as $frag) {
            if (strpos($cmd, $frag) !== false) {
                $this->add_error(stack_string('htmlfragment').' <pre>'.$this->strings_replace($cmd, $strings).'</pre>');
                $this->answernote[] = 'htmlfragment';
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
                $cmds = str_replace(' ', '<font color="red">_</font>', $this->strings_replace($cmd, $strings));
                $this->add_error(stack_string('stackCas_spaces', array('expr' => stack_maxima_format_casstring($cmds))));
                $this->answernote[] = 'spaces';
                $this->valid = false;
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
                    $this->add_error(stack_string('stackCas_percent',
                            array('expr' => stack_maxima_format_casstring($this->strings_replace($cmd, $strings)))));
                    $this->answernote[] = 'percent';
                    $this->valid   = false;
                }
            }
        }

        $inline = stack_utils::check_bookends($cmd, '(', ')');
        if ($inline !== true) { // The method check_bookends does not return false.
            $this->valid = false;
            if ($inline == 'left') {
                $this->answernote[] = 'missingLeftBracket';
                $this->add_error(stack_string('stackCas_missingLeftBracket',
                    array('bracket' => '(', 'cmd' => stack_maxima_format_casstring($this->strings_replace($cmd, $strings)))));
            } else {
                $this->answernote[] = 'missingRightBracket';
                $this->add_error(stack_string('stackCas_missingRightBracket',
                    array('bracket' => ')', 'cmd' => stack_maxima_format_casstring($this->strings_replace($cmd, $strings)))));
            }
        }
        $inline = stack_utils::check_bookends($cmd, '{', '}');
        if ($inline !== true) { // The method check_bookends does not return false.
            $this->valid = false;
            if ($inline == 'left') {
                $this->answernote[] = 'missingLeftBracket';
                $this->add_error(stack_string('stackCas_missingLeftBracket',
                 array('bracket' => '{', 'cmd' => stack_maxima_format_casstring($this->strings_replace($cmd, $strings)))));
            } else {
                $this->answernote[] = 'missingRightBracket';
                $this->add_error(stack_string('stackCas_missingRightBracket',
                 array('bracket' => '}', 'cmd' => stack_maxima_format_casstring($this->strings_replace($cmd, $strings)))));
            }
        }
        $inline = stack_utils::check_bookends($cmd, '[', ']');
        if ($inline !== true) { // The method check_bookends does not return false.
            $this->valid = false;
            if ($inline == 'left') {
                $this->answernote[] = 'missingLeftBracket';
                $this->add_error(stack_string('stackCas_missingLeftBracket',
                 array('bracket' => '[', 'cmd' => stack_maxima_format_casstring($this->strings_replace($cmd, $strings)))));
            } else {
                $this->answernote[] = 'missingRightBracket';
                $this->add_error(stack_string('stackCas_missingRightBracket',
                 array('bracket' => ']', 'cmd' => stack_maxima_format_casstring($this->strings_replace($cmd, $strings)))));
            }
        }

        if (!stack_utils::check_nested_bookends($cmd)) {
            $this->valid = false;
            $this->add_error(stack_string('stackCas_bracketsdontmatch',
                     array('cmd' => stack_maxima_format_casstring($this->strings_replace($cmd, $strings)))));
        }

        if ($security == 's') {
            // Check for apostrophes if a student.
            if (strpos($cmd, "'") !== false) {
                $this->add_error(stack_string('stackCas_apostrophe'));
                $this->answernote[] = 'apostrophe';
                $this->valid = false;
            }
            // Check new lines.
            if (strpos($cmd, "\n") !== false) {
                $this->add_error(stack_string('stackCas_newline'));
                $this->answernote[] = 'newline';
                $this->valid = false;
            }
        }

        if ($security == 's') {
            // Check for bad looking trig functions, e.g. sin^2(x) or tan*2*x
            // asin etc, will be included automatically, so we don't need them explicitly.
            $triglist = array('sin', 'cos', 'tan', 'sinh', 'cosh', 'tanh', 'sec', 'cosec', 'cot', 'csc', 'coth', 'csch', 'sech');
            $funlist  = array('log', 'ln', 'lg', 'exp', 'abs', 'sqrt');
            foreach (array_merge($triglist, $funlist) as $fun) {
                if (strpos($cmd, $fun.'^') !== false) {
                    $this->add_error(stack_string('stackCas_trigexp',
                        array('forbid' => stack_maxima_format_casstring($fun.'^'))));
                    $this->answernote[] = 'trigexp';
                    $this->valid = false;
                    break;
                }
                if (strpos($cmd, $fun.'[') !== false) {
                    $this->add_error(stack_string('stackCas_trigparens',
                        array('forbid' => stack_maxima_format_casstring($fun.'(x)'))));
                    $this->answernote[] = 'trigparens';
                    $this->valid = false;
                    break;
                }
                $opslist = array('*', '+', '-', '/');
                foreach ($opslist as $op) {
                    if (strpos($cmd, $fun.$op) !== false) {
                        $this->add_error(stack_string('stackCas_trigop',
                            array('trig' => stack_maxima_format_casstring($fun),
                                    'forbid' => stack_maxima_format_casstring($fun.$op))));
                        $this->answernote[] = 'trigop';
                        $this->valid = false;
                        break;
                    }
                }
            }
            foreach ($triglist as $fun) {
                if (strpos($cmd, 'arc'.$fun) !== false) {
                    $this->add_error(stack_string('stackCas_triginv',
                        array('badinv' => stack_maxima_format_casstring('arc'.$fun),
                                'goodinv' => stack_maxima_format_casstring('a'.$fun))));
                    $this->answernote[] = 'triginv';
                    $this->valid = false;
                    break;
                }
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
            $this->add_error(stack_string('stackCas_forbiddenChar', array( 'char' => implode(", ", array_unique($invalidchars)))));
            $this->answernote[] = 'forbiddenChar';
            $this->valid = false;
        }

        // Check for disallowed final characters,  / * + - ^ £ # = & ~ |, ? : ;.
        $disallowedfinalcharsregex = '~[' . preg_quote(self::$disallowedfinalchars, '~') . ']$~u';
        if (preg_match($disallowedfinalcharsregex, $cmd, $match)) {
            $this->valid = false;
            $a = array();
            $a['char'] = $match[0];
            $a['cmd']  = stack_maxima_format_casstring($this->strings_replace($cmd, $strings));
            $this->add_error(stack_string('stackCas_finalChar', $a));
            $this->answernote[] = 'finalChar';
        }

        // Check for empty parentheses `()`.
        if (strpos($cmd, '()') !== false) {
            $this->valid = false;
            $this->add_error(stack_string('stackCas_forbiddenWord', array('forbid' => stack_maxima_format_casstring('()'))));
            $this->answernote[] = 'forbiddenWord';
        }

        // Check for spurious operators.
        $spuriousops = array('<>', '||', '&', '..', ',,', '/*', '*/');
        foreach ($spuriousops as $op) {
            if (substr_count($cmd, $op) > 0) {
                $this->valid = false;
                $a = array();
                $a['cmd']  = stack_maxima_format_casstring($op);
                $this->add_error(stack_string('stackCas_spuriousop', $a));
                $this->answernote[] = 'spuriousop';
            }
        }

        // CAS strings may not contain
        // * reversed inequalities, i.e =< is not permitted in place of <=.
        // * chained inequalities 1<x<=3.
        if (strpos($cmd, '=<') !== false || strpos($cmd, '=>') !== false) {
            if (strpos($cmd, '=<') !== false) {
                $a['cmd'] = stack_maxima_format_casstring('=<');
            } else {
                $a['cmd'] = stack_maxima_format_casstring('=>');
            }
            $this->add_error(stack_string('stackCas_backward_inequalities', $a));
            $this->answernote[] = 'backward_inequalities';
            $this->valid = false;
        } else if (!($this->check_chained_inequalities($cmd))) {
            $this->add_error(stack_string('stackCas_chained_inequalities'));
            $this->answernote[] = 'chained_inequalities';
            $this->valid = false;
        }

        // Commas not inside brackets either should be, or indicate a decimal number not
        // using the decimal point.  In either case this is problematic.
        // For now, we just look for expressions with a comma, but without brackets.
        // [TODO]: improve this test to really look for unencapsulated commas.
        if (!(false === strpos($cmd, ',')) && !(!(false === strpos($cmd, '(')) ||
                !(false === strpos($cmd, '[')) || !(false === strpos($cmd, '{')) )) {
            $this->add_error(stack_string('stackCas_unencpsulated_comma'));
            $this->answernote[] = 'unencpsulated_comma';
            $this->valid = false;
        }

        $this->check_stars($security, $syntax, $insertstars);

        $this->check_security($security, $allowwords);

        $this->key_val_split();
        return $this->valid;
    }

    /**
     * Checks that there are no *s missing from expressions, eg 2x should be 2*x
     *
     * @return bool|string true if no missing *s, false if missing stars but automatically added
     * If stack is set to not add stars automatically, a string indicating the missing stars is returned.
     */
    private function check_stars($security, $syntax, $insertstars) {

        // Some patterns are always invalid syntax, and must have stars.
        $patterns[] = "|(\))(\()|";                   // Simply the pattern ")(".  Must be wrong!
        $patterns[] = "|(\))([0-9A-Za-z])|";          // E.g. )a, or )3.
        // We assume f and g are single letter functions.
        // 'E' and 'e' is used to denote scientific notation.
        // E.g. 3E2 = 300.0 or 3e-2 = 0.03.
        if ($syntax) {
            $patterns[] = "|([0-9]+)([A-DF-Za-dh-z])|";  // E.g. 3x.
            $patterns[] = "|([0-9])([A-DF-Za-dh-z]\()|"; // E.g. 3x(.
        } else {
            $patterns[] = "|([0-9]+)([A-Za-z])|";     // E.g. 3x.
            $patterns[] = "|([0-9])([A-Za-z]\()|";    // E.g. 3x(.
        }

        if ($security == 's') {
            $patterns[] = "|([0-9]+)(\()|";           // E.g. 3212 (.
            $patterns[] = "|(\Wi)(\()|";    // I.e. i( , the single pattern of i with a bracket, which is always wrong for students.
            if (!$syntax) {
                $patterns[] = "|(^[A-Za-z])(\()|";    // E.g. a( , that is a single letter.
                $patterns[] = "|(\*[A-Za-z])(\()|";
                $patterns[] = "|(\-[A-Za-z])(\()|";
                $patterns[] = "|(/[A-Za-z])(\()|";
                $patterns[] = "|([A-Za-z])([0-9]+)|"; // E.g. x3.
            }
        }

        /* Note to self: the "Assume single character variable names" option is actually
           carried out in Maxima, not using the regular expressions here.  This ensures that
           legitimate function names are not converted into lists of variables.  E.g. we want
           sin(nx)->sin(n*x) and NOT s*i*n*(n*x).  For this code see stackmaxima.mac.
        */

        // Loop over every CAS command checking for missing stars.
        $missingstar     = false;
        $missingstring   = '';

        // Prevent ? characters calling LISP or the Maxima help file.  Instead, these pass through and are displayed as normal.
        $cmd = str_replace('?', 'QMCHAR', $this->rawcasstring);

        // Remove the contents of any strings, so we don't test for missing *s within them.
        list ($cmd, $strings) = $this->strings_remove($cmd);

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

        $cmd = $this->strings_replace($cmd, $strings);
        $missingstring = $this->strings_replace($missingstring, $strings);

        if (false == $missingstar) {
            // If no missing stars return true.
            return true;
        }
        // Guard clause above - we have missing stars detected.
        $this->answernote[] = 'missing_stars';
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
    private function check_security($security, $allowwords) {

        // Sort out any allowwords.
        $allow = array();
        if (trim($allowwords) != '') {
            $allowwords = explode(',', $allowwords);
            foreach ($allowwords as $kw) {
                if (!in_array(strtolower($kw), self::$globalforbid)) {
                    $allow[] = trim($kw);
                } else {
                    throw new stack_exception('stack_cas_casstring: check_security: ' .
                            'attempt made to allow gloabally forbidden keyword: ' . $kw);
                }
            }
        }

        // Note, we do not strip out strings here.  This would be a potential secuity risk.
        // Teachers are trusted with any name already, and we would never permit a:"system('rm *')" as a string!
        // The contents of any string which look bad, probably is bad.
        $cmd = $this->casstring;
        $strinkeywords = array();
        $pat = "|[\?_A-Za-z0-9]+|";
        preg_match_all($pat, $cmd, $out, PREG_PATTERN_ORDER);
        // Filter out some of these matches.
        foreach ($out[0] as $key) {
            // Do we have only numbers, or only 2 characters?
            // These strings are fine.
            preg_match("|[0-9]+|", $key, $justnum);

            if (empty($justnum) and strlen($key) > 2) {
                array_push($strinkeywords, $key);
            }
            // This is not really a security issue, but it relies on access to the $allowwords.
            // It is also a two letter string, which are normally permitted.
            if ($security == 's' and $key == 'In' and !in_array($key, $allow)) {
                $this->add_error(stack_string('stackCas_badLogIn'));
                $this->answernote[] = 'stackCas_badLogIn';
                $this->valid = false;
            }
        }

        $strinkeywords = array_unique($strinkeywords);
        // Check for global forbidden words.
        foreach ($strinkeywords as $key) {
            if (in_array(strtolower($key), self::$globalforbid)) {
                // Very bad!
                $this->add_error(stack_string('stackCas_forbiddenWord',
                        array('forbid' => stack_maxima_format_casstring(strtolower($key)))));
                $this->answernote[] = 'forbiddenWord';
                $this->valid = false;
            } else {
                if ($security == 't') {
                    if (in_array($key, self::$teachernotallow)) {
                        // If a teacher check against forbidden commands.
                        $this->add_error(stack_string('stackCas_unsupportedKeyword',
                            array('forbid' => stack_maxima_format_casstring($key))));
                        $this->answernote[] = 'unsupportedKeyword';
                        $this->valid = false;
                    }
                } else {
                    // Only allow the student to use set commands.
                    if (!in_array($key, self::$studentallow) and !in_array($key, self::$distrib) and !in_array($key, $allow)) {
                        if (!in_array(strtolower($key), self::$studentallow) and !in_array(strtolower($key), self::$distrib)
                                and !in_array(strtolower($key), $allow)) {
                            $this->add_error(stack_string('stackCas_unknownFunction',
                                array('forbid' => stack_maxima_format_casstring($key))));
                            $this->answernote[] = 'unknownFunction';
                        } else {
                            $this->add_error(stack_string('stackCas_unknownFunctionCase',
                                array('forbid' => stack_maxima_format_casstring($key),
                                        'lower' => stack_maxima_format_casstring(strtolower($key)))));
                            $this->answernote[] = 'unknownFunctionCase';
                        }
                        $this->valid = false;
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

        if (substr_count($ex, '<') + substr_count($ex, '>') < 2) {
            return true;
        }

        // Plots, and HTML elements are protected within strings when they come back through the CAS.
        $found = stack_utils::substring_between($ex, '<html>', '</html>');
        if ($found[1] > 0) {
            $ex = str_replace($found[0], '', $ex);
        }

        // Separate out lists, sets, etc.
        $exsplit = explode(',', $ex);
        $bits = array();
        $ok = true;
        foreach ($exsplit as $bit) {
            $ok = $ok && $this->check_chained_inequalities_ind($bit);
        }

        return $ok;
    }

    private function check_chained_inequalities_ind($ex) {

        if (substr_count($ex, '<') + substr_count($ex, '>') < 2) {
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
        unset($bits[count($bits) - 1]);
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
        if (null === $this->valid) {
            $this->validate();
        }

        // Ensure all $keywords are lower case.
        // Replace lists of keywords with their actual values.
        $kws = array();
        foreach ($keywords as $val) {
            $kw = trim(strtolower($val));
            if (array_key_exists($kw, self::$keywordlists)) {
                $kws = array_merge($kws, self::$keywordlists[$kw]);
            } else {
                $kws[] = $kw;
            }
        }

        $found          = false;
        $strinkeywords  = array();
        $pat = "|[\?_A-Za-z0-9]+|";
        preg_match_all($pat, $this->casstring, $out, PREG_PATTERN_ORDER);

        // Filter out some of these matches.
        foreach ($out[0] as $key) {
            if (strlen($key) > 1) {
                $upkey = strtolower($key);
                array_push($strinkeywords, $upkey);
            }
        }
        $strinkeywords = array_unique($strinkeywords);

        foreach ($strinkeywords as $key) {
            if (in_array($key, $kws)) {
                $found = true;
                $this->valid = false;
                $this->add_error(stack_string('stackCas_forbiddenWord', array('forbid' => stack_maxima_format_casstring($key))));
            }
        }
        return $found;
    }

    /**
     * Check for strings within the casstring.  This is only used in the "fobidden words" option.
     * @return bool|string true if an element of array is found in the casstring.
     */
    public function check_external_forbidden_words_literal($keywords) {
        if (null === $this->valid) {
            $this->validate();
        }

        // Deal with escaped commas.
        $keywords = str_replace('\,', 'COMMA_TAG', $keywords);
        $keywords = explode(',', $keywords);
        // Replace lists of keywords with their actual values.
        $kws = array();
        foreach ($keywords as $val) {
            $kw = trim(strtolower($val));
            if (array_key_exists($kw, self::$keywordlists)) {
                $kws = array_merge($kws, self::$keywordlists[$kw]);
            } else {
                if ('COMMA_TAG' === $val) {
                    $kws[] = ',';
                } else {
                    $kws[] = trim($val);  // This test is case sensitive, but ignores surrounding whitespace.
                }
            }
        }

        $found = false;
        foreach ($kws as $key) {
            if (!(false === strpos($this->rawcasstring, $key))) {
                $found = true;
                $this->valid = false;
                $this->add_error(stack_string('stackCas_forbiddenWord', array('forbid' => stack_maxima_format_casstring($key))));
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
            // Need to check we don't have a function definition.
            if ('=' === substr($this->casstring, $i + 1, 1)) {
                $this->key   = '';
            } else {
                $this->key       = trim(substr($this->casstring, 0, $i));
                $this->casstring = trim(substr($this->casstring, $i + 1));
            }
        }
    }

    /*********************************************************/
    /* Return and modify information                         */
    /*********************************************************/

    public function get_valid($security='s', $syntax=true, $insertstars=0, $allowwords='') {
        if (null === $this->valid) {
            $this->validate($security, $syntax, $insertstars, $allowwords);
        }
        return $this->valid;
    }

    public function set_valid($val) {
        $this->valid = $val;
    }

    public function get_errors() {
        if (null === $this->valid) {
            $this->validate();
        }
        return $this->errors;
    }

    public function get_raw_casstring() {
        return $this->rawcasstring;
    }

    public function get_casstring() {
        if (null === $this->valid) {
            $this->validate();
        }
        return $this->casstring;
    }

    public function get_key() {
        if (null === $this->valid) {
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

    public function set_key($key, $appendkey=true) {
        if (null === $this->valid) {
            $this->validate();
        }
        if ('' != $this->key && $appendkey) {
            $this->casstring = $this->key.':'.$this->casstring;
            $this->key = $key;
        } else {
            $this->key = $key;
        }
    }

    public function set_value($val) {
        $this->value = $val;
    }

    public function set_display($val) {
        $this->display = $val;
    }

    public function get_answernote() {
        if (null === $this->valid) {
            $this->validate();
        }
        return implode(' | ', $this->answernote);
    }

    public function set_answernote($val) {
        $this->answernote[] = $val;
    }

    public function get_feedback() {
        return $this->feedback;
    }

    public function set_feedback($val) {
        $this->feedback = $val;
    }

    public function add_errors($err) {
        if ('' == trim($err)) {
            return false;
        } else {
            return $this->errors .= $err;
        }
    }

    // If we "CAS validate" this string, then we need to set various options.
    // If the teacher's answer is NULL then we use typeless validation, otherwise we check type.
    public function set_cas_validation_casstring($key, $forbidfloats = true,
                    $lowestterms = true, $singlecharvars = false, $tans = null, $allowwords = '') {
        if (null === $this->valid) {
            $this->validate('s', true, 0, $allowwords);
        }
        if (false === $this->valid) {
            return false;
        }

        $this->key = $key;
        $starredanswer = $this->casstring;

        // Turn PHP Booleans into Maxima true & false.
        if ($forbidfloats) {
            $forbidfloats = 'true';
        } else {
            $forbidfloats = 'false';
        }
        if ($lowestterms) {
            $lowestterms = 'true';
        } else {
            $lowestterms = 'false';
        }

        if ($singlecharvars) {
            $starredanswer = 'stack_singlevar_make('.$starredanswer.')';
        }

        if (null === $tans) {
            $this->casstring = 'stack_validate_typeless(['.$starredanswer.'],'.$forbidfloats.','.$lowestterms.')';
        } else {
            $this->casstring = 'stack_validate(['.$starredanswer.'],'.$forbidfloats.','.$lowestterms.','.$tans.')';
        }
        return true;
    }

    /*
     *  Remove contents of strings and replace them with safe tags.
     */
    private function strings_remove($cmd) {
        $strings = stack_utils::all_substring_strings($cmd);
        foreach ($strings as $key => $string) {
            $cmd = str_replace('"'.$string.'"', '[STR:'.$key.']', $cmd);
        }
        return array($cmd, $strings);
    }

    /*
     *  Replace tags with the contents of strings.
     */
    private function strings_replace($cmd, $strings) {
        foreach ($strings as $key => $string) {
            $cmd = str_replace('[STR:'.$key.']', '"'.$string.'"', $cmd);
        }
        return $cmd;
    }

    /**
     *  This function decodes the error generated by Maxima into meaningful notes.
     *  */
    public function decode_maxima_errors($error) {
        $searchstrings = array('CommaError', 'Illegal_floats', 'Lowest_Terms', 'SA_not_matrix',
                'SA_not_list', 'SA_not_equation', 'SA_not_inequality', 'SA_not_set', 'SA_not_expression', 'DivisionZero');
        $foundone = false;
        foreach ($searchstrings as $s) {
            if (false !== strpos($error, $s)) {
                $this->set_answernote($s);
                $foundone = true;
            }
        }
        if (!$foundone) {
            $this->set_answernote('CASError: '.$error);
        }
    }
}
