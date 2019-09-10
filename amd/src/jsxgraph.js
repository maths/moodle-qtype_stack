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
                    if (initialX !== point.X() || initialY !== point.Y()) {
                        var tmp = JSON.stringify([point.X(), point.Y()]);
                        initialX = false; // ignore these after initial change.
                        initialY = false;
                        if (theInput.value != tmp) {
                            // Avoid resetting this, as some event models migth trigger
                            // change events even when no change actually happens.
                            theInput.value = tmp;
                            // As we set the inputs value programmatically no events
                            // will be fired. But for two way binding we want to fire them...
                            // However we do not need this in the preview where it annoys people.
                            if (window.location.pathname.indexOf('preview.php') === -1) {
                                var e = new Event('change');
                                theInput.dispatchEvent(e);
                            }
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

            bind_point_dual: function(inputRef, point1, point2) {
                // This function takes two JXG point object and binds their coordinates to a given input.
                var theInput = document.getElementById(inputRef);
                if (theInput.value && theInput.value != '') {
                    // if a value exists move the points there.
                    // the value is stored as a list of float values e.g. "[[1,0.43],[2.1,-4]]"
                    var coords = JSON.parse(theInput.value);
                    try {
                        point1.setPosition(JXG.COORDS_BY_USER, coords[0]);
                        point2.setPosition(JXG.COORDS_BY_USER, coords[1]);
                    } catch (err) {
                        // We do not care about this.
                    }
                    point1.board.update();
                    point1.update();
                    point2.board.update();
                    point2.update();
                }

                var initial1X = point1.X();
                var initial1Y = point1.Y();

                // Then the binding from graph to input.
                point1.board.on('update', function() {
                    // We do not want to set the input before the point actually moves.
                    if (initial1X !== point1.X() || initial1Y !== point1.Y()) {
                        var tmp = JSON.stringify([[point1.X(), point1.Y()],[point2.X(), point2.Y()]]);
                        initial1X = false; // ignore these after initial change.
                        initial1Y = false;
                        if (theInput.value != tmp) {
                            // Avoid resetting this, as some event models migth trigger
                            // change events even when no change actually happens.
                            theInput.value = tmp;
                            // As we set the inputs value programmatically no events
                            // will be fired. But for two way binding we want to fire them...
                            // However we do not need this in the preview where it annoys people.
                            if (window.location.pathname.indexOf('preview.php') === -1) {
                                var e = new Event('change');
                                theInput.dispatchEvent(e);
                            }
                        }
                    }
                });

                var initial2X = point2.X();
                var initial2Y = point2.Y();

                // Then the binding from graph to input.
                point2.board.on('update', function() {
                    // We do not want to set the input before the point actually moves.
                    if (initial2X !== point2.X() || initial2Y !== point2.Y()) {
                        var tmp = JSON.stringify([[point1.X(), point1.Y()],[point2.X(), point2.Y()]]);
                        initial2X = false; // ignore these after initial change.
                        initial2Y = false;
                        if (theInput.value != tmp) {
                            // Avoid resetting this, as some event models migth trigger
                            // change events even when no change actually happens.
                            theInput.value = tmp;
                            // As we set the inputs value programmatically no events
                            // will be fired. But for two way binding we want to fire them...
                            // However we do not need this in the preview where it annoys people.
                            if (window.location.pathname.indexOf('preview.php') === -1) {
                                var e = new Event('change');
                                theInput.dispatchEvent(e);
                            }
                        }
                    }
                });

                var lastValue = JSON.stringify([[point1.X(), point1.Y()],[point2.X(), point2.Y()]]);

                // Then from input to graph. 'input' for live stuff and 'change' for other.
                theInput.addEventListener('input', function(e) {
                    if (theInput.value != lastValue) {
                        // Only when something changed.
                        try {
                            var tmp = JSON.parse(theInput.value);
                            if (typeof tmp[0][0] == 'number' && typeof tmp[0][1] == 'number') {
                                point1.setPosition(JXG.COORDS_BY_USER, tmp[0]);
                            }
                            if (typeof tmp[1][0] == 'number' && typeof tmp[1][1] == 'number') {
                                point2.setPosition(JXG.COORDS_BY_USER, tmp[1]);
                                point1.board.update();
                                point1.update();
                                point2.board.update();
                                point2.update();
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
                            if (typeof tmp[0][0] == 'number' && typeof tmp[0][1] == 'number') {
                                point1.setPosition(JXG.COORDS_BY_USER, tmp[0]);

                            }
                            if (typeof tmp[1][0] == 'number' && typeof tmp[1][1] == 'number') {
                                point2.setPosition(JXG.COORDS_BY_USER, tmp[1]);
                                point1.board.update();
                                point1.update();
                                point2.board.update();
                                point2.update();
                            }
                        } catch (err) {
                            // We do not care about this.
                        }
                        lastValue = theInput.value;
                    }
                });

            },

            bind_point_relative: function(inputRef, point1, point2) {
                // This function takes two JXG point object and binds their coordinates to a given input.
                var theInput = document.getElementById(inputRef);
                if (theInput.value && theInput.value != '') {
                    // if a value exists move the points there.
                    // the value is stored as a list of float values e.g. "[[1,0.43],[2.1,-4]]"
                    var coords = JSON.parse(theInput.value);
                    try {
                        point1.setPosition(JXG.COORDS_BY_USER, coords[0]);
                        var b = [coords[0][0] + coords[1][0], coords[0][1] + coords[1][1]];
                        point2.setPosition(JXG.COORDS_BY_USER, b);
                    } catch (err) {
                        // We do not care about this.
                    }
                    point1.board.update();
                    point1.update();
                    point2.board.update();
                    point2.update();
                }

                var initial1X = point1.X();
                var initial1Y = point1.Y();

                // Then the binding from graph to input.
                point1.board.on('update', function() {
                    // We do not want to set the input before the point actually moves.
                    if (initial1X !== point1.X() || initial1Y !== point1.Y()) {
                        var tmp = JSON.stringify([[point1.X(), point1.Y()],[point2.X() - point1.X(), point2.Y() - point1.Y()]]);
                        initial1X = false; // ignore these after initial change.
                        initial1Y = false;
                        if (theInput.value != tmp) {
                            // Avoid resetting this, as some event models migth trigger
                            // change events even when no change actually happens.
                            theInput.value = tmp;
                            // As we set the inputs value programmatically no events
                            // will be fired. But for two way binding we want to fire them...
                            // However we do not need this in the preview where it annoys people.
                            if (window.location.pathname.indexOf('preview.php') === -1) {
                                var e = new Event('change');
                                theInput.dispatchEvent(e);
                            }
                        }
                    }
                });

                var initial2X = point2.X();
                var initial2Y = point2.Y();

                // Then the binding from graph to input.
                point2.board.on('update', function() {
                    // We do not want to set the input before the point actually moves.
                    if (initial2X !== point2.X() || initial2Y !== point2.Y()) {
                        var tmp = JSON.stringify([[point1.X(), point1.Y()],[point2.X() - point1.X(), point2.Y() - point1.Y()]]);
                        initial2X = false; // ignore these after initial change.
                        initial2Y = false;
                        if (theInput.value != tmp) {
                            // Avoid resetting this, as some event models migth trigger
                            // change events even when no change actually happens.
                            theInput.value = tmp;
                            // As we set the inputs value programmatically no events
                            // will be fired. But for two way binding we want to fire them...
                            // However we do not need this in the preview where it annoys people.
                            if (window.location.pathname.indexOf('preview.php') === -1) {
                                var e = new Event('change');
                                theInput.dispatchEvent(e);
                            }
                        }
                    }
                });

                var lastValue = JSON.stringify([[point1.X(), point1.Y()],[point2.X() - point1.X(), point2.Y() - point1.Y()]]);

                // Then from input to graph. 'input' for live stuff and 'change' for other.
                theInput.addEventListener('input', function(e) {
                    if (theInput.value != lastValue) {
                        // Only when something changed.
                        try {
                            var tmp = JSON.parse(theInput.value);
                            if (typeof tmp[0][0] == 'number' && typeof tmp[0][1] == 'number') {
                                point1.setPosition(JXG.COORDS_BY_USER, tmp[0]);
                            }
                            if (typeof tmp[1][0] == 'number' && typeof tmp[1][1] == 'number') {
                                var b = [tmp[0][0] + tmp[1][0], tmp[0][1] + tmp[1][1]];
                                point2.setPosition(JXG.COORDS_BY_USER, b);
                                point1.board.update();
                                point1.update();
                                point2.board.update();
                                point2.update();
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
                            if (typeof tmp[0][0] == 'number' && typeof tmp[0][1] == 'number') {
                                point1.setPosition(JXG.COORDS_BY_USER, tmp[0]);
                        }
                            if (typeof tmp[1][0] == 'number' && typeof tmp[1][1] == 'number') {
                                var b = [tmp[0][0] + tmp[1][0], tmp[0][1] + tmp[1][1]];
                                point2.setPosition(JXG.COORDS_BY_USER, b);
                                point1.board.update();
                                point1.update();
                                point2.board.update();
                                point2.update();
                            }
                        } catch (err) {
                            // We do not care about this.
                        }
                        lastValue = theInput.value;
                    }
                });
            },

            bind_point_direction: function(inputRef, point1, point2) {
                // This function takes two JXG point object and binds their coordinates to a given input.
                var theInput = document.getElementById(inputRef);
                if (theInput.value && theInput.value != '') {
                    // if a value exists move the points there.
                    // the value is stored as a list of float values e.g. "[[1,0.43],[2.1,1.1]]"
                    // The second pair is now the angle in radians and the length.
                    var coords = JSON.parse(theInput.value);
                    try {
                        point1.setPosition(JXG.COORDS_BY_USER, coords[0]);
                        var angle = coords[1][0];
                        var len = coords[1][1];
                        var b = [coords[0][0], coords[0][1]];
                        if (len > 0) {
                            b[0] = b[0] + len*Math.cos(angle);
                            b[1] = b[1] + len*Math.sin(angle);
                        }
                        point2.setPosition(JXG.COORDS_BY_USER, b);
                    } catch (err) {
                        // We do not care about this.
                    }
                    point1.board.update();
                    point1.update();
                    point2.board.update();
                    point2.update();
                }

                var initial1X = point1.X();
                var initial1Y = point1.Y();

                // Then the binding from graph to input.
                point1.board.on('update', function() {
                    // We do not want to set the input before the point actually moves.
                    if (initial1X !== point1.X() || initial1Y !== point1.Y()) {
                        var tmp = JSON.stringify([[point1.X(), point1.Y()],
                        [Math.atan2(point2.Y() - point1.Y(), point2.X() - point1.X()),
                        Math.sqrt((point2.X() - point1.X())*(point2.X() - point1.X()) + (point2.Y() - point1.Y())*(point2.Y() - point1.Y()))]]);
                        initial1X = false; // ignore these after initial change.
                        initial1Y = false;
                        if (theInput.value != tmp) {
                            // Avoid resetting this, as some event models migth trigger
                            // change events even when no change actually happens.
                            theInput.value = tmp;
                            // As we set the inputs value programmatically no events
                            // will be fired. But for two way binding we want to fire them...
                            // However we do not need this in the preview where it annoys people.
                            if (window.location.pathname.indexOf('preview.php') === -1) {
                                var e = new Event('change');
                                theInput.dispatchEvent(e);
                            }
                        }
                    }
                });

                var initial2X = point2.X();
                var initial2Y = point2.Y();

                // Then the binding from graph to input.
                point2.board.on('update', function() {
                    // We do not want to set the input before the point actually moves.
                    if (initial2X !== point2.X() || initial2Y !== point2.Y()) {
                        var tmp = JSON.stringify([[point1.X(), point1.Y()],
                        [Math.atan2(point2.Y() - point1.Y(), point2.X() - point1.X()),
                        Math.sqrt((point2.X() - point1.X())*(point2.X() - point1.X()) + (point2.Y() - point1.Y())*(point2.Y() - point1.Y()))]]);
                        initial2X = false; // ignore these after initial change.
                        initial2Y = false;
                        if (theInput.value != tmp) {
                            // Avoid resetting this, as some event models migth trigger
                            // change events even when no change actually happens.
                            theInput.value = tmp;
                            // As we set the inputs value programmatically no events
                            // will be fired. But for two way binding we want to fire them...
                            // However we do not need this in the preview where it annoys people.
                            if (window.location.pathname.indexOf('preview.php') === -1) {
                                var e = new Event('change');
                                theInput.dispatchEvent(e);
                            }
                        }
                    }
                });


                var lastValue = JSON.stringify([[point1.X(), point1.Y()],
                        [Math.atan2(point2.Y() - point1.Y(), point2.X() - point1.X()),
                        Math.sqrt((point2.X() - point1.X())*(point2.X() - point1.X()) + (point2.Y() - point1.Y())*(point2.Y() - point1.Y()))]]);

                // Then from input to graph. 'input' for live stuff and 'change' for other.
                theInput.addEventListener('input', function(e) {
                    if (theInput.value != lastValue) {
                        // Only when something changed.
                        try {
                            var tmp = JSON.parse(theInput.value);
                            if (typeof tmp[0][0] == 'number' && typeof tmp[0][1] == 'number') {
                                point1.setPosition(JXG.COORDS_BY_USER, tmp[0]);
                            }
                            if (typeof tmp[1][0] == 'number' && typeof tmp[1][1] == 'number') {
                                var angle = tmp[1][0];
                                var len = tmp[1][1];
                                var b = [tmp[0][0], tmp[0][1]];
                                if (len > 0) {
                                    b[0] = b[0] + len*Math.cos(angle);
                                    b[1] = b[1] + len*Math.sin(angle);
                                }
                                point2.setPosition(JXG.COORDS_BY_USER, b);
                                point1.board.update();
                                point1.update();
                                point2.board.update();
                                point2.update();
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
                            if (typeof tmp[0][0] == 'number' && typeof tmp[0][1] == 'number') {
                                point1.setPosition(JXG.COORDS_BY_USER, tmp[0]);
                            }
                            if (typeof tmp[1][0] == 'number' && typeof tmp[1][1] == 'number') {
                                var angle = tmp[1][0];
                                var len = tmp[1][1];
                                var b = [tmp[0][0], tmp[0][1]];
                                if (len > 0) {
                                    b[0] = b[0] + len*Math.cos(angle);
                                    b[1] = b[1] + len*Math.sin(angle);
                                }
                                point2.setPosition(JXG.COORDS_BY_USER, b);
                                point1.board.update();
                                point1.update();
                                point2.board.update();
                                point2.update();
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
                            // However we do not need this in the preview where it annoys people.
                            if (window.location.pathname.indexOf('preview.php') === -1) {
                                var e = new Event('change');
                                theInput.dispatchEvent(e);
                            }
                        }
                    }
                });

                var lastValue = JSON.stringify(slider.Value());

                // Then from input to graph. 'input' for live stuff and 'change' for other.
                theInput.addEventListener('input', function(e) {
                    if (theInput.value !== lastValue) {
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
                    if (theInput.value !== lastValue) {
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
