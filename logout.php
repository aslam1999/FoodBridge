<?php
session_start();
session_destroy();
header('Location: logout-confirmation.html');
exit;
?>
