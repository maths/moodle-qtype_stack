<?php

require_once(__DIR__ . '/../../emulation/Language.php');
require_once(__DIR__ . '/../../config.php');

global $CFG;
foreach ($CFG->supportedlanguages as $lang) {
    if ($lang !== '*') {
        install_language($lang);
        $parent = get_parent_language($lang);
        if ($parent !== $lang && !in_array($parent, $CFG->supportedlanguages)) {
            install_language($parent);
        }
    }
}

// Update German translations if required and not done already.
if (in_array('*', $CFG->supportedlanguages) && !in_array('de', $CFG->supportedlanguages)) {
    install_language('de');
}