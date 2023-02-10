<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("ACCESS DENIED");
}

if (isset($_POST['cancel'])) {
    header("location: index.php");
    exit;
}

function check($f, $l, $e, $h, $s)
{
    if(strlen($f) < 1 || strlen($l) < 1 || strlen($e) < 1 || strlen($h) < 1 || strlen($s) < 1) {
        $_SESSION['error'] = "All fields are required";
        return;
    }

    if (!str_contains($e, "@")) {
        $_SESSION['error'] = "Email address must contain @";
        return;
    }
}

require("pdo.php");
if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
    check($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['headline'], $_POST['summary']);

    if (!isset($_SESSION['error'])) {
        $stmt = $pdo->prepare('INSERT INTO profile(user_id, first_name, last_name, email, headline, summary) VALUES (:ui, :fn, :ln, :e, :h, :s)');
        $stmt->execute(
            array(
                ':ui' => $_SESSION['user_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':e' => $_POST['email'],
                ':h' => $_POST['headline'],
                ':s' => $_POST['summary']
            )
        );
        $_SESSION['success'] = "Record Added";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['fn'] = $_POST['first_name'];
        $_SESSION['ln'] = $_POST['last_name'];
        $_SESSION['e'] = $_POST['email'];
        $_SESSION['h'] = $_POST['headline'];
        $_SESSION['s'] = $_POST['summary'];
        header("location: add.php");
        return;
    }
}

$fn = $_SESSION['fn'] ?? "";
$ln = $_SESSION['ln'] ?? "";
$e = $_SESSION['e'] ?? "";
$h = $_SESSION['h'] ?? "";
$s = $_SESSION['s'] ?? "";
unset($_SESSION['fn']);
unset($_SESSION['ln']);
unset($_SESSION['e']);
unset($_SESSION['h']);
unset($_SESSION['s']);
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
        <h1>Tracking Autos for
            <?= htmlentities($_SESSION['name']) ?>
        </h1>
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
                <form method="post" class="form-group">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control"
                            value="<?= htmlentities($fn) ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control"
                            value="<?= htmlentities($ln) ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" class="form-control"
                            value="<?= htmlentities($e) ?>">
                    </div>
                    <div class="form-group">
                        <label for="headline">Headline</label>
                        <input type="text" name="headline" id="headline" class="form-control"
                            value="<?= htmlentities($h) ?>">
                    </div>
                    <div class="form-group">
                        <label for="summary">Summary</label>
                        <textarea type="text" name="summary" id="summary" class="form-control" rows="5"><?=htmlentities($s)?></textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary save-btn" value="Add" onclick="return doValidate();">
                        <input type="submit" class="btn btn-danger" name="cancel" value="Cancel">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function doValidate() {
            try {
                fn = document.getElementById('first_name').value;
                ln = document.getElementById('last_name').value;
                e = document.getElementById('email').value;
                h = document.getElementById('headline').value;
                s = document.getElementById('summary').value;
                if (fn == null || fn == "" || ln == null || ln == "" || e == null || e == "" || h == null || h == "" || s == null || s == "") {
                    alert("All fields must be filled out");
                    return false;
                }
                if (! e.includes("@")) {
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