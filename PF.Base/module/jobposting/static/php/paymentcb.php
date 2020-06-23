<?php

$location = urldecode($_GET['location']);
header('Location: '.$location);
