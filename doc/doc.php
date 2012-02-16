<?php
// This file is part of Stack - http://stack.bham.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Documentation system as a static wiki of markdown.
 *
 * @package stackDoc
 * @author Ben Holmes
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../config.php');

require_once(dirname(__FILE__) . '/../locallib.php');
require_once(dirname(__FILE__) . '/phpMarkdown/markdown.php');// TODO: remove all this? Is this a core part of Moodle?
require_once(dirname(__FILE__) . '/../stack/stringutil.class.php');

/*
 *  This file serves the contents of a local directory and renders markup to html
 *  A file is requested by appending it's path (from the doc root) to doc.php
 *  e.g. for /CAS/Maxima.md    request    /doc.php/CAS/Maxima.md
 *  Language selection is done automatically.
 */

require_login();

if (substr($_SERVER['REQUEST_URI'], -7) == 'doc.php') {
    // Don't access doc.php directly, treat it like a directory instead.
    header('Location: ' . $_SERVER['REQUEST_URI'] . '/');
    exit();
}

$docroot = $CFG->dirroot;
$webroot = $CFG->wwwroot;
$docs    = '/question/type/stack/doc/' . $CFG->lang;    // docs local location
$webdocs = $webroot.'/question/type/stack/doc/doc.php';  // doc.php web address

$logpath = new STACK_StringUtil($CFG->dataroot . '/stack/logs');
$doclog  = $logpath->convertSlashPaths();

$uri     = get_file_argument();  // the uri string
$segs    = explode('/', $uri);
$doc     = $segs[count($segs) - 1];
$file    = $docroot . $docs . $uri . ($doc == '' ? 'index' : '') . '.md';

$webpix  = $CFG->wwwroot.'/question/type/stack/pix/logo-sm.png';

// the URL to the directory for static content to be served by the docs
// access this string in the docs with %CONTENT
$docscontent = $webroot.'/question/type/stack/doc/content';


if (!file_exists($file)) {
    header('HTTP/1.0 404 Not Found');
    //error_log(date(DATE_ISO8601) . "\tE\t404 - Could not find $file, called from {$_SERVER['REQUEST_URI']} \n", 3, $docLog);
    //$head =  stack_string('stackDoc_404', 'stack');
}

$context = context_system::instance();
require_capability('moodle/site:config', $context);
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/doc/doc.php');
$PAGE->set_title(stack_string('stackDoc_docs'));

echo $OUTPUT->header();
$pagetitle = "<img src=\"{$webpix}\" style=\"margin-right: 10px;\" />".stack_string('stackDoc_docs');
echo $OUTPUT->heading($pagetitle);

function index($d, $relPath = ''){
    // Write a list describing the directory structure, recursive, discriminates for .md files

    $i = '<ul class="dir">';
    if (is_dir($d)) {
        if ($dh = opendir($d)) {
            while (($f = readdir($dh)) !== false) {
                if (substr($f, 0, 1) != '.'){
                    $fPath = "$d/$f";
                        if (filetype($fPath) == 'dir') {
                            $i .= "<li><a href=\"$relPath/$f/\">" . str_replace('_', ' ', $f)
                               .  "</a>" . index($fPath, "$relPath/$f") . '</li>';
                            } else {
                                if ($f != 'index.md' && '.bak'!= substr($f,-4)) {
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

?>

<style type="text/css" media="all">

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

<?php

if (file_exists($file)){

    $handle = fopen($file, 'r');
    $page   = fread($handle, filesize($file));
    fclose($handle);

    $page =  preg_replace('/\\\%CONTENT/', '$$$PARSE_ERROR', $page); // escaped \%CONTENT won't get processed
    $page =  preg_replace('/\%CONTENT/', $docscontent, $page);
    $page =  preg_replace('/\$\$\$PARSE_ERROR/', '%CONTENT', $page);

    if (pathinfo($file, PATHINFO_EXTENSION) == 'md') {
        echo Markdown($page); 		// render it, in this case in Markdown
    } else {
        echo $page;
    }
    echo "\n<hr/>\n";

} else {
    ?>

   <h1><?php echo  stack_string('stackDoc_404', 'stack'); ?></h1>
   <p><?php  echo  stack_string('stackDoc_404message', 'stack'); ?></p>

   <?php
}

if ($uri == '/') {
    // I.e. at doc.php/ the docs front page
    $links = array($webdocs.'/Site_map' => stack_string('stackDoc_siteMap'));
} else {
    $links = array($webdocs   =>  stack_string('stackDoc_home'),
                   './'       =>   stack_string('stackDoc_index'),
                   '../'      =>  stack_string('stackDoc_parent'),
                   $webdocs.'/Site_map' =>  stack_string('stackDoc_siteMap'));
}

$linkStrs = array();
foreach ($links as $url => $link) {
    $linkstrs[] = "<a href=\"$url\">$link</a>";
}
$linkstr  = implode(' | ', $linkstrs);

echo $linkstr;

if ($doc == 'Site_map') {
	echo '<h3>' .  stack_string('stackDoc_directoryStructure', 'stack') . '</h3>' . index($docroot . $docs, $webdocs);	// assumes at a file in /
	// if in any directory root
	// echo '<h3>' .  stack_string('stackDoc_directoryStructure', 'stack') . '</h3>' . index($docroot . $docs . $uri, $webDocs . rtrim($uri, '/'));
}

echo $OUTPUT->footer();
