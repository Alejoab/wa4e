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
    <div class="container mt-3">
        <h1>Resume Registry</h1>
        <?php
        if (!isset($_SESSION['user_id'])){
            echo('<a href="login.php">Please log in</a>');
        }
        ?>

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
            $stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");

            if (!$stmt->fetchColumn()) {
                echo ("No rows found");
            } else {
                ?>
                <table class="table border-rounded w-100 table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Headline</th>
                            <?php
                            if (isset($_SESSION['user_id'])){
                                echo('<th scope="col">Action</th>');
                            }
                            
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT profile_id, user_id, first_name, last_name, headline FROM profile");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo ("<tr>");
                            echo ('<td>' . '<a href="view.php?profile_id=' . $row['profile_id'] . '">' . htmlentities($row['first_name']) . " " . htmlentities($row['last_name']) . "</a>" . '</td>');
                            echo ('<td>' . htmlentities($row['headline']) . '</td>');
                            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $row['user_id'] ){
                                echo ('<td><a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / <a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a></td>');
                            } elseif (isset($_SESSION['user_id'])) {
                                echo ("<td></td>");
                            }
                            echo ("<tr>");
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </div>

        <?php
        if (isset($_SESSION['user_id'])){
            echo('<p><a href="add.php">Add New Entry</a></p>');
            echo('<p><a href="logout.php">Logout</a></p>');
        }
        ?>
        
    </div>
</body>

</html>