<?php 

require_once('stringutil.class.php');
require_once('options.class.php');
require_once('cas/castext.class.php');

if ($_POST && isset($_POST['cas'])) {
    	
    $string	 =	$_POST['cas'];
}

$start =	microtime(true);

if (null != $string) {
    $ct           =	new STACK_CAS_CasText($string);
    $displayText  = $ct->Get_display_castext();
    $errs         = $ct->Get_errors();
}
echo $displayText;
echo $errs;

?>


<form action="test.php" method="POST">
	<!--<textarea cols="80" rows="5" name="cas">@ diff(x^5, x) @ \[ @diff(x^3, x)@ \]</textarea><br /><br /> -->
	<textarea cols="80" rows="5" name="cas"><?php echo $string;?></textarea><br /><br />
	<input type="submit" value="chat" />
</form>


