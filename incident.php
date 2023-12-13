<?php
require_once(dirname(__FILE__).'/require/settings.php');
$date = filter_input(INPUT_POST,'date',513);
if ($date == '') $date = date('Y-m-d');
header('Location: '.$globalURL.'/incident/'.$date);
?>