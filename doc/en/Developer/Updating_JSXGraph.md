# Updating JSXGraph

JSXGraph support in STACK relies on two parts.

1. `jsxgraph.js` is not part of jsxgraph. Its task is to include the (modified) official jsxgraph file and it adds some additional functions (like bind_point).
2. `jsxgraphcore-lazy.js` is the official jsxgraph fil.

These, and other JS, files are in `amd/src`.

## 1. Get JSXGraph

Download JSXGraph from here: [https://github.com/jsxgraph/jsxgraph](https://github.com/jsxgraph/jsxgraph).

We need just need the file `distrib/jsxgraphsrc.js`.

## 2. Rename and Modify

Rename the file to `jsxgraphcore-lazy.js`.

Open the file and delete the first lines up to the line

    var requirejs, require, define;

Insert

    define(function () {

as the new first line.

Change the last line from

    }));

to

    }());



## 3. Replace

Copy the file to `amd/src`.

## 4. Create minified file

Use `grunt amd` in the `amd` folder to create the minified versions in `amd/build`.

See [https://docs.moodle.org/dev/Javascript_Modules](https://docs.moodle.org/dev/Javascript_Modules) on how to properly setup nodejs and grunt.
