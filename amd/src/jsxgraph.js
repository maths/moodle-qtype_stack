
// So lets hope this is the correct way to name a Moodle AMD module
define(['qtype_stack/jsxgraphcore-lazy', 'core/yui'], function(JXG, Y) {
    return {
            init: function(divid, code) {
                // This is bad but as we cannot pass direct code as anything but a string...
                if (!(document.getElementById(divid) === null)) {
                    Y.use('mathjax', function(){
                        eval(code);
                    });
                }
            }
        };
    });
