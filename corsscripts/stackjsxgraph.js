/**
 * This is a library for bindign STACK inputs to JSXGraph primitives.
 * 
 * @copyright  2023 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

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

// Internal tally of objects that have been registered and do not need to be registered again.
// This is a dictionary with some depth `registeredobjects[eventtype][object.id]`
var registeredobjects = {};

// Flag to stop propagation.
var active = false;

function _commonsetup(inputname) {
    if (!(inputname in serializers)) {
        serializers[inputname] = {};
        deserializers[inputname] = [];

        var input = document.getElementById(inputname);
        input.addEventListener('input', () => generalinputupdatehandler(inputname));
        input.addEventListener('change', () => generalinputupdatehandler(inputname));
    }
}

function registerobject(object, eventtype) {
    if (eventtype === undefined) {
        // By default the events we track are the update events.
        eventtype = ['update'];
    } else if (typeof eventtype === 'string' || eventtype instanceof String) {
        // The argument can be a list of events to track or a singular one.
        eventtype = [eventtype];
    }
    eventtype.forEach((et) => {
        if (!(et in registeredobjects)) {
            registeredobjects[et] = {};
        }
        if (!(object.id in registeredobjects[et])) {
            object.board.on(et, () => generalobjectupdatehandlerid(object.id));
            registeredobjects[et][object.id] = object;
        }
    });
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
    generalobjectupdatehandlerid(object.id);
}

function generalobjectupdatehandlerid(id) {
    if (!active) {
        active = true;
        try {
            var handledinputs = [];
            if (id in objectinput) {
                for (var i = 0; i < objectinput[id].length; i++) {
                    var inputname = objectinput[id][i];
                    if (handledinputs.indexOf(inputname) === -1) {
                        handledinputs.push(inputname);
                        var input = document.getElementById(inputname);
                        var val = serializers[inputname][id]();
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
            for (var gi = 0; gi < objectgroups.length; gi++) {
                var group = objectgroups[gi];
                if (group.indexOf(id) !== -1) {
                    for (var gt = 0; gt < group.length; gt++) {
                        var obj = group[gt];
                        if (obj !== id) {
                            if (obj in objectinput) {
                                for (var i = 0; i < objectinput[obj].length; i++) {
                                    var inputname = objectinput[obj][i];
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
            for (var i = 0; i < handledinputs.length; i++) {
                var input = document.getElementById(handledinputs[i]);
                var e = new Event('change');
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
        // Check all the objects serializing to this input. Note that
        // some of them may exist in different graphs.
        var input = document.getElementById(inputname);
        var keys = Object.keys(serializers[inputname]);
        var ok = false;
        for (var i = 0; i < keys.length; i++) {
            var old = serializers[inputname][keys[i]]();
            if (old !== input.value) {
                ok = true;
                i = keys.length + 1;
            }
        }

        if (ok) {
            // And yes we trigger everything as we do not actually
            // keep track of the ones that truly need to be triggered.
            // But this is fast and converges in a few iterations.
            for (var i = 0; i < deserializers[inputname].length; i++) {
                deserializers[inputname][i](input.value);
            }
        }
    }
}

// Simple syntax changer for lists of floats and nothing more complex.
// This will probably not work for your custom-bindings, and will thus not
// be available there.
//  `naiveseparatorreplacer("[1,3;2]",',',".",';',",") -> "[1.3,2]"`
function naiveseparatorreplacer(jsonish, dfrom, dto, lfrom, lto) {
    if (dfrom === dto && lfrom === lto) {
        return jsonish;
    }
    var work = jsonish.replaceAll(dfrom, " dsep ").replaceAll(lfrom, " lsep ");
    work = work.replaceAll(" dsep ", dto).replaceAll(" lsep ", lto);
    return work;
}

// Utility for internal logic.
function getinputseps(inputname) {
    const input = document.getElementById(inputname);
    var result = ['.', ','];
    if ('stackInputDecimalSeparator' in input.dataset) {
        result[0] = input.dataset['stackInputDecimalSeparator'];
    }
    if ('stackInputListSeparator' in input.dataset) {
        result[1] = input.dataset['stackInputListSeparator'];
    }
    return result;
}

export const stack_jxg = {
    define_group: function(list) {
        // Moving any of these objects (points sliders) leads to all of them
        // being considered moved.
        var l = [];
        for (var i = 0; i < list.length; i++) {
            if (l.indexOf(list[i].id) === -1) {
                l.push(list[i].id);
            }
        }
        objectgroups.push(l);
    },

    starts_moved: function(obj) {
        // Makes this object start its life as moved.
        // Call after bindings have been defined and possible groups declared.
        if (obj.id in objectinput) {
            for (var i = 0; i < objectinput[obj.id].length; i++) {
                initials[objectinput[obj.id][i]] = null;
            }
            // This is not a registration of the update handler
            // we actually force call it.
            generalobjectupdatehandler(obj);
        }
    },

    clear_initial: function(obj) {
        // Removes the protected initial value from this object.
        // This may be of use if one wants to trigger input population by
        // events that don't actually change the serialisation result.
        //
        // Basically this is starts_moved without filling in the input.
        if (obj.id in objectinput) {
            for (var i = 0; i < objectinput[obj.id].length; i++) {
                initials[objectinput[obj.id][i]] = null;
            }
        }
    },

    // Note that the eventtypes argument is optional. If not defined
    // the logic is going to be connected to "update"-events.
    // Should you want other events you can give a single string for one 
    // specific type or a list of strings. Do note that the "update" event
    // is not necessary, but there are plenty of authoring patterns like
    // "handle-objects" that tend to use it, so including it as an eventtype 
    // might make sense.
    custom_bind: function(input, serializer, deserializer, objects, eventtypes) {
        // Allows one to define a custom binding using whatever 
        // serialization one wishes.
        _commonsetup(input);

        // Initialse the initial value store.
        initials[input] = serializer();

        var theInput = document.getElementById(input);
        // If a value is already in the input restore it.
        if (theInput.value && theInput.value != '') {
            deserializer(theInput.value);
        }

        // Register this as a normal deserialiser for this input.
        deserializers[input].push(deserializer);

        // For each of these objects set the serialiser from them to 
        // the input be the one defined.
        // Also build the map of objects to inputs and register for event tracking.
        for (var i = 0; i < objects.length; i++) {
            this.register_object(input, objects[i], serializer, eventtypes);
        }
    },

    // Note that the eventtypes argument is optional. If not defined
    // the logic is going to be connected to "update"-events.
    // Should you want other events you can give a single string for one 
    // specific type or a list of strings. Do note that the "update" event
    // is not necessary, but there are plenty of authoring patterns like
    // "handle-objects" that tend to use it, so including it as an eventtype 
    // might make sense.
    register_object: function(input, object, serializer, eventtypes) {
        // For when you need to declare a new object that was not there during 
        // the initial binding.
        if (object.id in objectinput) {
            if (!(input in objectinput[object.id])) {
                objectinput[object.id].push(input);
            }
        } else {
            objectinput[object.id] = [input];
        }
        if (!(input in serializers)) {
            /* If someone does registrations before other bindings to that input. */
            _commonsetup(input);
        }
        serializers[input][object.id] = serializer;

        registerobject(object, eventtypes);
    },

    bind_point: function(inputRef, point) {
        var serializer = () => pointserializer(point);
        var deserializer = (value) => pointdeserializer(point, value);

        const seps = getinputseps(inputRef);
        if (seps !== ['.', ',']) {
            var serializer2 = () => naiveseparatorreplacer(serializer(), '.', seps[0], ',', seps[1]);
            var deserializer2 = (value) => deserializer(naiveseparatorreplacer(value, seps[0], '.', seps[1], ','));
            this.custom_bind(inputRef, serializer2, deserializer2, [point]);
        } else {
            this.custom_bind(inputRef, serializer, deserializer, [point]);    
        }        
    },

    bind_point_dual: function(inputRef, p1, p2) {
        var serializer = () => {
            return JSON.stringify([[p1.X(),p1.Y()],[p2.X(),p2.Y()]]);
        };

        var deserializer = (value) => {
            var tmp = JSON.parse(value);
            pointdeserializerparsed(p1, tmp[0]);
            pointdeserializerparsed(p2, tmp[1]);
        };

        const seps = getinputseps(inputRef);
        if (seps !== ['.', ',']) {
            var serializer2 = () => naiveseparatorreplacer(serializer(), '.', seps[0], ',', seps[1]);
            var deserializer2 = (value) => deserializer(naiveseparatorreplacer(value, seps[0], '.', seps[1], ','));
            this.custom_bind(inputRef, serializer2, deserializer2, [p1, p2]);
        } else {
            this.custom_bind(inputRef, serializer, deserializer, [p1, p2]);
        }
    },

    bind_point_relative: function(inputRef, p1, p2) {
        var serializer = () => {
            return JSON.stringify([[p1.X(),p1.Y()],[p2.X()-p1.X(),p2.Y()-p1.Y()]]);
        };

        var deserializer = (value) => {
            var tmp = JSON.parse(value);
            pointdeserializerparsed(p1, tmp[0]);
            tmp[1][0] = tmp[1][0] + tmp[0][0];
            tmp[1][1] = tmp[1][1] + tmp[0][1];
            pointdeserializerparsed(p2, tmp[1]);
        };

        const seps = getinputseps(inputRef);
        if (seps !== ['.', ',']) {
            var serializer2 = () => naiveseparatorreplacer(serializer(), '.', seps[0], ',', seps[1]);
            var deserializer2 = (value) => deserializer(naiveseparatorreplacer(value, seps[0], '.', seps[1], ','));
            this.custom_bind(inputRef, serializer2, deserializer2, [p1, p2]);
        } else {
            this.custom_bind(inputRef, serializer, deserializer, [p1, p2]);
        }
    },

    bind_point_direction: function(inputRef, p1, p2) {
        var serializer = () => {
            return JSON.stringify([[p1.X(),p1.Y()],[Math.atan2(p2.Y()-p1.Y(),p2.X()-p1.X()),
                Math.sqrt((p2.X()-p1.X())*(p2.X()-p1.X())+(p2.Y()-p1.Y())*(p2.Y()-p1.Y()))]]);
        };

        var deserializer = (value) => {
            var tmp = JSON.parse(value);
            pointdeserializerparsed(p1, tmp[0]);
            var angle = tmp[1][0];
            var len = tmp[1][1];
            tmp[1][0] = tmp[0][0] + len*Math.cos(angle);
            tmp[1][1] = tmp[0][1] + len*Math.sin(angle);
            pointdeserializerparsed(p2, tmp[1]);
        };

        const seps = getinputseps(inputRef);
        if (seps !== ['.', ',']) {
            var serializer2 = () => naiveseparatorreplacer(serializer(), '.', seps[0], ',', seps[1]);
            var deserializer2 = (value) => deserializer(naiveseparatorreplacer(value, seps[0], '.', seps[1], ','));
            this.custom_bind(inputRef, serializer2, deserializer2, [p1, p2]);
        } else {
            this.custom_bind(inputRef, serializer, deserializer, [p1, p2]);
        }
    },

    bind_slider: function(inputRef, slider) {
        var serializer = () => sliderserializer(slider);
        var deserializer = (value) => sliderdeserializer(slider, value);

        const seps = getinputseps(inputRef);
        if (seps !== ['.', ',']) {
            var serializer2 = () => naiveseparatorreplacer(serializer(), '.', seps[0], ',', seps[1]);
            var deserializer2 = (value) => deserializer(naiveseparatorreplacer(value, seps[0], '.', seps[1], ','));
            this.custom_bind(inputRef, serializer2, deserializer2, [slider]);
        } else {
            this.custom_bind(inputRef, serializer, deserializer, [slider]);
        }
    },

    bind_list_of: function(inputRef, list_of_objects) {
        var serializer = () => {
            var r =  '[';
            for (var i = 0; i < list_of_objects.length; i++) {
                var obj = list_of_objects[i];
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
            for (var i = 0; (i < list_of_objects.length && i < tmp.length); i++) {
                var obj = list_of_objects[i];
                if (obj.getType() === 'slider') {
                    obj.setValue(tmp[i]);
                } else {
                    pointdeserializerparsed(obj, tmp[i]);
                }
            }
        };

        const seps = getinputseps(inputRef);
        if (seps !== ['.', ',']) {
            var serializer2 = () => naiveseparatorreplacer(serializer(), '.', seps[0], ',', seps[1]);
            var deserializer2 = (value) => deserializer(naiveseparatorreplacer(value, seps[0], '.', seps[1], ','));
            this.custom_bind(inputRef, serializer2, deserializer2, list_of_objects);
        } else {
            this.custom_bind(inputRef, serializer, deserializer, list_of_objects);
        }
    },

    /**
     * Convert a string containing a MAXIMA /STACK expression into a JSXGraph / JessieCode string
     * or an array of JSXGraph / JessieCode strings.
     *
     * @example
     * console.log( stack_jxg.stack2jsxgraph("%e**x") );
     * // Output:
     * //    "EULER**x"
     *
     * @example
     * console.log( stack_jxg.stack2jsxgraph("[%pi*(x**2 - 1), %phi*(x - 1), %gamma*(x+1)]") );
     * // Output:
     * //    [ "PI*(x**2 - 1)", "1.618033988749895*(x - 1)", "0.5772156649015329*(x+1)" ]
     *
     * @param {String} str
     * @returns String
     */
    stack2jsxgraph: function(str) {
        var t;

        t = str.
            replace(/%pi/g, 'PI').
            replace(/%e/g, 'EULER').
            replace(/%phi/g, '1.618033988749895').
            replace(/%gamma/g, '0.5772156649015329').
            trim();

        // String containing array -> array containing strings
        if (t[0] === '[' && t[t.length - 1] === ']') {
            t = t.slice(1, -1).split(/\s*,\s*/);
        }

        return t;
    }
};

export default stack_jxg;