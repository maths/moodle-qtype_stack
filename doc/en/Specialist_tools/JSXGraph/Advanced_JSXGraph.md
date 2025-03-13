# Advanced JSXGraph: `stack_jxg.custom_bind`

As stated in the general [JSXGraph-block docs](index.md#manual_binding) binding together
STACK inputs and the state of the graph consists of three important things:

 1. We need to turn the state we wish to store into a string when it needs to be stored.
 2. We need to be able to restore the state from a string when need be, typically on page
    load or during two-way binding style usage.
 3. We need to know when those things need to happen, i.e. what interactions with the graph
    trigger the first point and when to trigger the second.

As this is almost always the same for all bindings the `stack_jxg` library provides a general
function called `custom_bind` which when given four things will ensure that those things
happen at the correct times. The arguments of that function are as follows:

 1. Reference to the input that will be used to store the value and which will be watched
    for external changes, i.e. for two-way binding and for the initial value.
 2. A serialiser-function, something that when called will generate a string representation
    of the relevant state of the graph. It will be called without any arguments, and will most
    likely not be a generally useful function.
 3. A deserialiser-function, something that will modify the state of the graph to match
    the string given to the function. Again this is probably not a generally useful function.
 4. A list of JSXGraph elements, points etc. that when updated should trigger serialisation.
    Note that this might be a static value during the time of the call of the `custom_bind`
    function but one can extend this list later, as we will see in an example soon. Also note
    that if your state is not directly tied to objects that the user interracts with you may
    use a hidden point here and simply trigger `"update"`-events for that point.

Basically, if you can turn the state into a string and back and know the objects you should
be able to construct any binding you want.

## Some special notes

There are things that may not be obvious, here are some that are worth noting:

 1. The serialisation string may contain much more than is necessary to restore the state
    during deserialisation. For example, if the state is defined by the position of points
    that is enough to restore it, but the serialisation could still contain various angles
    or other helpful details in addition to those points so that you do not need to calculate
    them in Maxima.
 2. The counter point to that is that you should never trust anything coming from
    the browser and nothing grading related should be calculated in the browser. However,
    it is pretty rare to see a student modifying the serialised state stored in the input
    so that it would contain wrong grading details, so do what feels natural. What is likely
    though is for the student to read the code that generates those grading related details
    and if it for example generates boolean values the student may get some hints on what is
    required from the code.


## A complicated example

Lets construct a custom binding that demonstrates some advanced concepts. For an example lets
build a graph construction tool where one can create nodes (i.e. points) and connect and
disconnect edges (line segments) between them. We will additionally want to store all of this
state into a singular input field, thus mixing two very different things in the same input.
The tricky bit here will be the fact that the sets of edges and nodes will not be statically
sized and thus the deserialiser-logic will need to be able to recreate and destroy objects if
the numbers of default configuration objects and stored state objects do not match.

Our objective is to generate the following style of a JSON string, for use on the Maxima side:
```
{
	"nodes": [
		[1.0,2.0],
		[0.0,2.0],
		[1.0,1.0]
	],
	"edges": [
		[1,2],
		[1,3],
		[2,3]
	]
}
```
The graph will not be directed and we will sort the edge listing for ease of use. We will also use
1-based indexing in the edge listing, and do some padding to keep our JavaScript side indexing of
points 1-based as well.

For user interface we will use a logic where dragging nodes into a particular area will
connect/disconnect them from other nodes within that area. There will also be an area from which
one can drag new nodes out of or into which one can drop extra nodes for destruction.

Here is the whole code, the comments will include numbering and some parts will have extra details
after the code.

```
[[jsxgraph input-ref-ans1="state"]]
/* A perfectly normal board. */
const board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-10, 10, 10, -10]});

/* With some circles representing the UI. */
const source = board.create('circle',[board.create('point',[-8,-8],{visible: false}),1.5], {  strokeColor:'black',frozen:true, fixed:true, method:'pointRadius',hasInnerPoints:true, label:'Add/remove nodes'});
const connector = board.create('circle',[board.create('point',[-4,-8],{visible: false}),1.5], {  strokeColor:'black',frozen:true, fixed:true, method:'pointRadius',hasInnerPoints:true, label:'Connect nodes'});
const disconnector = board.create('circle',[board.create('point',[0,-8],{visible: false}),1.5], {  strokeColor:'black',frozen:true, fixed:true, method:'pointRadius',hasInnerPoints:true, label:'Disconnect nodes'});

/* These represent the graph state. */
let nodes = [board.create('point',[0,0], {visible:false})]; /* hidden point as a padding, so that we start indexing nodes from 1. */
let edges = {}; /* A map from node to map of node to linesegment

/* A convenience function for finding a node by id. */
function get_node_index(id) {
	return nodes.findIndex((node) => node !== null && node.id == id);
}

/* Common UI-logic bits. */
function is_node_within(node, circle) {
	return JXG.Math.Geometry.distance([node.X(),node.Y()],[circle.center.X(),circle.center.Y()]) < circle.Radius();
}

function get_nodes_within(circle) {
	return nodes.filter((node) => node !== null && is_node_within(node, circle));
}


/* 1. The serialiser function. */
const serialiser = () => {
	let R = {'nodes': [], 'edges': []};
	for (let i = 1; i < nodes.length; i++) {
		R.nodes.push([nodes[i].X(), nodes[i].Y()]);
	}
	
	for (const [from, others] of Object.entries(edges)) {
		for (const [to, edge] of Object.entries(others)) {
		  	R.edges.push([get_node_index(from), get_node_index(to)]);
		}
	}
	/* Sort that list, for the Maxima side use. */
	R.edges.sort((a,b) => {
		let c = a[0] - b[0];
		if (c === 0) {
			c = a[1] - b[1];
		}
		return c;
		});

	return JSON.stringify(R);
};

/* Before the deserialiser we really need to have the tools for manipulation of
   the state. The deserialiser may create or delete nodes and edges, and we will
   be doing that elsewhere as well.

   So here are some basic functions.
*/
function create_edge(pointA, pointB) {
	/* This one uses JSXGraph point objects. */
	/* For consistency we always draw the edges from the node earlier in the node list. */
	let lowerIndex = get_node_index(pointA.id) < get_node_index(pointB.id) ? pointA : pointB;
	let higherIndex = lowerIndex === pointA ? pointB : pointA;

	if (!(lowerIndex.id in edges)) {
		edges[lowerIndex.id] = {};
	}
	if (higherIndex.id in (edges[lowerIndex.id])) {
		/* That edge already exists do not recreate. */
		return;
	}
	/* Create the segment and update the books... */
	/* Note to keep UI logic easy we disable dragging by the edge. If you want
	   to use this UI logic you would need an `up`-handler also for edges. */
	let edge = board.create('segment', [lowerIndex, higherIndex], {fixed: true});
	edges[lowerIndex.id][higherIndex.id] = edge;

	/* Ensure update. The edge was added after any points last moved. */
	pointA.trigger(['update']);
}

function delete_edge(pointA, pointB) {
	/* This one uses JSXGraph point objects. */
	/* For consistency we always draw the edges from the node earlier in the node list. */
	let lowerIndex = get_node_index(pointA.id) < get_node_index(pointB.id) ? pointA : pointB;
	let higherIndex = lowerIndex === pointA ? pointB : pointA;	

	if (!(lowerIndex.id in edges)) {
		/* No such edge. */
		return;
	}
	if (higherIndex.id in (edges[lowerIndex.id])) {
		/* Remove the edge from the board. */
		board.removeObject(edges[lowerIndex.id][higherIndex.id]);
		/* And from the books. */
		delete edges[lowerIndex.id][higherIndex.id];
	}

	/* Ensure update. Don't use the ends, they might not exist. */
	nodes[0].trigger(['update']);
}

function delete_node(point) {
	const i = get_node_index(point.id);
	if (i === -1) {
		return; /* Should not happen */
	}
	
	/* Delete edges starting from this node. */
	if (point.id in edges) {
		for (const [to, edge] of Object.entries(edges[point.id])) {
			board.removeObject(edge);  	
		}
		delete edges[point.id];
	}

	/* Delete edges ending to this node. */
	for (const [from, others] of Object.entries(edges)) {
		for (const [to, edge] of Object.entries(others)) {
			if (to === point.id) {
				board.removeObject(edge);
				delete edges[from][to];
			}
		}
	}

	/* Remove from board. */
	board.removeObject(point);
	delete nodes[i];

	/* Ensure update. Note that might have been the last bound node we just removed. */
	nodes[0].trigger(['update']);
}

function create_node(x,y) {
	var node = board.create('point',[x,y],{name:''});
	/* We need to add some UI logic to this node. */
	node.on('up', () => {
		if (is_node_within(node, source)) {
			/* Returned to source, delete it. */
			delete_node(node);
		} else if (is_node_within(node, connector)) {
			/* In the connector area, connect to all others in the area. */
			for (let n of get_nodes_within(connector)) {
				if (n !== node) {
					create_edge(n, node);
				}
			}
		}  else if (is_node_within(node, disconnector)) {
			/* In the disconnector area, disconnect from all others in the area. */
			for (let n of get_nodes_within(disconnector)) {
				if (n !== node) {
					delete_edge(n, node);
				}
			}
		}
	});
	nodes.push(node);
	/* 2. As this is a new node that we need to track in the binding we need to register it. */
	stack_jxg.register_object(state, node, serialiser);

	/* Ensure update. Also trigger the up-handler. */
	node.trigger(['up', 'update']);
}

/* 3. The deserialiser, i.e. the hard part when we can create elements. */
const deserialiser = (value) => {
	let newState = JSON.parse(value);

	/* First sync the nodes. That null padding is the reason for the +1. */
	while (newState.nodes.length + 1 < nodes.length) {
		/* We have extra nodes present in the current state, delete them. */
		delete_node(nodes[nodes.length - 1]);
	}
	for (let i = 0; i < newState.nodes.length; i++) {
		if (i+1 < nodes.length) {
			/* Reposition existing node. */
			nodes[i+1].setPosition(JXG.COORDS_BY_USER, newState.nodes[i]);
		} else {
			/* Create new node. */
			create_node(newState.nodes[i][0], newState.nodes[i][1]);
		}
	}

	/* Then the edges. We basically need to check each existing for deletion
	   and each new for creation. The easy way to get the list of edges in
	   the same format is to get it through the `serialiser`. */
	const newEdges = newState.edges;
	const oldEdges = JSON.parse(serialiser()).edges;
	for (let edge of oldEdges) {
		if (newEdges.indexOf(edge) < 0) {
			delete_edge(nodes[edge[0]], nodes[edge[1]]);
		}
	}
	for (let edge of newEdges) {
		if (oldEdges.indexOf(edge) < 0) {
			create_edge(nodes[edge[0]], nodes[edge[1]]);
		}	
	}
	board.update();
};

/* Then lets add a magical point for creating new nodes. If one drags it outside
   the circle it will create a new node at that place before returning back. */
const magicPoint = board.create('point', [source.center.X(), source.center.Y()], {name: 'Place me to create a new node.', size: 0.2, sizeUnit: 'user'});
magicPoint.on('up', () => {
	if (!is_node_within(magicPoint, source)) {
		create_node(magicPoint.X(), magicPoint.Y());
	}
	/* Always return to the source. */
	magicPoint.setPosition(JXG.COORDS_BY_USER, [source.center.X(), source.center.Y()]);
	board.update();
});

/* 4. In the end lets create the default state, like with all the binding functions
   we must call the function after the default has been set. */
create_node(1.0,2.0);
create_node(0.0,2.0);
create_node(1.0,1.0);
create_edge(nodes[1], nodes[2]);
create_edge(nodes[1], nodes[3]);
create_edge(nodes[2], nodes[3]);

/* 5. Now in our example the `create_node`-function already registered those points for binding.
   so the only object we give it is the padding point that we also use as a handle for triggering
   sync if all other elements have been eliminated. */
stack_jxg.custom_bind(state, serialiser, deserialiser, [nodes[0]]);

/* After that many changes it may make sense to call board update... */
board.update();
[[/jsxgraph]]
```


### Specific comments

At 1. the serialiser is rather simple, if yours is not consider whether the way represent your
state is clear enough. Typically, well constructed state is easy to serialise.

At 2. the key thing to note is that when we create new elements that need to be bound we need
to register them, un-registering is not possible and one should probably not care about that, as
extra registered items, while taking room and time during evaluation of logic are not that
common and will be dropped during page refresh. Typically, users do not do so many actions that
the lag would be noticeable.

At 3. like serialisers deserialisers are simpler if the state is formulated suitably. Here we
can often use the serialiser-function to our advantage when comparing the current state and
incoming state and then do the minimal set of updates. Alternatively, simply throwing
everything away and rebuilding from scratch is a valid tactic, maybe not efficient but this is
a place where premature optimisation is often pointless.

At 4. always do the default state building before binding anything, as the binding function call
will at that moment read the current value from the input and if it find something from there it
will deserialise it on top of the current state.

At 5. Do note the use of that extra point that is hidden. It has two uses, first the padding in
the list to help with mapping indices so that they work better in Maxima. But the more important
bit is the use as a handle to the binding-logic. In this code we often build extra state, i.e.,
those edges after the serialisation has already dealt with the movement of points and we thus
need to trigger an update for these elements that have not been bound. It is also the backup for
a situation where all nodes get removed, in that situation the removal of tha last node would
basically be impossible to update to the input as the node would no longer be there to notify
about its own demise.
