<?php

include 'boot.php';
session_destroy();
header('Location: index.php');
exit;