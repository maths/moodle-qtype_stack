## Reacting to feedback from the STACK question in JSXGraph

In some situations, it can be quite useful to change the graph state based on the feedback that students get displayed after submitting the task.

With STACK-JS, JSXGraph is contained inside an IFRAME and thus can not directly access DOM elements from the STACK question. So if you want to check whether some feedback is present in the STACK question, you have to use the function  `stack_js.get_content(id)` from the stack_js namespace. The functions from this namespace can be called in the JavaScript code inside the JSXGraph block just like the binding functions from the `stack_jxg` namespace.

The following steps should be taken to react to feedback inside of the JSXGraph applet:

1. Include an empty span with a unique identifier inside the feedback of a PRT node, so that JSXGraph can look for that element
2. Call the function `stack_js.get_content(id)` with the id of the span you placed inside your feedback in the JSXGraph code. As this function is async and returns a promise for the content, make sure to write your code for changing the graph state inside a chained `.then()`.

A common use case for this could be that you want to make a point fixed so that the user can not drag it anymore after he submitted the question and received a certain feedback. A minimal example for this would then look like this:

In one of your PRTs, you place an empty span with an id like for example `feedback-1`


    [[jsxgraph]]

    // A sample board
    var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-5, 5, 5, -5], axis: true, showCopyright: false});
    
    // Create a point for demo purpose
    var a = board.create('point',[1,2],{name:'a'});
    
    // Here we check if there is a certain feedback span present in the STACK question  
    stack_js.get_content('feedback-1').then((content) => {

    if (content !== null) {
    // As the content is not null this means the span is present so feedback is displayed and we can react to it here
    a.setAttribute({ fixed: true, highlight: false});
    }

    });

    [[/jsxgraph]]

The function `stack_js.get_content(id)` looks for an element in the DOM of the parent document and returns a promise that will resolve to the content of that element. If the content is not `null`, that means it found the element somewhere in the question. As this operation is async, you will always have to use a callback using `.then()`.

If you want to know more about STACK-JS and the functions provided for interacting with the STACK question content (change inputs, switch content, toggle the visibility of content), then you can have a look at [STACK-JS](../../Developer/STACK-JS.md).