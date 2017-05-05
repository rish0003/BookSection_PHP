<?php
include_once 'EntityClass_Lib.php';

class DataAccessObject
{
    private $pdo;
    
    function __construct($iniFile)
    {
        $dbConnection = parse_ini_file($iniFile);
        extract($dbConnection);
        $this->pdo = new PDO($dsn, $user, $password);
    }
    function __destruct()
    {
        $this->pdo = null;
    }
     
    public function getSemesters()
    {
        $semesters = array();
        $sql = "SELECT SemesterCode, Term, Year FROM Semester";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        foreach ($stmt as $row)
        {
            $semester = new Semester($row['SemesterCode'], intval($row['Year']), $row['Term'] );
            $semesters[] = $semester;
        }
        usort($semesters, "Semester::compareSemester" );
        return $semesters;
    }
    
    public function getCourseBySemeter($semeter)
    {
        $courses = array();
        $sql = "SELECT Course.CourseCode Code, Title,  WeeklyHours "
                . "FROM Course INNER JOIN CourseOffer ON Course.CourseCode = CourseOffer.CourseCode "
                . "WHERE CourseOffer.SemesterCode = :semesterCode";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['semesterCode' => $semeter->getSemesterCode()]);
        
        foreach ($stmt as $row)
        {
            $course = new Course($row['Code'], $row['Title'], $row['WeeklyHours']);
            $courses[] = $course;
        }
        return $courses;
    }

     public function getStudentById($studentId)
    {
        $student = null;
        $sql = "SELECT StudentId, Name, Phone FROM Student WHERE StudentId = :studentId";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['studentId' => $studentId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row)
        {
            $student = new Student($row['StudentId'], $row['Name'], $row['Phone'] );
            $student->setCurrentRegistrations($this->getStudentRegistrations($student));
        }
        return $student;
    }
    public function getStudentByIdAndPassword($studentId, $password)
    {
        $student = null;
        $sql = "SELECT StudentId, Name, Phone FROM Student WHERE StudentId = :studentId AND Password = :password";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['studentId' => $studentId, 'password' => $password]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row)
        {
            $student = new Student($row['StudentId'], $row['Name'], $row['Phone'] );
            $student->setCurrentRegistrations($this->getStudentRegistrations($student));
        }
        return $student;
    }
    
    private function getStudentRegistrations($student)
    {
        $registrations = array();
        
        $sql = "SELECT C.CourseCode CCode, C.Title Title, C.WeeklyHours WeeklyHours, S.SemesterCode SCode, S.Year Year, S.Term Term "
                . "FROM Course C INNER JOIN Registration R ON C.CourseCode = R.CourseCode "
                . "INNER JOIN Semester S ON R.SemesterCode = S.SemesterCode "
                . "WHERE R.StudentId = :studentID";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['studentID' => $student->getStudentId()]);
        
        foreach ($stmt as $row)
        {
            $course = new Course($row['CCode'], $row['Title'], $row['WeeklyHours']);
            $semester = new Semester($row['SCode'], $row['Year'], $row['Term']);
            $courseOffer = new CourseOffer($course, $semester);        
            $registrations[] = $courseOffer;
        }
        return $registrations;
    }
            
    public function saveStudent($studentId, $name, $phone, $password)
    {
        $sql = "INSERT INTO Student VALUES( :studentId, :name, :phone, :password)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['studentId' => $studentId, 'name' => $name, 'phone' => $phone, 'password' => $password]);
    }
    
    public function saveRegistrations($courseOffers, $student)
    {
        $sql = "INSERT INTO Registration VALUES( :studentId, :courseCode, :semesterCode)";
        $stmt = $this->pdo->prepare($sql);
        foreach ($courseOffers as $courseOffer) 
        {
            $stmt->execute(['studentId' => $student->getStudentId(), 'courseCode' => $courseOffer->getCourse()->getCourseCode(), 'semesterCode' => $courseOffer->getSemester()->getSemesterCode()]);
        }
    
    }
    
    public function deleteRegistrations($courseOffers, $student)
    {
        $sql = "DELETE FROM Registration WHERE StudentId=:studentId, CourseCOde=:courseCode, SemesterCode=:semesterCode";
        $stmt = $this->pdo->prepare($sql);
        foreach ($courseOffers as $courseOffer) 
        {
            $stmt->execute(['studentId' => $student->getStudentId(), 'courseCode' => $courseOffer->getCourse()->getCourseCode(), 'semesterCode' => $courseOffer->getSemester()->getSemesterCode()]);
        }
    }
   
}

