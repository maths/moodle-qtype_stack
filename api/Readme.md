# Minimal STACK API

The purpose of the files in this directory are to provide a minimal and direct API to STACK questions.

This is to prove the functionality of including STACK questions in other interactive websites.

## Installation

1. Install Maxima and Gnuplot on your server.  (Plots not yet supported).
2. Download 'qtype_stack' onto your webserver.  For example into the directory

    $CFG->wwwroot = "/var/www/api";

3. You must have a data directory into which the webserver can write.  Don't put this in your web directory.

    $CFG->dataroot = "/var/data/api";

4. Create the following temporary directories given in '$CFG->dataroot'.  [TODO: automate this?]

    $CFG->dataroot.'/stack'
    $CFG->dataroot.'/stack/plots'
    $CFG->dataroot.'/stack/logs'
    $CFG->dataroot.'/stack/tmp'

5. Copy 'api/config.php.dist' to '$CFG->wwwroot. "/config.php"' and edit the file to reflect your current settings.
6. Edit 'install.php' to run the command '$api->install();'.  This command compiles a Maxima image with your local settings. You will now need to edit 'config.php' to point to your maxima image.  This varies by lisp version, but it might look like this:

    $CFG->maximacommand = 'timeout --kill-after=10s 10s /usr/lib/clisp-2.49/base/lisp.run -q -M /var/data/api/stack/maxima_opt_auto.mem';

7. Make sure 'install.php' can't execute again.

Note, at this stage there is no error trapping....

8. Point your webserver to 'api/endpoint.html' for a basic interaction.  You will need to supply questions in YAML format for this to be operational.

# Docker

To build run in the project root:
```
sudo docker build -t stack .
```

Start container:
```
sudo docker run -v plots:/var/data/api/stack/plots --name stack-test-container -p 80:80 stack
```

`plots` is used as a common storage for generated images, you can map local code by start the container with -v flag
`-v {local_path_the_project}:/var/www/html`
