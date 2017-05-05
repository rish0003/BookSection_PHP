<?php
include "./Lab6Common/Function_Lib.php";
include_once "./Lab6Common/EntityClass_Lib.php";
include_once "./Lab6Common/DataAccessClass_Lib.php";
include "./Lab6Common/Constants.php";
session_start();
	
session_destroy();

header("Location: Index.php"); 