<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('redaksi/login.php');
csrf_check();
logout();
redirect('redaksi/login.php');