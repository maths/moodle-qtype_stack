<?php
namespace qtype_stack\check;
use core\check\check;
use core\check\result;
require_once($CFG->dirroot . '/question/type/stack/stack/cas/connectorhelper.class.php');

class version extends check {

    public function get_action_link(): ?\action_link {
        $url = new \moodle_url('/admin/settings.php', ['section' => 'qtypesettingstack']);
        return new \action_link($url, stack_string('pluginname'));
    }

    public function get_result(): result {
        try {
            list($message, $details, $result) = \stack_connection_helper::stackmaxima_version_healthcheck();
            $message = stack_string($message, $details);
        } catch (\Exception $e) {
            $message = stack_string('healthchecksstackmaximaversion') . ': ' . $e->getMessage();
            $result = false;
        }
        $details = '';
        $status = $result ? result::OK : result::ERROR;
        return new result($status, $message, $details);
    }
}