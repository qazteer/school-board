<?php

require_once "config/config.php";
require_once "db/database.php";

/**
 * Class Student
 */
class Student
{
    private $config;
    private $mysqli;
    private $studentId = 0;

    /**
     * Student constructor.
     * @param int $studentId
     */
    public function __construct($studentId)
    {
        $this->config = new Config();
        $this->mysqli = DataBase::getDB()->getMysqli();
        $this->studentId = $studentId;
    }

    /**
     * @return array|bool|null
     */
    private function getStudent()
    {
        $query = "SELECT s.id, s.name, b.name AS board_name FROM `student` AS s 
                    LEFT JOIN `board` AS b ON s.board_id = b.id WHERE s.id = $this->studentId";
        $result = $this->mysqli->query($query);
        if(!$result) return false;

        return $result->fetch_assoc();
    }

    /**
     * @return array|bool
     */
    private function getGrade()
    {
        $query = "SELECT grade FROM `grade` WHERE student_id = $this->studentId";
        $result = $this->mysqli->query($query);
        if(!$result) return false;

        $data = [];
        $i=0;
        while($row=$result->fetch_assoc()){
            $data[$i] = $row['grade'];
            $i++;
        }

        return $data;
    }

    /**
     * @return string
     */
    private function calculateCSM()
    {
        $grade = $this->getGrade();
        $average_of_grade = $this->helperAverage($grade);

        return ($average_of_grade >= 7 ) ? "Pass" : "Fail";
    }

    /**
     * @param $grade
     * @return float|int
     */
    private function helperAverage($grade)
    {
        return array_sum($grade) / count($grade);;
    }

    /**
     * @return string
     */
    private function calculateCSMB()
    {
        $grade = $this->getGrade();
        return (max($grade) > 8 ) ? "Pass" : "Fail";
    }

    /**
     * @return false|mixed|string|null
     */
    public function studentResult()
    {
        $result = null;
        $student = $this->getStudent();
        switch ($student["board_name"]) {
            case "CSM":
                $student["guards"] = $this->getGrade();
                $student["average"] = $this->helperAverage($this->getGrade());
                $student["result"] = $this->calculateCSM();
                $result = json_encode($student);
                break;
            case "CSMB":
                $student["guards"] = $this->getGrade();
                $student["average"] = $this->helperAverage($this->getGrade());
                $student["result"] = $this->calculateCSMB();
                $result = $this->helperArrayToXml($student);
                break;
            default: exit();
        }
        return $result;
    }

    /**
     * @param array $array
     * @param null $rootElement
     * @param null $xml
     * @return mixed
     */
    private function helperArrayToXml($array, $rootElement = null, $xml = null)
    {
        $_xml = $xml;

        // If there is no Root Element then insert root
        if ($_xml === null) {
            $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
        }

        // Visit all key value pair
        foreach ($array as $k => $v) {
            // If there is nested array then
            if (is_array($v)) {
                // Call function for nested array
                $this->helperArrayToXml($v, $k, $_xml->addChild($k));
            } else {
                // Simply add child element.
                $_xml->addChild($k, $v);
            }
        }

        return $_xml->asXML();
    }
}
