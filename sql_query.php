<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>ZSTU_sqlDash | Panel SQL</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    require_once 'conn_config.php';
    // Pobieramy bazę spójnie z POST (priorytet) lub awaryjnie z GET
    $database = $_POST['db_name'] ?? $_GET['db'] ?? '';
    $sql_query = $_POST['sql_query'] ?? '';

    if (empty($database)) {
        die("Błąd: Nie wybrano bazy danych. Wróć do <a href='index.php'>strony głównej</a>.");
    }
    ?>

    <h1>ZSTU_sqlDash | Panel SQL</h1>
    <h2>Wykonaj własne zapytanie SQL w bazie: <?php echo htmlspecialchars($database); ?></h2>
    
    <form method="POST" action="sql_query.php" id="sql_form">
        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($database); ?>">
        
        <textarea name="sql_query" id="sql_textarea" rows="10" cols="80" placeholder="Wpisz swoje zapytanie SQL tutaj..." required><?php echo htmlspecialchars($sql_query); ?></textarea>
        <div class="button-group">
            <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer;">Wykonaj zapytanie</button>
            <button type="reset" style="background-color: #f44336; color: white; padding: 10px 20px; border: none; cursor: pointer;">Resetuj</button>
        </div>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($sql_query)) {
        $conn = get_db_connection($database);

        if (!$conn) {
            die("Połączenie z bazą '$database' nie powiodło się: " . mysqli_connect_error());
        }

        // Pozbywamy się średnika na końcu zapytania, żeby ew. doklejenie LIMIT nie wywaliło błędu syntaxu
        $executed_query = rtrim(trim($sql_query), ';');
        $result = mysqli_query($conn, $executed_query);

        if ($result === false) {
            echo "<p style='color:red;'>Błąd zapytania: " . mysqli_error($conn) . "</p>";
        } elseif ($result === true) {
            echo "<p style='color:green;'>Zapytanie wykonane pomyślnie.</p>";
        } else {
            echo "<h3>Wyniki zapytania:</h3>";
            echo "<table border='1'><tr>";

            // Nagłówki kolumn
            while ($field_info = mysqli_fetch_field($result)) {
                echo "<th>" . htmlspecialchars($field_info->name) . "</th>";
            }
            echo "</tr>";

            // Dane
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell ?? '') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";

            mysqli_free_result($result);
        }

        mysqli_close($conn);
    }
    ?>

    <br>
    <form action="db_view.php" method="POST" style="display:inline;">
        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($database); ?>">
        <button type="submit">Wróć do widoku bazy</button>
    </form>
</body>
</html>