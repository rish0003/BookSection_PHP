<?php
include "./Lab6Common/Function_Lib.php";
include_once "./Lab6Common/EntityClass_Lib.php";
include_once "./Lab6Common/DataAccessClass_Lib.php";
include "./Lab6Common/Constants.php";
session_start();

if (isset($_SESSION["student"]))
{
   $loginMessage = "You have already logged in!";
}
else
{
    $loginMessage="";
}

extract($_POST);

$studentIdError = "";
$loginError = "";
if (isset($btnSubmit))
{
    $studentIdError = ValidateStudentId($studentId);
    
    if ($studentIdError == "")
    {
        $dao = new DataAccessObject(INI_FILE_PATH);
        $student = $dao->getStudentByIdAndPassword($studentId, $password);
        
        if ($student != null)
        {
            $_SESSION["student"] = $student;
            if(isset($_SESSION["rurl"]) )
            {
                header("Location: $_SESSION[rurl]"); 
            }
            else {
                header("Location: CourseSelection.php"); 
            }
            exit();
        }
        else
        {
            $loginError = "Incorrect student ID and/or Password!";
        }
    }        
}
else if (isset($btnClear))
{
    $studentId = "";
    $password = "";
}
include "./Lab6Common/Header.php";
?>
<div class="container">
    <div class="row vertical-margin">
        <div class="col-md-5 text-center"><h2>Log In</h2></div>
    </div>
    <?php 
        if ($loginMessage == "")
        {
    ?>    
            <div class="row vertical-margin">
                    <div class="col-md-6">You need to <a href="NewUser.php">sign up</a> if you a new user</div>
            </div>
            <br/>
            <form action='Login.php' method='post'>
                 <div class="row vertical-margin">
                    <div class="col-md-6 error"><?php print($loginError) ?></div>
            </div>
                <div class="row vertical-margin">
                    <div class="col-md-2"><label class="no-margin no-padding">Student ID:</label></div>
                    <div class="col-md-3"><input type="text" name="studentId"  class="form-control" value="<?php print $studentId; ?>"/></div>
                    <div class="col-md-7 error"><?php print($studentIdError) ?></div>
                </div>
                <div class="row vertical-margin">
                    <div class="col-md-2"><label class="no-margin no-padding">Password:</label></div>
                    <div class="col-md-3"><input type="password" name="password"  class="form-control" value="<?php print $password; ?>"/></div>
                    <div class="col-md-7 error"><?php print($passwordError) ?></div>
                </div>
                <br/>
                <div class="row vertical-margin">
                    <div class="col-md-4 col-md-offset-2">
                        <input type="submit" value="Submit" class="btn btn-primary btn-min-width" name="btnSubmit"/>
                        &nbsp;&nbsp; 
                        <input type="submit" value="Clear" class="btn btn-primary btn-min-width" name="btnClear"/>
                    </div>
                </div>
            </form>
    <?php 
        } 
        else 
        { 
    ?>
            <div class="row vertical-margin">
                <div class="col-md-6"><span style="font-size: large"><?php print $loginMessage;?></span> </div>
            </div>
    <?php 
        } 
    ?>
</div>
<?php include "./Lab6Common/Footer.php";


