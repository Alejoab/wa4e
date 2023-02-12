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

function addProfile($pdo) {
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
}

function addPosition($pdo, $profile_id){
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
        require_once("pdo.php");
        addProfile($pdo);

        $profile_id = $pdo->lastInsertId();

        addPosition($pdo, $profile_id);
        addEducation($pdo, $profile_id);
        

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

    <?php require_once('head_html.php') ?>

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
                        <label for="education">Education</label>
                        <input type="submit" value="+" id="addEducation">
                    </div>
                    <div id="education_fields"></div>


                    <div class="form-group">
                        <label for="positiion">Position</label>
                        <input type="submit" value="+" id="addPost">
                    </div>
                    <div id="position_fields"></div>


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
        
        countPos = 0;
        countEdu = 0;

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

        });
        
        function deleteYearEdu(value){
            $('#divEduYear'+value).remove();
            countEdu -= 1;
        }
        
        function deleteYear(value){
            $('#divYear'+value).remove();
            countPos -= 1;
        }
        </script>     
    </body>
</html>