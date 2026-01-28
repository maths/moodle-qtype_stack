<?php

require_once(__DIR__ . '/../../emulation/Language.php');
require_once(__DIR__ . '/../../config.php');

global $CFG;
$supportlanguages = explode(',', $CFG->supportedlanguages);
foreach ($supportedlanguages as $variant) {
    if ($variant !== '*') {
        ApiLanguage::install_language($variant);
        $region = ApiLanguage::get_next_parent_language($variant);
        if ($region !== $variant && !in_array($region, $supportedlanguages)) {
            ApiLanguage::install_language($region);
        }
        $language = ApiLanguage::get_next_parent_language($region);
        if ($language !== $region && !in_array($language, $supportedlanguages)) {
            ApiLanguage::install_language($language);
        }
    }
}

// Update German translations if required and not done already.
if (in_array('*', $supportedlanguages) && !in_array('de', $supportedlanguages)) {
    ApiLanguage::install_language('de');
}