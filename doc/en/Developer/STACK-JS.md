## STACK-JS

Including JavaScript in questions has always been a bit suspicious as one it
essenttialy executes with the users rights in the browser. For this reason it
is necessary to somehow separate that scripting from the VLE context. It also
good to separate the scripting from the VLE to ease portability of materials
to other VLEs.

STACK-JS is a sandbox IFRAME based solution where the dangerous bits exist
inside an IFRAME and communicate with the VLE through a limited set of
messages. The idea is to make it so that the script does not directly see
the VLE nor can it interact with parts of it it should not interract with.

### Basics

The solution consist of two parts:
 
 1. The VLE side beachead script which needs to adapt to the VLE while at
    the same time maintain the STACK-JS features. For Moodle this script
    is in the `amd/src/stackjsvle.js` file and it has been designed so
    that the VLE-specific parts are separated into functions name `vle_`
    porting this to other VLEs should not be difficult.

 2. The IFRAME side script currently stored at 
    `corsscripts/stackjsiframe.js` provides automated Promise based APIs
    for doing basic actions through the messaging system. This script 
    should never be changed and it should always be the same on all VLEs.
    It includes logic for synchronisation that may not be necessary with
    the default Moodle implementation, but should still be kept there for
    possible other uses.

The VLE side has more control here, as it can decide what it allows and
who it listens. In general, the IFRAMEs are constructed through the VLE
side scripts and those scripts only listen to the IFRAMEs they constructed.

The primary security feature of the VLE side is to limit the access to
inputs to just those inputs present within questions. The access is also
limited to lsitening input changes and settign their values so no DOM
modification is possible. However, there are messages that allow togling
the visibility and changing the contents of elements by ID, however again
the VLE can limit those elements to jsut those elements within 
the questions and also apply whatever cleaning (strip all scripts and so
on) to any content received.

### Promises for the content

The content going into those IFRAMEs can be anything and it can do
whatever it wants in those IFRAMEs. The STACK-JS logic will promise that
no filtering for the content will ever be done. Other than Markdown etc.
if requested. No VLE level filters will affect it nor will we tidy it.

Content can also rest assured that it is not within the same origin as
the VLE and thus cannot leak authorisation or other sensitive details
from that side.


## Inconveniences

As the IFRAME is in its own "origin" it cannot load scripts from just any
source. And as it is important to be able to load some scripts, especially
that `stackjsiframe.js` we need a source that is suitable. That source
needs to set a particular header, and as we do not want to add extra install
requirements for different web-servers we now have a special script that
modifies headers at `corsscripts/cors.php`. We could also serve that script
from external source through some CDN, but we probably want to maintain
the ability to run the whole system in a closed network so that is why we
provide our own header modification script.

If one needs to serve anything to those IFRAMEs from the local system one
can either drop that thing into that `corsscripts/` directory or config
ones own headers elsewhere. Currently, works with `.css` and `.js` files.


## The general security reason

While no attacks using the scripting are currently known, securing this
border is necessary in a world where material sharing is more common. If your
STACK installation is not new enough to use this security feature. Do pay
extra attention to materials received from random sources, as they might
contain scripting that would execute with your rights. Such execution could,
for example:
 
 1. Exfiltrate information from the system that is likely to house
    student details.
 2. Modify information in the system, for example, points, quiz settings, or
    access rights.
 3. Do arbitrary actions "as you", e.g., post messages onto course forums.

Do note that similar risks are related to any materials that allow arbitrary
scripts to be included either by the student or by whoever authored
the material and these types of attacks are always just one spoofed e-mail
away.

While unlikely, it is worth being careful until STACK has completed
the migration to secured JavaScript. The current plan is to first provide
means for doing things in a secure way and then forbid insecure methods
in a following release. Until that following release, keep your eyes open.


## Minimal example for access to an input

Given a STACK question with an input named `ans1` you can create an IFRAME
that executes arbitrary code that can refrence that input like this:

```
[[iframe]]
[[script type="module"]]
import {stack_js} from '[[cors src="stackjsiframe.js"/]]';
var promiseforaninput = stack_js.request_access_to_input("ans1", true);
promiseforaninput.then((id) => {
   document.getElementById(id).type = 'input';
});
[[/script]]
[[/iframe]]
```

The first two lines first open up an `[[iframe]]` which generates an XHTML
document and an IFRAME to contain it, and then we use the `[[script]]` block
to generate a script-tag in that documents head. With the `type="module"` we
make it possible to use the `import` syntax to bring in libraries and in this
case we bring in the `stack_js` library from an URL provided by the `[[cors]]`
block so that we do not need to write hard coded references to the full URL.
Once we have the library, we then ask it for access to the input named `ans1`,
we also add that `true` to signal the we would want to see `input` events
being synchronised in addition to normal `change` events, to make this demo
more interactive. As the connection process is asynchronous we will receive
a promise that will resolve into the identifier of an hidden input that will
be constructed inside the IFRAME, in this example we simply make that input
visible so that we can try interacting with it directly.

If you modify that IFRAME side inputs value through code or other means it
will only get synchronised to the VLE side once a `change` event is emitted,
so do dispatch some events if things do not otherwise work.

Also if your only purpose is to run JavaScript you might want to hide
the IFRAME, you can simply place it inside something that is not being
displayed or use `hidden="true"` as an argument to the `[[iframe]]`-block.
If it needs to be visible, all the dimension options of `[[jsxgraph]]` also
work here.