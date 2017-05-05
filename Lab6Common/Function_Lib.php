<?php
function ValidateName($name) {
    $name = trim($name);
    if( empty($name) )
    {
        return "Name can not be blank!";
    }
    else
    {
        return "";
    }
}
function ValidateStudentId($id) {
    $id = trim($id);
    if( empty($id) )
    {
        return "Student ID can not be blank!";
    }
    else
    {
        return "";
    }
}

function ValidatePassword($password) {
    $password = trim($password);
    
    $upperCaseRegex = "/[A-Z]/";
    $lowerCaseRegex = "/[a-z]/";
    $numericRegex = "/[0-9]/";

    if ($password == "")
    {
        return "Password can not be blank";
    }
    elseif (strlen($password) < 6)
    {
        return "Need at least 6 characters long";
    }
    elseif(!preg_match($upperCaseRegex, $password))
    {
        return "Need at least one upper case letter";
    }
    elseif(!preg_match($lowerCaseRegex, $password))
    {
        return "Need at least one lower case letter";
    }
    elseif(!preg_match($numericRegex, $password))
    {
        return "Need at least one numeric character";
    }
    else
    {
            return "";
    }
}

function ValidatePassword2($password, $password2) {
    $password = trim($password);
    $password2 = trim($password2);
    if( empty($password2) )
    {
        return "Password 2 can not be blank!";
    }
    elseif($password2 !== $password)
    {
        return "Password 2 must be the same as password 1!";
    }
    else
    {
        return "";
    }
}
function ValidatePhone($phone) {
    $phoneRegex ='/[2-9][0-9]{2}-[2-9][0-9]{2}-[0-9]{4}/';
    $code= trim($phone);
    if( empty($code) )
    {
        return "Phone is required";
    }
    else if (!preg_match($phoneRegex, $code, $matches))
    {            
        return "Incorrect phone number";
    }
    else
    {
        return "";
    }
}