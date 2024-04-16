<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * STACK question type backup code.
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Provides the information to backup STACK questions
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_qtype_stack_plugin extends backup_qtype_plugin {

    /**
     * @return backup_plugin_element the qtype information to attach to question element.
     */
    protected function define_question_plugin_structure() {

        // Define the virtual plugin element with the condition to fulfill.
        $plugin = $this->get_plugin_element(null, '../../qtype', 'stack');

        // Create one standard named plugin element (the visible container).
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect the visible container ASAP.
        $plugin->add_child($pluginwrapper);

        // Now create the necessary elements.
        $stackoptions = new backup_nested_element('stackoptions', ['id'],
                ['stackversion', 'questionvariables', 'specificfeedback', 'specificfeedbackformat',
                      'questionnote', 'questionsimplify', 'assumepositive', 'assumereal',
                      'prtcorrect', 'prtcorrectformat', 'prtpartiallycorrect', 'prtpartiallycorrectformat',
                      'prtincorrect', 'prtincorrectformat', 'decimals', 'multiplicationsign', 'sqrtsign',
                      'complexno', 'inversetrig', 'logicsymbol', 'matrixparens', 'variantsselectionseed']);

        $stackinputs = new backup_nested_element('stackinputs');
        $stackinput = new backup_nested_element('stackinput', ['id'],
                ['name', 'type', 'tans', 'boxsize', 'strictsyntax', 'insertstars',
                       'syntaxhint', 'syntaxattribute', 'forbidwords', 'allowwords', 'forbidfloat', 'requirelowestterms',
                       'checkanswertype', 'mustverify', 'showvalidation', 'options']);

        $stackprts = new backup_nested_element('stackprts');
        $stackprt = new backup_nested_element('stackprt', ['id'],
                ['name', 'value', 'autosimplify', 'feedbackstyle', 'feedbackvariables', 'firstnode']);

        $stackprtnodes = new backup_nested_element('stackprtnodes');
        $stackprtnode = new backup_nested_element('stackprtnode', ['id'],
                ['nodename', 'answertest', 'sans', 'tans', 'testoptions', 'quiet',
                      'truescoremode', 'truescore', 'truepenalty', 'truenextnode',
                      'trueanswernote', 'truefeedback', 'truefeedbackformat',
                      'falsescoremode', 'falsescore', 'falsepenalty', 'falsenextnode',
                      'falseanswernote', 'falsefeedback', 'falsefeedbackformat']);

        $stackqtests = new backup_nested_element('stackqtests');
        $stackqtest = new backup_nested_element('stackqtest', ['id'], ['testcase', 'description', 'timemodified']);

        $stackqtestinputs = new backup_nested_element('stackqtestinputs');
        $stackqtestinput = new backup_nested_element('stackqtestinput', ['id'],
                ['inputname', 'value']);

        $stackqtestexpecteds = new backup_nested_element('stackqtestexpecteds');
        $stackqtestexpected = new backup_nested_element('stackqtestexpected', ['id'],
                ['prtname', 'expectedscore', 'expectedpenalty', 'expectedanswernote']);

        // Note, we intentionally don't backup stack_qtest_results. That is derived data.

        $stackdeployedseeds = new backup_nested_element('stackdeployedseeds');
        $stackdeployedseed = new backup_nested_element('stackdeployedseed', ['id'], ['seed']);

        // Build the tree.
        $pluginwrapper->add_child($stackoptions);

        $pluginwrapper->add_child($stackinputs);
        $stackinputs->add_child($stackinput);

        $pluginwrapper->add_child($stackprts);
        $stackprts->add_child($stackprt);
        $stackprt->add_child($stackprtnodes);
        $stackprtnodes->add_child($stackprtnode);

        $pluginwrapper->add_child($stackqtests);
        $stackqtests->add_child($stackqtest);
        $stackqtest->add_child($stackqtestinputs);
        $stackqtestinputs->add_child($stackqtestinput);
        $stackqtest->add_child($stackqtestexpecteds);
        $stackqtestexpecteds->add_child($stackqtestexpected);

        $pluginwrapper->add_child($stackdeployedseeds);
        $stackdeployedseeds->add_child($stackdeployedseed);

        // Set source to populate the data.
        $stackoptions->set_source_table('qtype_stack_options', ['questionid' => backup::VAR_PARENTID]);
        $stackinput->set_source_table('qtype_stack_inputs', ['questionid' => backup::VAR_PARENTID]);
        $stackprt->set_source_table('qtype_stack_prts', ['questionid' => backup::VAR_PARENTID]);
        $stackprtnode->set_source_table('qtype_stack_prt_nodes',
                ['questionid' => '../../../../../../../id', 'prtname' => '../../name']);
        $stackqtest->set_source_sql(
                'SELECT * FROM {qtype_stack_qtests} WHERE questionid = ? ORDER BY testcase',
                [backup::VAR_PARENTID]);
        $stackqtestinput->set_source_table('qtype_stack_qtest_inputs',
                ['questionid' => '../../../../../../../id', 'testcase' => '../../testcase']);
        $stackqtestexpected->set_source_table('qtype_stack_qtest_expected',
                ['questionid' => '../../../../../../../id', 'testcase' => '../../testcase']);
        $stackdeployedseed->set_source_sql(
                'SELECT * FROM {qtype_stack_deployed_seeds} WHERE questionid = ? ORDER BY id',
                [backup::VAR_PARENTID]);

        return $plugin;
    }

    /**
     * Returns one array with filearea => mappingname elements for the qtype
     *
     * Used by {@link get_components_and_fileareas} to know about all the qtype
     * files to be processed both in backup and restore.
     */
    public static function get_qtype_fileareas() {
        return [
            'specificfeedback'     => 'question_created',
            'prtcorrect'           => 'question_created',
            'prtpartiallycorrect'  => 'question_created',
            'prtincorrect'         => 'question_created',
            'prtnodetruefeedback'  => 'qtype_stack_prt_nodes',
            'prtnodefalsefeedback' => 'qtype_stack_prt_nodes',
        ];
    }
}
