<?php
session_start();
if (isset($_POST['cancel'])) {
    header("Location: index.php");
    exit;
}

function check($email, $get)
{
    if (strlen($email) < 1 || strlen($get) < 1) {
        $_SESSION['error'] = "User name and password are required";
        header("Location: login.php");
        return;
    }

    if (!str_contains($email, "@")) {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        return;
    }
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1'; //Pass php123
$stored_email = "umsi@umich.edu";

if (isset($_POST['email']) && isset($_POST['pass'])) {
    check($_POST['email'], $_POST['pass']);

    if (!isset($_SESSION['error'])) {
        $pass_f = hash('md5', $salt . $_POST['pass']);
        if ($pass_f === $stored_hash && $stored_email === $_POST['email']) {
            error_log("Login success " . $_POST['email']);
            $_SESSION['user'] = $_POST['email'];
            header("Location: index.php");
            exit;
        } else {
            $_SESSION['error'] = "Incorrect password";
            error_log("Login fail " . $_POST['email'] . " $pass_f");
        }
    }
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alejandro Alvarez Botero</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
        integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">


</head>

<body>
    <div class="container mt-3">
        <h1>Please Log In</h1>

        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo ('<p class="alert alert-danger">' . $_SESSION['error'] . "</p>\n");
                        unset($_SESSION['error']);
                    }
                    ?>
                </div>
                <form method="POST" class="form-group">
                    <div class="form-group">
                        <label for="nam">Email</label>
                        <input type="text" class="form-control" name="email" id="nam"><br />
                    </div>
                    <div class="form-group">
                        <label for="id_1723">Password</label>
                        <input type="password" class="form-control" name="pass" id="id_1723"><br />
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary save-btn" value="Log In">
                        <input type="submit" class="btn btn-danger" name="cancel" value="Cancel">
                    </div>

                </form>
            </div>
        </div>
</body>

</html>