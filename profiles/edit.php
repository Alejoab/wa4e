<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Not logged in");
}

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    exit;
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
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

    for($i=0; $i<9; $i++) {
        if ( ! isset($_POST["year"][$i]) ) continue;
        if ( ! isset($_POST["textYear"][$i]) ) continue;
    
        $year = $_POST["year"][$i];
        $desc = $_POST["textYear"][$i];
    
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            $_SESSION['error'] = "All fields are required";
            return;
        }
    
        if ( ! is_numeric($year) ) {
            $_SESSION['error'] = "Position year must be numeric";
            return;
        }
    }

    for($i=0; $i<9; $i++) {
        if ( ! isset($_POST["yearEdu"][$i]) ) continue;
        if ( ! isset($_POST["textSchool"][$i]) ) continue;
    
        $year = $_POST["yearEdu"][$i];
        $desc = $_POST["textSchool"][$i];
    
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            $_SESSION['error'] = "All fields are required";
            return;
        }
    
        if ( ! is_numeric($year) ) {
            $_SESSION['error'] = "Position year must be numeric";
            return;
        }
    }
}

function addPosition($pdo, $profile_id){
    $stmt = $pdo->prepare("DELETE FROM position WHERE profile_id = :pi");
    $stmt->execute(array(":pi" => $_GET["profile_id"]));

    for($i=0; $i<9; $i++) {
        if ( ! isset($_POST["year"][$i]) ) continue;
        if ( ! isset($_POST["textYear"][$i]) ) continue;

        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

        $stmt->execute(array(
        ':pid' => $profile_id,
        ':rank' => $i,
        ':year' => $_POST["year"][$i],
        ':desc' => $_POST["textYear"][$i])
        );
    }
}

function addEducation($pdo, $profile_id){
    $stmt = $pdo->prepare("DELETE FROM education WHERE profile_id = :pi");
    $stmt->execute(array(":pi" => $_GET["profile_id"]));

    for($i=0; $i<9; $i++) {
        if ( ! isset($_POST["yearEdu"][$i]) ) continue;
        if ( ! isset($_POST["textSchool"][$i]) ) continue;

        $stmt = $pdo->prepare('SELECT institution_id FROM institution WHERE name = :name');
        $stmt->execute(array('name'=> $_POST['textSchool'][$i]));
        $institution_id = $stmt->fetch(PDO::FETCH_ASSOC)['institution_id'];

        if (! $institution_id) {
            $stmt = $pdo->prepare('INSERT INTO institution(name) VALUES (:name)');
            $stmt->execute(array('name'=> $_POST['textSchool'][$i]));
            $institution_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare('INSERT INTO education(profile_id, institution_id, rank, year) VALUES ( :pid, :iid, :rank, :year)');

        $stmt->execute(array(
        ':pid' => $profile_id,
        ':iid' => $institution_id,
        ':rank' => $i,
        ':year' => $_POST["yearEdu"][$i]
        ));
    }
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
    check($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['headline'], $_POST['summary']);
    
    if (!isset($_SESSION['error'])) {
        require("pdo.php");
        $stmt = $pdo->prepare('UPDATE profile SET first_name = :fn, last_name = :ln, email = :e, headline = :h, summary = :s WHERE profile_id = :pi');
        $stmt->execute(
            array(
                ':pi' => $_GET['profile_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':e' => $_POST['email'],
                ':h' => $_POST['headline'],
                ':s' => $_POST['summary']
            )
        );

        $profile_id = $_GET['profile_id'];

        addPosition($pdo, $profile_id);
        addEducation($pdo, $profile_id);

        $_SESSION['success'] = "Record Edited";
        header("Location: index.php");
        exit;
    } else {
        header("location: edit.php?profile_id=".$_GET['profile_id']);
        return;
    }
}

require("pdo.php");
$stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :pi AND user_id = :ui");
$stmt->execute(array(":pi" => $_GET['profile_id'], ":ui" => $_SESSION['user_id']));
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


$position = [];
$stmt = $pdo->prepare("SELECT year, description, rank FROM position WHERE profile_id = :pi ORDER BY rank");
$stmt->execute(array(":pi" => $_GET['profile_id']));
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $year = $row['year'];
    $desc = $row['description'];
    $rank = $row['rank'];

    $position[] = ["year" => $year, "description" => $desc, "rank" => $rank];
}

$education = [];
$stmt = $pdo->prepare("SELECT education.year, institution.name, education.rank FROM education, institution WHERE education.profile_id = :pi AND education.institution_id = institution.institution_id ORDER BY education.rank");
$stmt->execute(array(":pi" => $_GET['profile_id']));
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $year = $row['year'];
    $name = $row['name'];
    $rank = $row['rank'];

    $education[] = ["year" => $year, "name" => $name, "rank" => $rank];
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
        <h1>Editing Profile</h1>
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
                        <label for="positiion">Position</label>
                        <input type="submit" value="+" id="addPost">
                    </div>
                    <div id="position_fields"">
                        <?php
                            foreach($position as $pos) {
                                echo('<div class="form-group" id="divYear'. $pos['rank'] . '">');
                                echo('<label>Year</label>');
                                echo('<input type="text" name="year[]" value="' . htmlentities($pos['year']) . '">');
                                echo('<input type="button" value="-" onclick="deleteYear(' . $pos['rank'] . ');">');
                                echo('<textarea type="text" name="textYear[]" class="form-control" rows="5">' . htmlentities($pos['description']) . '</textarea>');
                                echo('</div>');
                            }
                        ?>
                    </div>

                    <div class="form-group">
                        <label for="education">Education</label>
                        <input type="submit" value="+" id="addEducation">
                    </div>
                    <div id="education_fields" class="form-group">
                        <?php
                            foreach($education as $edu) {
                                echo('<div class="form-group" id="divEduYear'. $edu['rank'] . '">');
                                echo('<label>Year</label>');
                                echo('<input type="text" name="yearEdu[]" value="' . htmlentities($edu['year']) . '">');
                                echo('<input type="button" value="-" onclick="deleteYearEdu(' . $edu['rank'] . ');"><br><label>School</label>');
                                echo('<input type="text" name="textSchool[]" class="school" value="' . htmlentities($edu['name']) . '"></input>');
                                echo('</div>');
                            }
                        ?>
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary save-btn" value="Save" onclick="return doValidate();">
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

        function deleteYearEdu(value){
            $('#divEduYear'+value).remove();
            countEdu -= 1;
        }
        
        function deleteYear(value){
            $('#divYear'+value).remove();
            countPos -= 1;
        }

        countPos = $("#position_fields").find('div').length;
        countEdu = $("#education_fields").find('div').length;

        $(document).ready(function(){
            $('#addPost').click(function(event){
                event.preventDefault();
                
                if (countPos === 9) {
                    alert("No se pueden mas");
                    return;
                }

                var count = countPos;
                countPos += 1;
                
                $('#position_fields').append(
                    '<div class="form-group" id="divYear'+count+'">\
                    <label>Year</label>\
                    <input type="text" name="year[]">\
                    <input type="button" value="-" onclick="deleteYear('+count+');">\
                    <textarea type="text" name="textYear[]" class="form-control" rows="5"></textarea>\
                    </div>');
            });

            $('#addEducation').click(function(event){
                event.preventDefault();
                
                if (countEdu === 9) {
                    alert("No se pueden mas");
                    return;
                }
                
                var count = countEdu;
                countEdu += 1;
                
                $('#education_fields').append(
                    '<div class="form-group" id="divEduYear'+count+'">\
                    <label>Year</label>\
                    <input type="text" name="yearEdu[]">\
                    <input type="button" value="-" onclick="deleteYearEdu('+count+');">\
                    <br>\
                    <label>School</label>\
                    <input type="text" name="textSchool[]" class="school" value=""></input>\
                    </div>');

                $('.school').autocomplete({ source: "school.php" });

            });
                
        })
    </script>
</body>

</html>