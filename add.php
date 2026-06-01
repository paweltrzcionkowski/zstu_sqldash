<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>ZSTU_sqlDash | Dodawanie rekordu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    require_once 'conn_config.php';
    $database = $_POST['db_name'] ?? '';
    $table = $_POST['table'] ?? '';

    if (empty($database) || empty($table)) {
        die("Błąd: Brak danych bazy lub tabeli.");
    }

    $conn = get_db_connection($database);

    if (isset($_POST['submit_add'])) {
        $data = $_POST['data'];
        $columns = implode(", ", array_keys($data));
        $values = implode("', '", array_map(function($val) use ($conn) {
            return mysqli_real_escape_string($conn, $val);
        }, array_values($data)));
        
        $sql = "INSERT INTO $table ($columns) VALUES ('$values')";
        
        if (mysqli_query($conn, $sql)) {
            // Powrót przez formularz POST (żeby table_view nie wywaliło błędu)
            echo "<form id='back' action='table_view.php' method='POST'>
                    <input type='hidden' name='db_name' value='$database'>
                    <input type='hidden' name='table' value='$table'>
                  </form>
                  <script>document.getElementById('back').submit();</script>";
            exit;
        } else {
            echo "Błąd zapisu: " . mysqli_error($conn);
        }
    }

    echo "<h1>Dodaj rekord do $table</h1>";
    $res = mysqli_query($conn, "DESCRIBE $table");

    echo "<form method='POST'>";
    echo "<input type='hidden' name='db_name' value='$database'>";
    echo "<input type='hidden' name='table' value='$table'>";

    while ($row = mysqli_fetch_assoc($res)) {
        $field = $row['Field'];
        $type = $row['Type'];

        if ($row['Extra'] !== 'auto_increment') {
            echo "<label>$field: </label>";

            // Sprawdzenie, czy pole to ENUM
            if (preg_match('/^enum\((.*)\)$/', $type, $matches)) {
                $enum_values = str_getcsv($matches[1], ',', "'");
                
                echo "<select name='data[$field]' required>";
                foreach ($enum_values as $option) {
                    $opt_val = htmlspecialchars($option);
                    echo "<option value='$opt_val'>$opt_val</option>";
                }
                echo "</select><br><br>";
            } else {
                // Standardowy input tekstowy
                echo "<input type='text' name='data[$field]' required><br><br>";
            }
        }
    }
    echo "<button type='submit' name='submit_add' style='background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer;'>Dodaj rekord</button>";
    echo "</form>";

    // Przycisk Anuluj (też POST)
    echo "<form action='table_view.php' method='POST'>
            <input type='hidden' name='db_name' value='$database'>
            <input type='hidden' name='table' value='$table'>
            <button type='submit' style='background-color: #f44336; color: white; padding: 10px 20px; border: none; cursor: pointer;'>Anuluj</button>
          </form>";
    ?>
</body>
</html>