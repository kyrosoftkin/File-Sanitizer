<?php
namespace Sanitize;

include "methods.php";

use Sanitize\FileMethods as File;

$file = new File\Methods("files/");
$file->moveFiles();
?>
