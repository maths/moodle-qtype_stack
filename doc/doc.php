<?php
/**
*
* Welcome to STACK.  A system for teaching and assessment using a
* computer algebra kernel.
*
* This file is licensed under the GPL License.
*
* A copy of the license is in your STACK distribution called
* license.txt.  If you are missing this file you can obtain
* it from:
* http://www.stack.bham.ac.uk/license.txt
*
* @package stackDoc
* @author Ben Holmes
*/

/*
*	This file serves the contents of a local directory and renders markup to html
*	A file is requested by appending it's path (from the docs root) to doc.php
*	e.g. for /docs/CAS/Maxima.md	request	   /doc.php/CAS/Maxima.md
*/

header('Content-Type: text/html; charset=utf-8');

if (substr($_SERVER['REQUEST_URI'], -7) == 'doc.php') {
	// Don't access doc.php directly, treat it like a directory instead.
	header('Location: ' . $_SERVER['REQUEST_URI'] . '/');
	exit();
}

// TODO get config to work with new setup
require_once 'config.php';
$config = new StackConfig();
global $config;

$docroot = $config->get('docroot');
$webroot = $config->get('webroot');
$docs    = '/lang/' . $config->get('lang') . '/doc'; 					   // docs local location
$webDocs = "$webroot/doc.php";			               					   // doc.php web address
$docLog  = "$docroot/docLog.txt"; //$config->get('tmp') . '/docLog.txt';
$uri	 = str_replace($webDocs, '', urldecode($_SERVER['REQUEST_URI']));  // the uri string
$segs 	 = explode('/', $uri);
$doc	 = $segs[count($segs) - 1];
/*
 * Only really needed back when the extension was appended automatically
$ext  	 = '.md';
*/
$file	 = $docroot . $docs . $uri . ($doc == '' ? 'index' : '');

// the path to the directory for static content to be served by the docs
// access this string in the docs with %CONTENT
$docsContent = "$webroot/docs_content";

require_once $docroot . '/doc/phpMarkdown/markdown.php';
// Frontend was required for html_head, possibly nothing else
require_once $docroot . '/lib/ui/frontend.php'; // TODO find the new version of this
require_once $docroot . '/lib/translator.php';  // TODO find the new version of this

if ($uri == '/') {			// docs root
	$head = get_string('stackDoc_home', 'stack');
} else if ($doc == '') {	// directory
	$head = $segs[count($segs) - 2];
} else {
	$head = $doc;
}

if (!file_exists($file)) {
	header('HTTP/1.0 404 Not Found');
	error_log(date(DATE_ISO8601) . "\tE\t404 - Could not find $file, called from {$_SERVER['REQUEST_URI']} \n", 3, $docLog);
	$head = get_string('stackDoc_404', 'stack');
}

function index($d, $relPath = ''){
	// Write a list describing the directory structure, recursive, discriminates for .md files
	
	$i = '<ul class="dir">';
	if (is_dir($d)) {
		if ($dh = opendir($d)) {
			while (($f = readdir($dh)) !== false) {
				if (substr($f, 0, 1) != '.'){
					$fPath = "$d/$f";
					if(filetype($fPath) == 'dir') {
						$i .= "<li><a href=\"$relPath/$f/\">" . str_replace('_', ' ', $f)
						   .  "</a>" . index($fPath, "$relPath/$f") . '</li>';
					} else {
// Old code			    if (pathinfo($fPath, PATHINFO_EXTENSION) == 'md'
//                          and $f != 'index.md') {
                        if ($f != 'index.md') {
							$fName = pathinfo($fPath, PATHINFO_FILENAME);
							$i .= "<li><a href=\"$relPath/$fName\">" . str_replace('_', ' ', $fName) . "</a></li>";
						}
					}
				}
			}
			closedir($dh);
		}
	}
	$i .= '</ul>';
	return $i;
}

// TODO replace this
echo html_head($head . ' - ' . get_string('stackDoc_docs', 'stack'), "$webroot/", "$webroot/styles.css");
?>

<style type="text/css" media="all">
	@import "<?php echo $webroot; ?>/styles.css";

	ul.dir ul {
		display: block;
	}

	ul.dir ul li {
		/* display: inline;
		margin-right: 20px; */
		line-height: 90%;
	}

	a.ext span, a.email span {
		/* The idea being that if css isn't displayed the link will still show (external link) */
		display: none;
	}

	a.ext {
		<?php // see http://en.wikipedia.org/wiki/File:Icon_External_Link.png ?> 
		background: url(<?php echo $docsContent; ?>/external.png) no-repeat 100% 0;
		padding-right: 10px;
		margin-right:  2px;
	}

	a.email {
		<?php // see http://commons.wikimedia.org/wiki/File:Icon_External_Link_E-Mail.png ?> 
		background: url(<?php echo $docsContent; ?>/email.png) no-repeat 100% 0;
		padding-right: 14px;
		margin-right:  2px;
	}

    hr {
        border: 1px solid #ccc;
    }

</style>

</head>
<body>
<?php

// TODO update these strings
if ($uri == '/') { // I.e. at doc.php/ the docs front page
	$links = array($webroot   			=> get_string('stackDoc_mainMenu', 'stack'),
				   "$webDocs/Site_map" 	=> get_string('stackDoc_siteMap', 'stack'));
} else {
	$links = array($webDocs   			=> get_string('stackDoc_home', 'stack'),
				   './'		  			=> get_string('stackDoc_index', 'stack'),
				   '../'	  			=> get_string('stackDoc_parent', 'stack'),
				   "$webDocs/Site_map" 	=> get_string('stackDoc_siteMap', 'stack'),
				   $webroot   			=> get_string('stackDoc_mainMenu', 'stack'));
}

$linkStrs = array();
foreach ($links as $url => $link) $linkStrs[] = "<a href=\"$url\">$link</a>";
$linkStr  = implode(' | ', $linkStrs);

$header = $footer = "<p>$linkStr</p>";
$header = "<h2><img src=\"$docsContent/logo-sm.png\" style=\"margin-right: 10px;\" />"
            . get_string('stackDoc_docs', 'stack') . "</h2>" . $header;
$header .= "\n<hr />\n";

echo $header;

if(file_exists($file)){

	$handle = fopen($file, 'r');
	$page   = fread($handle, filesize($file));
	fclose($handle);

    $page =  preg_replace('/\\\%CONTENT/', '$$$PARSE_ERROR', $page); // escaped \%CONTENT won't get processed
    $page =  preg_replace('/\%CONTENT/', $docsContent, $page);
    $page =  preg_replace('/\$\$\$PARSE_ERROR/', '%CONTENT', $page);

    if(pathinfo($file, PATHINFO_EXTENSION) == 'md') {
        echo Markdown($page); 		// render it, in this case in Markdown
    } else {
        echo $page;
    }
    echo "\n<hr/>\n";

} else {
	?>

	<h1><?php echo get_string('stackDoc_404', 'stack'); ?></h1>
	<p><?php  echo get_string('stackDoc_404message', 'stack'); ?></p>

	<?php
}

if ($doc == 'Site_map') {
	echo '<h3>' . get_string('stackDoc_directoryStructure', 'stack') . '</h3>' . index($docroot . $docs, $webDocs);	// assumes at a file in /
	// if in any directory root
	// echo '<h3>' . get_string('stackDoc_directoryStructure', 'stack') . '</h3>' . index($docroot . $docs . $uri, $webDocs . rtrim($uri, '/'));
}

echo $footer;

?>
</body>
</html>
