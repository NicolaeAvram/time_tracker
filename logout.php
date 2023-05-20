<?php 

session_start();
require_once("utils/logging.inc.php");
event_logger();

session_unset();
session_destroy();

header('location:login.php');