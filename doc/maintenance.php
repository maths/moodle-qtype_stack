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
*	Various maintenance functions for the documentation
*/

/*
 * Needs updating to the new moodle style setup
 *
require_once 'config.php';
$config = new StackConfig();
global $config;

$root 	 = $config->get('docroot');
$docs    = $root . '/lang/' . $config->get('lang') . '/doc'; 			   // docs local location - Note differs to doc.php, full path here
$webDocs = $config->get('weburl') . '/doc.php';			               	   // doc.php web address - Note differs to doc.php, full url here
$host    = $config->get('host');
$docLog  = 'docLog.txt'; //$config->get('tmp') . '/docLog.txt';

session_start();
require 	 $root . '/lib/ui/AdminInterface.php';
require_once $root . '/other/phpMarkdown/markdown.php';
require_once $root . '/lib/ui/frontend.php';
require_once $root . '/lib/reporting/ReportWidgets.php';

if ($_GET['flush'] == true) {
	if(unlink($docLog)) {
		$req = $_SERVER['REQUEST_URI'];
		echo 'Log file flushed.';
	} else {
		echo 'Error finding log file, probably because it has already been removed';
	}
	exit();
}

$w = new ReportWidgets('text');

function report($d){
	// Check various properties of file, make it all tabular	

	global $root;
	global $docs;
	global $webDocs;
	global $host;
	$a = array();

	if (is_dir($d)) {
		if ($dh = opendir($d)) {
			while (($f = readdir($dh)) !== false) {
				if (substr($f, 0, 1) != '.'){
					$fPath = "$d/$f";
					if(filetype($fPath) == 'dir') {
						$a = array_merge($a, report($fPath));
					} else { 
						$fName  = pathinfo($fPath, PATHINFO_FILENAME);
						$fExt   = pathinfo($fPath, PATHINFO_EXTENSION);
						$fSize  = filesize($fPath);
						$rel    = str_replace(array($docs, 'index.md', '.md'), '', $fPath); 
						$relDir = str_replace($docs, '', $d);

											$a[] = array($fPath, 'F', 'Found file ' . "$rel");

						if ($fSize >= 7000)	$a[] = array($fPath, 'W', "Large file ($fSize bytes)");
						if ($fExt != 'md')  $a[] = array($fPath, 'W', "Not a markdown file ($fExt)");

						// Let's do some link checking, step one: scrape the links off the document's web page
						$links = strip_tags(file_get_contents($webDocs . $rel), "<a>");
						preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $links, $found);

						//found[0] will have the full a tags, found[1] contains their href properties
						
						// Step two, visit these links and check for 404s
						foreach($found[1] as $i => $link) {
							if (strpos($link, 'mailto:') !== 0
							    and strpos($link, 'docMaintenance.php') === false
								and ($_GET['ext'] or strpos($link, 'http') !== 0)) {
									// Don't check mailto:, this file (ARGH!)
									// Also if ?ext not true then better not be an external link
								
								if (strpos($link, 'http') !== 0) {
									// If a local link, do some preparation

									if (strpos($link, '/') === 0) {
										$link = $host . $link; // Not a relative link
									} else {
										$link = $webDocs . rtrim($relDir, '/') . '/' . $link;
									}

									$segs = explode('/', $link); // it looks like get_headers isn't evaluating these so lets do it manually

									while(($pos = array_search('.', $segs)) !== false) {
										unset($segs[$pos]);
									}

									while(($pos = array_search('..', $segs)) !== false) {
										unset($segs[$pos], $segs[$pos - 1]);
									}

									$link = implode('/', $segs);

									// finally it looks like #--- are getting parsed in the request, let's ommit them
									if (strpos($link, '#') !== false) $link = substr($link, 0, strpos($link, '#'));
								
								}

								$hs = get_headers($link);

								if (strpos($hs[0], '404') !== false) {
									$a[] = array($fPath, 'E', 'Error 404 [' . $found[0][$i] . '] appears to be a dead link');
								}
							}
						}
					}
				}
			}
			closedir($dh);
		}
	}

	return $a;
}

echo html_head('STACK Document Maintenance Script', $config->get('webroot') . '/', $config->get('webroot') . '/styles.css');
?>

<style type="text/css" media="all">
	@import "<?php echo $config->get('webroot'); ?>/styles.css";
</style>

</head>
<body>
<h2>STACK Documentation Maintenance</h2>

<p><a href=".">STACK menu</a></p>
<p>Key: <i>F - Found file, W - Warning, E - Error</i></p>
<p>This script crawls the entire documentation and checks for dead links and other issues.
	Currently the script is crawling locally for speed, to check external links as well
	<a href="docMaintenance.php?ext=1">click here</a></p>
<pre><?php 

	$heading = array(array(
		'File',
		'Type',
		'Description'
	));

	echo $w->head("Scanning $docs");
	echo $w->table(array_merge($heading, report($docs)));

?></pre>

<h2>Docs log</h2>
<p>Displaying the log file for the documentation which can be found at <a href="<?php echo $docLog; ?>"><code>docLog.txt</code></a>. <a href="?flush=1">Click here to flush the log file</a></p>

<?php
if(file_exists($docLog)) {
	echo '<pre>' . file_get_contents($docLog) . '</pre>';
} else {
	echo '<p>Log file is empty</p>';
}
?>

</body>
</html>
