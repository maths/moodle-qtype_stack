define(["qtype_stack/jsxgraphcore-lazy"], function(JXG) {
    // 4.4 rewrite, now with groups and ability to mark as moved.
    // Should perform better and have less listeners.

    // Functions that generate the value for an input. By input then  by object.
    var serializers = {};

    // Functions that extract values from inputs. Lists of them by input.
    // Single argument functions taking the value of the input.
    var deserializers = {};

    // Initial values for input serialisations before restore, if an object set of an input
    // serialises to something else this value will be nulled otherwise if the values match
    // the input won't get updated.
    var initials = {};

    // Object groups, if any of these objects moves consider others to have moved as well.
    // Moving an object only considers those groups the object itself belongs to touching
    // "part A" does not cascade to "part B" if they overlap unless the moved thing is in
    // the intersection.
    var objectgroups = [];

    // Object input mappings. Which inputs tie to this object. Object.id to lists of inputs.
    var objectinput = {};

    // Flag to stop propagation.
    var active = false;

    function _commonsetup(inputname) {
        if (!(inputname in serializers)) {
            serializers[inputname] = {};
            deserializers[inputname] = [];

            var input = document.getElementById(inputname);
            input.addEventListener('input', () => generalinputupdatehandler(inputname));
            input.addEventListener('change', () => generalinputupdatehandler(inputname));
            input.addEventListener('change', function() {
                M.core_formchangechecker.set_form_changed();
            });
        }
    }


    function pointserializer(point) {
        return JSON.stringify([point.X(), point.Y()]);
    }

    function pointdeserializer(point, data) {
        try {
            var tmp = JSON.parse(data);
            if (typeof tmp[0] == 'number' && typeof tmp[1] == 'number') {
                point.setPosition(JXG.COORDS_BY_USER, tmp);
                point.board.update();
                point.update();
            }
        } catch (err) {
            // We do not care about this. What could we even do?
        }
    }
    // And for cases where we have already parsed that.
    function pointdeserializerparsed(point, data) {
        try {
            if (typeof data[0] == 'number' && typeof data[1] == 'number') {
                point.setPosition(JXG.COORDS_BY_USER, data);
                point.board.update();
                point.update();
            }
        } catch (err) {
            // We do not care about this. What could we even do?
        }
    }


    function sliderserializer(slider) {
        return JSON.stringify(slider.Value());
    }

    function sliderdeserializer(slider, data) {
        try {
            slider.setValue(JSON.parse(data));
            slider.board.update();
            slider.update();
        } catch (err) {
            // We do not care about this.
        }
    }

    function generalobjectupdatehandler(object) {
        if (!active) {
            active = true;
            try {
                var handledinputs = [];
                if (object.id in objectinput) {
                    for (var inputname of objectinput[object.id]) {
                        if (handledinputs.indexOf(inputname) === -1) {
                            handledinputs.push(inputname);
                            var input = document.getElementById(inputname);
                            var val = serializers[inputname][object.id]();
                            if (val !== initials[inputname]) {
                                initials[inputname] = null;
                                input.value = val;
                            } else {
                                // Exit.
                                active = false;
                                return;
                            }
                        }
                    }
                }
                // Update groups at the same time. Here the initial value matters not a bit.
                for (var group of objectgroups) {
                    if (group.indexOf(object.id) !== -1) {
                        for (var obj of group) {
                            if (obj !== object.id) {
                                if (obj in objectinput) {
                                    for (var inputname of objectinput[obj]) {
                                        if (handledinputs.indexOf(inputname) === -1) {
                                            initials[inputname] = null;
                                            handledinputs.push(inputname);
                                            var input = document.getElementById(inputname);
                                            input.value = serializers[inputname][obj]();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                for (var inputname of handledinputs) {
                    var input = document.getElementById(inputname);
                    if (window.location.pathname.indexOf('preview.php') === -1) {
                        var e = new Event('change');
                        input.dispatchEvent(e);
                    }
                    var e = new Event('input');
                    input.dispatchEvent(e);
                }
            } catch (err) {
                // If there is an error there we want to reset active anyway.
                // Might be that some serializer explodes if some scripting
                // messes with things.
            }
            active = false;
        }
    }

    // Updates to inputs coming from outside are handled like this.
    function generalinputupdatehandler(inputname) {
        if (inputname in deserializers) {
            // Only trigger everything if the value has truly changed.
            // Check for one of the objects serialising to this input.
            var old = serializers[inputname][Object.keys(serializers[inputname])[0]]();

            var input = document.getElementById(inputname);
            if (old !== input.value) {
                for (var ds of deserializers[inputname]) {
                    ds(input.value);
                }
            }
        }
    }



    return {
            find_input_id: function(divid, name) {
                // Note this is here for compatibility and for documentation.
                // Not used since CASText2.
                var tmp = document.getElementById(divid);
                while ((tmp = tmp.parentElement) &&
                       !(tmp.classList.contains("formulation") &&
                       tmp.parentElement.classList.contains("content"))) {}
                tmp = tmp.querySelector('input[id$="_' + name + '"]');
                // We use this function to also tie into the change tracking of Moodle.
                // We do it here so that all possible code written by authors will also be tracked.
                // The author just needst to generate a change event they do not need to know how the VLE works.
                tmp.addEventListener('change', function() {
                    M.core_formchangechecker.set_form_changed();
                });
                return tmp.id;
            },

            define_group: function(list) {
                // Moving any of these objects (points sliders) leads to all of them
                // being considered moved.
                var l = [];
                for (var obj of list) {
                    if (!(obj.id in l)) {
                        l.push(obj.id);
                    }
                }
                objectgroups.push(l);
            },

            starts_moved: function(obj) {
                // Makes this object start its life as moved.
                // Call after bindings have been defined and possible groups declared.
                if (obj.id in objectinput) {
                    for (var inputname of objectinput[obj.id]) {
                        initials[inputname] = null;
                    }
                    generalobjectupdatehandler(obj);
                }
            },

            bind_point: function(inputRef, point) {
                _commonsetup(inputRef);
                // This function takes a JXG point object and binds its coordinates to a given input.
                var theInput = document.getElementById(inputRef);

                initials[inputRef] = pointserializer(point);

                if (theInput.value && theInput.value != '') {
                    pointdeserializer(point, theInput.value);
                }
                serializers[inputRef][point.id] = () => {
                    return pointserializer(point);
                };

                deserializers[inputRef].push((value) => {
                    pointdeserializer(point, value);
                });

                if (point.id in objectinput) {
                    if (!(inputRef in objectinput[point.id])) {
                        objectinput[point.id].push(inputRef);
                    }
                } else {
                    objectinput[point.id] = [inputRef];
                }

                // Then the binding from graph to input.
                point.board.on('update', () => generalobjectupdatehandler(point));
            },

            bind_point_dual: function(inputRef, point1, point2) {
                _commonsetup(inputRef);
                // This function takes a JXG point object and binds its coordinates to a given input.
                var theInput = document.getElementById(inputRef);

                var serializer = (p1, p2) => {
                    return JSON.stringify([[p1.X(),p1.Y()],[p2.X(),p2.Y()]]);
                };

                initials[inputRef] = serializer(point1, point2);

                if (theInput.value && theInput.value != '') {
                    var tmp = JSON.parse(theInput.value);
                    pointdeserializerparsed(point1, tmp[0]);
                    pointdeserializerparsed(point2, tmp[1]);
                }

                serializers[inputRef][point1.id] = () => {
                    return serializer(point1, point2);
                };
                serializers[inputRef][point2.id] = () => {
                    return serializer(point1, point2);
                };

                deserializers[inputRef].push((value) => {
                    var tmp = JSON.parse(value);
                    pointdeserializerparsed(point1, tmp[0]);
                    pointdeserializerparsed(point2, tmp[1]);
                });

                if (point1.id in objectinput) {
                    if (!(inputRef in objectinput[point1.id])) {
                        objectinput[point1.id].push(inputRef);
                    }
                } else {
                    objectinput[point1.id] = [inputRef];
                }

                if (point2.id in objectinput) {
                    if (!(inputRef in objectinput[point2.id])) {
                        objectinput[point2.id].push(inputRef);
                    }
                } else {
                    objectinput[point2.id] = [inputRef];
                }

                // Then the binding from graph to input.
                point1.board.on('update', () => generalobjectupdatehandler(point1));
                point2.board.on('update', () => generalobjectupdatehandler(point2));
            },

            bind_point_relative: function(inputRef, point1, point2) {
                _commonsetup(inputRef);
                // This function takes a JXG point object and binds its coordinates to a given input.
                var theInput = document.getElementById(inputRef);

                var serializer = (p1, p2) => {
                    return JSON.stringify([[p1.X(),p1.Y()],[p2.X()-p1.X(),p2.Y()-p1.Y()]]);
                };

                var deserializer = (value) => {
                    var tmp = JSON.parse(value);
                    pointdeserializerparsed(point1, tmp[0]);
                    tmp[1][0] = tmp[1][0] + tmp[0][0];
                    tmp[1][1] = tmp[1][1] + tmp[0][1];
                    pointdeserializerparsed(point2, tmp[1]);
                };

                initials[inputRef] = serializer(point1, point2);

                if (theInput.value && theInput.value != '') {
                    deserializer(theInput.value);
                }

                serializers[inputRef][point1.id] = () => {
                    return serializer(point1, point2);
                };
                serializers[inputRef][point2.id] = () => {
                    return serializer(point1, point2);
                };

                deserializers[inputRef].push((value) => {
                    deserializer(value);
                });

                if (point1.id in objectinput) {
                    if (!(inputRef in objectinput[point1.id])) {
                        objectinput[point1.id].push(inputRef);
                    }
                } else {
                    objectinput[point1.id] = [inputRef];
                }

                if (point2.id in objectinput) {
                    if (!(inputRef in objectinput[point2.id])) {
                        objectinput[point2.id].push(inputRef);
                    }
                } else {
                    objectinput[point2.id] = [inputRef];
                }

                // Then the binding from graph to input.
                point1.board.on('update', () => generalobjectupdatehandler(point1));
                point2.board.on('update', () => generalobjectupdatehandler(point2));
            },

            bind_point_direction: function(inputRef, point1, point2) {
                _commonsetup(inputRef);
                var theInput = document.getElementById(inputRef);

                var serializer = (p1, p2) => {
                    return JSON.stringify([[p1.X(),p1.Y()],[Math.atan2(p2.Y()-p1.Y(),p2.X()-p1.X()),
                        Math.sqrt((p2.X()-p1.X())*(p2.X()-p1.X())+(p2.Y()-p1.Y())*(p2.Y()-p1.Y()))]]);
                };

                var deserializer = (value) => {
                    var tmp = JSON.parse(value);
                    pointdeserializerparsed(point1, tmp[0]);
                    var angle = tmp[1][0];
                    var len = tmp[1][1];
                    tmp[1][0] = tmp[0][0] + len*Math.cos(angle);
                    tmp[1][1] = tmp[0][1] + len*Math.sin(angle);
                    pointdeserializerparsed(point2, tmp[1]);
                };

                initials[inputRef] = serializer(point1, point2);

                if (theInput.value && theInput.value != '') {
                    deserializer(theInput.value);
                }

                serializers[inputRef][point1.id] = () => {
                    return serializer(point1, point2);
                };
                serializers[inputRef][point2.id] = () => {
                    return serializer(point1, point2);
                };

                deserializers[inputRef].push((value) => {
                    deserializer(value);
                });

                if (point1.id in objectinput) {
                    if (!(inputRef in objectinput[point1.id])) {
                        objectinput[point1.id].push(inputRef);
                    }
                } else {
                    objectinput[point1.id] = [inputRef];
                }

                if (point2.id in objectinput) {
                    if (!(inputRef in objectinput[point2.id])) {
                        objectinput[point2.id].push(inputRef);
                    }
                } else {
                    objectinput[point2.id] = [inputRef];
                }

                // Then the binding from graph to input.
                point1.board.on('update', () => generalobjectupdatehandler(point1));
                point2.board.on('update', () => generalobjectupdatehandler(point2));
            },

            bind_slider: function(inputRef, slider) {
                _commonsetup(inputRef);
                // This function takes a JXG slider object and binds its value to a given input.
                var theInput = document.getElementById(inputRef);

                initials[inputRef] = sliderserializer(slider);

                if (theInput.value && theInput.value != '') {
                    sliderdeserializer(slider, theInput.value);
                }

                serializers[inputRef][slider.id] = () => {
                    return sliderserializer(slider);
                };

                deserializers[inputRef].push((value) => {
                    sliderdeserializer(slider, value);
                });

                if (slider.id in objectinput) {
                    if (!(inputRef in objectinput[slider.id])) {
                        objectinput[slider.id].push(inputRef);
                    }
                } else {
                    objectinput[slider.id] = [inputRef];
                }

                // Then the binding from graph to input.
                slider.board.on('update', () => generalobjectupdatehandler(slider));
            },

            bind_list_of: function(inputRef, list_of_objects) {
                _commonsetup(inputRef);
                // Takes a list of objects (points or sliders) and binds them to a single input.
                // The number and order of the elements in that list may not vary between page
                // loads for this same question seed.
                var theInput = document.getElementById(inputRef);

                var serializer = (objects) => {
                    var r =  '[';
                    for (var obj of objects) {
                        if (obj.getType() === 'slider') {
                            r = r + JSON.stringify(obj.Value()) + ',';
                        } else {
                            // Assume all else to be points.
                            r = r + pointserializer(obj) + ',';
                        }
                    }
                    r = r.substring(0, r.length - 1);
                    return r + ']';
                };

                var deserializer = (value) => {
                    var tmp = JSON.parse(value);
                    var i = 0;
                    for (var obj of list_of_objects) {
                        if (obj.getType() === 'slider') {
                            obj.setValue(tmp[i]);
                        } else {
                            pointdeserializerparsed(obj, tmp[i]);
                        }
                        i = i + 1;
                    }
                };

                initials[inputRef] = serializer(list_of_objects);

                if (theInput.value && theInput.value != '') {
                    deserializer(theInput.value);
                }

                deserializers[inputRef].push((value) => {
                    deserializer(value);
                });

                list_of_objects.forEach((obj) => {
                    serializers[inputRef][obj.id] = () => {
                        return serializer(list_of_objects);
                    };

                    if (obj.id in objectinput) {
                        if (!(inputRef in objectinput[obj.id])) {
                            objectinput[obj.id].push(inputRef);
                        }
                    } else {
                        objectinput[obj.id] = [inputRef];
                    }

                    // Then the binding from graph to input.
                    obj.board.on('update', () => generalobjectupdatehandler(obj));
                });
            }
        };
    });
