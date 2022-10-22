<?php

namespace AppCatalog;

require __DIR__."../config/www-dev.php";

$app = new \MLFW\Application($site_config);
\MLFW\Root::$app=$app;
$app->main();