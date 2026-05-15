const {metadata} = require('../../amd/src/metadata/metadata.js');

test('JSON stringify', () => {
  const value = 27;
  const key = "isPartOf";
  expect(metadata.reviver(key, value)).toEqual({value: 27});
});
