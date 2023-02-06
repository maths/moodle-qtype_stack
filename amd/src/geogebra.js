/**
 * geogebra embedding used by geogebra.block.php
 * derived by jsxGraph STACK implementation
 * @copyright  2022 University of Edinburgh
 * @author     Tim Lutz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/// NOTE! This code does eval() a string with no validation.
// So lets hope this is the correct way to name a Moodle AMD module
define(["qtype_stack/geogebracore-lazy"], function(GEOGEBRA) {
    return {

            find_input_id: function(divid, name) {
                if(GEOGEBRA){}//GEOGEBRA variable: deprecated and not used any more
                var tmp = document.getElementById(divid);
                while ((tmp = tmp.parentElement) && !(tmp.classList.contains("formulation") &&
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
            bind_point: function(inputRef, appletRef, pointname) {
                // This function takes a GeoGebra point object and binds its coordinates to a given STACK input.
                var theInput = document.getElementById(inputRef);
                if (typeof theInput.value !== 'undefined' && theInput.value != '') {
                    // if a value exists move the point to it.
                    // the value is stored as a list of float values e.g. "[1,0.43]"
                    var coords = JSON.parse(theInput.value);
                    try {
                        appletRef.setCoords(pointname,coords[0],coords[1]);
                    } catch (err) {
                        // We do not care about this.
                    }
                }

                var initialX = appletRef.getXcoord(pointname);
                var initialY = appletRef.getYcoord(pointname);

                /**
                * Then the binding from graph to input
                */
                function updateValues(){

                    var pointx = appletRef.getXcoord(pointname);
                    var pointy = appletRef.getYcoord(pointname);

                    if (initialX !== pointx || initialY !== pointy) {
                        var tmp = JSON.stringify([pointx, pointy]);
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
                }
              setInterval(updateValues, 300);//time based listening is the only option because we can not listen to single objects

                var lastValue = JSON.stringify([appletRef.getXcoord(pointname), appletRef.getYcoord(pointname)]);

                // Then from input to graph. 'input' for live stuff and 'change' for other.
                theInput.addEventListener('input', function() {
                    if (theInput.value != lastValue) {
                        // Only when something changed.
                        try {
                            var tmp = JSON.parse(theInput.value);
                            if (typeof tmp[0] == 'number' && typeof tmp[1] == 'number') {
                                appletRef.setCoords(pointname,tmp);
                            }
                        } catch (err) {
                            // We do not care about this.
                        }
                        lastValue = theInput.value;
                    }
                });
                theInput.addEventListener('change', function() {
                    if (theInput.value != lastValue) {
                        // Only when something changed.
                        try {
                            var tmp = JSON.parse(theInput.value);
                            if (typeof tmp[0] == 'number' && typeof tmp[1] == 'number') {
                                appletRef.setCoords(pointname,tmp);
                            }
                        } catch (err) {
                            // We do not care about this.
                        }
                        lastValue = theInput.value;
                    }
                });
            },
            bind_value: function(inputRef, appletRef, valuename) {
                // This function takes a GeoGebra name of a value object and binds its value to a given input.
                var theInput = document.getElementById(inputRef);
                if (typeof theInput.value !== 'undefined' && theInput.value != '') {
                    // if a value exists move the valuename to it.
                    // the value is stored as a float value "0.43"
                    try {
                        appletRef.setValue(valuename,JSON.parse(theInput.value));
                    } catch (err) {
                        // We do not care about this.
                    }
                }

                var initialValue = appletRef.getValue(valuename);
                var movedInitial = false;
                /**
                * Then the binding from graph to input
                */
                function updateValues(){
                  // We do not want to set the input before the point actually moves.
                  if (movedInitial == false && initialValue != appletRef.getValue(valuename)){
                    movedInitial = true;
                  }
                  if (movedInitial) {
                      var tmp = JSON.stringify(appletRef.getValue(valuename));
                      if (theInput.value != tmp) {
                          // Avoid resetting this, as some event models might trigger
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
                }
                setInterval(updateValues, 300);//time based listening is the only option because we can not listen to single objects

                var lastValue = JSON.stringify(appletRef.getValue(valuename));

                // Then from input to graph. 'input' for live stuff and 'change' for other.
                theInput.addEventListener('input', function() {
                    if (theInput.value !== lastValue) {
                        // Only when something changed.
                        try {
                            var tmp = JSON.parse(theInput.value);
                            if (typeof tmp == 'number') {
                                appletRef.setValue(valuename,tmp);
                            }
                        } catch (err) {
                            // We do not care about this.
                        }
                        lastValue = theInput.value;
                    }
                });
                theInput.addEventListener('change', function() {
                    if (theInput.value !== lastValue) {
                        // Only when something changed.
                        try {
                            var tmp = JSON.parse(theInput.value);
                            if (typeof tmp == 'number') {
                                appletRef.setValue(valuename,tmp);
                            }
                        } catch (err) {
                            // We do not care about this.
                        }
                        lastValue = theInput.value;
                    }
                });
            },
            bind_value_to_remember_JSON: function(inputRef, appletRef, valuename) {
                //This function takes a GeoGebra name of a value object and binds its value to a given input.
                //bind_value_to_remember_JSON will manage bindings,
                //even if more than one GeoGebra object is bound to the same STACK input reference.
                //This function is created for the "remember" tag,
                //which stores more than one value in a single input field
                var theInput = document.getElementById(inputRef);
                if (typeof theInput.value !== 'undefined' && theInput.value != '') {
                    // if a value exists move the valuename to it.
                    // the value is stored as a float value "0.43"
                    try {
                        appletRef.setValue(valuename,JSON.parse(theInput.value)[valuename]);
                    } catch (err) {
                        // We do not care about this.
                    }
                }

                var initialValue = appletRef.getValue(valuename);
                var movedInitial = false;
                /**
                * Then the binding from graph to input
                */
                function updateValues(){
                  // We do not want to set the input before the point actually moves.
                  // but we have to initialize JSON with "{}"
                  if(theInput.value.trim().length === 0){
                    theInput.value="{}";
                  }

                  if (movedInitial == false && initialValue != appletRef.getValue(valuename)){
                    movedInitial = true;
                  }
                  if (movedInitial) {
                      var tmpValue = appletRef.getValue(valuename);
                      //check if geogebraname is listed in JSON
                      try{
                            var lastinput = JSON.parse(theInput.value);
                        } catch(err){
                            //We do not care about this
                        }
                        if(!lastinput || !lastinput[valuename] || lastinput[valuename] != tmpValue){
                            // Avoid resetting this, as some event models might trigger
                            // change events even when no change actually happens.

                            //overwriting value in last object
                            lastinput[valuename] = tmpValue;
                            //saving as JSON string
                            theInput.value = JSON.stringify(lastinput);

                            // As we set the inputs value programmatically no events
                            // will be fired. But for two way binding we want to fire them...
                            // However we do not need this in the preview where it annoys people.
                            if (window.location.pathname.indexOf('preview.php') === -1) {
                                    var e = new Event('change');
                                    theInput.dispatchEvent(e);
                            }
                        }
                    }
                }
                setInterval(updateValues, 300);//time based listening is the only option because we can not listen to single objects

                var lastValue = JSON.stringify(appletRef.getValue(valuename));

                // Then from input to graph. 'input' for live stuff and 'change' for other.
                theInput.addEventListener('input', function() {
                    try {
                        var tmp = JSON.parse(theInput.value);
                        if (JSON.stringify(tmp[valuename]) !== lastValue) {
                            // Only when something changed.
                            if (typeof tmp[valuename] == 'number') {
                                appletRef.setValue(valuename,tmp[valuename]);
                            }
                        }
                        lastValue = JSON.stringify(tmp[valuename]);
                    } catch (err) {
                        // We do not care about this.
                    }
                });
                theInput.addEventListener('change', function() {
                    try {
                        var tmp = JSON.parse(theInput.value);
                        if (tmp[valuename] !== lastValue) {
                            // Only when something changed.
                            if (typeof tmp[valuename] == 'number') {
                                appletRef.setValue(valuename,tmp[valuename]);
                            }
                        lastValue = JSON.stringify(tmp[valuename]);
                        }
                    } catch (err){
                            // We do not care about this.
                    }
                });
            },
            bind_point_to_remember_JSON: function(inputRef, appletRef, pointname) {
                // This function takes a GeoGebra point object and binds its coordinates to a given STACK input.
                //bind_point_to_remember_JSON will manage bindings,
                //even if more than one GeoGebra object is bound to the same STACK input reference.
                //This function is created for the "remember" tag,
                //which stores more than one value in a single input field
                var theInput = document.getElementById(inputRef);
                if (typeof theInput.value !== 'undefined' && theInput.value != '') {
                    // if a value exists move the point to it.
                    // the value is stored as a list of float values e.g. "[1,0.43]"
                    var coords = JSON.parse(theInput.value)[pointname];
                    try {
                        appletRef.setCoords(pointname,coords[0],coords[1]);
                    } catch (err) {
                        // We do not care about this.
                    }
                }

                var initialX = appletRef.getXcoord(pointname);
                var initialY = appletRef.getYcoord(pointname);

                /**
                * Then the binding from graph to input
                */
                function updateValues(){
                    // we have to initialize JSON with "{}"
                    if(theInput.value.trim().length === 0){
                        theInput.value="{}";
                      }

                    var pointx = appletRef.getXcoord(pointname);
                    var pointy = appletRef.getYcoord(pointname);

                    if (initialX !== pointx || initialY !== pointy) {
                        var tmpValue = [pointx, pointy];
                        initialX = false; // ignore these after initial change.
                        initialY = false;

                        try{
                            var lastinput = JSON.parse(theInput.value);
                        } catch(err){
                            //We do not care about this

                        }
                        if (!lastinput || !lastinput[pointname]
                            || lastinput[pointname][0] != tmpValue[0]
                            || lastinput[pointname][1] != tmpValue[1]) {
                            // Avoid resetting this, as some event models migth trigger
                            // change events even when no change actually happens.
                            lastinput[pointname] = tmpValue;
                            //saving as JSON string
                            theInput.value = JSON.stringify(lastinput);
                            // As we set the inputs value programmatically no events
                            // will be fired. But for two way binding we want to fire them...
                            // However we do not need this in the preview where it annoys people.
                            if (window.location.pathname.indexOf('preview.php') === -1) {
                                var e = new Event('change');
                                theInput.dispatchEvent(e);
                            }

                        }

                    }
                }
              setInterval(updateValues, 300);//time based listening is the only option because we can not listen to single objects

                var lastValue = JSON.stringify([appletRef.getXcoord(pointname), appletRef.getYcoord(pointname)]);

                // Then from input to graph. 'input' for live stuff and 'change' for other.
                theInput.addEventListener('input', function() {

                    try{
                        var tmp = JSON.parse(theInput.value);
                        if (JSON.stringify(tmp[pointname]) !== lastValue) {
                            // Only when something changed.
                                if (typeof tmp[pointname][0] == 'number' && typeof tmp[pointname][1] == 'number') {
                                    appletRef.setCoords(pointname,tmp[pointname]);
                                }
                            lastValue =  JSON.stringify(tmp[pointname]);
                        }
                    } catch (err) {
                        // We do not care about this.
                    }
                });
                theInput.addEventListener('change', function() {
                    try{
                        var tmp = JSON.parse(theInput.value);
                        if (JSON.stringify(tmp[pointname]) !== lastValue) {
                            // Only when something changed.
                            if (typeof tmp[pointname][0] == 'number' && typeof tmp[pointname][1] == 'number') {
                                appletRef.setCoords(pointname,tmp[pointname]);
                            }
                            lastValue = JSON.stringify(tmp[pointname]);
                        }
                    } catch(err){
                        // We do not care about this.
                    }
                });
            }
        };
    });
