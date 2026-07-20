To build the stackascii.bundle.js, etc., you need to be in this folder with write permissions. Then:

```
npm ci   # Installs node modules
npm run build   # Bundles and minifies 
```

New filters need to be placed in filters/ and explicitly added to stackascii.js and filter.block.php
New extractors need to be placed in extractors/ and explicitly added to stackascii.js and extractor.block.php
New markdown transforms need to be placed in markdownittransforms/ and explicitly added to filters/markdown.js and filter.block.php