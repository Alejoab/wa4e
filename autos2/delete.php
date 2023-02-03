<?php
session_start();

if (!isset($_SESSION['user'])) {
    die("ACCESS DENIED");
}

if (!isset($_GET['autos_id'])) {
    $_SESSION['error'] = "Missing autos_id";
    header('Location: index.php');
    return;
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

require_once("pdo.php");
if (isset($_POST['delete']) && isset($_POST['autos_id'])) {
    $stmt = $pdo->prepare("DELETE FROM autos WHERE autos_id = :autos_id");
    $stmt->execute(array(":autos_id" => $_POST["autos_id"]));
    $_SESSION['success'] = "Record Delete";
    header("location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM autos WHERE autos_id = :autos_id");
$stmt->execute(array(":autos_id" => $_GET['autos_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    return;
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
        <h3>Confirm deleting auto maked for
            <?= htmlentities($row['make']) ?> with model
            <?= htmlentities($row['model']) ?>
        </h3>
        <form class="form-group" method="post">
            <input type="hidden" name="autos_id" value="<?=htmlentities($row['autos_id'])?>">
            <input type="submit" class="btn btn-danger" name="delete" value="Delete">
            <input type="submit" class="btn" name="cancel" value="Cancel">
        </form>
    </div>
</body>

</html>