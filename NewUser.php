<?php
include "./Lab6Common/Function_Lib.php";
include_once "./Lab6Common/EntityClass_Lib.php";
include_once "./Lab6Common/DataAccessClass_Lib.php";
include "./Lab6Common/Constants.php";
session_start();

extract($_POST);
$studentIdError = "";
$nameError = "";
$phoneError = "";
$passwordError = "";
$password2Error = "";

if (isset($btnSubmit))
{
    $studentIdError = ValidateStudentId($studentId);
    $nameError = ValidateName($name);
    $phoneError = ValidatePhone($phone);
    $passwordError = ValidatePassword($password);
    $password2Error = ValidatePassword2($password, $password2);
    if ($studentIdError == "" && $nameError == ""&& $phoneError == ""&& $passwordError == "" && $password2Error == "")
    {
        $dao = new DataAccessObject(INI_FILE_PATH);
        
        $st = $dao->getStudentById($studentId);
        if ($st == null)
        {
            $dao->saveStudent($studentId, $name, $phone, $password);

            $student = new Student($studentId, $name, $phone);
            $_SESSION["student"] = $student;
            header("Location: CourseSelection.php"); 
            exit();
        }
        else
        {
            $studentIdError = "A student with this ID has already signed up";
        }
    }      
}
else if (isset($btnClear))
{
    $studentId = "";
    $name = "";
    $phone = "";
    $password = "";
}
include "./Lab6Common/Header.php";
?>
<div class="container">
    <div class="row vertical-margin">
        <div class="col-md-5 text-center"><h2>Sign Up</h2></div>
    </div>
    <div class="row vertical-margin">
        <div class="col-md-6">All fields are required</div>
    </div>
    <br/>
    <form action='NewUser.php' method='post'>
         <div class="row vertical-margin">
            <div class="col-md-6 error"><?php print($loginError) ?></div>
    </div>
        <div class="row vertical-margin">
            <div class="col-md-2"><label class="no-margin no-padding">Student ID:</label></div>
            <div class="col-md-3"><input type="text" name="studentId"  class="form-control" value="<?php print $studentId; ?>"/></div>
            <div class="col-md-7 error"><?php print($studentIdError) ?></div>
        </div>
        <div class="row vertical-margin">
            <div class="col-md-2"><label class="no-margin no-padding">Name:</label></div>
            <div class="col-md-3"><input type="text" name="name"  class="form-control" value="<?php print $name; ?>"/></div>
            <div class="col-md-7 error"><?php print($nameError) ?></div>
        </div>
        <div class="row vertical-margin">
            <div class="col-md-2"><label class="no-margin no-padding">Phone Number:</label><br/>(nnn-nnn-nnnn)</div>
            <div class="col-md-3"><input type="text" name="phone"  class="form-control" value="<?php print $phone; ?>"/></div>
            <div class="col-md-7 error"><?php print($phoneError) ?></div>
        </div>
        <div class="row vertical-margin">
            <div class="col-md-2"><label class="no-margin no-padding">Password:</label></div>
            <div class="col-md-3"><input type="password" name="password"  class="form-control" value="<?php print $password; ?>"/></div>
            <div class="col-md-7 error"><?php print($passwordError) ?></div>
        </div>
         <div class="row vertical-margin">
            <div class="col-md-2"><label class="no-margin no-padding">Password Again:</label></div>
            <div class="col-md-3"><input type="password" name="password2"  class="form-control" value="<?php print $password2; ?>"/></div>
            <div class="col-md-7 error"><?php print($password2Error) ?></div>
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
</div>
    
<?php 
include './Lab6Common/Footer.php';