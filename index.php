<?php

require_once "controllers/StudentController.php";

$student = new StudentController();
print_r($student->getResult());
