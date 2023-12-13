<?php
require_once('require/class.Connection.php');
require_once('require/class.Spotter.php');
require_once('require/class.Language.php');
$Spotter = new Spotter();
if ($_GET['flightaware_id'] != "")
{
	$flightaware_id = filter_input(INPUT_GET,'flightaware_id',513);
	$spotter_id = $Spotter->getSpotterIDBasedOnFlightAwareID($flightaware_id);
	if ($spotter_id != "")
	{
		header('Location: '.$globalURL.'/flightid/'.$spotter_id);
	} else {
		if ($globalURL == '') {
			header('Location: /');
		} else {
			header('Location: '.$globalURL);
		}
	}
}
?>