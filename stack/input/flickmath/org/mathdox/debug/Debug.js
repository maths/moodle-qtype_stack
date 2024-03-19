$package("org.mathdox.debug");

$identify("org/mathdox/debug/Debug.js");

$main(function() {
  org.mathdox.debug.Debug = $extend(Object, {

    //other variables
    debug             : true,
    debugBuffer       : "",
    debugDiv          : null,
    debugLevel        : 5,
    frameInitTimeStep : 10,
    idstr             : "MathDox Exercise System - PostDebug",
    
    initialize: function() {
      this.debugChildInit.idstr = this.idstr;
    },

    addDebug: function(text, level) {
      if (level === undefined || level === null) {
        level = 0;
      }

      if(this.debug && level <= this.debugLevel) {
        try {
          this.debugDiv.innerHTML+=text+'<br>';
        } catch(err) {
          this.debugBuffer+=text+'<br>';
        }
      }
    },

    addMessageListeners: function(messageEventLib) {
      this.messageListener.debug = this;
      messageEventLib.addMessageListener(this.idstr, "debug", this.messageListener);
    },

    createDebug: function() {
      if(this.debug) {
        var debugDiv = document.createElement("div");
        debugDiv.id="debugWindow";
        debugDiv.style.position="absolute";
        debugDiv.style.right="5px";
        debugDiv.style.top="5px";
        debugDiv.style.borderStyle="solid";
        debugDiv.style.borderColor="#000000";
        debugDiv.style.borderWidth="1px";
        debugDiv.style.backgroundColor="#EEEEEE";
        debugDiv.style.padding="5px";
        debugDiv.style.fontSize="12px";
        debugDiv.innerHTML="<b>Debug-window</b><br />"+this.debugBuffer;
        this.debugDiv = debugDiv;
        try {
          this.addDebug("- add debug window");
          document.body.insertBefore(debugDiv,document.body.firstChild);
        } catch(err) {
          this.addDebug("- delayed adding debug window (not ready)");
          var obj = this;

          setTimeout(function() {obj.createDebug(); }, this.frameInitTimestep);
        }
      }
    },
  
    debugChildInit: function() {
      var data = {
        idstr : arguments.callee.idstr,
        mode  : "debugInit"
      };
      var list = document.getElementsByTagName('iframe');
      for(var i=0;i<list.length;i++) {
        var iframe=list[i];

        iframe.contentWindow.postMessage(data, "*");
      }
    },

    messageListener: function(event) {
      if (typeof event.data === "object") {
        var data = event.data;
        if (data.idstr == arguments.callee.debug.idstr) {
          if (data.mode == "debug") {
            if (data.text !== undefined && data.text !== null) {
              arguments.callee.debug.addDebug("[child] "+data.text);
            }
          }
        }
      }
    },

    startDebug: function() {
    }
  });
});
