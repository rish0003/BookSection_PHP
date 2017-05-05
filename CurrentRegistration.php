<?php
    include_once "./Lab6Common/EntityClass_Lib.php";
    include_once "./Lab6Common/DataAccessClass_Lib.php";
    include "./Lab6Common/Function_Lib.php";
    include "./Lab6Common/Constants.php";
    session_start();

    if (!isset($_SESSION["student"]))
    {
        $_SESSION["rurl"] = "CurrentRegistration.php";
        header("Location: Login.php"); 
        exit();
    }
    extract($_POST);
    $errorMsg='';
    $student = $_SESSION["student"];
    
    $dao = new DataAccessObject(INI_FILE_PATH);
    $semesters = $dao->getSemesters();
    
    if( isset($btnDelete))
    {
        if (sizeof($selections) > 0)
        {
            $registrations = $student->getCurrentRegistrations();
            $courseOffers = array();
            foreach($selections as $selection)
            {
               foreach ($registrations as $courseOffer)
               {
                   if ($courseOffer->getCourse()->getCourseCode() == $selection)
                   {
                       $courseOffers[] = $courseOffer;
                   }
               }
            }
            $student->removeRegistrations($courseOffers);
            $dao->deleteRegistrations($courseOffers, $student);
        }
        else
        {
            $errorMsg = "You need select at least one course!";
        }
    }
    
      include "./Lab6Common/Header.php";
?>
<div class="container">
    <form action='CurrentRegistration.php' method='post' id='current-registration-form'>
    <div class="row vertical-margin">
        <div class="row vertical-margin">
            <div class="col-md-12 text-center"><h2>Current Registrations</h2></div>
        </div>
    </div>
    <div class="row vertical-margin">
        <div class="col-md-12">
            <p>Hello <span style="font-weight: bolder"><?php print $student->getName(); ?></span> (not you? change user <a href="logout.php">here</a>), the followings are your current registrations</p>
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
                    <th>Year</th>
                    <th>Term</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Hours</th>
                    <th>Select</th>
                </tr>
                <?php
                    $noRegistration = true;
                    foreach($semesters as $semester)
                    {
                        $registrations = $student->getRegistrationsForSemester($semester);
                        if (sizeof($registrations) > 0)
                        {
                            $noRegistration = false;
                            foreach($registrations as $courseOffer)
                            {
                                $year = $courseOffer->getSemester()->getYear();
                                $term = $courseOffer->getSemester()->getTerm();
                                $code = $courseOffer->getCourse()->getCourseCode();
                                $title = $courseOffer->getCourse()->getTitle();
                                $hours = $courseOffer->getCourse()->getWeeklyHours();
                                print "<tr><td>$year</td><td>$term</td><td>$code</td><td>$title</td><td>$hours</td>";
                                $selected = (isset($selections) && in_array($code, $selections)) ? "selected": ""; 
                                print "<td><input type='checkbox' name='selections[]' $selected value='$code'></td></tr>";
                            }
                            $totalHours = $student->getTotalWeeklyHoursForSemester($semester);
                            print "<tr><th colspan='4' style='text-align:right'>Total Weekly Hours</th><td colspan='2'>$totalHours</td></tr>";
                        }
                    }
                    if ($noRegistration)
                    {
                        print "<tr><td colspan='6'>You have not registered any course yet</td>";
                    }
                ?>
            </table>
        </div>
    </div>
    <br/>
    <div class="row vertical-margin">
        <div class="col-md-4 col-md-offset-8">
            <input type="submit" value="DeleteSelected" class="btn btn-primary  btn-min-width" name="btnDelete" onclick="return confirm('The selected registrations will be delected!');"/>
            &nbsp; &nbsp; &nbsp;
            <input type="reset" value="Clear" class="btn btn-primary  btn-min-width" name="btnClear"/>
        </div>
    </div>
    </form>
</div>
<br/>
<?php include "./Lab6Common/Footer.php"; ?>
