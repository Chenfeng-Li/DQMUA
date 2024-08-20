<?php 
session_start();
session_destroy();
header('Location: ninecomments.php');