<?php
session_start();

require_once("pdo.php");
$stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :pi");
$stmt->execute(array(":pi" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for id';
    header('Location: index.php');
    exit;
}

$fn = $row['first_name'];
$ln = $row['last_name'];
$e = $row['email'];
$h = $row['headline'];
$s = $row['summary'];
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
        <h1>Viewing Profile</h1>
        <div class="card">
            <div class="card-body">
                <form class="form-group">
                    <div class="form-group">
                        <label for="fname">First Name</label>
                        <input type="text" name="fname" id="fname" class="form-control"
                            value="<?= htmlentities($fn) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="lname">Last Name</label>
                        <input type="text" name="lname" id="lname" class="form-control"
                            value="<?= htmlentities($ln) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" class="form-control"
                            value="<?= htmlentities($e) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="headline">Headline</label>
                        <input type="text" name="headline" id="headline" class="form-control"
                            value="<?= htmlentities($h) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="summary">Summary</label>
                        <textarea type="text" name="summary" id="summary" class="form-control" rows="5" readonly><?=htmlentities($s)?></textarea>
                    </div>

                    <h5>Positions</h5>
                    <u class="list-group mb-4">
                        <?php
                            require_once("pdo.php");
                            $stmt = $pdo->prepare("SELECT year, description FROM position WHERE profile_id = :pi ORDER BY rank");
                            $stmt->execute(array(":pi" => $_GET['profile_id']));
                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                echo('<li class="list-group-item"><strong>' . htmlentities($row['year']). ':</strong>   ' . htmlentities($row['description']) . '</li>');
                            }
                        ?>
                    </u>

                    <h5>Education</h5>
                    <u class="list-group mb-4">
                        <?php
                            require_once("pdo.php");
                            $stmt = $pdo->prepare("SELECT education.year, institution.name FROM education, institution WHERE education.profile_id = :pi AND education.institution_id = institution.institution_id ORDER BY education.rank");
                            $stmt->execute(array(":pi" => $_GET['profile_id']));
                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                echo('<li class="list-group-item"><strong>' . htmlentities($row['year']). ':</strong>   ' . htmlentities($row['name']) . '</li>');
                            }
                        ?>
                    </u>

                    <div class="form-group">
                        <a href="index.php">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>