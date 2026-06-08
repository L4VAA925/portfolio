<?php
require_once __DIR__ . '/../includes/init.php';

logoutUser();
setFlash('success', 'Uspješno ste se odjavili.');
redirect('/index.php');
