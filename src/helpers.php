<?php

if (! function_exists("icelog")) {
	function icelog($format, ...$params): void
	{
		fprintf(STDOUT, sprintf("[%s] %s\n", date("Y-m-d H:i:s"), sprintf($format, ...$params)));
	}
}