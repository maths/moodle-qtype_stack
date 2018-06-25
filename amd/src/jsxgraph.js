/// NOTE! This code does eval() a string with no validation.
// So lets hope this is the correct way to name a Moodle AMD module
define(["qtype_stack/jsxgraphcore-lazy"], function(JXG) {
    return {
            find_input_id: function(divid, name) {
                var tmp = document.getElementById(divid);
                while ((tmp = tmp.parentElement) && !(tmp.classList.contains("formulation") && tmp.parentElement.classList.contains("content"))) {}
                tmp = tmp.querySelector('input[id$="_' + name + '"]');
                // We use this function to also tie into the change tracking of Moodle.
                // We do it here so that all possible code written by authors will also be tracked.
                // The author just needst to generate a change event they do not need to know how the VLE works.
                tmp.addEventListener('change', function(e) {
                    M.core_formchangechecker.set_form_changed();
                });
                return tmp.id;
            },

            bind_point: function(inputRef, point) {
                // This function takes a JXG point object and binds its coordinates to a given input.
                var theInput = document.getElementById(inputRef);
                if (theInput.value && theInput.value != '') {
                    // if a value exists move the point to it.
                    // the value is stored as a list of float values e.g. "[1,0.43]"
                    var coords = JSON.parse(theInput.value);
                    try {
                        point.setPosition(JXG.COORDS_BY_USER, coords);
                    } catch (err) {
                        // We do not care about this.
                    }
                    point.board.update();
                    point.update();
                }

                var initialX = point.X();
                var initialY = point.Y();

                // Then the binding from graph to input.
                point.board.on('update', function() {
                    // We do not want to set the input before the point actually moves.
                    if (initialX != point.X() || initialY != point.Y()) {
                        var tmp = JSON.stringify([point.X(), point.Y()]);
                        initialX = false; // ignore these after initial change.
                        initialY = false;
                        if (theInput.value != tmp) {
                            // Avoid resetting this, as some event models migth trigger
                            // change events even when no change actually happens.
                            theInput.value = tmp;
                            // As we set the inputs value programmatically no events
                            // will be fired. But for two way binding we want to fire them...
                            var e = new Event('change');
                            theInput.dispatchEvent(e);
                        }
                    }
                });

                var lastValue = JSON.stringify([point.X(), point.Y()]);

                // Then from input to graph. 'input' for live stuff and 'change' for other.
                theInput.addEventListener('input', function(e) {
                    if (theInput.value != lastValue) {
                        // Only when something changed.
                        try {
                            var tmp = JSON.parse(theInput.value);
                            if (typeof tmp[0] == 'number' && typeof tmp[1] == 'number') {
                                point.setPosition(JXG.COORDS_BY_USER, tmp);
                                point.board.update();
                                point.update();
                            }
                        } catch (err) {
                            // We do not care about this.
                        }
                        lastValue = theInput.value;
                    }
                });
                theInput.addEventListener('change', function(e) {
                    if (theInput.value != lastValue) {
                        // Only when something changed.
                        try {
                            var tmp = JSON.parse(theInput.value);
                            if (typeof tmp[0] == 'number' && typeof tmp[1] == 'number') {
                                point.setPosition(JXG.COORDS_BY_USER, tmp);
                                point.board.update();
                                point.update();
                            }
                        } catch (err) {
                            // We do not care about this.
                        }
                        lastValue = theInput.value;
                    }
                });
            },

            bind_slider: function(inputRef, slider) {
                // This function takes a JXG slider object and binds its value to a given input.
                var theInput = document.getElementById(inputRef);
                if (theInput.value && theInput.value != '') {
                    // if a value exists move the slider to it.
                    // the value is stored as a float value "0.43"
                    try {
                        slider.setValue(JSON.parse(theInput.value));
                    } catch (err) {
                        // We do not care about this.
                    }
                    slider.board.update();
                    slider.update();
                }

                var initialValue = slider.Value();

                // The binding from graph to input.
                slider.board.on('update', function() {
                    // We do not want to set the input before the point actually moves.
                    if (initialValue != slider.Value()) {
                        var tmp = JSON.stringify(slider.Value());
                        initialValue = false;
                        if (theInput.value != tmp) {
                            // Avoid resetting this, as some event models migth trigger
                            // change events even when no change actually happens.
                            theInput.value = tmp;
                            // As we set the inputs value programmatically no events
                            // will be fired. But for two way binding we want to fire them...
                            var e = new Event('change');
                            theInput.dispatchEvent(e);
                        }
                    }
                });

                var lastValue = JSON.stringify(slider.Value());

                // Then from input to graph. 'input' for live stuff and 'change' for other.
                theInput.addEventListener('input', function(e) {
                    if (theInput.value != lastValue) {
                        // Only when something changed.
                        try {
                            var tmp = JSON.parse(theInput.value);
                            if (typeof tmp == 'number') {
                                slider.setValue(tmp);
                                slider.board.update();
                                slider.update();
                            }
                        } catch (err) {
                            // We do not care about this.
                        }
                        lastValue = theInput.value;
                    }
                });
                theInput.addEventListener('change', function(e) {
                    if (theInput.value != lastValue) {
                        // Only when something changed.
                        try {
                            var tmp = JSON.parse(theInput.value);
                            if (typeof tmp == 'number') {
                                slider.setValue(tmp);
                                slider.board.update();
                                slider.update();
                            }
                        } catch (err) {
                            // We do not care about this.
                        }
                        lastValue = theInput.value;
                    }
                });
            }
        };
    });
