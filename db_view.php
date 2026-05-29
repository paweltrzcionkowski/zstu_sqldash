<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>ZSTU_sqlDash | Wybór Tabeli</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    require_once 'conn_config.php';
    // Logika przechwytywania nazwy bazy
    $database = $_POST['db_name'] ?? $_GET['db'] ?? null;

    if (!$database) {
        die("Błąd: Nie wybrano bazy danych. Wróć do <a href='index.php'>strony głównej</a>.");
    }

    // Łączymy się bezpośrednio z wybraną bazą
    $conn = get_db_connection($database);

    echo "<h1>Zarządzanie bazą danych: " . htmlspecialchars($database) . "</h1>";
    
    // POPRAWKA: Przejście do panelu SQL przez formularz POST, aby sql_query.php nie gubił wybranej bazy
    echo "<div style='text-align:center; margin-bottom:20px;'>
            <form action='sql_query.php' method='POST' style='display:inline;'>
                <input type='hidden' name='db_name' value='" . htmlspecialchars($database) . "'>
                <button type='submit' style='background:none; border:none; color:#4da6ff; text-decoration:underline; cursor:pointer; padding:0; margin:0; font-size: 1.17em; font-weight: bold;'>
                    Przejdź do panelu SQL
                </button>
            </form>
          </div>";

    echo "<h2 style='margin-bottom: 0;'>Lista tabel:</h2>";
    echo "<p style='margin-bottom: 0;'>Wybierz tabelę, aby zobaczyć jej zawartość lub zarządzać rekordami.</p>";
    
    $sql = "SHOW TABLES";
    $tables = mysqli_query($conn, $sql);

    // Twoja oryginalna 3-kolumnowa siatka z CSS
    echo "<div class='db-grid'>";

    while ($row = mysqli_fetch_array($tables)) {
        $tableName = $row[0];
        
        // Twoje oryginalne karty tabel z przekazywaniem danych przez POST
        echo "<div class='table-card'>
                <form action='table_view.php' method='POST' style='display:block; width:100%; height:100%;'>
                    <input type='hidden' name='db_name' value='" . htmlspecialchars($database) . "'>
                    <input type='hidden' name='table' value='" . htmlspecialchars($tableName) . "'>
                    <button type='submit' style='background:none; border:none; color:white; text-decoration:underline; width:100%; height:100%; padding: 20px 10px; cursor:pointer; text-align:center; margin:0;'>
                        " . htmlspecialchars($tableName) . "
                    </button>
                </form>
              </div>";
    }

    echo "</div>";

    mysqli_close($conn);
    ?>
    <br>
    <div style="text-align: center;">
        <a href="index.php">Wróć do wyboru bazy</a>
    </div>
</body>
</html>