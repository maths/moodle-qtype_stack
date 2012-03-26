# Setting up the docs again

This file documents the current progress with setting up the documentation again:

Work on this is taking place in the `docs` branch.

## Changes in functionality

The local browser would assume a `.md` extension and append one automatically.
Not only is this not necessary but it means that the links break when browsing
the documentation on github. This has been changed.

So now to view
    /docs/CAS/Maxima.md
request
    /doc.php/CAS/Maxima.md

Files with extension `.md` will be assumed to be markdown and will be rendered to
HTML as such. Other files will have their contents embedded in the (HTML) page.

## Remaining tasks in the code

+ In `doc.php`: NB: lines requiring attention are marked with `TODO`, these include:
    + Fix access to config
    + Fix translator strings
+ The files themselves:
    + Fixing relative urls
        + adding `.md` extension
        + media content directory will have changed.
          NB this will also have to be fixed in `phpMarkdown.php` since some
          links are hardcoded (such as images for emails and external links)
+ These files are taken from before Dan moved it to the new system.
  It is possible that Dan made some nice changes to the functionality of the code.
  Check for these in the history and move them over.

## Maintenance script
A maintenance script was written to perform various tasks. It requires much the
same attention as `doc.php`. It used for rendering some of the functions that
came with reporting which are currently not present in this repository.
