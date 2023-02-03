<?php
session_start();
if (!isset($_SESSION['user'])) {
    die("ACCESS DENIED");
}

if (isset($_POST['cancel'])) {
    header("location: index.php");
    exit;
}

function check($make, $year, $mileage, $model)
{
    if(strlen($make) < 1 || strlen($year) < 1 || strlen($mileage) < 1 || strlen($model) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("location: add.php");
        return;
    }

    if (!is_numeric($year)) {
        $_SESSION['error'] = "Year must be an integer";
        header("location: add.php");
        return;
    }

    if (!is_numeric(($mileage))) {
        $_SESSION['error'] = "Mileage must be an integer";
        header("location: add.php");
        return;
    }

    if (strlen($make) < 1) {
        $_SESSION['error'] = "Make is required";
        header("location: add.php");
        return;
    }
}

require("pdo.php");

if (isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) && isset($_POST['model'])) {
    check($_POST['make'], $_POST['year'], $_POST['mileage'], $_POST['model']);

    if (!isset($_SESSION['error'])) {
        $stmt = $pdo->prepare('INSERT INTO autos(make, model, year, mileage) VALUES ( :mk, :md, :yr, :mi)');
        $stmt->execute(
            array(
                ':mk' => $_POST['make'],
                ':md' => $_POST['model'],
                ':yr' => $_POST['year'],
                ':mi' => $_POST['mileage']
            )
        );
        $_SESSION['success'] = "Record Added";
        header("Location: index.php");
        return;
    } else {
        $_SESSION['make'] = $_POST['make'];
        $_SESSION['year'] = $_POST['year'];
        $_SESSION['mileage'] = $_POST['mileage'];
        $_SESSION['model'] = $_POST['model'];
        header("location: add.php");
        return;
    }
}

$make = $_SESSION['make'] ?? "";
$year = $_SESSION['year'] ?? "";
$mileage = $_SESSION['mileage'] ?? "";
$model = $_SESSION['model'] ?? "";
unset($_SESSION['make']);
unset($_SESSION['year']);
unset($_SESSION['mileage']);
unset($_SESSION['model']);
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
            <?= htmlentities($_SESSION['user']) ?>
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
                        <label for="make">Make</label>
                        <input type="text" name="make" id="make" class="form-control"
                            value="<?= htmlentities($make) ?>">
                    </div>
                    <div class="form-group">
                        <label for="model">Model</label>
                        <input type="text" name="model" id="model" class="form-control"
                            value="<?= htmlentities($model) ?>">
                    </div>
                    <div class="form-group">
                        <label for="year">Year</label>
                        <input type="text" name="year" id="year" class="form-control"
                            value="<?= htmlentities($year) ?>">
                    </div>
                    <div class="form-group">
                        <label for="mileage">Mileage</label>
                        <input type="text" name="mileage" id="mileage" class="form-control"
                            value="<?= htmlentities($mileage) ?>">
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary save-btn" value="Add">
                        <input type="submit" class="btn btn-danger" name="cancel" value="Cancel">
                    </div>
                </form>
            </div>
        </div>
</body>

</html>