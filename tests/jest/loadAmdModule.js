const fs = require('fs');

function loadAmdModule(modulePath, dependencyMap) {
    const source = fs.readFileSync(modulePath, 'utf8');
    let exported;

    const define = (dependencies, factory) => {
        const resolved = dependencies.map((dependency) => {
            if (!(dependency in dependencyMap)) {
                throw new Error(`Missing AMD dependency mock: ${dependency}`);
            }
            return dependencyMap[dependency];
        });
        exported = factory(...resolved);
    };

    const wrapped = new Function('define', source);
    wrapped(define);

    return exported;
}

module.exports = {
    loadAmdModule,
};
