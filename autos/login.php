<?php
if (isset($_POST['cancel'])) {
    header("Location: index.php");
    exit;
}

function check($who, $get) {
    if (strlen($who) < 1 || strlen($get) < 1) {
        return "User name and password are required";
    }

    if (! str_contains($who, "@")) {
        return " Email must have an at-sign (@)";
    }
}

$error = false;
$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1'; //Pass php123

if (isset($_POST['who']) && isset($_POST['pass'])) {
    $error = check($_POST['who'], $_POST['pass']);

    if (!$error) {
        $pass_f = hash('md5', $salt . $_POST['pass']);
        if ($pass_f === $stored_hash) {
            error_log("Login success ".$_POST['who']);
            header("Location: autos.php?name=".urlencode($_POST['who']));
            exit;
        } else {
            $error = " Incorrect password";
            error_log("Login fail ".$_POST['who']." $pass_f");
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
    <title>Alejandro Alvarez Botero</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
        integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
        integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

</head>

<body>
    <div class="container">
        <h1>Please Log In</h1>
        <?php
        if ($error) {
            echo ('<p class="alert alert-danger">' . $error . "</p>\n");
        }
        ?>
        <form method="POST">
            <label for="nam">Email</label>
            <input type="text" name="who" id="nam"><br />
            <label for="id_1723">Password</label>
            <input type="password" name="pass" id="id_1723"><br />
            <input type="submit" value="Log In">
            <input type="submit" name="cancel" value="Cancel">
        </form>
        <p>
            For a password hint, view source and find a password hint
            in the HTML comments.
            <!-- Hint: The password is the three character name of the 
programming language used in this class (all lower case) 
followed by 123. -->
        </p>
</body>

</html>