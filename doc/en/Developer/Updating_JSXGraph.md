# Updating JSXGraph

Since the stack-js update, we have been able to use JSXGraph in its released form.

The files related to it are now stored under `corsscripts/`.

Download JSXGraph from here: [https://github.com/jsxgraph/jsxgraph](https://github.com/jsxgraph/jsxgraph).

The files one needs to copy over are `jsxgraph.min.css` and `jsxgraphcore.min.js`, there is no need to copy the non minified versions.

The old STACK side `jsxgraph.js` that provided the `stack_jxg` features is now called `stackjsxgraph.js` and is being served from that same CORS-header tuning directory with that specific script.

We do not apply Moodles or any other systems JavaScript processing on these, no need to run `grunt` or any such tool.

## NOTE!

We really want to have a local JSXGraph copy instead of relying on a CDN version. We want to make it easy to run STACK in a closed network with no external requirements and having a local JSXGraph is one of the things we do to remove an external requirement.

