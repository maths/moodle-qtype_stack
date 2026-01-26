<?php

require_once(__DIR__ . '/../../emulation/Language.php');
require_once(__DIR__ . '/../../config.php');

global $CFG;
foreach ($CFG->supportedlanguages as $lang) {
    if ($lang !== '*') {
        install_language($lang);
    }
}