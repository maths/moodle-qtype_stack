# Updating JSXGraph

JSXGraph is used in its released form. The files related to it are now stored under `corsscripts/`.

Download JSXGraph: [https://github.com/jsxgraph/jsxgraph](https://github.com/jsxgraph/jsxgraph).

1. Copy over minified files `jsxgraph.min.css` and `jsxgraphcore.min.js`, there is no need to copy the non minified versions.
2. Add frozen CDN URLs to the named version map in stack/cas/castext2/blocks/jsxgraph.block.php.
3. Test that the healthcheck example works, the binding in particular and the render of the formula.
4. Test the binding in more detail by checking that samplequestions/stack_jxg.binding-demo-4.4.xml still does sensible things.
5. If all looks good and keeps looking good after Moodle JavaScript reset, NOOP edit of question to recompile it and running in private-mode/incognito-mode to ensure that things are new and not coming from any cache. Then things should be fine.
6. Check Meclib materials, and further testing!

An example commit is https://github.com/maths/moodle-qtype_stack/commit/409cd0960f003e80d81a982fb96d6f7c310576de

The old STACK side `jsxgraph.js` that provided the `stack_jxg` features is now called `stackjsxgraph.js` and is being served from that same CORS-header tuning directory with that specific script. (Minification can be done using uglify-js:  
`npm install -g uglify-js`  
`uglifyjs stackjsxgraph.js > stackjsxgraph.min.js`.)

We do not apply Moodles or any other systems JavaScript processing on these, no need to run `grunt` or any such tool.

## NOTE!

We really want to have a local JSXGraph copy instead of relying on a CDN version. We want to make it easy to run STACK in a closed network with no external requirements and having a local JSXGraph is one of the things we do to remove an external requirement.

One can always just state that a particular `[[jsxgraph]]` block should use the official CDN version by setting `[[jsxgraph version="cdn"]]` or any specific other versions by tuning `[[jsxgraph overridecss="..." overridejs="..."]]` separately. Then, one can test if things work, but then one needs to check that one is testing with the correct version every time one tests...