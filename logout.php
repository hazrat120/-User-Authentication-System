<?php
    session_start();
    session_unset();
    session_destroy();
    header("Localtion: index.php");
    exit();
?>