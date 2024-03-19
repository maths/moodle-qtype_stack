$package("com.oreilly.javascript.tdg");

$identify("com/oreilly/javascript/tdg/XMLHttp.js");

$main(function(){
  /**
   * Class with static methods that facilitates XML HTTP calls
   * Note: modified to be initialized as an object and to use this
   * 
   * Several methods from JavaScript: The Definitive Guide, 5th edition
   * David Flanagan, 2006, O'Reilly, Sebastopol, ISBN-13: 978-0-596-10199-2
   * Chapter 20, starting at page 478, example 20-1, 20-2, 20-3
   */
  com.oreilly.javascript.tdg.XMLHttp = {
    
    /* example 20-1, page 480-481 */
     
    // This is a list of XMLHttpRequest-creation factory functions to try
    _factories : [
      function() { return new XMLHttpRequest(); },
      function() { return new ActiveXObject("Msxml2.XMLHTTP"); },
      function() { return new ActiveXObject("Microsoft.XMLHTTP"); }
    ],

    // When we find a factory that works, store it here.
    _factory : null,

    // Create and return a new XMLHttpRequest object.
    //
    // The first time we're called, try the list of factory functions until
    // we find one that returns a non-null value and does not throw an 
    // exception. Once we find a working factory, remember it for later use.
    //
    newRequest : function() {
      if (this._factory !== null) {
        return this._factory();
      }

      for (var i = 0; i < this._factories.length; i++) {
        try {
          var factory = this._factories[i];
          var request = factory();
          if (request !== null) {
            this._factory = factory;
            return request;
          }
        }
        catch (e) {
          continue;
        }
      }

      // If we ever get here, none of the factory candidates succeeded, 
      // so thown an exception now and for all future calls.
      this._factory = function() {
        throw new Error("XMLHttpRequest not supported");
      };
      this._factory(); // Throw an error
    },

    /* example 20-2, page 486 */
    getText : function(url, callback) {
      var request = this.newRequest();
      request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
          callback(request.responseText);
        }
      };
      request.open("GET", url);
      request.send(null);
    },

    /* example 20-3, page 486 */
    getXML : function(url, callback) {
      var request = this.newRequest();
      request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
          callback(request.responseXML);
        }
      };
      request.open("GET", url);
      request.send(null);
    },

    /* example 20-5, page 488 */
    /**
     * Send an HTTP POST request to the specified URL, using the names and
     * values of the properties of the values object as the body of the
     * request. Parse the server's response according to its content type and
     * pass the resulting value to the callback function. If an HTTP error
     * occurs, call the specified errorHandler function, or pass null to the
     * callback if no error handler is specified.
     **/
    post : function(url, values, callback, errorHandler) {
      var request = this.newRequest();
      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          if (request.status == 200) {
            callback(request.responseText); // NOTE: example modified here
          } 
          else {
            if (errorHandler) {
              errorHandler(request.status, request.statusText);
            } else {
              callback(null);
            }
          }
        } 
      };

      request.open("POST", url);
      // This header tells the server how to interpret the body of the request.
      request.setRequestHeader("Content-Type", 
                               "application/x-www-form-urlencoded");
      // Encode the properties of the values object and send them as
      // the body of the request.
      request.send(this.encodeFormData(values));
    },

    /**
     * Encode the property name/value pairs of an object as if they were from
     * an HTML for, using application/x-www-urlencoded format
     */
    encodeFormData : function(data) {
      var pairs = [];
      var regexp = /%20/g; // A regular expression to match an encoded space

      for (var name in data) {
        var value = data[name].toString();
        // Create a name/value pair, but encode name and value first
        // The global function encodeURIComponent does almost what we want,
        // but it encodes spaces as %20 instead of as "+". We have to
        // fix that with String.replace()
        var pair = encodeURIComponent(name).replace(regexp,"+") + '=' +
          encodeURIComponent(value).replace(regexp,"+");
        pairs.push(pair);
      }

      // Concatenate all the name/value pairs, separating them with &
      return pairs.join('&');
    }
  };

});
