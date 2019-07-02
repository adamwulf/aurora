<?
	$done_style = "margin-bottom: 4px; border-bottom: 1px solid black; width: 400px;";
	$start_style = "margin-top: 4px; border-top: 1px solid black; width: 400px;";



	include "../include.avalanche.fullApp.php";
	$data = new module_bootstrap_data($_REQUEST, "form input");

	$bootstrap = $avalanche->getModule("bootstrap");
	$os = $avalanche->getModule("os");
	$strongcal = $avalanche->getModule("strongcal");

	echo "loading avalanche: " . $avalanche->getQueryCount() . "<br>";

	$module = new module_bootstrap_strongcal_header($avalanche, new Document());
	$bootstrap = $avalanche->getModule("bootstrap");
	$runner = $bootstrap->newDefaultRunner();
	$runner->add($module);
	$aurora_header = $runner->run($data);
	$aurora_header = $aurora_header->data();

	echo "strongcal header: " . $avalanche->getQueryCount() . "<br>";

	exit;

?>
