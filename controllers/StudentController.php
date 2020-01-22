<?php

require_once "models/Student.php";

/**
 * Class StudentController
 */
class StudentController
{
    private $studentObject = null;

    /**
     * StudentController constructor.
     */
    public function __construct()
    {
        $studentId = empty($_GET['student']) ? 0 : $_GET['student'];
        $this->studentObject = new Student($studentId);
    }

    /**
     * @return false|mixed|string|null
     */
    public function getResult()
    {
        return is_object($this->studentObject) ? $this->studentObject->studentResult() : null;
    }
}