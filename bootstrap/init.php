<?php

if (! defined("INIT")) {
	define("INIT", 1);

	require __DIR__."/../../config/init.php";
	require __DIR__."/../../vendor/autoload.php";

	if (! defined("BASEPATH")) {
		print "BASEPATH is not defined!\n";
		exit(1);
	}

	/**
	 * @param string $class
	 * @return void
	 */
	function iceTeaInternalAutoloader(string $class): void
	{
		$class = str_replace("\\", "/", $class);
		if (substr($class, 0, 3) === "Phx") {
			require BASEPATH."/src/phx/".substr($class, 4).".phx";
		} else {
			require BASEPATH."/src/classes/".$class.".php";
		}
	}

	spl_autoload_register("iceTeaInternalAutoloader");

	require BASEPATH."/src/helpers.php";
}
