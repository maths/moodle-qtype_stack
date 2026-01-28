<?php

require_once(__DIR__ . '/../../emulation/Language.php');
require_once(__DIR__ . '/../../config.php');

global $CFG;
foreach ($CFG->supportedlanguages as $variant) {
    if ($variant !== '*') {
        install_language($variant);
        $region = get_parent_language($variant);
        if ($region !== $variant && !in_array($region, $CFG->supportedlanguages)) {
            install_language($region);
        }
        $language = get_parent_language($region);
        if ($language !== $region && !in_array($language, $CFG->supportedlanguages)) {
            install_language($language);
        }
    }
}

// Update German translations if required and not done already.
if (in_array('*', $CFG->supportedlanguages) && !in_array('de', $CFG->supportedlanguages)) {
    install_language('de');
}