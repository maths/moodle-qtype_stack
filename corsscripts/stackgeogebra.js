/**
 * Geogebra embedding used by geogebra.block.php
 * Derived from the jsxGraph STACK implementation.
 *
 * The creation of these resources has been (partially) funded by the ERASMUS+ grant program of the
 * European Union under grant No. 2021-1-DE01-KA220-HED-000032031. Neither the European Commission
 * nor the project's national funding agency DAAD are responsible for the content or liable for
 * any losses or damage resulting of the use of these resources.
 *
 * @copyright  2022 University of Edinburgh
 * @author     Tim Lutz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export const stack_geogebra = {
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
                // Avoid event when some of the coords are not valid numbers.
                if (theInput.value != tmp) {
                    // Avoid resetting this, as some event models might trigger
                    // change events even when no change actually happens.
                    if (pointx!=null && !Number.isNaN(pointx) && pointy!=null && !Number.isNaN(pointy)){
                        theInput.value = tmp;
                        var e = new Event('change');
                        theInput.dispatchEvent(e);
                    }
                }
            }
        }
      setInterval(updateValues, 300);//time based listening is the only option because we can not listen to single objects
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
              if (theInput.value != tmp && tmp!=null) {
                  // Avoid resetting this, as some event models might trigger
                  // change events even when no change actually happens.
                  theInput.value = tmp;
                  var e = new Event('change');
                  theInput.dispatchEvent(e);
               }
          }
        }
        setInterval(updateValues, 300);//time based listening is the only option because we can not listen to single objects
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

                    var e = new Event('change');
                    theInput.dispatchEvent(e);
                }
            }
        }
        setInterval(updateValues, 300);//time based listening is the only option because we can not listen to single objects
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
                    var e = new Event('change');
                    theInput.dispatchEvent(e);
                }

            }
        }
        setInterval(updateValues, 300);//time based listening is the only option because we can not listen to single objects
}
}

export default stack_geogebra;