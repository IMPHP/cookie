<?php

if (!defined('\im\IMPHP_BASE')) {
    echo "Could not find imphp/base"; exit(1);

} else if (!defined('\im\IMPHP_HTTP')) {
    echo "Could not find imphp/http"; exit(1);
}

require "static.php";

$loader = \im\ImClassLoader::load();
$loader->addBasePath(__DIR__);
