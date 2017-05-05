<?php

class Course {
    private $courseCode;
    private $title;
    private $weeklyHours;
    
    public function __construct($courseCode, $title, $weeklyHours)
    {
        $this->courseCode = $courseCode;
        $this->title = $title;
        $this->weeklyHours = $weeklyHours;
    }
    public function getCourseCode()
    {
        return $this->courseCode;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getWeeklyHours()
    {
        return $this->weeklyHours;
    }
    
    public function __toString()
    {
        return  $this->courseCode." ".$this->title;
    }
}

class Semester 
{
    private $semesterCode;
    private $year;
    private $term;
    
    public function __construct($semesterCode, $year, $term)
    {
        $this->semesterCode = $semesterCode;
        $this->year = $year;
        $this->term = $term;
    }
    
    public function getSemesterCode()
    {
        return $this->semesterCode;
    }
    
    public function getYear()
    {
        return $this->year;
    }
    
    public function getTerm()
    {
        return $this->term;
    }
    
    public function __toString()
    {
        return   $this->year." ".$this->term;
    }
    
    public static function compareSemester($semester1, $semester2)
    {
        if ($semester1 == $semester2)
        {
            return 0;
        }
        if ($semester1->year < $semester2->year)
        {
            return -1;
        }
        elseif ($semester1->year > $semester2->year)
        {
            return 1;
        }
        else
        {
            if ($semester1->term == "Fall")
            {
                return -1;
            }
            elseif ($semester1->term == "Winter" && $semester2->term == "Summer")
            {
                return -1;
            }
            else
            {
                return 1;
            }
        }
    }
}

class CourseOffer
{
    private $course;
    private $semester;
    
    public function __construct($course, $semester)
    {
        $this->course = $course;
        $this->semester = $semester;
    }
    
    public function getCourse()
    {
        return $this->course;
    }
    public function getSemester()
    {
        return $this->semester;
    }
}


class Student {
    private $studentId;
    private $name;
    private $phone;
    private $registrations;
    
    public function __construct($studentId, $name, $phone)
    {
        $this->studentId = $studentId;
        $this->name = $name;
        $this->phone = $phone;
        
        $this->registrations = array();
    }
    
    public function getName()
    {
        return $this->name;
    }
    public function getStudentId()
    {
        return $this->studentId;
    }
    public function getPhone()
    {
        return $this->phone;
    }
    
    public function addRegistration($courseOffers)
    {
        foreach ($courseOffers as $courseOffer)
        {
            $this->registrations[] = $courseOffer;
        }
    }
    
    public function removeRegistrations($courseOffers)
    {
        foreach($courseOffers as $courseOffer)
        {
            for($i=0; $i < sizeof($this->registrations); $i++)
            {
                if ($this->registrations[$i] == $courseOffer )
                {
                    unset($this->registrations[$i]);
                }
            }
        }
        $this->registrations = array_values($this->registrations);
    }
    
    public function getCurrentRegistrations()
    {
        return $this->registrations;
    }
    
    public function setCurrentRegistrations($registrations)
    {
        $this->registrations = $registrations;
    }
    
    public function getRegistrationsForSemester($semester)
    {
        $courseOffers = array();
        foreach($this->registrations as $courseOffer)
        {
            if ($courseOffer->getSemester() == $semester )
            {
                $courseOffers[] = $courseOffer;
            }
        }
        return $courseOffers;
    }
    
    public function getTotalWeeklyHoursForSemester($semester)
    {
        $hours = 0;
        foreach($this->getRegistrationsForSemester($semester) as $courseOffer)
        {
            $hours += $courseOffer->getCourse()->getWeeklyHours();
        }
        return $hours;
    }
}

