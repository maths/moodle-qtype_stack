$package("org.mathdox.formulaeditor"); 

$identify("org/mathdox/formulaeditor/EventHandlerLocal.js"); 

$main(function(){ 
  org.mathdox.formulaeditor.EventHandlerLocal = $extend(Object, {
    initialize : function(element) { 

      // save the 'this' pointer so that it can be used in the event handlers 
      var handler = this;

      // register the onkeydown handler, if present
      if (this.onkeydown instanceof Function) {

        var saved1 = element.onkeydown;
        element.onkeydown = function(event) {

          if (!event) {
            event = window.event; // MSIE's non-standard way of supplying events
          }

          return handler.onkeydown(event) && saved1 && saved1(event);

        };

      }

      // register the onkeypress handler, if present
      if (this.onkeypress instanceof Function) {

        var saved2 = element.onkeypress;
        element.onkeypress = function(event) {

          if (!event) {
            event = window.event; // MSIE's non-standard way of supplying events
          }

          if (!("charCode" in event)) {
            event.charCode = event.keyCode; // MSIE doesn't set charCode
          }

          return handler.onkeypress(event) && saved2 && saved2(event);

        };

      }

      // register the onmousedown handler, if present
      if (this.onmousedown instanceof Function) {

        var saved3 = element.onclick;
        var saved3a = element.ontouchstart;
        element.onmousedown = function(event) {

          if (!event) {
            event = window.event; // MSIE's non-standard way of supplying events
          }

          return handler.onmousedown(event) && saved3 && saved3(event);

        };

        element.ontouchstart = function(event) {
          if (!event) {
            event = window.event; // MSIE's non-standard way of supplying events
          }

          return handler.onmousedown(handler.rewriteTouchEvent(event)) && saved3a && saved3a(event);

        }

      }

      // register the onmouseup handler, if present
      if (this.onmouseup instanceof Function) {

        var saved4 = element.onclick;
        var saved4a = element.ontouchend;
        element.onmouseup = function(event) {

          if (!event) {
            event = window.event; // MSIE's non-standard way of supplying events
          }

          return handler.onmouseup(event) && saved4 && saved4(event);

        };

        element.ontouchend = function(event) {
          if (!event) {
            event = window.event; // MSIE's non-standard way of supplying events
          }
        
          return handler.onmouseup(handler.rewriteTouchEvent(event)) && saved5a && saved4a(event);
	}
      }

      // register the onmousemove handler, if present
      if (this.onmousemove instanceof Function) {

        var saved5 = element.onmousemove;
        element.onmousemove = function(event) {

          if (!event) {
            event = window.event; // MSIE's non-standard way of supplying events
          }

          return handler.onmousemove(event) && saved5 && saved5(event);

        };

      }

      // register the onmouseout handler, if present
      if (this.onmouseout instanceof Function) {

        var saved6 = element.onmouseout;
        element.onmouseout = function(event) {

          if (!event) {
            event = window.event; // MSIE's non-standard way of supplying events
          }

          return handler.onmouseout(event) && saved6 && saved6(event);

        };

      }

      // register the onmouseover handler, if present
      if (this.onmouseover instanceof Function) {

        var saved7 = element.onmouseover;
        element.onmouseover = function(event) {

          if (!event) {
            event = window.event; // MSIE's non-standard way of supplying events
          }

          return handler.onmouseover(event) && saved7 && saved7(event);

        };

      }

    },

    // see also http://ross.posterous.com/2008/08/19/iphone-touch-events-in-javascript/
    rewriteTouchEvent: function(event) {
      var touches = event.changedTouches;
      var first = touches[0];
      var type = "";

      switch(event.type)
      {
        case "touchstart": 
          type = "mousedown"; 
          break;
        case "touchmove":  
          type="mousemove"; 
          break;        
        case "touchend":
          type="mouseup"; 
          break;
        default: return;
      }
      
      // initMouseEvent(type, canBubble, cancelable, view, clickCount, 
      //           screenX, screenY, clientX, clientY, ctrlKey, 
      //           altKey, shiftKey, metaKey, button, relatedTarget);
    
      var simulatedEvent = element.createEvent("MouseEvent");
      simulatedEvent.initMouseEvent(type, true, true, window, 1, 
        first.screenX, first.screenY, 
        first.clientX, first.clientY, false, 
        false, false, false, 0/*left*/, null);
      simulatedEvent.mathdoxnoadjust = true;

      return simulatedEvent;
    }

  });

});
