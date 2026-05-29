<?php
/**
 * admin/logout.php
 * Proses logout admin
 */
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/app.php';

session_unset();
session_destroy();
redirect(BASE_URL . '/admin/login.php');