# Moodle App compatibility

The following files have been added to make STACK compatible with the Moodle App:

## Files

### db/mobile.php
Sets up a Moodle webservice to serve STACK questions to the app. Points to the mobile CSS file. Update the version here if you update the CSS.

### classes/output/monile.php
Points the Moodle webservice to the ionic template and javascript files. (Worth changing the JS to the unminified version while developing.)

### mobile.css
Mostly a straight copy of styles.css with a few extra classes. Also moves pix references to within STACK due to relative URL issues.

### mobile/stack.html
Ionic template to build the question. This is very basic as we are essentially just filtering the original in-browser HTML. Because STACK questions are so complicated and can have elements in any order we can't do anything clever here.

### mobile/stack.js, mobile/stack.min.js
The complicated bit. The Moodle App receives a JSON object with the original HTML of the question, metadata and script code. This is then inspected by stack.js. Plain radio, dropdown and checkbox elements are replaced by native elements. Code for iFrames is extracted, updated and then used to create the iFrames. Inputs are initialised.

The code includes large chunks of duplicate code from input.js and stackjsvle.js with updates due to requiring different element selectors for the native elements. Because the JS is served to the App as part of the webservice, even a way to split it up into separate files is currently obscure, let alone modularising it so we can use the same code in STACK and the App.

`uglifyjs stack.js > stack.min.js`

### app.feature, jsx_app_test.feature
Behat tests for all input types and a basic JSX test.

## Running locally

- Clone the app repository.  
`npm install`  
`npm start`
- Close the browser window that opens automatically and instead start chromium:
`chromium --allow-file-access-from-files --disable-web-security --disable-site-isolation-trials --allow-running-insecure-content --no-referrers --unlimited-storage  --ignore-certificate-errors --disable-infobars --user-data-dir=~/.chromium-dev-data`
- Go to `localhost:8100`

## Known issues
- GeoGebra not yet tested due to local setup issues.
