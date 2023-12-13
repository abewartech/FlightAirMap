<?php
require_once('require/class.Connection.php');
require_once('require/class.Language.php');

if (isset($_POST['country']) && $_POST['country'] != "")
{
	$country = filter_input(INPUT_POST,'country',513);
	header('Location: '.$globalURL.'/country/'.$country);
} else {
	if ($globalURL == '') {
		header('Location: /');
	} else {
		header('Location: '.$globalURL);
	}
}
?>