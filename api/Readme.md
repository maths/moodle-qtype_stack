# Minimal STACK API

The purpose of the files in this directory are to provide a minimal and direct API to STACK questions.

This is to prove the functionality of including STACK questions in other interactive websites.

## Installation

1. Copy 'api/config.php.dist' to 'config.php' and edit the file to reflect your current directory.
2. Create the following temporary directories given in $CFG->dataroot

    $CFG->dataroot.'/stack'
    $CFG->dataroot.'/stack/plots'
    $CFG->dataroot.'/stack/logs'
    $CFG->dataroot.'/stack/tmp'