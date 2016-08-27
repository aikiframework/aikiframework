<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'src/Handlebars/Autoloader.php';
Handlebars\Autoloader::register();

/*use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Handlebars\Helpers;
*/
use Handlebars\Handlebars;


header('Content-Type: text/plain');

$engine = new Handlebars();
$engine->addHelper('escape', function ($template, $context, $arg, $source) {
    return $source;
});

function get($context, $var) {
    $stack = array();
    while (true) {
        try {
            $value = $context->get($var, true);
            //restore the context
            while (end($stack)) {
                $context->push(array_pop($stack));
            }
            return $value;
        } catch(Exception $e) {
            // save context for restoration
            array_push($stack, $context->pop());
            if (!$context->last()) {
                throw new Exception("Can't find variable $var");
            }
        }
    }
}

$engine->addHelper('sql', function ($template, $context, $arg, $source) {
    $mysqli = new mysqli("localhost", "test", "test", "test");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $parsedArgs = $template->parseArguments($arg);
    $sql = preg_replace_callback('/\$([^\s]+)/', function($matches) use ($context) {
        return get($context, $matches[1]);
    }, (string)$parsedArgs[0]);
    $res = $mysqli->query($sql);
    $buffer = '';
    while ($row = $res->fetch_assoc()) {
        $context->push($row);
        $buffer .= $template->render($context);
        $context->pop();
        $template->rewind();
    }
    return $buffer;
});
require('../expression.php/expression.php');

$engine->addHelper('if', function ($template, $context, $arg, $source) {
    $math = new Expression();
    $parsedArgs = $template->parseArguments($arg);
    $expr = preg_replace_callback('/\$([^\s]+)/', function($matches) use ($context) {
        return get($context, $matches[1]);
    }, (string)$parsedArgs[0]);
    $tmp = $math->evaluate($expr);
    if ($math->last_error) {
        throw new Exception($math->last_error);
    }
    if ($tmp) {
        $template->setStopToken('else');
        $buffer = $template->render($context);
        $template->setStopToken(false);
        $template->discard($context);
    } else {
        $template->setStopToken('else');
        $template->discard($context);
        $template->setStopToken(false);
        $buffer = $template->render($context);
    }
    
    return $buffer;
});
session_start();
$data = array(
    'foo' => array(
        '1', '2', '3', '4'
    ),
    'id' => 1,
    'GET' => $_GET,
    'POST' => $_POST,
    'COOKIE' => $_COOKIE,
    'SESSION' => $_SESSION
);
/*
$array = array();
$array['square'] = function($a) {
    return $a*$a;
};
$func_reflection = new ReflectionFunction($array['square']);
$num_of_params = $func_reflection->getNumberOfParameters();
echo $num_of_params . "\n";
*/
$template = '{{#sql SELECT * FROM test}}
    {{#sql SELECT content FROM test WHERE id = $id}}
        {{content}}
        {{#if $id == 2}}
            id = 2
        {{/if}}
    {{/sql}}
{{/sql}}
{{GET.foo}}
{{#each foo}}
{{this}}
{{/each}}
{{#escape}}xxx{{foo}}xxx{{/escape}}
xxx
{{#each foo}}{{this}}{{#unless @last}}, {{/unless}}{{/each}}';
$template = preg_replace("/\{\{#(sql|if)\s+([^\"'](.*?)[^\"'])\s*\}\}/", '{{#$1 "$2"}}', $template);
echo $engine->render($template, $data);
