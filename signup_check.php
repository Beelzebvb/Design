<?php

include 'includes/functions.php';


var_dump($_POST);
var_dump($_FILES);

if (!isset($_POST['submit'])) {
    header("location: signup.php?code=accessdenied");
    exit;
}

if (!aresetAndNotEmpty(
    [
        $_POST['firstname'],
        $_POST['lastname'],
        $_POST['birthdate'],
        $_POST['email'],
        $_POST['password'],
        $_POST['password_repeat'],
        $_POST['username'],
        $_POST['language'],
        $_POST['country'],
        //! Pas obligatoire : $_POST['bio'],
        $_POST['sport']
    ]
)) {
    header('location: signup.php?code=emptyinput');
    exit;
}

if (!createUser(
    $_POST['firstname'],
    $_POST['lastname'],
    $_POST['username'],
    $_POST['email'],
    18,
    $_POST['password'],
    1
)) {
    header('location: signup.php?code=couldnotcreateuser');
    exit;
}

header('location: signup.php?code=usercreated');
exit;
