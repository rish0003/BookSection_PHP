<?php
    include_once "./Lab6Common/EntityClass_Lib.php";
    include_once "./Lab6Common/DataAccessClass_Lib.php";
    include "./Lab6Common/Function_Lib.php";
    include "./Lab6Common/Constants.php";
    session_start();

    if (!isset($_SESSION["student"]))
    {
        $_SESSION["rurl"] = "CourseSelection.php";
        header("Location: Login.php"); 
        exit();
    }
    extract($_POST);
    $errorMsg='';
    $student = $_SESSION["student"];

    $dao = new DataAccessObject(INI_FILE_PATH);
    $semesters = $dao->getSemesters();
    
    if (isset($btnSubmit))
    {
        if (sizeof($selections) > 0)
        {
            $courses = $_SESSION['courses'];
            $selectedSemester = $_SESSION["selectedSemester"];
            $courseOffers = array();
            $hours = 0;
            foreach($selections as $selection)
            {
               foreach ($courses as $course)
               {
                   if ($course->getCourseCode() == $selection)
                   {
                       $hours += $course->getWeeklyHours();
                       $courseOffer = new CourseOffer($course, $selectedSemester);
                       $courseOffers[] = $courseOffer;
                   }
               }
            }
            if ($hours + $student->getTotalWeeklyHoursForSemester($selectedSemester) <= MAX_WEEKLY_HOURS)
            {
                $student->addRegistration($courseOffers);
                $dao->saveRegistrations($courseOffers, $student);
            }
            else
            {
                $errorMsg = "Your selection exceed the max weekly hours";
            }
           	
        }
        else
        {
                $errorMsg = "You need select at least one course!";
        }
    }
    
    if ($semesterChangedFlag)
    {
        foreach ($semesters as $semester)
        {
            if ($semester->getSemesterCode() == $sltSemesterCode)
            {
                $selectedSemester = $semester;
            }
        }
        
        $_SESSION["selectedSemester"] = $selectedSemester;
        $courses = $dao->getCourseBySemeter($selectedSemester);
        $_SESSION['courses'] = $courses;
    }
    else {
       if (isset($_SESSION["selectedSemester"]))
       {
           $selectedSemester = $_SESSION["selectedSemester"];
           $courses = $_SESSION['courses'];
       }
       else
       {
           $selectedSemester = $semesters[0];
           $_SESSION["selectedSemester"] = $selectedSemester;
           $courses = $dao->getCourseBySemeter($selectedSemester);
           $_SESSION['courses'] = $courses;
       }
    }
    
    include "./Lab6Common/Header.php";
?>
<div class="container">
    <form action='CourseSelection.php' method='post' id='course-selection-form'>
    <div class="row vertical-margin">
        <div class="col-md-12 text-center"><h2>Course Selection</h2></div>
    </div>
    <div class="row vertical-margin">
        <div class="col-md-12">
            <p>Welcome <span style="font-weight: bolder"><?php print $student->getName(); ?></span>! (not you? change user <a href="logout.php">here</a>) 
            <p>You have registered <span style="font-weight: bolder"><?php print $student->getTotalWeeklyHoursForSemester($selectedSemester); ?></span> hours for the selected semester.</p>
            <p>You can register <span style="font-weight: bolder"><?php print MAX_WEEKLY_HOURS - $student->getTotalWeeklyHoursForSemester($selectedSemester); ?></span> more hours of course(s) for the semester
            <p>Please note that the courses you have registered will not be displayed in the list</p>
        </div>
    </div>
    <div class="row vertical-margin">
        <div class="col-md-3 text-center col-md-offset-9">
            <select name='sltSemesterCode' class="form-control" onchange="onSemesterChanged()">
<?php            
            foreach($semesters as $semester) 
            {
                $semesterCode = $semester->getSemesterCode();
                print "<option value='$semesterCode' ". ($semester == $selectedSemester ? "selected" : ""). ">$semester</option>";           
            } 
?>
            </select> 
            <input type="hidden" id="semesterChangedFlag" name="semesterChangedFlag" value="" />
        </div>
    </div>
    <div class="row vertical-margin">
        <div class="col-md-12">
        <span class='error'><?php print $errorMsg; ?></span>
        </div>
    </div>
    <div class="row vertical-margin">  
        <div class="col-md-12">
            <table class="table">
                <tr>
                    <th>Code</th>			
                    <th>Course Title</th>
                    <th>Hours</th>
                    <th>Select</th>
                </tr>
            <?php
                $currentRegistrations = $student->getCurrentRegistrations();
                foreach($courses as $course)
                {
                    $alreadRegistered = false;
                    foreach($currentRegistrations as $registeredCourseOffer)
                    {
                        if ($registeredCourseOffer->getCourse()->getCourseCode() === $course->getCourseCode() )
                        {
                            $alreadRegistered = true;
                            break;
                        }
                    }
                    if (!$alreadRegistered)
                    {
                        $code = $course->getCourseCode();
                        $title = $course->getTitle();
                        $hours = $course->getWeeklyHours();
                        print "<tr><td>$code</td><td>$title</td><td>$hours</td>";
                        $selected = (isset($selections) && in_array($code, $selections)) ? "selected": ""; 
                        print "<td><input type='checkbox' name='selections[]' $selected value='$code'></td></tr>";
                    }
                }
            ?>
            </table>
        </div>
    </div>
    <div class="row vertical-margin">
        <div class="col-md-4 col-md-offset-8">
            <input type="submit" value="Submit" class="btn btn-primary  btn-min-width" name="btnSubmit"/>
            &nbsp; &nbsp; &nbsp;
            <input type="submit" value="Clear" class="btn btn-primary  btn-min-width" name="btnClear"/>
        </div>
    </div>
</form>
</div>
<?php include "./Lab6Common/Footer.php"; ?>
