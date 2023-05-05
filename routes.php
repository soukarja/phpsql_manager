<?php

require_once __DIR__.'/config/router.php';


get('/', '/index.php');
any('/404','/404.php');