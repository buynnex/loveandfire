<?php
require_once dirname(__FILE__) . '/core/bootstrap.php';

if (is_file(dirname(__FILE__) . '/access.php')) {
    redirect('access.php');
}

if (auth_current_user()) {
    redirect('dashboard.php');
}

redirect('login.php');
