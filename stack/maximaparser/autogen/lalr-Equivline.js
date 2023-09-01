/*
 * This script generates the tables necessary for the LALR
 * parser as well as various debug details.
 *
 * This will hardcode precedence of operators into those tables.
 * 
 * This will execute the step:
 *  'bottom-up-grammar.json' => 'lalr.json', 'numbered-grammar.json'
 *
 * Additional steps to convert that to actual code are needed.
 * Basically, execute scripts like 'php-generator.js' to get 
 * the result.
 *
 *
 * @copyright  2023 Matti Harjula, Aalto University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */


const firstFollow = require('first-follow');
const fs = require('fs');


const prefixOps = {
"-" : 134,
"+" : 100,
"''" : 0,
"'" : 0,
"not" : 70,
"?? " : 0,
"? " : 0,
"?" : 0,
"UNARY_RECIP": 100,
"nounnot": 70,
"nounsub": 100
};

const suffixOps = {
"!" : 160,
"!!" : 160
};

const infixOps = {
"#" : [80, 80],
"#pm#" : [100, 100],
"STACKpmOPT": [100,100],
"**" : [140, 139],
"^^" : [140, 139],
"^" : [140, 139],
"*" : [120,120],
"/" : [120,120],
"-" : [100,134],
"+" : [100,100],
"+-" : [100,100],
"and" : [65,0],
"or" : [60,0],
"nounand" : [65,0],
"nounadd" : [100,100],
"nounmul" : [120,120],
"nounor" : [60,0],
"nouneq" : [150,150],
"nounpow": [140,139],
"noundiv": [122,123],
"::=" : [180,20],
":=" : [180,20],
"::" : [180,20],
":" : [180,20],
"<=" : [80,80],
"<" : [80,80],
">=" : [80,80],
">" : [80,80],
"=" : [80,80],
"~" : [0,0],
"blankmult" : [0,0],
};


fs.readFile('bottom-up-grammar-Equivline.json', 'utf8', function(err, data) {

	const rules = JSON.parse(data);

	const { firstSets, followSets, predictSets } = firstFollow(rules);

	// Write them out. Skipping error handling not an issue for this tool.
	fs.writeFile('first.json', JSON.stringify(firstSets, null, 2), (err) => {});
	fs.writeFile('follow.json', JSON.stringify(followSets, null, 2), (err) => {});
	fs.writeFile('predict.json', JSON.stringify(predictSets, null, 2), (err) => {});

	// Number all rules. Also find non-terminals.
	var nonterminals = {};
	for (var i = 0; i < rules.length; i++) {
		rules[i]['num'] = i;
		nonterminals[rules[i]['left']] = true;
	}

	// Closures.
	var closures = [];
	var transitions = {};

	// First closure. Lets store the rule number and the offset of the dot in it.
	// Also mark the next token for simpler debug readability.
	var first = [[0, 0, rules[0]['right'][0]]];
	// The non terminals already handled.
	var covered = [];
	var grew = true;
	while (grew) {
		grew = false;
		var firstNt = [];
		for (var line of first) {
			if (line[1] < rules[line[0]]['right'].length) {
				var tmp = rules[line[0]]['right'][line[1]];
				if (nonterminals.hasOwnProperty(tmp) && covered.indexOf(tmp) < 0 && firstNt.indexOf(tmp) < 0) {
					firstNt[firstNt.length] = tmp;
				}
			}
		}
		for (var nt of firstNt) {
			covered[covered.length] = nt;
			for (var rule of rules) {
				if (rule['left'] === nt) {
					first[first.length] = [rule['num'], 0, rule['right'][0]];
					grew = true;
				}
			}
		}
	}
	closures[0] = first;

	function divTargets(closure) {
		var r = {};
		for (var line of closure) {
			if (line[2] === null || line[1] === rules[line[0]]['right'].length) {
				// Skip this is something reducing here.
			} else {
				if (!r.hasOwnProperty(line[2])) {
					r[line[2]] = [];
				}
				r[line[2]][r[line[2]].length] = [line[0], line[1] + 1, rules[line[0]]['right'][line[1] + 1]];
			}
		}
		return r;
	}
	var nextSplitState = 0;
	while (closures.length > nextSplitState) {
		var splits = divTargets(closures[nextSplitState]);
		transitions[nextSplitState] = {};
		for (var split in splits) {
			// Check if we already have that state.
			var target = -1;
			for (var i = 0; i < closures.length; i++) {
				var same = closures[i].length >= splits[split].length;
				for (var j = 0; j < splits[split].length; j++) {
					if (JSON.stringify(closures[i][j]) !== JSON.stringify(splits[split][j])) {
						same = false;
						break;
					}
				}
				if (same) {
					target = i;
					break;
				}
			}

			if (target === -1) {
				// Populate the new closure/state.
				target = closures.length;
				covered = [];
				var closure = splits[split];
				grew = true;
				while (grew) {
					grew = false;
					var firstNt = [];
					for (var line of closure) {
						if (line[1] < rules[line[0]]['right'].length) {
							var tmp = rules[line[0]]['right'][line[1]];
							if (nonterminals.hasOwnProperty(tmp) && covered.indexOf(tmp) < 0 && firstNt.indexOf(tmp) < 0) {
								firstNt[firstNt.length] = tmp;
							}
						}
					}
					for (var nt of firstNt) {
						covered[covered.length] = nt;
						for (var rule of rules) {
							if (rule['left'] === nt) {
								closure[closure.length] = [rule['num'], 0, rule['right'][0]];
								grew = true;
							}
						}
					}
				}
				closures[closures.length] = closure;
			}
			transitions[nextSplitState][split] = target;
		}
		
		nextSplitState++;
	}

	// Then construct the table. We will write it so that the actual rules do not need
	// to be included. i.e. the reduce cell values will include the length of the rule in them.
	var table = [];
	var gotot = {};
	for (var i = 0; i < closures.length; i++) {
		table[i] = {};
		// The reduces, i.e. all the patterns with the dot at the end and the correct follow.
		for (var line of closures[i]) {
			if (line[1] === rules[line[0]]['right'].length || line[2] === null) {
				var follow = followSets[rules[line[0]]['left']];
				for (var t of follow) {
					if (!table[i].hasOwnProperty(t)) {
						table[i][t] = [];
					}
					if (rules[line[0]]['right'][0] === null) {
						table[i][t][table[i][t].length] = [1, line[0], 0, rules[line[0]]['left']];
					} else {
						table[i][t][table[i][t].length] = [1, line[0], rules[line[0]]['right'].length, rules[line[0]]['left']];
					}
				}
			}
		}
		// Then all shifts.
		for (var lah in transitions[i]) {
			if (transitions[i].hasOwnProperty(lah)) {
				if (nonterminals.hasOwnProperty(lah)) {
					if (!gotot.hasOwnProperty(i)) {
						gotot[i] = {};
					}
					gotot[i][lah] = transitions[i][lah];
				} else {
					if (!table[i].hasOwnProperty(lah)) {
						table[i][lah] = [];
					}
					table[i][lah][table[i][lah].length] = [0, transitions[i][lah]];
				}
			}
		}
	}

	// Check conflicts.
	for (var i = 0; i < table.length; i++) {
		for (var t in table[i]) {
			if (table[i][t].length > 1) {
				//console.log('State ' + i + ' lookahead ' + t + ' option count ' + table[i][t].length);
				if (table[i][t].length === 2) {
					var opcase = false;
					var red = null;
					if (table[i][t][0][0] === 0) {
						red = table[i][t][1];
					} else {
						red = table[i][t][0];
					}
					if (red[3] === 'OpInfix' || red[3] === 'OpSuffix' || red[3] === 'OpPrefix') {
						opcase = true;
					}

					if (opcase) {
						const rop = t;
						var lop = false;
						if (red[3] === 'OpInfix' || red[3] === 'OpSuffix') {
							lop = rules[red[1]]['right'][1];
						} else {
							lop = rules[red[1]]['right'][0]
						}

						var rb = -1000;
						if (lop in prefixOps) {
							rb = prefixOps[lop];
						} else if (lop in infixOps) {
							rb = infixOps[lop][1];
						}
						var lb = -1000;
						if (rop in suffixOps) {
							lb = prefixOps[rop];
						} else if (rop in infixOps) {
							lb = infixOps[rop][0];
						}
						if (rb === -1000 || lb === -1000) {
							opcase = false;
						} else {
							if (lb >= rb) {
								if (table[i][t][0][0] === 0) {
									table[i][t] = table[i][t][0];
								} else {
									table[i][t] = table[i][t][1];
								}	
							} else {
								table[i][t] = red;
							}
							//console.log(' fixed was shift-reduce, with operator precendence between ' + lop + ' and ' + rop);	
						}
					} 
					if (!opcase) {
						// If shift-reduce always shift.
						if (table[i][t][0][0] !== table[i][t][1][0]) {
							if (table[i][t][0][0] === 0) {
								table[i][t] = table[i][t][0];
							} else {
								table[i][t] = table[i][t][1];
							}
							//console.log(' fixed was shift-reduce.');
						} else {
							console.log('State ' + i + ' lookahead ' + t + ' option count ' + table[i][t].length);
							console.log(' NOT DIRECTLY FIXABLE.');
						}
					}
				}
			} else if (table[i][t].length === 1) {
				// Unwrap to save space.
				table[i][t] = table[i][t][0];
			}
		}
	}


	fs.writeFile('closures.json', JSON.stringify(Object.assign({},closures), null, 2), (err) => {});
	fs.writeFile('transitions.json', JSON.stringify(Object.assign({},transitions), null, 2), (err) => {});
	fs.writeFile('table.json', JSON.stringify(Object.assign({},table), null, 2), (err) => {});
	fs.writeFile('goto.json', JSON.stringify(gotot, null, 2), (err) => {});
	fs.writeFile('numbered-grammar-Equivline.json', JSON.stringify(rules, null, 2), (err) => {});

	// Construct a dictionary for the reduce targets and replace all usage in both table rules and goto keys.
	// "Start" being different. The aim of this is to cut the JSON size down significantly.
	// Whether the actual reverse mapping is of any use latter is debatable, but maybe one
	// actually wants to modify error messaging based on the original names, of failed reductions
	// or some such, although reductions failing is a really odd thing.
	let targets = {};
	let countt = 0;
	for (var i = 0; i < table.length; i++) {
		for (let j in table[i]) {
			if (table[i][j][0] === 1 && table[i][j][3] !== 'Start') {
				if (table[i][j][3] in targets) {
					table[i][j][3] = targets[table[i][j][3]];
				} else {
					targets[table[i][j][3]] = countt;
					countt++;
					table[i][j][3] = targets[table[i][j][3]];
				}
			}
		}
	}
	for (let i in gotot) {
		let translated = {};
		for (let j in gotot[i]) {
			if (j === 'Start') {
				translated[j] = gotot[i][j];
			} else {
				translated[targets[j]] = gotot[i][j];
			}
		}
		gotot[i] = translated;
	}

	fs.writeFile('lalr-Equivline.json', JSON.stringify({'table': table, 'goto': gotot, 'dict': targets}), (err) => {});

	// Generate the transition diagram as a DOT.
	var dot = 'digraph {\n';
	dot += '/* Note this is large and will take around 20 minutes to compile, unless one uses straight edges or nslimit. */\n';
	dot += 'node   [ shape = rect ]\n'
	// The nodes.
	for (var i = 0; i < closures.length; i++) {
		dot += ' node_' + i + ' [ label = "' + i + '\\r';
		for (var line of closures[i]) {
			dot += '' + line[0] + ': ' + rules[line[0]]['left'] + ' &rarr; ';
			if (rules[line[0]]['right'][0] === null) {
				dot += ' &#8226; ';
			} else {
				for (var j = 0; j < rules[line[0]]['right'].length; j++) {
					if (j === line[1]) {
						dot += ' &#8226; ';
					}
					dot += rules[line[0]]['right'][j] + ' ';
				}
			}
			dot += '\\l';
		}
		dot += '" ]\n';
	}
	// The edges.
	for (var i in transitions) {
		for (var trg in transitions[i]) {
			dot += ' node_' + i + ' -> node_' + transitions[i][trg];
			dot += ' [ label = "' + trg + '"]\n';
		}
	}

	dot += '\n}';
	fs.writeFile('graph.dot', dot, (err) => {});

	// Write closures to a readable format file:
	var clr = '';
	for (var i = 0; i < closures.length; i++) {
		clr += 'Closure ' + i + '\n';
		for (var line of closures[i]) {
			clr += '' + line[0] + '\t: ' + rules[line[0]]['left'] + ' -> ';
			if (rules[line[0]]['right'][0] === null) {
				clr += '. ';
			} else {
				var j = 0;
				for (j = 0; j < rules[line[0]]['right'].length; j++) {
					if (j === line[1]) {
						clr += '. ';
					}
					if (nonterminals[rules[line[0]]['right'][j]]) {
						clr += "" + rules[line[0]]['right'][j] + " ";
					} else {
						clr += "'" + rules[line[0]]['right'][j] + "' ";
					}
				}
				if (j === line[1]) {
					clr += '. ';
				}
			}
			clr += '\n';
		}
		clr += '\n\n';
	}
	fs.writeFile('closures.txt', clr, (err) => {});
});