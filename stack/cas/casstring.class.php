<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
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

defined('MOODLE_INTERNAL') || die();

// CAS strings and related functions.
//
// @copyright  2012 University of Birmingham.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');
require_once(__DIR__ . '/../maximaparser/utils.php');
require_once(__DIR__ . '/../maximaparser/MP_classes.php');
require_once(__DIR__ . '/casstring.units.class.php');
require_once(__DIR__ . '/parsingrules/insertstars0.class.php');
require_once(__DIR__ . '/parsingrules/insertstars1.class.php');
require_once(__DIR__ . '/parsingrules/insertstars2.class.php');
require_once(__DIR__ . '/parsingrules/insertstars3.class.php');
require_once(__DIR__ . '/parsingrules/insertstars4.class.php');
require_once(__DIR__ . '/parsingrules/insertstars5.class.php');


class stack_cas_casstring {

    /** @var string as typed in by the user. */
    private $rawcasstring;

    /** @var string as modified by the validation. */
    private $casstring;

    /** @var bool if the string has passed validation. */
    private $valid;

    /** @var string */
    private $key;

    /** @var bool whether the string has scientific units. */
    private $units;

    /** @var array any error messages to display to the user. */
    private $errors;

    /**
     * @var string the value of the CAS string, in Maxima syntax. Only gets set
     *             after the casstring has been processed by the CAS.
     */
    private $value;

    /**
     * @array of additional CAS strings which are conditions when the main expression can
     * be evaluated.  I.e. this encapsulates restrictions on the domain of the main value.
     */
    private $conditions;

    /**
     * @var string A sanitised version of the value, e.g. with decimal places printed
     *             and stackunits replaced by multiplication.  Used sparingly, e.g. for
     *             the teacher's answer, and testing inputs.
     */
    private $dispvalue;

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

    /**
     * @var MPNode the parse tree presentation of this CAS string. Public for debug access...
     */
    public $ast = null;

    /*
     * @var array For efficient searching we cache the various lists of keywords.
     */
    private static $cache = false;

    /** @var array blacklist of globally forbidden CAS keywords. */
    private static $globalforbid = array(
        'functions' => array('alias' => true, 'appendfile' => 'io', 'apropos' => 'system state',
                             'assume_external_byte_order' => 'io', 'backtrace' => 'system state', 'batch' => 'io',
                             'barsplot' => true, 'batchload' => 'io', 'boxplot' => true, 'bug_report' => 'system state',
                             'build_info' => 'system state', 'catch' => true, 'close' => 'io', 'closefile' => 'io',
                             'compfile' => 'io', 'compile' => 'io', 'compile_file' => 'io', 'concat' => true,
                             'deactivate' => 'system state', 'define' => true, 'define_variable' => true, 'demo' => 'io',
                             'dependencies' => 'system state', 'describe' => 'system state', 'dimacs_export' => 'io',
                             'dimacs_import' => 'io', 'entermatrix' => true, 'errcatch' => 'system state',
                             'error' => 'system state', 'errormsg' => 'system state', 'eval_string' => 'system state',
                             'example' => true, 'featurep' => 'system state', 'features' => 'system state', 'file_type' => 'io',
                             'filename_merge' => 'io', 'flength' => 'io', 'fortran' => 'io',
                             'fposition' => 'io', 'freshline' => 'io', 'fundef' => 'system state', 'funmake' => 'system state',
                             'grind' => true, 'kill' => 'system state', 'killcontext' => 'system state',
                             'labels' => 'system state', 'ldisp' => true, 'ldisplay' => true, 'lisp' => 'io',
                             'load' => 'io', 'loadfile' => 'io', 'macroexpand' => true, 'macroexpand1' => true,
                             'multiplot_mode' => 'graphics option', 'newline' => 'io', 'opena' => 'io', 'opena_binary' => 'io',
                             'openr' => 'io', 'openr_binary' => 'io', 'openw' => 'io', 'openw_binary' => 'io',
                             'parse_string' => 'io', 'pathname_directory' => 'io', 'pathname_name' => 'io',
                             'pathname_type' => 'io', 'pickapart' => true, 'playback' => 'system state',
                             'plotdf' => true, 'print' => 'io', 'print_graph' => 'system state', 'printf' => 'io',
                             'printfile' => 'system state', 'quit' => 'system state', 'read' => 'io',
                             'read_array' => 'io', 'read_binary_array' => 'io', 'read_binary_list' => 'io',
                             'read_binary_matrix' => 'io', 'read_hashed_array' => 'io', 'read_list' => 'io',
                             'read_matrix' => 'io', 'read_nested_list' => 'io', 'read_xpm' => 'io',
                             'readline' => 'io', 'readbyte' => 'io', 'readchar' => 'io', 'readonly' => 'io',
                             'rembox' => true, 'remvalue' => 'system state', 'remfunction' => 'system state',
                             'reset' => 'system state', 'room' => 'system state', 'run_testsuite' => 'io',
                             'save' => 'io', 'scatterplot' => true, 'starplot' => true, 'stemplot' => true,
                             'set_plot_option' => true, 'setup_autoload' => 'io', 'sparse6_export' => 'io',
                             'sparse6_import' => 'io', 'splice' => true, 'sprint' => 'io',
                             'status' => 'system state', 'stringout' => 'io', 'supcontext' => 'system state',
                             'system' => 'system state', 'tcl_output' => 'io', 'tex' => true, 'throw' => true,
                             'time' => true, 'timer' => true, 'timer_info' => true, 'to_lisp' => 'system state',
                             'trace' => true, 'trace_options' => true, 'transcompile' => true, 'translate' => 'io',
                             'translate_file' => 'io', 'untimer' => true, 'untrace' => true, 'with_stdout' => 'io',
                             'write_binary_data' => true, 'write_data' => 'io', 'writefile' => 'io'
                             ),
        // The @ aoperator could be allowed at some point but then we need to allow structs as well.
        // The parser does not support ? in either its forms but if it were to support we will
        // not allow it NOTE: "?blaah" is very much different than "? blaah" the space is very significant.
        'operators' => array('@' => true, '?' => true, '? ' => true),
        'variables' => array('%th' => true, 'adapth_depth' => 'graphics option', 'aliases' => 'system state',
                             'alphabetic' => 'property', 'boxchar' => 'display option',
                             'current_let_rule_package' => 'system state', 'data_file_name' => 'io',
                             'debugmode' => 'system state', 'del_cmd' => 'io', 'dependencies' => 'system state',
                             'error' => 'system state', 'error_size' => 'system state', 'error_syms' => 'system state',
                             'errormsg' => 'system state', 'file_name' => 'io', 'file_output_append' => 'io',
                             'file_search' => 'io', 'file_search_demo' => 'io', 'file_search_lisp' => 'io',
                             'file_search_maxima' => 'io', 'file_search_tests' => 'io', 'file_search_usage' => 'io',
                             'fortindent' => 'io', 'fortspaces' => 'io', 'functions' => 'system state',
                             'gnuplot_file_name' => 'io', 'gnuplot_out_file' => 'io', 'gnuplot_preamble' => 'io',
                             'gnuplot_ps_term_command' => 'io', 'gnuplot_cmd' => 'io', 'gnuplot_term' => 'io',
                             'inchar' => 'system state', 'infeval' => 'system state', 'infolists' => 'system state',
                             'lisp' => 'io', 'leftjust' => 'system state', 'linechar' => 'system state',
                             'linel' => 'system state', 'linenum' => 'system state', 'linsolvewarn' => 'system state',
                             'load_pathname' => 'io', 'loadprint' => 'io', 'macroexpansion' => 'system state',
                             'macros' => 'system state', 'manual_demo' => 'io', 'maxima_tempdir' => 'io',
                             'maxima_userdir' => 'io', 'myoptions' => 'system state', 'nolabels' => 'system state',
                             'outchar' => 'system state', 'packagefile' => true, 'piece' => true,
                             'plot_terminal' => 'io', 'plot_term' => 'io', 'prompt' => 'system state',
                             'psfile' => 'io', 'ps_file' => 'io', 'refcheck' => true, 'rmxchar' => 'system state',
                             'run_viewer' => 'io', 'savedef' => true, 'setcheck' => 'system state',
                             'setcheckbreak' => 'system state', 'setval' => 'system state', 'showtime' => true,
                             'terminal' => 'graphics option', 'testsuite_files' => 'io', 'timer_devalue' => 'system state',
                             'transrun' => 'io', 'ttyoff' => 'system state', 'user_preamble' => 'graphics option',
                             'values' => 'system state'
                             )
    );


    /** @var array blacklist of CAS keywords forbidden to teachers. */
    // Note we allow RANDOM_PERMUTATION.
    private static $teachernotallow = array('%unitexpand' => true, 'abasep' => true, 'absboxchar' => true,
                'absolute_real_time' => true, 'activate' => true, 'activecontexts' => true, 'additive' => true, 'adim' => true,
                'af' => true, 'aform' => true, 'agd' => true, 'alg_type' => true, 'all_dotsimp_denoms' => true,
                'allsym' => true, 'antid' => true, 'antidiff' => true, 'antidifference' => true, 'antisymmetric' => true,
                'arithmetic' => true, 'arithsum' => true, 'array' => true, 'arrayapply' => true, 'arrayinfo' => true,
                'arraymake' => true, 'arrays' => true, 'assoc_legendre_p' => true, 'assoc_legendre_q' => true,
                'asymbol' => true, 'atensimp' => true, 'atomgrad' => true, 'atrig1' => true, 'atvalue' => true,
                'augmented_lagrangian_method' => true, 'av' => true, 'axis_3d' => true, 'axis_bottom' => true,
                'axis_left' => true, 'axis_right' => true, 'axis_top' => true, 'azimut' => true, 'backsubst' => true,
                'bars' => true, 'bashindices' => true, 'bdvac' => true, 'berlefact' => true, 'bfpsi' => true, 'bfpsi0' => true,
                'bimetric' => true, 'bode_gain' => true, 'bode_phase' => true, 'border' => true, 'boundaries_array' => true,
                'canform' => true, 'canten' => true, 'cbffac' => true, 'cbrange' => true, 'cbtics' => true, 'cdisplay' => true,
                'cframe_flag' => true, 'cgeodesic' => true, 'changename' => true, 'chaosgame' => true, 'chebyshev_t' => true,
                'chebyshev_u' => true, 'check_overlaps' => true, 'checkdiv' => true, 'christof' => true, 'clear_rules' => true,
                'cmetric' => true, 'cnonmet_flag' => true, 'cograd' => true, 'collapse' => true, 'colorbox' => true,
                'columns' => true, 'combination' => true, 'comp2pui' => true, 'components' => true, 'concan' => true,
                'conmetderiv' => true, 'constvalue' => true, 'cont2part' => true, 'context' => true, 'contexts' => true,
                'contortion' => true, 'contour' => true, 'contour_levels' => true, 'contour_plot' => true,
                'contract_edge' => true, 'contragrad' => true, 'contrib_ode' => true, 'convert' => true, 'coord' => true,
                'copy_graph' => true, 'covdiff' => true, 'covers' => true, 'create_list' => true, 'csetup' => true,
                'ct_coords' => true, 'ct_coordsys' => true, 'ctaylor' => true, 'ctaypov' => true, 'ctaypt' => true,
                'ctayswitch' => true, 'ctayvar' => true, 'ctorsion_flag' => true, 'ctransform' => true,
                'ctrgsimp' => true, 'cunlisp' => true, 'declare_constvalue' => true, 'declare_dimensions' => true,
                'declare_fundamental_dimensions' => true, 'declare_fundamental_units' => true, 'declare_qty' => true,
                'declare_translated' => true, 'declare_unit_conversion' => true, 'declare_units' => true,
                'declare_weights' => true, 'decsym' => true, 'default_let_rule_package' => true, 'defcon' => true,
                'defmatch' => true, 'defrule' => true, 'delay' => true, 'deleten' => true, 'diag' => true,
                'diagmatrixp' => true, 'diagmetric' => true, 'dim' => true, 'dimension' => true, 'dimensionless' => true,
                'dimensions' => true, 'dimensions_as_list' => true, 'direct' => true, 'dispcon' => true,
                'dispflag' => true, 'dispform' => true, 'dispfun' => true, 'dispjordan' => true, 'display' => true,
                'display2d' => true, 'display_format_internal' => true, 'disprule' => true, 'dispterms' => true,
                'distrib' => true, 'domxexpt' => true, 'domxmxops' => true, 'domxnctimes' => true, 'dotsimp' => true,
                'draw' => true, 'draw2d' => true, 'draw3d' => true, 'draw_file' => true, 'draw_graph' => true,
                'draw_graph_program' => true, 'dscalar' => true, 'einstein' => true, 'elapsed_real_time' => true,
                'elapsed_run_time' => true, 'ele2comp' => true, 'ele2polynome' => true, 'ele2pui' => true, 'elem' => true,
                'elevation' => true, 'ellipse' => true, 'enhanced3d' => true, 'entermatrix' => true, 'entertensor' => true,
                'entier' => true, 'eps_height' => true, 'eps_width' => true, 'ev_point' => true, 'evflag' => true,
                'evfun' => true, 'evolution' => true, 'evolution2d' => true, 'evundiff' => true, 'explicit' => true,
                'explose' => true, 'expon' => true, 'expop' => true, 'expt' => true, 'exsec' => true, 'extdiff' => true,
                'extract_linear_equations' => true, 'f90' => true, 'facts' => true, 'fast_central_elements' => true,
                'fast_linsolve' => true, 'fb' => true, 'file_bgcolor' => true, 'fill_color' => true, 'fill_density' => true,
                'fillarray' => true, 'filled_func' => true, 'findde' => true, 'fix' => true, 'flipflag' => true,
                'flush' => true, 'flush1deriv' => true, 'flushd' => true, 'flushnd' => true, 'font' => true,
                'font_size' => true, 'forget' => true, 'frame_bracket' => true, 'fundamental_dimensions' => true,
                'fundamental_units' => true, 'gaussprob' => true, 'gcdivide' => true, 'gcfac' => true, 'gd' => true,
                'gdet' => true, 'gen_laguerre' => true, 'gensumnum' => true, 'geomap' => true, 'geometric' => true,
                'geosum' => true, 'get' => true, 'get_pixel' => true, 'get_plot_option' => true, 'get_tex_environment' => true,
                'get_tex_environment_default' => true, 'ggf' => true, 'ggfcfmax' => true, 'ggfinfinity' => true,
                'globalsolve' => true, 'gnuplot_close' => true, 'gnuplot_curve_styles' => true, 'gnuplot_curve_titles' => true,
                'gnuplot_default_term_command' => true, 'gnuplot_dumb_term_command' => true, 'gnuplot_pm3d' => true,
                'gnuplot_replot' => true, 'gnuplot_reset' => true, 'gnuplot_restart' => true, 'gnuplot_start' => true,
                'gosper' => true, 'gosper_in_zeilberger' => true, 'gospersum' => true, 'gr2d' => true, 'gr3d' => true,
                'gradef' => true, 'gradefs' => true, 'graph6_decode' => true, 'graph6_encode' => true, 'graph6_export' => true,
                'graph6_import' => true, 'grid' => true, 'grobner_basis' => true, 'harmonic' => true, 'hav' => true,
                'head_angle' => true, 'head_both' => true, 'head_length' => true, 'head_type' => true, 'hermite' => true,
                'histogram' => true, 'hodge' => true, 'ic_convert' => true, 'icc1' => true, 'icc2' => true, 'ichr1' => true,
                'ichr2' => true, 'icounter' => true, 'icurvature' => true, 'idiff' => true, 'idim' => true, 'idummy' => true,
                'idummyx' => true, 'ieqn' => true, 'ieqnprint' => true, 'ifb' => true, 'ifc1' => true, 'ifc2' => true,
                'ifg' => true, 'ifgi' => true, 'ifr' => true, 'iframe_bracket_form' => true, 'iframes' => true, 'ifri' => true,
                'ifs' => true, 'igeodesic_coords' => true, 'igeowedge_flag' => true, 'ikt1' => true, 'ikt2' => true,
                'image' => true, 'imetric' => true, 'implicit' => true, 'implicit_plot' => true, 'implicit_derivative' => true,
                'indexed_tensor' => true, 'indices' => true, 'infix' => true, 'init_atensor' => true,
                'init_ctensor' => true, 'inm' => true, 'inmc1' => true, 'inmc2' => true, 'inprod' => true, 'intervalp' => true,
                'intopois' => true, 'invariant1' => true, 'invariant2' => true, 'invert_by_lu' => true, 'ip_grid' => true,
                'ip_grid_in' => true, 'ishow' => true, 'isolate' => true, 'isolate_wrt_times' => true, 'itr' => true,
                'jacobi_p' => true, 'jf' => true, 'jordan' => true, 'julia' => true, 'kdels' => true, 'kdelta' => true,
                'key' => true, 'kinvariant' => true, 'kostka' => true, 'kt' => true, 'label_alignment' => true,
                'label_orientation' => true, 'laguerre' => true, 'lassociative' => true, 'lbfgs' => true,
                'lbfgs_ncorrections' => true, 'lbfgs_nfeval_max' => true, 'lc2kdt' => true, 'lc_l' => true,
                'lc_u' => true, 'lcharp' => true, 'legendre_p' => true, 'legendre_q' => true, 'leinstein' => true,
                'let' => true, 'let_rule_packages' => true, 'letrat' => true, 'letrules' => true, 'letsimp' => true,
                'levi_civita' => true, 'lfg' => true, 'lg' => true, 'lgtreillis' => true, 'li' => true, 'liediff' => true,
                'lindstedt' => true, 'line_type' => true, 'line_width' => true, 'linear' => true,
                'linear_solver' => true, 'lispdisp' => true, 'list_nc_monomials' => true, 'listarray' => true,
                'listoftens' => true, 'logand' => true, 'logcb' => true, 'logor' => true, 'logxor' => true, 'logz' => true,
                'lorentz_gauge' => true, 'lpart' => true, 'lriem' => true, 'lriemann' => true, 'lsquares_estimates' => true,
                'lsquares_estimates_approximate' => true, 'lsquares_estimates_exact' => true, 'lsquares_mse' => true,
                'lsquares_residual_mse' => true, 'lsquares_residuals' => true, 'ltreillis' => true, 'm1pbranch' => true,
                'mainvar' => true, 'make_array' => true, 'make_level_picture' => true, 'make_poly_continent' => true,
                'make_poly_country' => true, 'make_polygon' => true, 'make_random_state' => true, 'make_rgb_picture' => true,
                'makebox' => true, 'makeorders' => true, 'mandelbrot' => true, 'maperror' => true, 'mat_function' => true,
                'max_ord' => true, 'maxapplydepth' => true, 'maxapplyheight' => true, 'maxi' => true,
                'maxnegex' => true, 'maxposex' => true, 'maxpsifracdenom' => true, 'maxpsifracnum' => true,
                'maxpsinegint' => true, 'maxpsiposint' => true, 'maxtayorder' => true, 'maybe' => true, 'mesh' => true,
                'mesh_lines_color' => true, 'metricexpandall' => true, 'mini' => true, 'minimalpoly' => true,
                'minor' => true, 'mnewton' => true, 'mod_big_prime' => true, 'mod_test' => true,
                'mod_threshold' => true, 'mode_check_errorp' => true, 'mode_check_warnp' => true, 'mode_checkp' => true,
                'mode_declare' => true, 'mode_identity' => true, 'modematrix' => true, 'modular_linear_solver' => true,
                'mon2schur' => true, 'mono' => true, 'monomial_dimensions' => true, 'multi_elem' => true, 'multi_orbit' => true,
                'multi_pui' => true, 'multinomial' => true, 'multsym' => true, 'natural_unit' => true, 'nc_degree' => true,
                'negative_picture' => true, 'newcontext' => true, 'newton' => true, 'newtonepsilon' => true,
                'newtonmaxiter' => true, 'nextlayerfactor' => true, 'niceindices' => true, 'niceindicespref' => true,
                'nm' => true, 'nmc' => true, 'nonmetricity' => true, 'nonzeroandfreeof' => true,
                'noundisp' => true, 'np' => true, 'npi' => true, 'nptetrad' => true, 'ntermst' => true, 'ntrig' => true,
                'numbered_boundaries' => true, 'ode2' => true, 'ode_check' => true, 'odelin' => true, 'optimize' => true,
                'optimprefix' => true, 'optionset' => true, 'orbit' => true, 'orbits' => true, 'orthopoly_recur' => true,
                'orthopoly_returns_intervals' => true, 'orthopoly_weight' => true, 'outofpois' => true, 'palette' => true,
                'parametric_surface' => true, 'pargosper' => true, 'partpol' => true, 'pdf_width' => true, 'permut' => true,
                'petrov' => true, 'pic_height' => true, 'pic_width' => true, 'picture_equalp' => true,
                'picturep' => true, 'piechart' => true, 'plot2d' => true, 'plot3d' => true, 'ploteq' => true,
                'plot_format' => true, 'plot_options' => true, 'plot_real_part' => true, 'plsquares' => true,
                'pochhammer' => true, 'pochhammer_max_index' => true, 'points_joined' => true,  'polar' => true,
                'polar_to_xy' => true, 'polygon' => true, 'prederror' => true, 'primep_number_of_tests' => true,
                'printprops' => true, 'prodrac' => true, 'product' => true, 'product_use_gamma' => true, 'programmode' => true,
                'proportional_axes' => true, 'props' => true, 'propvars' => true, 'psexpand' => true,
                'pui' => true, 'pui2comp' => true, 'pui2ele' => true, 'pui2polynome' => true, 'pui_direct' => true,
                'puireduc' => true, 'qty' => true, 'random' => true, 'ratchristof' => true, 'rateinstein' => true,
                'rational' => true, 'ratprint' => true, 'ratriemann' => true, 'ratweyl' => true, 'ratwtlvl' => true,
                'rearray' => true, 'rectangle' => true, 'rediff' => true, 'redraw' => true, 'reduce_consts' => true,
                'reduce_order' => true, 'region_boundaries' => true, 'region_boundaries_plus' => true, 'remarray' => true,
                'uppercasep' => true, 'remcomps' => true, 'remcon' => true, 'remcoord' => true, 'remlet' => true,
                'remove_dimensions' => true, 'remove_fundamental_dimensions' => true, 'remove_fundamental_units' => true,
                'rempart' => true, 'remsym' => true, 'rename' => true, 'resolvante' => true, 'resolvante_alternee1' => true,
                'resolvante_bipartite' => true, 'resolvante_diedrale' => true, 'resolvante_klein' => true,
                'resolvante_klein3' => true, 'resolvante_produit_sym' => true, 'resolvante_unitaire' => true,
                'resolvante_vierer' => true, 'revert' => true, 'revert2' => true, 'rgb2level' => true, 'ric' => true,
                'ricci' => true, 'riem' => true, 'riemann' => true, 'rinvariant' => true, 'rk' => true, 'rot_horizontal' => true,
                'rot_vertical' => true, 'savefactors' => true, 'scurvature' => true, 'set_draw_defaults' => true,
                'set_random_state' => true, 'set_tex_environment' => true, 'set_tex_environment_default' => true,
                'set_up_dot_simplifications' => true, 'setunits' => true, 'setup_autoload' => true, 'sf' => true,
                'showcomps' => true, 'similaritytransform' => true, 'simplified_output' => true, 'simplify_products' => true,
                'simplify_sum' => true, 'simplode' => true, 'simpmetderiv' => true, 'simtran' => true, 'solve_rec' => true,
                'solve_rec_rat' => true, 'somrac' => true, 'sparse6_decode' => true, 'sparse6_encode' => true,
                'spherical_bessel_j' => true, 'spherical_bessel_y' => true, 'spherical_hankel1' => true,
                'spherical_hankel2' => true, 'spherical_harmonic' => true, 'sqrtdenest' => true,
                'sstatus' => true, 'staircase' => true, 'stardisp' => true, 'stirling' => true, 'stirling1' => true,
                'stirling2' => true, 'stringdisp' => true, 'summand_to_rec' => true, 'surface_hide' => true,
                'symmetricp' => true, 'tab' => true, 'take_channel' => true, 'tcontract' => true, 'tensorkill' => true,
                'tentex' => true, 'timedate' => true, 'title' => true, 'totaldisrep' => true, 'totient' => true,
                'tpartpol' => true, 'tr' => true, 'tr_array_as_ref' => true, 'tr_bound_function_applyp' => true,
                'tr_file_tty_messagesp' => true, 'tr_float_can_branch_complex' => true, 'tr_function_call_default' => true,
                'tr_numer' => true, 'tr_optimize_max_loop' => true, 'tr_semicompile' => true, 'tr_state_vars' => true,
                'tr_warn_bad_function_calls' => true, 'tr_warn_fexpr' => true, 'tr_warn_meval' => true, 'tr_warn_mode' => true,
                'tr_warn_undeclared' => true, 'tr_warn_undefined_variable' => true, 'tr_warnings_get' => true,
                'tr_windy' => true, 'tracematrix' => true, 'transform_xy' => true, 'transparent' => true, 'treillis' => true,
                'treinat' => true, 'trivial_solutions' => true, 'tube' => true, 'tube_extremes' => true, 'tutte_graph' => true,
                'ueivects' => true, 'ufg' => true, 'uforget' => true, 'ug' => true, 'ultraspherical' => true, 'undiff' => true,
                'unit_step' => true, 'unit_vectors' => true, 'uniteigenvectors' => true, 'unitvector' => true,
                'unorder' => true, 'uric' => true, 'uricci' => true, 'uriem' => true, 'uriemann' => true,
                'usersetunits' => true, 'uvect' => true, 'vector' => true, 'verbose' => true,
                'vers' => true, 'warnings' => true, 'weyl' => true, 'wronskian' => true, 'x_voxel' => true, 'xaxis' => true,
                'xaxis_color' => true, 'xaxis_secondary' => true, 'xaxis_type' => true, 'xaxis_width' => true, 'xrange' => true,
                'xrange_secondary' => true, 'xtics_axis' => true, 'xtics_rotate' => true, 'xtics_rotate_secondary' => true,
                'xtics_secondary' => true, 'xtics_secondary_axis' => true, 'xu_grid' => true, 'xy_file' => true,
                'xyplane' => true, 'y_voxel' => true, 'yaxis' => true, 'yaxis_color' => true, 'yaxis_secondary' => true,
                'yaxis_type' => true, 'yaxis_width' => true, 'yrange' => true, 'yrange_secondary' => true, 'ytics_axis' => true,
                'ytics_rotate' => true, 'ytics_rotate_secondary' => true, 'ytics_secondary' => true,
                'ytics_secondary_axis' => true, 'yv_grid' => true, 'z_voxel' => true, 'zaxis' => true, 'zaxis_color' => true,
                'zaxis_type' => true, 'zaxis_width' => true, 'zeilberger' => true, 'zeroa' => true, 'zerob' => true,
                'zlabel' => true, 'zlange' => true, 'zrange' => true, 'ztics_axis' => true, 'ztics_rotate' => true);

    /** @var array CAS keywords ALLOWED by students. */
    private static $studentallow    = array('%c' => true, '%e' => true, '%gamma' => true, '%i' => true, '%k1' => true,
                '%k2' => true, '%phi' => true, '%pi' => true, 'abs' => true, 'absint' => true, 'acos' => true, 'acosh' => true,
                'acot' => true, 'acoth' => true, 'acsc' => true, 'acsch' => true, 'addmatrices' => true, 'adjoin' => true,
                'and' => true, 'ascii' => true, 'asec' => true, 'asech' => true, 'asin' => true, 'asinh' => true,
                'atan' => true, 'atan2' => true, 'atanh' => true, 'augcoefmatrix' => true, 'axes' => true, 'belln' => true,
                'bessel_i' => true, 'bessel_j' => true, 'bessel_k' => true, 'bessel_y' => true, 'besselexpand' => true,
                'beta' => true, 'bezout' => true, 'bffac' => true, 'bfhzeta' => true, 'bfloat' => true, 'bfloatp' => true,
                'binomial' => true, 'black' => true, 'blockmatrixp' => true, 'blue' => true, 'box' => true, 'burn' => true,
                'cabs' => true, 'cardinality' => true, 'carg' => true, 'cartan' => true, 'cartesian_product' => true,
                'ceiling' => true, 'cequal' => true, 'cequalignore' => true, 'cf' => true, 'cfdisrep' => true,
                'cfexpand' => true, 'cflength' => true, 'cgreaterp' => true, 'cgreaterpignore' => true, 'charat' => true,
                'charfun' => true, 'charfun2' => true, 'charlist' => true, 'charp' => true, 'charpoly' => true, 'cint' => true,
                'clessp' => true, 'clesspignore' => true, 'coeff' => true, 'coefmatrix' => true, 'col' => true,
                'columnop' => true, 'columnspace' => true, 'columnswap' => true, 'combine' => true, 'compare' => true,
                'conjugate' => true, 'cons' => true, 'constituent' => true, 'copy' => true, 'cos' => true,
                'cosh' => true, 'cot' => true, 'coth' => true, 'color' => true, 'covect' => true, 'csc' => true,
                'csch' => true, 'cspline' => true, 'cyan' => true, 'cosec' => true, 'ctranspose' => true, 'dblint' => true,
                'defint' => true, 'del' => true, 'delete' => true, 'delta' => true, 'denom' => true, 'desolve' => true,
                'determinant' => true, 'detout' => true, 'dgauss_a' => true, 'dgauss_b' => true, 'diag_matrix' => true,
                'diagmatrix' => true, 'diff' => true, 'digitcharp' => true, 'disjoin' => true, 'disjointp' => true,
                'disolate' => true, 'divide' => true, 'divisors' => true, 'divsum' => true, 'dkummer_m' => true,
                'dkummer_u' => true, 'dotproduct' => true, 'echelon' => true, 'eigenvalues' => true, 'eigenvectors' => true,
                'eighth' => true, 'eivals' => true, 'eivects' => true, 'elementp' => true, 'eliminate' => true,
                'elliptic_e' => true, 'elliptic_ec' => true, 'elliptic_eu' => true, 'elliptic_f' => true,
                'elliptic_kc' => true, 'elliptic_pi' => true, 'ematrix' => true, 'emptyp' => true, 'endcons' => true,
                'equal' => true, 'equalp' => true, 'equiv_classes' => true, 'erf' => true,
                'euler' => true, 'ev' => true, 'eval' => true, 'evenp' => true, 'every' => true, 'exp' => true,
                'expand' => true, 'expandwrt' => true, 'expandwrt_denom' => true, 'expandwrt_factored' => true,
                'express' => true, 'extremal_subset' => true, 'ezgcd' => true, 'facsum' => true, 'facsum_combine' => true,
                'factcomb' => true, 'factlim' => true, 'factor' => true, 'factorfacsum' => true, 'factorial' => true,
                'factorout' => true, 'factorsum' => true, 'false' => true, 'fasttimes' => true, 'fft' => true, 'fib' => true,
                'fibtophi' => true, 'fifth' => true, 'find_root' => true, 'find_root_abs' => true, 'find_root_error' => true,
                'find_root_rel' => true, 'first' => true, 'flatten' => true, 'float' => true, 'float2bf' => true,
                'floor' => true, 'fourcos' => true, 'fourexpand' => true, 'fourier' => true, 'fourint' => true,
                'fourintcos' => true, 'fourintsin' => true, 'foursimp' => true, 'foursin' => true, 'fourth' => true,
                'freeof' => true, 'full_listify' => true, 'fullmap' => true, 'fullmapl' => true, 'fullratsimp' => true,
                'fullratsubst' => true, 'fullsetify' => true, 'funcsolve' => true, 'funp' => true, 'gamma' => true,
                'gamma_incomplete' => true, 'gamma_incomplete_generalized' => true, 'gamma_incomplete_regularized' => true,
                'gauss_a' => true, 'gauss_b' => true, 'gcd' => true, 'gcdex' => true, 'gcfactor' => true, 'genmatrix' => true,
                'get_lu_factors' => true, 'gfactor' => true, 'gfactorsum' => true, 'gramschmidt' => true, 'green' => true,
                'hankel' => true, 'hessian' => true, 'hgfred' => true, 'hilbert_matrix' => true, 'hipow' => true,
                'horner' => true, 'hypergeometric' => true, 'hypergeometric_representation' => true, 'ident' => true,
                'identfor' => true, 'identity' => true, 'ifactors' => true, 'imagpart' => true, 'ind' => true, 'inf' => true,
                'infinity' => true, 'innerproduct' => true, 'inrt' => true, 'integer_partitions' => true, 'integrate' => true,
                'intersect' => true, 'intersection' => true, 'intosum' => true, 'inv_mod' => true, 'inverse_jacobi_cd' => true,
                'inverse_jacobi_cn' => true, 'inverse_jacobi_cs' => true, 'inverse_jacobi_dc' => true, 'grid2d' => true,
                'inverse_jacobi_dn' => true, 'inverse_jacobi_ds' => true, 'inverse_jacobi_nc' => true,
                'inverse_jacobi_nd' => true, 'inverse_jacobi_ns' => true, 'inverse_jacobi_sc' => true,
                'inverse_jacobi_sd' => true, 'inverse_jacobi_sn' => true, 'invert' => true, 'isqrt' => true, 'jacobi' => true,
                'jacobi_cd' => true, 'jacobi_cn' => true, 'jacobi_cs' => true, 'jacobi_dc' => true, 'jacobi_dn' => true,
                'jacobi_ds' => true, 'jacobi_nc' => true, 'jacobi_nd' => true, 'jacobi_ns' => true, 'jacobi_sc' => true,
                'jacobi_sd' => true, 'jacobi_sn' => true, 'jacobian' => true, 'join' => true, 'kron_delta' => true,
                'kronecker_product' => true, 'kummer_m' => true, 'kummer_u' => true, 'lagrange' => true, 'lambda' => true,
                'lambert_w' => true, 'laplace' => true, 'last' => true, 'lcm' => true, 'ldefint' => true, 'legend' => true,
                'length' => true, 'lhs' => true, 'limit' => true, 'linearinterpol' => true, 'linsolve' => true,
                'linsolve_params' => true, 'listify' => true, 'lmax' => true, 'lmin' => true, 'locate_matrix_entry' => true,
                'log' => true, 'logy' => true, 'logx' => true, 'log10' => true, 'log_gamma' => true, 'logabs' => true,
                'logarc' => true, 'logcontract' => true, 'logexpand' => true, 'lognegint' => true, 'lognumer' => true,
                'logy' => true, 'logsimp' => true, 'lopow' => true, 'lowercasep' => true, 'lratsubst' => true,
                'lreduce' => true, 'lsum' => true, 'lu_backsub' => true, 'lu_factor' => true, 'magenta' => true,
                'make_transform' => true, 'makefact' => true, 'makegamma' => true, 'makelist' => true, 'makeset' => true,
                'map' => true, 'mapatom' => true, 'maplist' => true, 'mat_cond' => true, 'mat_fullunblocker' => true,
                'mat_norm' => true, 'mat_trace' => true, 'mat_unblocker' => true, 'matrix' => true,
                'matrix_element_add' => true, 'matrix_element_mult' => true, 'matrix_element_transpose' => true,
                'matrix_size' => true, 'matrixmap' => true, 'matrixp' => true, 'mattrace' => true, 'max' => true,
                'member' => true, 'min' => true, 'minf' => true, 'minfactorial' => true, 'mod' => true, 'moebius' => true,
                'multinomial_coeff' => true, 'multthru' => true, 'ncexpt' => true, 'ncharpoly' => true, 'newdet' => true,
                'ninth' => true, 'noeval' => true, 'nonnegintegerp' => true, 'not' => true, 'notequal' => true,
                'nroots' => true, 'nterms' => true, 'nthroot' => true, 'nticks' => true, 'nullity' => true, 'nullspace' => true,
                'num' => true, 'num_distinct_partitions' => true, 'num_partitions' => true, 'numberp' => true, 'numer' => true,
                'numerval' => true, 'numfactor' => true, 'nusum' => true, 'nzeta' => true, 'nzetai' => true, 'nzetar' => true,
                'oddp' => true, 'op' => true, 'operatorp' => true, 'or' => true, 'ordergreat' => true, 'ordergreatp' => true,
                'orderless' => true, 'orderlessp' => true, 'orthogonal_complement' => true, 'outermap' => true, 'pade' => true,
                'parabolic_cylinder_d' => true, 'part' => true, 'part2cont' => true, 'partfrac' => true, 'partition' => true,
                'partition_set' => true, 'permanent' => true, 'permutations' => true, 'plog' => true, 'plot_realpart' => true,
                'point_type' => true, 'point_size' => true, 'points' => true, 'poisdiff' => true, 'poisexpt' => true,
                'poisint' => true, 'poislim' => true, 'poismap' => true, 'poisplus' => true, 'poissimp' => true,
                'poisson' => true, 'poissubst' => true, 'poistimes' => true, 'poistrim' => true, 'polarform' => true,
                'polartorect' => true, 'polymod' => true, 'polynome2ele' => true, 'polynomialp' => true, 'psi' => true,
                'polytocompanion' => true, 'posfun' => true, 'potential' => true, 'power_mod' => true, 'powerdisp' => true,
                'powers' => true, 'powerseries' => true, 'powerset' => true, 'primep' => true, 'printpois' => true,
                'quad_qag' => true, 'quad_qagi' => true, 'quad_qags' => true, 'quad_qawc' => true, 'quad_qawf' => true,
                'uppercasep' => true, 'quad_qawo' => true, 'quad_qaws' => true, 'qunit' => true, 'quotient' => true,
                'radcan' => true, 'radexpand' => true, 'radsubstflag' => true, 'rank' => true, 'rassociative' => true,
                'rat' => true, 'ratalgdenom' => true, 'ratcoef' => true, 'ratdenom' => true, 'ratdenomdivide' => true,
                'ratdiff' => true, 'ratdisrep' => true, 'ratepsilon' => true, 'ratexpand' => true, 'ratfac' => true,
                'rationalize' => true, 'ratmx' => true, 'ratnumer' => true, 'ratnump' => true, 'ratp' => true, 'ratsimp' => true,
                'ratsimpexpons' => true, 'ratsubst' => true, 'ratvars' => true, 'ratweight' => true, 'ratweights' => true,
                'realonly' => true, 'realpart' => true, 'realroots' => true, 'rectform' => true, 'recttopolar' => true,
                'red' => true, 'remainder' => true, 'remfun' => true, 'residue' => true, 'rest' => true, 'resultant' => true,
                'reverse' => true, 'rhs' => true, 'risch' => true, 'rncombine' => true, 'romberg' => true, 'rombergabs' => true,
                'rombergit' => true, 'rombergmin' => true, 'rombergtol' => true, 'rootsconmode' => true, 'rootscontract' => true,
                'rootsepsilon' => true, 'round' => true, 'row' => true, 'rowop' => true, 'rowswap' => true,
                'rreduce' => true, 'scalarmatrixp' => true, 'scalarp' => true, 'scaled_bessel_i' => true,
                'scaled_bessel_i0' => true, 'scaled_bessel_i1' => true, 'scalefactors' => true, 'scanmap' => true,
                'schur2comp' => true, 'scsimp' => true, 'sec' => true, 'sech' => true, 'second' => true,
                'sequal' => true, 'sequalignore' => true, 'set_partitions' => true, 'setdifference' => true,
                'setequalp' => true, 'setify' => true, 'setp' => true, 'seventh' => true, 'sign' => true, 'signum' => true,
                'simpsum' => true, 'sin' => true, 'sinh' => true, 'sinnpiflag' => true, 'sinsert' => true,
                'sinvertcase' => true, 'sixth' => true, 'slength' => true, 'smake' => true, 'solve' => true,
                'solvedecomposes' => true, 'solveexplicit' => true, 'solvefactors' => true, 'solvenullwarn' => true,
                'solveradcan' => true, 'solvetrigwarn' => true, 'some' => true, 'sort' => true, 'space' => true,
                'sparse' => true, 'specint' => true, 'sposition' => true, 'sqfr' => true, 'sqrt' => true,
                'struve_h' => true, 'struve_l' => true, 'style' => true, 'sublis' => true, 'sublis_apply_lambda' => true,
                'sublist' => true, 'sublist_indices' => true, 'submatrix' => true, 'subset' => true, 'subsetp' => true,
                'subst' => true, 'substinpart' => true, 'substpart' => true, 'substring' => true, 'subvarp' => true,
                'sum' => true, 'sumcontract' => true, 'sumexpand' => true, 'supcase' => true, 'symbolp' => true,
                'symmdifference' => true, 'tan' => true, 'tanh' => true, 'taylor' => true, 'taylor_logexpand' => true,
                'taylor_order_coefficients' => true, 'taylor_simplifier' => true, 'taylor_truncate_polynomials' => true,
                'taylordepth' => true, 'taylorinfo' => true, 'taylorp' => true, 'taytorat' => true, 'tellsimp' => true,
                'tellsimpafter' => true, 'tenth' => true, 'third' => true, 'tlimit' => true, 'tlimswitch' => true,
                'todd_coxeter' => true, 'toeplitz' => true, 'transpose' => true, 'tree_reduce' => true,
                'triangularize' => true, 'trigexpand' => true, 'trigexpandplus' => true, 'trigexpandtimes' => true,
                'triginverses' => true, 'trigrat' => true, 'trigreduce' => true, 'trigsign' => true,
                'trigsimp' => true, 'true' => true, 'trunc' => true, 'und' => true, 'union' => true, 'unique' => true,
                'unsum' => true, 'untellrat' => true, 'vandermonde_matrix' => true, 'vect_cross' => true,
                'vectorpotential' => true, 'vectorsimp' => true, 'xreduce' => true, 'xthru' => true,
                'zerobern' => true, 'zeroequiv' => true, 'zerofor' => true, 'zeromatrix' => true, 'zeromatrixp' => true,
                'zeta' => true, 'pi' => true, 'e' => true, 'i' => true, 'float' => true,
                'round' => true, 'truncate' => true, 'decimalplaces' => true,
                'anyfloat' => true, 'anyfloatex' => true, 'expand' => true, 'expandp' => true, 'simplify' => true,
                'divthru' => true, 'factor' => true, 'factorp' => true, 'diff' => true, 'int' => true, 'rand' => true,
                'plot' => true, 'plot_implicit' => true, 'stack_validate_typeless' => true, 'stack_validate' => true,
                'alpha' => true, 'nu' => true, 'beta' => true, 'xi' => true, 'gamma' => true, 'omicron' => true,
                'delta' => true, 'pi' => true, 'epsilon' => true, 'rho' => true, 'zeta' => true, 'sigma' => true, 'eta' => true,
                'tau' => true, 'theta' => true, 'upsilon' => true, 'iota' => true, 'phi' => true, 'kappa' => true,
                'chi' => true, 'lambda' => true, 'psi' => true, 'mu' => true, 'omega' => true, 'parametric' => true,
                'discrete' => true, 'xlabel' => true, 'ylabel' => true, 'label' => true, 'cdf_bernoulli' => true,
                'cdf_beta' => true, 'cdf_binomial' => true, 'cdf_cauchy' => true, 'cdf_chi2' => true,
                'cdf_continuous_uniform' => true, 'cdf_discrete_uniform' => true, 'cdf_exp' => true, 'cdf_f' => true,
                'cdf_gamma' => true, 'cdf_general_finite_discrete' => true, 'cdf_geometric' => true, 'cdf_gumbel' => true,
                'cdf_hypergeometric' => true, 'cdf_laplace' => true, 'cdf_logistic' => true, 'cdf_lognormal' => true,
                'cdf_negative_binomial' => true, 'cdf_noncentral_chi2' => true, 'cdf_noncentral_student_t' => true,
                'cdf_normal' => true, 'cdf_pareto' => true, 'cdf_poisson' => true, 'cdf_rayleigh' => true,
                'cdf_student_t' => true, 'cdf_weibull' => true, 'kurtosis_bernoulli' => true, 'kurtosis_beta' => true,
                'kurtosis_binomial' => true, 'kurtosis_chi2' => true, 'kurtosis_continuous_uniform' => true,
                'kurtosis_discrete_uniform' => true, 'kurtosis_exp' => true, 'kurtosis_f' => true, 'kurtosis_gamma' => true,
                'kurtosis_general_finite_discrete' => true, 'kurtosis_geometric' => true, 'kurtosis_gumbel' => true,
                'kurtosis_gumbel' => true, 'kurtosis_hypergeometric' => true, 'kurtosis_laplace' => true,
                'kurtosis_logistic' => true, 'kurtosis_lognormal' => true, 'kurtosis_negative_binomial' => true,
                'kurtosis_noncentral_chi2' => true, 'kurtosis_noncentral_student_t' => true, 'kurtosis_normal' => true,
                'kurtosis_pareto' => true, 'kurtosis_poisson' => true, 'kurtosis_rayleigh' => true,
                'kurtosis_student_t' => true, 'kurtosis_weibull' => true, 'mean_bernoulli' => true, 'mean_beta' => true,
                'mean_binomial' => true, 'mean_chi2' => true, 'mean_continuous_uniform' => true,
                'mean_discrete_uniform' => true, 'mean_exp' => true, 'mean_f' => true, 'mean_gamma' => true,
                'mean_general_finite_discrete' => true, 'mean_geometric' => true, 'mean_gumbel' => true,
                'mean_hypergeometric' => true, 'mean_laplace' => true, 'mean_logistic' => true, 'mean_lognormal' => true,
                'mean_negative_binomial' => true, 'mean_noncentral_chi2' => true, 'mean_noncentral_student_t' => true,
                'mean_normal' => true, 'mean_pareto' => true, 'mean_poisson' => true, 'mean_rayleigh' => true,
                'mean_student_t' => true, 'mean_weibull' => true, 'pdf_bernoulli' => true, 'pdf_beta' => true,
                'pdf_binomial' => true, 'pdf_cauchy' => true, 'pdf_chi2' => true, 'pdf_continuous_uniform' => true,
                'pdf_discrete_uniform' => true, 'pdf_exp' => true, 'pdf_f' => true, 'pdf_gamma' => true,
                'pdf_general_finite_discrete' => true, 'pdf_geometric' => true, 'pdf_gumbel' => true,
                'pdf_hypergeometric' => true, 'pdf_laplace' => true, 'pdf_logistic' => true, 'pdf_lognormal' => true,
                'pdf_negative_binomial' => true, 'pdf_noncentral_chi2' => true, 'pdf_noncentral_student_t' => true,
                'pdf_normal' => true, 'pdf_pareto' => true, 'pdf_poisson' => true, 'pdf_rayleigh' => true,
                'pdf_student_t' => true, 'pdf_weibull' => true, 'quantile_bernoulli' => true, 'quantile_beta' => true,
                'quantile_binomial' => true, 'quantile_cauchy' => true, 'quantile_chi2' => true,
                'quantile_continuous_uniform' => true, 'quantile_discrete_uniform' => true, 'quantile_exp' => true,
                'quantile_f' => true, 'quantile_gamma' => true, 'quantile_general_finite_discrete' => true,
                'quantile_geometric' => true, 'quantile_gumbel' => true, 'quantile_hypergeometric' => true,
                'quantile_laplace' => true, 'quantile_logistic' => true, 'quantile_lognormal' => true,
                'quantile_negative_binomial' => true, 'quantile_noncentral_chi2' => true,
                'quantile_noncentral_student_t' => true, 'quantile_normal' => true,
                'quantile_pareto' => true, 'quantile_poisson' => true, 'quantile_rayleigh' => true,
                'quantile_student_t' => true, 'quantile_weibull' => true, 'random_bernoulli' => true, 'random_beta' => true,
                'random_binomial' => true, 'random_cauchy' => true, 'random_chi2' => true, 'random_continuous_uniform' => true,
                'random_discrete_uniform' => true, 'random_exp' => true, 'random_f' => true, 'random_gamma' => true,
                'random_general_finite_discrete' => true, 'random_geometric' => true, 'random_gumbel' => true,
                'random_hypergeometric' => true, 'random_laplace' => true, 'random_logistic' => true,
                'random_lognormal' => true, 'random_negative_binomial' => true, 'random_noncentral_chi2' => true,
                'random_noncentral_student_t' => true, 'random_normal' => true, 'random_pareto' => true,
                'random_poisson' => true, 'random_rayleigh' => true, 'random_student_t' => true, 'random_weibull' => true,
                'skewness_bernoulli' => true, 'skewness_beta' => true, 'skewness_binomial' => true, 'skewness_chi2' => true,
                'skewness_continuous_uniform' => true, 'skewness_discrete_uniform' => true, 'skewness_exp' => true,
                'skewness_f' => true, 'skewness_gamma' => true, 'skewness_general_finite_discrete' => true,
                'skewness_geometric' => true, 'skewness_gumbel' => true, 'skewness_hypergeometric' => true,
                'skewness_laplace' => true, 'skewness_logistic' => true, 'skewness_lognormal' => true,
                'skewness_negative_binomial' => true, 'skewness_noncentral_chi2' => true,
                'skewness_noncentral_student_t' => true, 'skewness_normal' => true, 'skewness_pareto' => true,
                'skewness_poisson' => true, 'skewness_rayleigh' => true, 'skewness_student_t' => true,
                'skewness_weibull' => true, 'std_bernoulli' => true, 'std_beta' => true, 'std_binomial' => true,
                'std_chi2' => true, 'std_continuous_uniform' => true, 'std_discrete_uniform' => true, 'std_exp' => true,
                'std_f' => true, 'std_gamma' => true, 'std_general_finite_discrete' => true, 'std_geometric' => true,
                'std_gumbel' => true, 'std_hypergeometric' => true, 'std_laplace' => true, 'std_logistic' => true,
                'std_lognormal' => true, 'std_negative_binomial' => true, 'std_noncentral_chi2' => true,
                'std_noncentral_student_t' => true, 'std_normal' => true, 'std_pareto' => true, 'std_poisson' => true,
                'std_rayleigh' => true, 'std_student_t' => true, 'std_weibull' => true, 'var_bernoulli' => true,
                'var_beta' => true, 'var_binomial' => true, 'var_chi2' => true, 'var_continuous_uniform' => true,
                'var_discrete_uniform' => true, 'var_exp' => true, 'var_f' => true, 'var_gamma' => true,
                'var_general_finite_discrete' => true, 'var_geometric' => true, 'var_gumbel' => true,
                'var_hypergeometric' => true, 'var_laplace' => true, 'var_logistic' => true, 'var_lognormal' => true,
                'var_negative_binomial' => true, 'var_noncentral_chi2' => true, 'var_noncentral_student_t' => true,
                'var_normal' => true, 'var_pareto' => true, 'var_poisson' => true, 'var_rayleigh' => true,
                'var_student_t' => true, 'var_weibull' => true, 'null' => true, 'net' => true, 'texsub' => true,
                'logbase' => true, 'day' => true, 'year' => true, 'rpm' => true, 'rev' => true, 'product' => true,
                'gal' => true, 'deg' => true, 'cal' => true, 'btu' => true, 'rem' => true,
                'nounor' => true, 'nounand' => true, 'xor' => true, 'nounint' => true, 'noundiff' => true, 'root' => true,
                'all' => true, 'none' => true, 'stackeq' => true, 'stacklet' => true,
                'stackunits' => true, 'stackvector' => true
                );

    /**
     * Upper case Greek letters are allowed.
     */
    private static $greekupper = array(
        'Alpha' => true, 'Beta' => true, 'Gamma' => true, 'Delta' => true, 'Epsilon' => true,
        'Zeta' => true, 'Eta' => true, 'Theta' => true, 'Iota' => true, 'Kappa' => true, 'Lambda' => true,
        'Mu' => true, 'Nu' => true, 'Xi' => true, 'Omicron' => true, 'Pi' => true, 'Rho' => true,
        'Sigma' => true, 'Tau' => true, 'Upsilon' => true, 'Phi' => true, 'Chi' => true, 'Psi' => true,
        'Omega' => true);

    /**
     * These lists are used by question authors for groups of words.
     * They should be lower case, because Maxima is lower case, and these correspond to Maxima names.
     */
    private static $keywordlists = array(
            '[[basic-algebra]]' => array('coeff', 'conjugate', 'cspline', 'disjoin', 'divisors',
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
    // @codingStandardsIgnoreStart
    private static $allowedchars =
            '0123456789,./\%&{}[]()$@!"\'?`^~*_+qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM:=><|: -';
    // @codingStandardsIgnoreEnd

    /**
     * @var all the permitted which are not allowed to be the final character.
     * Note, these are used in regular expression ranges, so - must be at the end, and ^ may not be first.
     */
    // @codingStandardsIgnoreStart
    private static $disallowedfinalchars = '/+*^#~=,_&`;:$-.<>';
    // @codingStandardsIgnoreEnd

    /**
     * @var all the permitted patterns in which spaces occur.  Simple find and replace.
     */
    private static $spacepatterns = array(
             ' or ' => 'STACKOR', ' and ' => 'STACKAND', 'not ' => 'STACKNOT',
             ' nounor ' => 'STACKNOUNOR', ' nounand ' => 'STACKNOUNAND');


    public function __construct($rawstring, $conditions = null, $ast = null) {
        // If null the validation will need to parse, in case of keyval just give the statement from bulk parsing.
        // Also means that potenttial unparseable insert starts magick will need to be done.
        $this->ast            = &$ast;
        $this->rawcasstring   = $rawstring;
        $this->answernote     = array();
        $this->errors         = array();
        // If null then the validate command has not yet been run.
        $this->valid          = null;

        if (!is_string($this->rawcasstring)) {
            throw new stack_exception('stack_cas_casstring: rawstring must be a string.');
        }
        $this->rawcasstring = $rawstring;
        if (!($conditions === null || is_array($conditions))) {
            throw new stack_exception('stack_cas_casstring: conditions must be null or an array.');
        }
        if ($conditions !== null && count($conditions) != 0) {
            $this->conditions   = $conditions;
        } else {
            $this->conditions   = array();
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

        // CAS strings must be non-empty.
        if (trim($this->casstring) == '') {
            $this->answernote[] = 'empty';
            $this->valid = false;
            return false;
        }

        // Now then do we already have validly parsed AST? if not what do we need to do to get one?
        // If we have then this is most certainly coming from keyval and $security better be 't'.
        if ($this->ast === null) {
            if ($security === 't') {
                try {
                    $this->ast = maxima_parser_utils::parse($this->casstring);
                } catch (SyntaxError $e) {
                    $this->valid = false;
                    $this->teacher_parse_errors($e);
                    return false;
                }
            } else {
                $logic = null;
                switch ($insertstars) {
                    case 5:
                        $logic = new stack_parser_logic_insertstars5();
                        break;
                    case 4:
                        $logic = new stack_parser_logic_insertstars4();
                        break;
                    case 3:
                        $logic = new stack_parser_logic_insertstars3();
                        break;
                    case 2:
                        $logic = new stack_parser_logic_insertstars2();
                        break;
                    case 1:
                        $logic = new stack_parser_logic_insertstars1();
                        break;
                    case 0:
                    default:
                        $logic = new stack_parser_logic_insertstars0();
                        break;
                }
                $this->ast = $logic->parse($this->casstring, $this->valid, $this->errors, $this->answernote, $syntax,
                        array(), array());
                if ($this->ast === null) {
                    $this->valid = false;
                    return false;
                }
            }

        }

        // Check that we have only one statement. Should not have comments either.
        if ($this->ast instanceof MP_Root && count($this->ast->items) > 1) {
            // We either have comments, semicolons or even dollars in play.
            $comments = 0;
            foreach ($this->ast->items as $node) {
                if ($node instanceof MP_Comment) {
                    $comments++;
                }
            }
            if ((count($this->ast->items) - $comments) > 1) {
                // There are multiple statements not good.
                $this->add_error(stack_string('stackCas_forbiddenChar', array( 'char' => ';')));
                $this->answernote[] = 'forbiddenChar';
                $this->valid = false;
                return;
            }
        }

        // Extract the key part out of the expression first so that our rules do not act on it.
        $this->key = '';
        $root = $this->ast;
        if ($root instanceof MP_Root) {
            $root = $root->items[0];
        }
        if ($root->statement instanceof MP_Operation && $root->statement->op === ':') {
            $this->key = $root->statement->lhs->toString();
            $root->replace($root->statement, $root->statement->rhs);
        }

        // Minimal accuracy matching of mixed use.
        $usages = array('functions' => array(), 'variables' => array());

        // Lets do this in phases, first go through all identifiers. Rewrite things related to them.
        $processidentifiers = function($node) use($security, $allowwords, $insertstars) {
            if ($node instanceof MP_Identifier) {
                return $this->process_identifier($node, $security, $allowwords, $insertstars);
            }
            return true;
        };

        $processfunctioncalls = function($node) use($security, $syntax, $allowwords, $insertstars) {
            if ($node instanceof MP_FunctionCall) {
                return $this->process_functioncall($node, $security, $syntax, $allowwords, $insertstars);
            }
            return true;
        };
        // We repeat this untill all is done. Identifiers first as they may turn into function calls.
        while ($this->ast->callbackRecurse($processidentifiers) !== true) {
            // Do nothing.
        }
        while ($this->ast->callbackRecurse($processfunctioncalls) !== true) {
            // Do nothing.
        }

        // Then the rest. Note that the security check happens here, as we might have done some changes
        // earlier and we cannot be certain that the results of those changes do not undo security...
        $mainloop = function($node)  use($security, $allowwords, $insertstars) {
            if ($node instanceof MP_Identifier) {
                $this->check_characters($node->value);
                if ($node->is_function_name()) {
                    $usages['functions'][$node->value] = true;
                } else {
                    $usages['variables'][$node->value] = true;
                }
            } else if ($node instanceof MP_Group) {
                if (count($node->items) === 0) {
                    $this->valid = false;
                    $this->add_error(stack_string('stackCas_forbiddenWord',
                            array('forbid' => stack_maxima_format_casstring('()'))));
                    $this->answernote[] = 'forbiddenWord';
                }
            } else if ($node instanceof MP_PrefixOp || $node instanceof MP_PostfixOp || $node instanceof MP_Operation) {
                $this->check_operators($node);
            } else if ($node instanceof MP_Comment && $security === 's') {
                $this->valid = false;
                $a = array('cmd' => stack_maxima_format_casstring($op));
                $this->add_error(stack_string('stackCas_spuriousop', $a));
                $this->answernote[] = 'spuriousop';
            } else if ($node instanceof MP_EvaluationFlag && $security === 's') {
                // Some bad commas are parsed correctly as evaluation flags.
                $this->valid = false;
                $this->add_error(stack_string('stackCas_unencpsulated_comma'));
                $this->answernote[] = 'unencpsulated_comma';
            }
            return true;
        };

        $this->ast->callbackRecurse($mainloop);

        foreach ($usages['variables'] as $key => $duh) {
            if (isset($usages['functions'][$key])) {
                $this->answernote[] = 'Variable_function';
            }
        }

        // Combined security check.
        $this->check_security($security, $allowwords);

        $root = $this->ast;
        if ($this->ast instanceof MP_Root) {
            $root = $this->ast->items[0];
        }

        $this->ast = $root;

        $this->casstring = $this->ast->toString();

        return $this->valid;
    }

    private function process_identifier($id, $security, $allowwords, $insertstars) {
        static $percentconstants = array('%e' => true, '%pi' => true, '%i' => true, '%j' => true,
                                             '%gamma' => true, '%phi' => true, '%and' => true,
                                             '%or' => true, '%union' => true);

        static $alwaysfunctiontrigs = array('sin' => true, 'cos' => true, 'tan' => true, 'sinh' => true, 'cosh' => true,
            'tanh' => true, 'sec' => true, 'cosec' => true, 'cot' => true, 'csc' => true, 'coth' => true, 'csch' => true,
            'sech' => true);
        static $alwaysfunctiontrigsa = array('asin' => true, 'acos' => true, 'atan' => true, 'asinh' => true,
            'acosh' => true, 'atanh' => true, 'asec' => true, 'acosec' => true, 'acot' => true, 'acsc' => true,
            'acoth' => true, 'acsch' => true, 'asech' => true);
        static $alwaysfunctiontrigsarc = array('arcsin' => true, 'arccos' => true, 'arctan' => true, 'arcsinh' => true,
            'arccosh' => true, 'arctanh' => true, 'arcsec' => true, 'arccosec' => true, 'arccot' => true, 'arccsc' => true,
            'arccoth' => true, 'arccsch' => true, 'arcsech' => true);

        static $alwaysfunctionother = array('log' => true, 'ln' => true, 'lg' => true, 'exp' => true, 'abs' => true,
            'sqrt' => true);

        // The return values here are false for structural changes and true otherwise.
        if ($id instanceof MP_Identifier) {
            $raw = $id->value;
            if (core_text::substr($raw, 0, 1) === '%') {
                // Is this a good constant?
                if (!isset($percentconstants[$raw])) {
                    $this->add_error(stack_string('stackCas_percent',
                        array('expr' => stack_maxima_format_casstring($this->casstring))));
                    $this->answernote[] = 'percent';
                    $this->valid   = false;
                    return true;
                }
            }
            if ($this->units) {
                // These could still be in stack_cas_casstring_units, but why do a separate call
                // and we need that strutural change detection here.
                if ($id->value === 'Torr') {
                    $id->value = 'torr';
                    return true;
                } else if ($id->value === 'kgm') {
                    // TODO: Does this really need that '/s' in the original? Or is it due to regexp?
                    // If not just drop the ifs...
                    if ($id->parentnode instanceof MP_Operation &&
                        $id->parentnode->lhs === $id &&
                        $id->parentnode->op === '/') {
                        $operand = $id->parentnode->leftmostofright(); // This is here for /s^2.
                        if ($operand instanceof MP_Identifier && $operand->value === 's') {
                            $id->value = 'kg';
                            $id->parentnode->replace($id, new MP_Operation('*', $id, new MP_Identifier('m')));
                            return false;
                        }
                    }
                }
            }
            // TODO: is the name is a common function e.g. sqrt or sin and it is not a function-name
            // we should warn about it... in cases where we have no op on right...
            if ($id->is_function_name() && isset($alwaysfunctiontrigsarc[$raw])) {
                // Using arcsin(x) is bad go for asin(x).
                // TODO: we could write the whole function arguments and all here...
                // We might even fix/rename this but atleast Matti opposes that.
                // This test should logically be in the process_functioncall side but we already have
                // the identifier lists here...
                $this->add_error(stack_string('stackCas_triginv',
                array('badinv' => stack_maxima_format_casstring($raw),
                        'goodinv' => stack_maxima_format_casstring('a' . core_text::substr($raw, 3)))));
                $this->answernote[] = 'triginv';
                $this->valid = false;
                return true;
            }
            if ($id->parentnode instanceof MP_Indexing && $id->parentnode->target === $id && (isset($alwaysfunctionother[$raw])
                    || isset($alwaysfunctiontrigs[$raw]) || isset($alwaysfunctiontrigsa[$raw]))) {
                // Examples such as sin[x].
                // TODO: other-functions should probably be handled separately with a separate error...
                // TODO: we could write the whole function arguments and all here...
                // We might even fix but atleast Matti opposes that.
                $this->add_error(stack_string('stackCas_trigparens',
                    array('forbid' => stack_maxima_format_casstring($raw.'(x)'))));
                $this->answernote[] = 'trigparens';
                $this->valid = false;
                return true;
            }
            if ($id->parentnode instanceof MP_Operation && (isset($alwaysfunctionother[$raw])
                    || isset($alwaysfunctiontrigs[$raw]) || isset($alwaysfunctiontrigsa[$raw]))) {
                // TODO: other-functions should probably be handled separately with a separate error...
                if ($id->parentnode->lhs === $id) {
                    $op = $id->parentnode->op;
                    $this->valid = false;
                    if ($op === '^') {
                        $this->add_error(stack_string('stackCas_trigexp',
                            array('forbid' => stack_maxima_format_casstring($raw.'^'))));
                        $this->answernote[] = 'trigexp';
                        return true;
                    } else if ($op === '*' || $op === '+' || $op === '-' || $op === '/') {
                        if ($op === '*' && $id->parentnode->position === false) {
                            // Note the special case of inserted star on top of an space...
                            $this->add_error(stack_string('stackCas_trigspace',
                                array('trig' => stack_maxima_format_casstring($raw.'(...)'))));
                            $this->answernote[] = 'trigspace';
                            return true;
                        } else {
                            $this->add_error(stack_string('stackCas_trigop',
                                array('trig' => stack_maxima_format_casstring($raw),
                                    'forbid' => stack_maxima_format_casstring($raw.$op))));
                            $this->answernote[] = 'trigop';
                            return true;
                        }
                    }
                } else {
                    $op = $id->parentnode->operationOnRight();
                    $this->valid = false;
                    if ($op === '*' || $op === '+' || $op === '-' || $op === '/') {
                        $this->add_error(stack_string('stackCas_trigop',
                            array('trig' => stack_maxima_format_casstring($fun),
                                  'forbid' => stack_maxima_format_casstring($fun.$op))));
                        $this->answernote[] = 'trigop';
                        return false;
                    }
                }
            }

            if (!$id->is_function_name() && core_text::substr($raw, 0, 4) === 'log_') {
                if ($id->parentnode instanceof MP_Operation && $id->parentnode->lhs === $id) {
                    // Examples such as log_...*(...).
                    if ($id->parentnode->op === '*' && $id->parentnode->rhs instanceof MP_Group) {
                        $nf = new MP_FunctionCall($id, $id->parentnode->rhs->items);
                        $id->parentnode->parentnode->replace($id->parentnode, $nf);
                        return false;
                    }
                    // Examples such aslog_...+zz(...).
                    if ($id->parentnode->rhs instanceof MP_FunctionCall) {
                        $nf = new MP_FunctionCall(new MP_Identifier($id->value . $id->parentnode->op
                                . $id->parentnode->rhs->name->toString()), $id->parentnode->rhs->arguments);
                        $id->parentnode->parentnode->replace($id->parentnode, $nf);
                        return false;
                    }
                    // Examples such aslog_...-a.
                    if ($id->parentnode->rhs instanceof MP_Atom) {
                        $ni = new MP_Identifier($id->value . $id->parentnode->op . $id->parentnode->rhs->toString());
                        $id->parentnode->parentnode->replace($id->parentnode, $ni);
                        return false;
                    }
                    // Examples such aslog_...-a^b.
                    if ($id->parentnode->rhs instanceof MP_Operation && $id->parentnode->rhs->lhs instanceof MP_Atom) {
                        $ni = new MP_Identifier($id->value . $id->parentnode->op . $id->parentnode->rhs->lhs->toString());
                        $id->parentnode->rhs->replace($id->parentnode->rhs->lhs, $ni);
                        $id->parentnode->parentnode->replace($id->parentnode, $id->parentnode->rhs);
                        return false;
                    }

                }
            }

            /* TODO: Do we need these or is the upper one complete now that the insert stars mess is separated.

            // The tricky bit is this... we want to eat ops to the right untill a (group) is found.
            // Though only if we are on the rhs of an assignment.
            if (!$id->is_function_name() && core_text::substr($raw, 0, 4) === 'log_' &&
                !($id->parentnode instanceof MP_Operation && $id->parentnode->lhs === $id && $id->parentnode->op === ':')) {
                $group = false; // The target.
                $container = false; // if we are within a list we must not look the group from
                // outside or from other elements in the list.
                // Check if there is a group to aim for.
                $container = $id->parentnode;
                while ($container instanceof MP_Operation) {
                    if ($container->parentnode === null) {
                        break;
                    }
                    $container = $container->parentnode;
                }
                $before = true;
                foreach ($container->asAList() as $node) {
                    if ($before) {
                        if ($node === $id) {
                            $before = false;
                        }
                    } else if ($node instanceof MP_Group || $node instanceof MP_FunctionCall){
                        $group = $node;
                        break;
                    }
                }
                if ($group === false) {
                    // TODO: We have a bad 'log_*' do we ned to whine?
                } else {
                    // We have something to aim for.
                    if ($id->parentnode instanceof MP_Operation && $id->parentnode->lhs === $id) {
                        if ($id->parentnode->rhs instanceof MP_Group) {
                            // The easy case. log_x*(x) due to spaces or some other reason.
                            if ($id->parentnode->op === '*') {
                                $id->parentnode->parentnode->replace($id->parentnode,
                                    new MP_FunctionCall($id, $id->parentnode->rhs->items));
                                return false;
                            } else {
                                // TODO: So do we allow any other op? 'log_10+(x)' means nothing
                            }
                        } else if ($id->parentnode->rhs instanceof MP_FunctionCall) {
                            // log_x*xx(x)
                            $newname = new MP_Identifier($raw . $id->parentnode->op . $id->parentnode->rhs->name->value);
                            $id->parentnode->rhs->replace($id->parentnode->rhs->name, $newname);
                            $id->parentnode->parentnode->replace($id->parentnode, $id->parentnode->rhs);
                            return false;
                        } else if ($id->parentnode->rhs instanceof MP_Atom) {
                            // log_x+x+xx(x)
                            $id->parentnode->parentnode->replace($id->parentnode, new MP_Identifier($id->parentnode->toString()));
                            return false;
                        } else if ($id->parentnode->rhs instanceof MP_Operation && $id->parentnode->rhs->lhs instanceof MP_Atom) {
                            // We only deal with atoms if it is not one then it does not work.
                            $newname = new MP_Identifier($raw . $id->parentnode->op . $id->parentnode->rhs->lhs->toString());
                            $id->parentnode->rhs->replace($id->parentnode->rhs->lhs, $newname);
                            $id->parentnode->parentnode->replace($id->parentnode, $id->parentnode->rhs);
                            return false;
                        } else if ($id->parentnode->rhs instanceof MP_Operation && $id->parentnode->rhs->lhs instanceof MP_Operation) {
                            // We have this.  That needs to be turned around.
                            //     op1                op1
                            //    /   \              /   \
                            //  log_   op2     =>  log_   op3
                            //        /   \              /   \
                            //      op3    z            x     op2
                            //     /   \                     /   \
                            //    x     y                   y     z
                            $op1 = $id->parentnode;
                            $op2 = $op1->rhs;
                            $op3 = $op2->lhs;
                            $y = $op3->rhs;
                            $op1->replace($op2, $op3);
                            $op2->replace($op3, $y);
                            $op3->replace($y, $op2);
                            return false;
                        }
                    } else if ($id->parentnode instanceof MP_Operation && $id->parentnode->rhs === $id) {
                        if ($id->parentnode->parentnode instanceof MP_Operation && $id->parentnode->parentnode->lhs === $id->parentnode) {
                            // We have this. That needs to be turned around.
                            //      op1          op2
                            //     /   \        /   \
                            //   op2    Y  =>  X    op1
                            //  /   \              /   \
                            // X   log_          log_   Y
                            $op1 = $id->parentnode->parentnode;
                            $op2 = $id->parentnode;
                            $op1->parentnode->replace($op1, $op2);
                            $op1->replace($op2, $id);
                            $op2->replace($id, $op1);
                            return false;
                        } else if ($id->parentnode->parentnode instanceof MP_Operation && $id->parentnode->parentnode->rhs === $id->parentnode) {
                            // We have this... or we might not have depends if there is an op1...
                            //
                            //     op1                op2
                            //    /   \              /   \
                            //   op2   Y            X2   op3
                            //  /   \                   /  ...
                            // X2    op3        =>     X3    opN
                            //      /  ...                  /   \
                            //     X3    opN               XN    op1
                            //          /   \                   /   \
                            //         XN   log_              log_   Y
                            $opN = $id->parentnode;
                            $i = $id;
                            $op1 = false;
                            while ($i->parentnode instanceof MP_Operation) {
                                if ($i->parentnode->lhs === $i) {
                                    $op1 = $i->parentnode;
                                    break;
                                }
                                $i = $i->parentnode;
                            }
                            if ($op1 !== false) {
                                $op2 = $op1->lhs;
                                $op1->replace($op1->lhs, $id);
                                $opN->replace($id, $op1);
                                $op1->parentnode->replace($op1, $op2);
                                return false;
                            }
                        }
                    }
                }
            }
            */

        } else {
            // Other params have been vetted already.
            throw new stack_exception('stack_cas_casstring: process_identifier: called with non identifier');
        }
        return true;
    }

    private function process_functioncall($fc, $security, $syntax, $allowwords, $insertstars) {
        // The return values here are false for structural changes and true otherwise.
        if ($fc instanceof MP_FunctionCall) {
            $name = $fc->name;
            if ($name instanceof MP_Identifier) {
                // Known name branch.
                $name = $name->value;
                // Handle some renames.
                if ($name === 'log10') {
                    $fc->name->value = 'log_10';
                    $name = 'log_10';
                }
                if (core_text::substr($name, 0, 4) === 'log_') {
                    $num = core_text::substr($name, 4);
                    if (ctype_digit($num)) {
                        $fc->name->value = 'lg';
                        // Not actually replace this is append.
                        $fc->replace(-1, new MP_Integer((int)$num));
                    } else {
                        $fc->name->value = 'lg';
                        // Now things get difficult, but as we have just plugged terms to that part
                        // of the identifier we can just parse it as a casstring.
                        $operand = new MP_Identifier($num);
                        $cs = new stack_cas_casstring($num);
                        $cs->set_units($this->units);
                        if ($cs->get_valid($security, $syntax, $insertstars, $allowwords)) {
                            // There are no evaluationflags here.
                            $operand = $cs->ast->statement;
                        } else {
                            if ($cs->ast !== null) {
                                $operand = $cs->ast->statement;
                            }
                            $this->valid = false;
                        }
                        foreach ($cs->answernote as $note) {
                            $this->answernote[] = $note;
                        }
                        foreach ($cs->errors as $err) {
                            $this->errors[] = $err;
                        }

                        // Not actually replace this is append.
                        $fc->replace(-1, $operand);
                    }
                    $this->answernote[] = 'logsubs';
                    return false;
                }

            } else {
                // Unknown name branch.
            }
        } else {
            // Other params have been vetted already.
            throw new stack_exception('stack_cas_casstring: process_functioncall: called with non functioncall');
        }
        return true;
    }

    private function check_characters($string) {
        // We are only checking identifiers now so no need for ops or newlines...
        // TODO: do we need to check? All the chars that go through the parser should work
        // with maxima... although πππππππ?

        // Only permit the following characters to be sent to the CAS.
        $allowedcharsregex = '~[^' . preg_quote(self::$allowedchars, '~') . ']~u';
        // Check for permitted characters.
        if (preg_match_all($allowedcharsregex, $string, $matches)) {
            $invalidchars = array();
            foreach ($matches as $match) {
                $badchar = $match[0];
                if (!array_key_exists($badchar, $invalidchars)) {
                    switch ($badchar) {
                        case "\n":
                            $invalidchars[$badchar] = "\\n";
                            break;
                        case "\r":
                            $invalidchars[$badchar] = "\\r";
                            break;
                        case "\t":
                            $invalidchars[$badchar] = "\\t";
                            break;
                        case "\v":
                            $invalidchars[$badchar] = "\\v";
                            break;
                        case "\e":
                            $invalidchars[$badchar] = "\\e";
                            break;
                        case "\f":
                            $invalidchars[$badchar] = "\\f";
                            break;
                        default:
                            $invalidchars[$badchar] = $badchar;
                    }
                }
            }
            $this->add_error(stack_string('stackCas_forbiddenChar', array( 'char' => implode(", ", array_unique($invalidchars)))));
            $this->answernote[] = 'forbiddenChar';
            $this->valid = false;
        }
    }



    private function check_operators($opnode) {
        // This gets tricky as the old one mainly focused to syntax errors...
        // But atleast we have the chained ones still.
        static $ineqs = array('>' => true, '<' => true, '<=' => true, '>=' => true, '=' => true);
        if ($opnode instanceof MP_Operation && isset($ineqs[$opnode->op])) {
            // TODO: This was security 's' in the old system, but that probably was only due to the test failing.
            if ($opnode->lhs instanceof MP_Operation && isset($ineqs[$opnode->lhs->op])
                    || $opnode->rhs instanceof MP_Operation && isset($ineqs[$opnode->rhs->op])) {
                $this->add_error(stack_string('stackCas_chained_inequalities'));
                $this->answernote[] = 'chained_inequalities';
                $this->valid = false;
            }
        }
        if ($opnode instanceof MP_PrefixOp && ($opnode->op === "?" || $opnode->op === "?? " || $opnode->op === "? ")) {
            $this->add_error(stack_string('stackCas_qmarkoperators'));
            $this->answernote[] = 'qmark';
            $this->valid = false;
        }
        if ($opnode instanceof MP_PrefixOp && $opnode->op === "'" && $security === 's') {
            $this->add_error(stack_string('stackCas_apostrophe'));
            $this->answernote[] = 'apostrophe';
            $this->valid = false;
        }
        // 1..1, essenttially a matrix multiplication of float of particular presentation.
        if ($opnode instanceof MP_Operation && $opnode->op === '.') {
            // TODO: this should just fail in parser...
            // There is an parser error here:
            // 0.1..1.2
            // -------- MP_Statement
            // -------- MP_Operation .
            // ---      MP_Float 0.1
            //     ---- MP_Operation .
            //     --   MP_Float 0.1
            //        - MP_Integer 2
            $operand = $opnode->leftmostofright();
            if ($operand instanceof MP_Float && $operand->raw !== null &&
                    core_text::substr($operand->raw, 0, 1) === '.') {
                $this->valid = false;
                $a = array();
                $a['cmd']  = stack_maxima_format_casstring('..');
                $this->add_error(stack_string('stackCas_spuriousop', $a));
                $this->answernote[] = 'spuriousop';
            }
        }
    }

    /**
     * Check for forbidden CAS commands, based on security level
     *
     * @return bool|string true if passes checks if fails, returns string of forbidden commands
     */
    private function check_security($security, $rawallowwords) {
        // Names of functions that apply functions, so that we can check the first parameter.
        static $mapfunctions = array('apply' => true, 'map' => true,
                                         'matrixmap'  => true, 'scanmap' => true, 'maplist' => true,
                                         'outermap' => true, 'fullmapl' => true, 'fullmap' => true,
                                         'funmake' => true);

        // Create a minimal cache to store words as keys.
        // This gives faster searching using the search functionality of that map.
        if (self::$cache === false) {
            self::$cache = array(
                    'allows' => array(),
                    'merged-sallow' => array_merge(self::$studentallow, self::$greekupper,
                            stack_cas_casstring_units::get_permitted_units(2)),
                    'teachernotallow' => self::$teachernotallow,
                    'studentallow' => self::$studentallow,
            );
        }

        $allow = null;
        if (!isset(self::$cache['allows'][$rawallowwords])) {
            // Sort out any allowwords.
            $allow = array();
            if (trim($rawallowwords) != '') {
                $allowwords = explode(',', $rawallowwords);
                foreach ($allowwords as $kw) {
                    if (!isset(self::$globalforbid['functions'][strtolower($kw)]) &&
                        !isset(self::$globalforbid['variables'][strtolower($kw)])) {
                        $allow[trim($kw)] = true;
                    } else {
                        throw new stack_exception('stack_cas_casstring: check_security: ' .
                                'attempt made to allow gloabally forbidden keyword: ' . $kw);
                    }
                }
            }
            self::$cache['allows'][$rawallowwords] = $allow;
        } else {
            // To lessen the changes to the code and pointless map lookups we read it here to this variable.
            $allow = self::$cache['allows'][$rawallowwords];
        }


        // First extract things of interest from the tree, i.e. functioncalls,
        // variable references and operations.
        $ofinterest = array();
        $extraction = function($node) use (&$ofinterest){
            if ($node instanceof MP_Identifier ||
                $node instanceof MP_FunctionCall ||
                $node instanceof MP_Operation ||
                $node instanceof MP_PrefixOp ||
                $node instanceof MP_PostfixOp) {
              $ofinterest[] = $node;
            }
            return true;
        };
        $this->ast->callbackRecurse($extraction);

        // Separate the identifiers we meet for latter use. Not the nodes
        // the string identifiers. Key is the value so unique from the start.
        $functionnames = array();
        $writtenvariables = array();
        $variables = array();
        $operators = array();

        // Now loop over the initially found things of interest. Note that
        // the list may grow as we go forward and unwrap things.
        $i = 0;
        while ($i < count($ofinterest)) {
            $node = $ofinterest[$i];
            $i = $i + 1;

            if ($node instanceof MP_Operation || $node instanceof MP_PrefixOp || $node instanceof MP_PostfixOp) {
                // We could just strip these out in the recurse but maybe we want
                // to check something in the future.
                $operators[$node->op] = true;
            } else if ($node instanceof MP_Identifier && !$node->is_function_name()) {
                $variables[$node->value] = true;
                if ($node->is_being_written_to()) {
                    // This can be used to check if someone tries to redefine
                    // %pi or some other important thing.
                    $writtenvariables[$node->value] = true;
                }
            } else if ($node instanceof MP_FunctionCall) {
                $notsafe = true;
                if ($node->name instanceof MP_Identifier || $node->name instanceof MP_String) {
                    $notsafe = false;
                    $functionnames[$node->name->value] = true;
                    $safemap = false;
                    if (isset(self::$mapfunctions[$node->name->value])) {
                        // If it is an apply or map function throw it in for
                        // validation.
                        switch ($node->name->value) {
                            case 'apply':
                            case 'funmake':
                                $safemap = true;

                            // TODO: add errors about applying to wrong types
                            // of things and check them.
                            case 'fullmapl':
                            case 'fullmap':

                            case 'map':
                            case 'maplist':
                            case 'scanmap':

                            case 'matrixmap':
                            case 'outermap':

                            default:
                                // NOTE: this is a correct virtual form for only
                                // 'apply' and 'funmake' others will need to be
                                // written out as multiplce calls. And are
                                // therefore still unsafe atleast untill we do
                                // the writing out...
                                $virtualfunction = new MP_FunctionCall($node->arguments[0], array_slice($node->arguments, 1));
                                $virtualfunction->position['virtual'] = true;
                                $ofinterest[] = $virtualfunction;
                                break;
                        }
                        if (isset($node->position['virtual']) && !$safemap) {
                            // TODO: localise "Function application through mapping
                            // functions has depth limits as it hides things."
                            $this->add_error(stack_string('stackCas_deepmap'));
                            $this->answernote[] = 'deepmap';
                            $this->valid = false;
                        }
                    }

                } else if ($node->name instanceof MP_FunctionCall) {
                    $outter = $node->name;
                    if (($outter->name instanceof MP_Identifier || $outter->name instanceof MP_String)
                        && $outter->name->value === 'lambda') {
                        // This is safe, but we will not go out of our way to identify the function from further.
                        $notsafe = false;
                    } else {
                        // Calling the result of a function that is not lambda.
                        $this->add_error(stack_string('stackCas_callingasfunction',
                                                      array('problem' => stack_maxima_format_casstring($node->toString()))));
                        $this->answernote[] = 'forbiddenWord';
                        $this->valid = false;
                    }
                } else if ($node->name instanceof MP_Group) {
                    $outter = $node->name->items[count($node->name->items) - 1];
                    // We do this due to this (1,(cos,sin))(x) => sin(x)
                    $notsafe = false;
                    $virtualfunction = new MP_FunctionCall($outter, $node->arguments);
                    $virtualfunction->position['virtual'] = true;
                    $ofinterest[] = $virtualfunction;
                } else if ($node->name instanceof MP_Indexing) {
                    if (count($node->name->indices) === 1 && $node->name->target instanceof MP_List) {
                        $ind = -1;
                        if (count($node->name->indices[0]) === 1 && $node->name->indices[0]->items[0] instanceof MP_Integer) {
                            $ind = $node->name->indices[0]->items[0]->value - 1;
                        }
                        if ($ind >= 0 && $ind < count($node->name->target->items)) {
                            // We do this due to this [1,(cos,sin)][2](x) => sin(x)
                            $notsafe = false;
                            $virtualfunction = new MP_FunctionCall($node->name->target->items[$ind], $node->arguments);
                            $virtualfunction->position['virtual'] = true;
                            $ofinterest[] = $virtualfunction;
                        } else {
                            $notsafe = false;
                            foreach ($node->name->target->items as $id) {
                                $virtualfunction = new MP_FunctionCall($id, $node->arguments);
                                $virtualfunction->position['virtual'] = true;
                                $ofinterest[] = $virtualfunction;
                            }
                        }
                    }
                }
                if ($notsafe) {
                    // As in not safe identification of the function to be
                    // called.
                    $this->add_error(stack_string('stackCas_applyingnonobviousfunction',
                                                  array('problem' => $node->toString())));
                    $this->answernote[] = 'forbiddenWord';
                    $this->valid = false;
                }
            }
        }

        // TODO: build the typed security checks, but for now just dump all in.
        $identifiers = array_keys(array_merge($functionnames, $variables));

        $strinkeywords = array();

        // Filter out some of these matches.
        foreach ($identifiers as $key) {
            // The old number test is irrelevant, no identifier will ever start with a number.
            // And if someone wants to build string named functions with just numbers it is their right.
            if (strlen($key) > 2) {
                array_push($strinkeywords, $key);
            }
            // This is not really a security issue, but it relies on access to the $allowwords.
            // It is also a two letter string, which are normally permitted.
            if ($security == 's' and $key == 'In' and !isset($allow[$key])) {
                $this->add_error(stack_string('stackCas_badLogIn'));
                $this->answernote[] = 'stackCas_badLogIn';
                $this->valid = false;
            }

            if ($this->units) {
                // Check for unit synonyms.
                list ($fndsynonym, $answernote, $synonymerr) = stack_cas_casstring_units::find_units_synonyms($key);
                if ($security == 's' and $fndsynonym and !isset($allow[$key])) {
                    $this->add_error($synonymerr);
                    $this->answernote[] = $answernote;
                    $this->valid = false;
                }
            }
        }

        // Check for global forbidden words before we split over underscores.
        foreach ($strinkeywords as $key) {
            if (isset(self::$globalforbid['functions'][strtolower($key)]) ||
                    isset(self::$globalforbid['variables'][strtolower($key)])) {
                // Very bad!
                $this->add_error(stack_string('stackCas_forbiddenWord',
                        array('forbid' => stack_maxima_format_casstring(strtolower($key)))));
                $this->answernote[] = 'forbiddenWord';
                $this->valid = false;
            }
        }
        if ($this->valid == false) {
            return;
        }

        $keywords = array();
        // Create an array of unique keywords.  For students we split over underscores.
        // We don't do this for teachers otherwise
        // too many existing question break because teachers have defined function names with underscores.
        foreach ($strinkeywords as $key) {
            // Delete function names which students are allowed from the list of keywords before we split over underscore.
            if (!isset(self::$cache['merged-sallow'][$key])) {
                if ($security == 't') {
                    $keywords[$key] = true;
                } else {
                    foreach (explode("_", $key) as $kw) {
                        if (strlen($kw) > 2) {
                            $keywords[$kw] = true;
                        }
                    }
                }
            }
        }

        $strinkeywords = array_keys($keywords);

        foreach ($strinkeywords as $key) {
            // Check again for global forbidden words.
            if (isset(self::$globalforbid['functions'][strtolower($key)]) ||
                    isset(self::$globalforbid['variables'][strtolower($key)])) {
                // Very bad!
                $this->add_error(stack_string('stackCas_forbiddenWord',
                        array('forbid' => stack_maxima_format_casstring(strtolower($key)))));
                $this->answernote[] = 'forbiddenWord';
                $this->valid = false;
            } else {
                if ($security == 't') {
                    if (isset(self::$cache['teachernotallow'][strtolower($key)])) {
                        // If a teacher check against forbidden commands.
                        $this->add_error(stack_string('stackCas_unsupportedKeyword',
                                array('forbid' => stack_maxima_format_casstring($key))));
                        $this->answernote[] = 'unsupportedKeyword';
                        $this->valid = false;
                    }
                } else {
                    // Only allow the student to use set commands.
                    if (!isset($allow[$key]) && !isset(self::$cache['merged-sallow'][$key])) {
                        $this->valid = false;
                        if (isset(self::$cache['studentallow'][strtolower($key)]) || isset($allow[strtolower($key)])) {
                            // We have spotted a case sensitivity problem.
                            // Did they try to enter an upper case Greek letter perhaps?
                            if (isset(self::$greekupper[ucfirst(strtolower($key))]) && strtoupper($key) == $key) {
                                $this->add_error(stack_string('stackCas_unknownFunctionCase',
                                    array('forbid' => stack_maxima_format_casstring($key),
                                        'lower' => stack_maxima_format_casstring(ucfirst(strtolower($key))))));
                            } else {
                                $this->add_error(stack_string('stackCas_unknownFunctionCase',
                                    array('forbid' => stack_maxima_format_casstring($key),
                                        'lower' => stack_maxima_format_casstring(strtolower($key)))));
                            }
                            $this->answernote[] = 'unknownFunctionCase';
                        } else if ($err = stack_cas_casstring_units::check_units_case($key)) {
                            // We have spotted a case sensitivity problem in the units.
                            $this->add_error($err);
                                $this->answernote[] = 'unknownUnitsCase';
                        } else {
                            // We have no idea what they have done.
                            $this->add_error(stack_string('stackCas_unknownFunction',
                                    array('forbid' => stack_maxima_format_casstring($key))));
                            $this->answernote[] = 'unknownFunction';
                        }
                    }
                    // Else we have not found any security problems with keywords.
                }
            }
        }
    }

    /**
     * Check for CAS commands which appear in the $keywords array, which are not just single letter variables.
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
            $val = trim($val);
            $kw = strtolower($val);
            if (array_key_exists($kw, self::$keywordlists)) {
                $kws = array_merge($kws, self::$keywordlists[$kw]);
            } else if ('COMMA_TAG' === $val) {
                $kws[] = ',';
            } else if ($val !== '') {
                $kws[] = $val;
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
        $this->errors[] = trim($err);
    }


    /*********************************************************/
    /* Return and modify information                         */
    /*********************************************************/

    public function get_valid($security = 's', $syntax = true, $insertstars = 0, $allowwords = '') {
        if (null === $this->valid) {
            $this->validate($security, $syntax, $insertstars, $allowwords);
        }
        return $this->valid;
    }

    public function set_valid($val) {
        $this->valid = $val;
    }

    public function get_errors($raw = 'implode') {
        if (null === $this->valid) {
            $this->validate();
        }
        if ($raw == 'implode') {
            return implode(' ', array_unique($this->errors));
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

    public function get_dispvalue() {
        return $this->dispvalue;
    }

    public function get_conditions() {
        return $this->conditions;
    }

    public function set_key($key, $appendkey=false) {
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

    public function set_units($val) {
        $this->units = $val;
    }

    public function set_value($val) {
        $this->value = $val;
    }

    public function set_display($val) {
        $this->display = $val;
    }

    public function set_dispvalue($val) {
        $this->dispvalue = $val;
    }

    public function get_answernote($raw = 'implode') {
        if (null === $this->valid) {
            $this->validate();
        }
        if ($raw === 'implode') {
            return trim(implode(' | ', array_unique($this->answernote)));
        }
        return $this->answernote;
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
            $this->errors[] = $err;
            // Old behaviour was to return the combined errors, but apparently it was not used in master?
            // TODO: maybe remove the whole return?
            return $this->get_errors();
        }
    }

    // If we "CAS validate" this string, then we need to set various options.
    // If the teacher's answer is null then we use typeless validation, otherwise we check type.
    public function set_cas_validation_casstring($key, $forbidfloats = true,
                    $lowestterms = true, $tans = null, $validationmethod, $allowwords = '') {

        if (!($validationmethod == 'checktype' || $validationmethod == 'typeless' || $validationmethod == 'units'
            || $validationmethod == 'unitsnegpow' || $validationmethod == 'equiv' || $validationmethod == 'numerical')) {
            throw new stack_exception('stack_cas_casstring: validationmethod must one of "checktype", "typeless", ' .
                '"units" or "unitsnegpow" or "equiv" or "numerical", but received "'.$validationmethod.'".');
        }
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

        $fltfmt = stack_utils::decimal_digits($starredanswer);
        $fltfmt = $fltfmt['fltfmt'];

        $this->casstring = 'stack_validate(['.$starredanswer.'], '.$forbidfloats.','.$lowestterms.','.$tans.')';
        if ($validationmethod == 'typeless') {
            // Note, we don't pass in the teacher's as this option is ignored by the typeless validation.
            $this->casstring = 'stack_validate_typeless(['.$starredanswer.'], '.$forbidfloats.', '.$lowestterms.', false, false)';
        }
        if ($validationmethod == 'numerical') {
            $this->casstring = 'stack_validate_typeless(['.$starredanswer.'],
                    '.$forbidfloats.', '.$lowestterms.', false, '.$fltfmt.')';
        }
        if ($validationmethod == 'equiv') {
            $this->casstring = 'stack_validate_typeless(['.$starredanswer.'], '.$forbidfloats.', '.$lowestterms.', true, false)';
        }
        if ($validationmethod == 'units') {
            // Note, we don't pass in forbidfloats as this option is ignored by the units validation.
            $this->casstring = 'stack_validate_units(['.$starredanswer.'], '.$lowestterms.', '.$tans.', "inline", '.$fltfmt.')';
        }
        if ($validationmethod == 'unitsnegpow') {
            // Note, we don't pass in forbidfloats as this option is ignored by the units validation.
            $this->casstring = 'stack_validate_units(['.$starredanswer.'], '.$lowestterms.', '.$tans.', "negpow", '.$fltfmt.')';
        }

        return true;
    }

    /**
     *  Replace the contents of strings to the stringles version.
     */
    private function strings_replace($stringles) {
        // NOTE: This function should not exist, as this should only happen at the end of validate().
        // We still have some error messages that need it.
        $strings = stack_utils::all_substring_strings($this->rawcasstring);
        if (count($strings) > 0) {
            $split = explode('""', $stringles);
            $stringbuilder = array();
            $i = 0;
            foreach ($strings as $string) {
                $stringbuilder[] = $split[$i];
                $stringbuilder[] = $string;
                $i++;
            }
            $stringbuilder[] = $split[$i];
            $stringles = implode('"', $stringbuilder);
        }
        return $stringles;
    }

    /**
     *  This function decodes the error generated by Maxima into meaningful notes.
     *  */
    public function decode_maxima_errors($error) {
        $searchstrings = array('DivisionZero', 'CommaError', 'Illegal_floats', 'Lowest_Terms', 'SA_not_matrix',
                'SA_not_list', 'SA_not_equation', 'SA_not_inequality', 'SA_not_set', 'SA_not_expression',
                'Units_SA_excess_units', 'Units_SA_no_units', 'Units_SA_only_units', 'Units_SA_bad_units',
                'Units_SA_errorbounds_invalid', 'Variable_function', 'Bad_assignment');

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

    private function teacher_parse_errors($e) {

        $foundchar = $e->found;
        $previouschar = null;
        $nextchar = null;

        if ($e->grammarOffset >= 1) {
            $previouschar = core_text::substr($this->rawcasstring, $e->grammarOffset - 1, 1);
        }
        if ($e->grammarOffset < (core_text::strlen($this->rawcasstring) - 1)) {
            $nextchar = core_text::substr($this->rawcasstring, $e->grammarOffset + 1, 1);
        }

        if ($foundchar === ':' && (core_text::strpos($this->rawcasstring, ':lisp') !== false)) {
            $this->add_error(stack_string('stackCas_forbiddenWord',
                    array('forbid' => stack_maxima_format_casstring('lisp'))));
            $this->answernote[] = 'forbiddenWord';
        } else {
            $this->add_error($e->getMessage());
            $this->answernote[] = 'ParseError';

        }
    }


}
