<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('expression.php');
header('Content-Type: text/plain');
$math = new Expression();

$expr = '"foobar" =~ "/([a-z]+)/"';
$result = $math->evaluate($expr);
if ($math->last_error) {
    echo $math->last_error;
}
echo ($result ? 'true' : 'false') . "\n";
echo $math->evaluate('$1');
