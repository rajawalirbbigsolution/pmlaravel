<?php
 interface Swift_Transport_SmtpAgent { public function getBuffer(); public function executeCommand($command, $codes = array(), &$failures = null); } 