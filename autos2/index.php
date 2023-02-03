<?php
session_start();
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
    <?php
    if (!isset($_SESSION['user'])) {
        ?>
        <div class="container mt-3">
            <h1>Welcome to the Automobiles Database</h1>
            <p><a href="login.php">Please log in</a></p>
            <p>Attempt to <a href="add.php">add data</a> without logging in</p>
        </div>
        <?php
    } else {
        ?>
        <div class="container mt-3">
            <h1>Welcome to the Automobiles Database</h1>

            <?php
            if (isset($_SESSION['success'])) {
                echo ('<p class="alert alert-success">' . $_SESSION['success'] . "</p>\n");
                unset($_SESSION['success']);
            } elseif (isset($_SESSION['error'])) {
                echo ('<p class="alert alert-danger">' . $_SESSION['error'] . "</p>\n");
                unset($_SESSION['error']);
            }
            ?>

            <div class="col-10 px-2">
                <?php
                require_once("pdo.php");
                $stmt = $pdo->query("SELECT make, model, year, mileage FROM autos");

                if (!$stmt->fetchColumn()) {
                    echo ("No rows found");
                } else {
                    ?>
                    <table class="table border-rounded w-100 table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Make</th>
                                <th scope="col">Model</th>
                                <th scope="col">Year</th>
                                <th scope="col">Mileage</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT autos_id, make, model, year, mileage FROM autos");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo ("<tr>");
                                echo ('<td>' . htmlentities($row['make']) . '</td>');
                                echo ('<td>' . htmlentities($row['model']) . '</td>');
                                echo ('<td>' . htmlentities($row['year']) . '</td>');
                                echo ('<td>' . htmlentities($row['mileage']) . '</td>');
                                echo ('<td><a href="edit.php?autos_id=' . $row['autos_id'] . '">Edit</a> / <a href="delete.php?autos_id=' . $row['autos_id'] . '">Delete</a></td>');
                                echo ("<tr>");
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                }
                ?>
            </div>

            <p><a href="add.php">Add New Entry</a></p>
            <p><a href="logout.php">Logout</a></p>

        </div>
    <?php } ?>
</body>

</html>