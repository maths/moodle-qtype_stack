
// So lets hope this is the correct way to name a Moodle AMD module
define(['qtype_stack/jsxgraphcore-lazy', 'core/yui'], function(JXG, Y) {
    return {
            init: function(divid, code) {
                // This is bad but as we cannot pass direct code as anything but a string...
                if (!(document.getElementById(divid) === null)) {
                    Y.use('mathjax', function(){
                        // First define some convenience functions for use in graph authoring.
                        function stack_bind_jxg_point(inputRef, point) {
                            // This function takes a JXG point object and binds its coordinates to a given input.
                            var theInput = document.getElementById(inputRef);
                            if (theInput.value && theInput.value != '') {
                                // if a value exists move the point to it.
                                // the value is stored as a list of float values e.g. "[1,0.43]"
                                var coords = JSON.parse(theInput.value);
                                point.setPosition(JXG.COORDS_BY_USER, coords);
                            }

                            // Then the reverse. Hopefully, 'up' listener is free and the author does
                            // not override it.
                            point.on('up', function() {
                                theInput.value = JSON.stringify([point.X(), point.Y()]);
                            });
                        }

                        function stack_bind_jxg_slider(inputRef, slider) {
                            // This function takes a JXG slider object and binds its value to a given input.
                            var theInput = document.getElementById(inputRef);
                            if (theInput.value && theInput.value != '') {
                                // if a value exists move the slider to it.
                                // the value is stored as a float value "0.43"
                                slider.setValue(JSON.parse(theInput.value));
                                slider.board.update();
                                slider.update();
                            }

                            // Then the reverse. Hopefully, 'up' listener is free and the author does
                            // not override it.
                            slider.on('up', function() {
                                theInput.value = JSON.stringify(slider.Value());
                            });
                        }

                        eval(code);
                    });
                }
            }
        };
    });
