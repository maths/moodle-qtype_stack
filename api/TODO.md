# API "todo" list.

## Documentation.

0. Get doc.php working
1. Authoring YAML docs.
2. API calls.
   a. Use question (student view)
   b. CJS: Validate question (Returns JSON objects: Errors n Maand warning)
   c. Run an individual test.
   d. Run question tests.  Does this need to be asyncronous?
3. Example questions in YAML format.
4. Max: Document how to run Maxima in a Sandbox.

## Editing

1. Code mirror support. (Matti -> Max)

## Question tests.

1. _done_ Export via the Moodle question bank.
2. _done_ Import into YAML.
3. Run all tests.  Refactor questiontestrun to share some of the checking code....

## Unit tests of the YAML code.

## Plots/ Images

1. Plots works through the API (Check?)
2. Images.

## STACK import/export

1. in export.php trim off trailing zeros from floats.  sprintf in PHP?
2. $question -> $fromform conversion (I know....) and then expose the validation method via the API.
3. Images in YAML format. (See XML export....)
   CJS send image example to Max.
   Add images at the end and reference them.
   Export and import to Moodle.
4. Markdown support for the import/export in Moodle.   

# TODO for V1.2

1. Add in support for authoring answer tests as functions:  'AlgEquiv(ans1,x^2)'
2. Full qformat_yaml importer for Moodle. (CJS to implement)
