<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>ZSTU_sqlDash | Edycja rekordu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    require_once 'conn_config.php';
    $database = $_POST['db_name'] ?? '';
    $table = $_POST['table'] ?? '';
    $id = $_POST['id'] ?? '';
    $col = $_POST['col'] ?? '';

    if (empty($database) || empty($table) || empty($id)) {
        die("Błąd: Niepełne dane do edycji.");
    }
    $conn = get_db_connection($database);
    if (isset($_POST['update'])) {
        $updates = [];
        foreach ($_POST['data'] as $column => $value) {
            $val = mysqli_real_escape_string($conn, $value);
            $updates[] = "$column = '$val'";
        }
        $sql = "UPDATE $table SET " . implode(", ", $updates) . " WHERE $col = '$id'";
        
        if (mysqli_query($conn, $sql)) {
            echo "<form id='back' action='table_view.php' method='POST'>
                    <input type='hidden' name='db_name' value='$database'>
                    <input type='hidden' name='table' value='$table'>
                  </form>
                  <script>document.getElementById('back').submit();</script>";
            exit;
        }
    }

    // Pobranie obecnych danych
    $current_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $table WHERE $col = '$id'"));
    $res = mysqli_query($conn, "DESCRIBE $table");

    echo "<h1>Edytuj rekord w $table</h1>";
    echo "<form method='POST'>";
    echo "<input type='hidden' name='db_name' value='$database'>";
    echo "<input type='hidden' name='table' value='$table'>";
    echo "<input type='hidden' name='id' value='$id'>";
    echo "<input type='hidden' name='col' value='$col'>";

    while ($column = mysqli_fetch_assoc($res)) {
        $name = $column['Field'];
        $val = htmlspecialchars($current_data[$name] ?? '');
        $readonly = ($column['Key'] == 'PRI') ? "readonly" : "";

        echo "$name: <input type='text' name='data[$name]' value='$val' $readonly><br><br>";
    }
    echo "<button type='submit' name='update' style='background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer;'>Zapisz zmiany</button></form>";

    // Przycisk Anuluj
    echo "<form action='table_view.php' method='POST'>
            <input type='hidden' name='db_name' value='$database'>
            <input type='hidden' name='table' value='$table'>
            <button type='submit' style='padding: 10px 20px; border: none; cursor: pointer;'>Anuluj</button>
          </form>";
    ?>
</body>
</html>