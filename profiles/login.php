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
        exit;
    }

    if (!str_contains($email, "@")) {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        exit;
    }
}

require_once("pdo.php");
if (isset($_POST['email']) && isset($_POST['pass'])) {
    check($_POST['email'], $_POST['pass']);

    if (!isset($_SESSION['error'])) {
        $salt = 'XyZzy12*_';
        $pass = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users where email = :em AND password = :pass');
        $stmt->execute(array(":em" => $_POST['email'], ":pass" => $pass));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            error_log("Login success " . $_POST['email']);
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];
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

    <?php require_once('head_html.php') ?>
    
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
                        <label for="email">Email</label>
                        <input type="text" class="form-control" name="email" id="email"><br />
                    </div>
                    <div class="form-group">
                        <label for="pass">Password</label>
                        <input type="password" class="form-control" name="pass" id="pass"><br />
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary save-btn" value="Log In" onclick="return doValidate();">
                        <input type="submit" class="btn btn-danger" name="cancel" value="Cancel">
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        function doValidate() {
            try {
                pw = document.getElementById('pass').value;
                em = document.getElementById('email').value;
                if (pw == null || pw == "" || em == null || em == "") {
                    alert("Both fields must be filled out");
                    return false;
                }
                if (! em.includes("@")) {
                    alert("Invalid email address");
                    return false;
                }
                return true;
            } catch(e) {
                return false;
            }
            return false;
     }
    </script>

</body>

</html>