<?php
session_start();
if (!isset($_GET['name'])) {
    die("Name parameter missing");
}

if (isset($_POST['logout'])) {
    header("location: index.php");
    exit;
}

function check($make, $year, $mileage)
{
    if (!is_numeric($year) || !is_numeric(($mileage))) {
        return "Mileage and year must be numeric";
    }

    if (strlen($make) < 1) {
        return "Make is required";
    }
}

$error = false;
require("pdo.php");

if (isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage'])) {
    $error = check($_POST['make'], $_POST['year'], $_POST['mileage']);

    if (!$error) {
        $stmt = $pdo->prepare('INSERT INTO autos(make, year, mileage) VALUES ( :mk, :yr, :mi)');
        $stmt->execute(
            array(
                ':mk' => $_POST['make'],
                ':yr' => $_POST['year'],
                ':mi' => $_POST['mileage']
            )
        );
        $_SESSION['success'] = 1;
        header("Location: autos.php?name=" . urlencode($_GET['name']));
        exit;
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
        <h1>Tracking Autos for
            <?= htmlentities($_GET['name']) ?>
        </h1>
        <?php
        if ($error) {
            echo ('<p class="alert alert-danger">' . $error . "</p>\n");
        }
        if (isset($_SESSION['success']) && $_SESSION['success'] === 1) {
            echo ('<p class="alert alert-success">' . "Record Inserted" . "</p>\n");
            unset($_SESSION['success']);
        }
        ?>
        <form method="post">
            <p>Make:
                <input type="text" name="make" size="60" />
            </p>
            <p>Year:
                <input type="text" name="year" />
            </p>
            <p>Mileage:
                <input type="text" name="mileage" />
            </p>
            <input type="submit" value="Add">
            <input type="submit" name="logout" value="Logout">
        </form>

        <h1>Automobiles</h1>
        <?php
        $stmt = $pdo->query("SELECT auto_id, make, year, mileage FROM autos");

        echo ('<table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Auto id</th>
                    <th scope="col">Make</th>
                    <th scope="col">Year</th>
                    <th scope="col">Mileage</th>
                </tr>
            </thead>
            <tbody>');

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo ("<tr>");
            echo ('<th scope="row">' . htmlentities($row['auto_id']) . '</th>');
            echo ('<td>' . htmlentities($row['make']) . '</td>');
            echo ('<td>' . htmlentities($row['year']) . '</td>');
            echo ('<td>' . htmlentities($row['mileage']) . '</td>');
            echo ("<tr>");
        }
        echo ("</tbody>");
        echo ("</table>");

        ?>
</body>

</html>