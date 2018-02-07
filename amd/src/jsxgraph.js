
// So lets hope this is the correct way to name a Moodle AMD module
define(['qtype_stack/jsxgraphcore'], function(JXG) {
    return {
            init: function(divid) {
                // This is bad but as we cannot pass direct code as anything but a string...
                if (!(document.getElementById(divid) === null)) {
                    var code = document.getElementById(divid).dataset.code;
                    eval(code);
                }

            }
        };
    });