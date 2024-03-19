/**
 * Declare the global variables that will be exported by this script.
 */
var $package;
var $identify;
var $require;
var $contains;
var $main;
var $extend;
var $baseurl;
var $setOptions;

/**
 * Enter a private variable context, so that variables declared below are not
 * visible outside this script.
 */
(function(){

  /**
   * Contains a record of all $main arguments that have been executed. This is
   * used by the $main and $require functions to ensure that each $main argument
   * is executed only once.
   */
  var executed = {
    urls: {},
    nourl: {}
  };

  /**
   * This counter is used to keep track of how many urls were requested to be
   * loaded (using the $require function), but haven't finished loading yet.
   */
  var loading = 1;

  /**
   * A list of functions that wish to be called when the 'loading' counter
   * reaches 0.
   */
  var waiting = { 
    urls:{},
    nourl:[] 
  };

  /**
   * A list of scripts already added
   */
  var loadingurls = {};

  /**
   * Holds a reference to the script tag that was last added to the document by
   * the $require function. New scripts will be added below this script.
   */
  var lastadded;

  /**
   * Record the global variable scope in the 'global' object. This is used by
   * the $package function to create new package objects in the global scope.
   */
  var global = this;

  /**
   * Returns the package object for the specified package string. For instance,
   * the call $package("org.mathdox.javascript") will return the
   * org.mathdox.javascript object. If the package object doesn't exist, it is
   * created.
   */
  $package = function(string) {

    var parts = string.split(".");
    var parent = global;
    var i;

    for (i=0; i<parts.length; i++) {
      if (!parent[parts[i]]) {
        parent[parts[i]] = {};
      }
      parent = parent[parts[i]];
    }
    return parent;

  };

  /**
   * url of the currently procressed file 
   */
  var current={}; 

  /**
   * set the url of the currently processed file
   */
  $identify = function(name) {
    current.url = name;
  };

  var execute_with_requirements = function(url) {
    if (executed.urls[url]) {
      return;
    }

    if (!waiting.urls[url]) {
      return;
    }
	
    var requirements = waiting.urls[url].requirements;
    if (requirements && requirements.length) {
      var i;
      for (i=requirements.length-1; i>=0; i--) {
	execute_with_requirements(requirements[i].url);
      }
    }
    //alert("executing: "+url);
    waiting.urls[url].continuation();
    executed.urls[url] = true;
  };

  /**
   * When a script has been loaded, this function will check whether all
   * requirements have been loaded, and will then execute the main sections.
   */
  var onload = function() {

    loading = loading - 1;
    if (loading === 0) {
      while (waiting.nourl.length > 0) {
	var nourl = waiting.nourl.pop();
	var urlobj;
	for (urlobj in nourl.requirements) {
	  execute_with_requirements(nourl.requirements[urlobj].url);
	}
        var continuation = nourl.continuation;
        if (!executed.nourl[continuation]) {
          executed.nourl[continuation] = true;
          continuation();
        }
      }

      var url;
      for (url in waiting.urls) {
	execute_with_requirements(url);
      }
    }

  };

  /**
   * Ensures that the script at the specified url is loaded before the main
   * section is executed.
   * We cannot detect whether a script has finished loading when a third-party
   * script is loaded that doesn't contain a call to $main. In those cases a
   * 'ready' function to detect load completion must be specified. This function
   * should return 'true' when the script has loaded, and 'false' otherwise.
   * Typically, such a function would check for the presence of a variable that
   * is set by the third-party script.
   */

  /* delayed require */
  $require = function(url, ready) {
    if (!current.requirements) {
      current.requirements = new Array();
    }
    current.requirements.push( { url:url, ready:ready } );
  };

  var require_action = function(url, ready) {
    // already being loaded
    if (waiting.urls[url] || loadingurls[url]) {
      return;
    }

    // store that this url has been added
    loadingurls[url] = true;

    // add base url to url, unless it is an absolute url
    if (!url.match(/^\/|:/)) {
      url = $baseurl + url;
    }

    // increase the 'loading' counter
    loading = loading + 1;

    // add script tag to document
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src = url;
    lastadded.parentNode.insertBefore(script, lastadded.nextSibling);
    lastadded = script;

    // use the 'ready' function to check for load complete, if it was specified
    if (ready instanceof Function) {
      var wait = function() {
        if (ready()) {
          onload();
        }
        else {
          setTimeout(wait, 100);
        }
      };
      wait();
    }

  };

  /**
   * Calls the continuation function that is specified as a parameter when all
   * previously specified requirements have been loaded. Each main section is
   * only executed once.
   */
  $main = function(continuation) {
    current.continuation = continuation;

    /* add current to waiting queue */
    if (current.url) {
      waiting.urls[current.url] = current;
    } else {
      waiting.nourl.push({
	continuation:continuation, 
	requirements:current.requirements
      });
    }

    /* store requirements and reset current */
    var requirements = current.requirements;

    current={};

    /* do delayed requires and create waiting queue */
    if (requirements) {
      var i;

      for (i = requirements.length - 1; i>=0; i--) {
	var req = requirements[i];

	require_action(req.url, req.ready);
      }
    }
    /* call onload function */
    onload();
  };

  /**
   * Returns a new class that adds the specified properties to the specified
   * parent class.
   */
  $extend = function(parent, properties) {

    // figure out what the prototype of the new class will be
    var prototype;
    if (parent instanceof Function) {
      prototype = new parent();
    }
    else {
      var parentConstructor = function(){};
      parentConstructor.prototype = parent;
      prototype = new parentConstructor();
    }

    // create the new class constructor
    var constructor = function(){
      if (this.initialize instanceof Function) {
        this.initialize.apply(this, arguments);
      }
    };

    // copy any static class properties of the parent to the new class
    if (parent instanceof Function) {
      for (x in parent) {
        constructor[x] = parent[x];
      }
    }

    // copy the additional properties to the prototype
    for (x in properties) {

      // add a 'parent' property to methods, to enable super calls
      if (properties[x] instanceof Function) {
        if (!("parent" in properties[x])) {
          if (parent instanceof Function) {
            properties[x].parent = parent.prototype;
          }
          else {
            properties[x].parent = parent;
          }
        }
      }

      prototype[x] = properties[x];

    }

    // combine constructor and prototype
    constructor.prototype = prototype;

    // return new class constructor
    return constructor;

  };

  /*
   * Help function to indicate that more files are concatenated and do not
   * needed to be loaded seperately.
   */
  $contains = function(url) {
    loadingurls[url] = true;
    loading += 1;
  };

  $setOptions = function(prefix, optionList) {
    var options = $package(prefix);
    var optionName;
    for (optionName in optionList) {
      options[optionName] = optionList[optionName];
    }
    return options;
  };

  /**
   * Find the script tag that was used to load this script, and use it to
   * calculate the value of 'lastadded' and '$baseurl'.
   */
  var scripts = document.getElementsByTagName("script");
  var scriptfinder1 = /(.*)org\/mathdox\/javascript\/core\.js$/;
  var scriptfinder2 = /(.*)org\/mathdox\/formulaeditor\/FEConcatenation\.js$/;
  for (var i=0; i<scripts.length; i++) {
    var match = scripts[i].src.match(scriptfinder1);
    if (match === null) {
      match = scripts[i].src.match(scriptfinder2);
    }
    if (match !== null) {
      lastadded = scripts[i];
      $baseurl = match[1];
    }
  }

})();
