<?php

define('SEARCH_BLOCKED_REGEXP', 'AmazonBasics 360-Piece Clear Plastic Cutlery Set');

# Setting time and memory limits
ini_set('max_execution_time',0);
ini_set('memory_limit', '128M');

define('AC_DIR', dirname(__FILE__));

# Including classes
require_once( AC_DIR . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'RollingCurl.class.php');
require_once( AC_DIR . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'AngryCurl.class.php');

# Initializing AngryCurl instance with callback function named 'callback_function'
$AC = new AngryCurl('callback_function');

# Initializing so called 'web-console mode' with direct cosnole-like output
$AC->init_console();

# Importing proxy and useragent lists, setting regexp, proxy type and target url for proxy check
# You may import proxy from an array as simple as $AC->load_proxy_list($proxy array);
$AC->load_proxy_list(
    AC_DIR . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . 'proxy_list.txt',
    # optional: number of threads
    200,
    # optional: proxy type
    'http',
    # optional: target url to check
    'http://google.com',
    # optional: target regexp to check
    'title>G[o]{2}gle'
);
$AC->load_useragent_list( AC_DIR . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . 'useragent_list.txt');

# Basic request usage (for extended - see demo folder)
for ($i=1;$i<=2;$i++) {
    $AC->get('http://google.com/');
}

# Starting with number of threads = 200
echo $AC->execute(200);

# You may pring debug information, if console_mode is NOT on ( $AC->init_console(); )
//AngryCurl::print_debug(); 

# Destroying
unset($AC);

AngryCurl::clearOK();

# Callback function example
function callback_function($response, $info, $request)
{
    if($info['http_code']!==200)
    {
        AngryCurl::add_debug_msg(
            "->\t" .
            $request->options[CURLOPT_PROXY] .
            "\tFAILED\t" .
            $info['http_code'] .
            "\t" .
            $info['total_time'] .
            "\t" .
            $request->url
        );
    }else if ($info['http_code']==200 && preg_match('/' . SEARCH_BLOCKED_REGEXP .'/', $response, $matches) == 1)
    {
        AngryCurl::addOK();

        AngryCurl::add_debug_msg(
            "->\t" .
            $request->options[CURLOPT_PROXY] .
            "\tOK+\t" .
            $info['http_code'] .
            "\t" .
            $info['total_time'] .
            "\t" .
            $request->url
        );

    }

    return;
}

AngryCurl::add_debug_msg('Total OK for transaction = ' . AngryCurl::getOK());