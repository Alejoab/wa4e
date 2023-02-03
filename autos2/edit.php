<?php
session_start();

if (!isset($_SESSION['user'])) {
    die("ACCESS DENIED");
}

if (!isset($_GET['autos_id'])) {
    $_SESSION['error'] = "Missing autos_id";
    header('Location: index.php');
    exit;
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    exit;
}

function check($make, $year, $mileage, $model)
{

    if (strlen($make) < 1 || strlen($year) < 1 || strlen($mileage) < 1 || strlen($model) < 1) {
        $_SESSION['error'] = "All fields are required";
        header('Location: edit.php?autos_id=' . $_REQUEST['autos_id']);
        exit;
    }

    if (!is_numeric($year)) {
        $_SESSION['error'] = "Year must be an integer";
        header("Location: edit.php?autos_id=" . $_REQUEST['autos_id']);
        exit;
    }

    if (!is_numeric(($mileage))) {
        $_SESSION['error'] = "Mileage must be an integer";
        header("Location: edit.php?autos_id=" . $_REQUEST['autos_id']);
        exit;
    }

    if (strlen($make) < 1) {
        $_SESSION['error'] = "Make is required";
        header("Location: edit.php?autos_id=" . $_REQUEST['autos_id']);
        exit;
    }
}

require_once("pdo.php");
if (isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) && isset($_POST['model']) && isset($_POST['autos_id'])) {
    check($_POST['make'], $_POST['year'], $_POST['mileage'], $_POST['model']);

    if (!isset($_SESSION['error'])) {
        $stmt = $pdo->prepare('UPDATE autos SET make = :mk, model = :md, year = :yr, mileage = :mi WHERE autos_id = :autos_id');
        $stmt->execute(
            array(
                ':mk' => $_POST['make'],
                ':autos_id' => $_POST['autos_id'],
                ':md' => $_POST['model'],
                ':yr' => $_POST['year'],
                ':mi' => $_POST['mileage']
            )
        );
        $_SESSION['success'] = "Record Updated";
        header("Location: index.php");
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM autos WHERE autos_id = :autos_id");
$stmt->execute(array(":autos_id" => $_GET['autos_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    exit;
}

$make = $row['make'];
$year = $row['year'];
$mileage = $row['mileage'];
$model = $row['model'];
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
        <h1>Editing Automobile</h1>
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
                        <input type="hidden" name="autos_id" value="<?= htmlentities($row['autos_id']) ?>">
                        <input type="submit" class="btn btn-primary save-btn" value="Save" name="edit">
                        <input type="submit" class="btn btn-danger" name="cancel" value="Cancel">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>