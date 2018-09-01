<?php

defined("STDOUT") or define("STDOUT", fopen("php://stdout", "w"));
define("DAEMON_DELAY", 20);

cli_set_process_title("php master.php --fb-daemon");

$workers = [
  "group_daemond",
  "user_daemond"
];

$closures = [];


unset($_SERVER["argv"]);

foreach ($workers as $key => $value) {
  reg($closures, $action = function() use ($value) {
    cli_set_process_title($value." --daemonize --fb-daemon");
    $descriptorspec = array(
       0 => array("pipe", "r"),
       1 => array("pipe", "w"),
       2 => array("pipe", STDOUT, "a")
    );

    $cwd = __DIR__;

    $process = proc_open("php ".__DIR__."/{$value} --daemonize --fb-daemon", $descriptorspec, $pipes, $cwd, $_SERVER);

    if (is_resource($process)) {
        
        fclose($pipes[0]);
        while (! feof($pipes[1])) {
          print fread($pipes[1], 1024);    
        }

        fclose($pipes[1]);

        proc_close($process);
    }
  });
}

foreach ($closures as $v) {

  // if {$pid is not set || the current process is parent}
  if (!isset($pid) || $pid !== 0) {
    $pid = pcntl_fork();
  }

  // if {the current process is child}
  if (!$pid) {
    while(true) {
      $v();
      sleep(DAEMON_DELAY);
    }
  }

}


while (true) {
  // Minimize CPU usage by sleep.
  sleep(5000);
}

/**
 * @param array     $closures
 * @param \Closure  $action
 * @return void
 */
function reg(array &$closures, Closure $action): void
{
  $closures[] = $action;
}
