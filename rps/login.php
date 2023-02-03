<?php
if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1'; //Pass php123
$failure = false;

if (isset($_POST['who']) && isset($_POST['pass'])) {
    if (strlen($_POST['who']) < 1 || strlen($_POST['pass']) < 1) {
        $failure = "User name and password are required";
    } else {
        $md5 = hash('md5', $salt . $_POST['pass']);

        if ($md5 == $stored_hash) {
            header("Location: game.php?name=".urlencode($_POST['who']));
            exit;
        } else {
            $failure = "Incorrect password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alejandro Alvarez Botero e1b1546c</title>
    <?php require_once "bootstrap.php"; ?>
</head>

<body>
    <div class="container">
        <h1>Please Login</h1>

        <?php
        if ($failure) {
            echo('<p class="alert alert-danger">'.htmlentities($failure)."</p>\n");
        }
        ?>

        <form method="post">
            <label for="who">User Name</label>
            <input type="text" name="who" id="who"><br>
            <label for="pass">Password</label>
            <input type="password" name="pass" id="pass"><br>
            <input type="submit" value="Log In">
            <input type="submit" value="Cancel" name="cancel">
        </form>
        <p>
            For a password hint, view source and find a password hint
            in the HTML comments.
            <!-- Hint: The password is the four character sound a cat makes (all lower case) followed by 123. -->
        </p>
</body>

</html>