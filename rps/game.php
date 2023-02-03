<?php
if (!isset($_GET['name'])) {
    die("Name parameter missing");
}

if (isset($_POST['logout'])) {
    header('Location: index.php');
    exit;
}

$names = array('Rock', 'Paper', 'Scissors');
$human = isset($_POST["human"]) ? $_POST['human'] + 0 : -1;
$computer = rand(0, 2);

function check($computer, $human)
{
    if ($human === -1) {
        return false;
    }

    $dif = abs($human - $computer);

    if ($dif === 1) {
        $win = max($human, $computer);
    } else if ($dif == 2) {
        $win = min($computer, $human);
    } else {
        return "Tie";
    }

    if ($human == $win) {
        return "You Win";
    } else {
        return "You Lose";
    }

}

$result = check($computer, $human);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alejandro Alvarez Botero e1b1546c</title>
    <?php require_once "bootstrap.php"; ?>
</head>

<body>
    <div class="container">
        <h1>Rock Paper Scissors</h1>
        <p>Welcome:
            <?= htmlentities($_GET['name']) ?>
        </p>

        <form method="post">
            <select name="human">
                <option value="-1">Select</option>
                <option value="0">Rock</option>
                <option value="1">Paper</option>
                <option value="2">Scissors</option>
                <option value="3">Test</option>
            </select>
            <input type="submit" value="Play">
            <input type="submit" name="logout" value="Logout">
        </form>

        <pre>
<?php
if ($human == -1) {
    print "Please select a strategy and press Play.\n";
} else if ($human == 3) {
    for ($c = 0; $c < 3; $c++) {
        for ($h = 0; $h < 3; $h++) {
            $r = check($c, $h);
            print "Human=$names[$h] Computer=$names[$c] Result=$r\n";
        }
    }
} else {
    print "Your Play=$names[$human] Computer Play=$names[$computer] Result=$result\n";
}
?>
    </pre>
    </div>
</body>

</html>