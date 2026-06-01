<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>ZSTU_sqlDash | Panel SQL</title>
    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/hint/show-hint.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/abbott.min.css">

    <style>
        /* Centrowanie kontenera edytora */
        .CodeMirror {
            text-align: left;
            max-width: 800px;
            margin: 10px auto;
            height: auto;
            min-height: 200px;
            border-radius: 4px;
            border: 1px solid #444;
            font-size: 14px;
        }

        /* --- FIX DLA OKIENKA PODPOWIEDZI (AUTOCOMPLETE) --- */
        .CodeMirror-hints {
            background-color: #3d3d3d !important;
            /* Tło okienka podpowiedzi */
            border: 1px solid #555 !important;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            font-family: "Trebuchet MS", sans-serif;
            padding: 4px 0;
        }

        .CodeMirror-hint {
            color: #ffffff !important;
            /* Kolor tekstu podpowiedzi */
            padding: 4px 12px !important;
            white-space: pre;
            cursor: pointer;
        }

        /* Styl podświetlenia aktualnie wybranej pozycji klawiszami góra/dół */
        li.CodeMirror-hint-active {
            background-color: #4da6ff !important;
            /* Niebieski kolor podświetlenia */
            color: #000000 !important;
            /* Czarny tekst na wybranym elemencie */
            font-weight: bold;
        }
        /* Usunięcie zgniłozielonego tła pustej linii w motywie abbott */
.CodeMirror-lines, .CodeMirror-line {
    background: transparent !important;
}

/* Opcjonalnie: jeśli chcesz subtelne podświetlenie linii, w której stoisz kursorem */
.CodeMirror-activeline-background {
    background: rgba(255, 255, 255, 0.05) !important;
}
    </style>
</head>

<body>
    <?php
    require_once 'conn_config.php'; //
    $database = $_POST['db_name'] ?? $_GET['db'] ?? ''; //
    $sql_query = $_POST['sql_query'] ?? ''; //

    if (empty($database)) { //
        die("Błąd: Nie wybrano bazy danych. Wróć do <a href='index.php'>strony głównej</a>."); //
    }
    ?>

    <h1>ZSTU_sqlDash | Panel SQL</h1>
    <h2>Wykonaj własne zapytanie SQL w bazie: <?php echo htmlspecialchars($database); ?></h2>
<form method="POST" action="sql_query.php" id="sql_form">
        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($database); ?>">
        <input type="hidden" name="sql_query" id="sql_query_hidden" value="">
        
        <div id="sql_editor_container"></div>
        
        <div class="button-group">
            <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer;">Wykonaj zapytanie</button>
            <button type="button" id="reset_btn" style="background-color: #f44336; color: white; padding: 10px 20px; border: none; cursor: pointer;">Resetuj</button>
        </div>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($sql_query)) { //
        $conn = get_db_connection($database); //

        if (!$conn) { //
            die("Połączenie z bazą '$database' nie powiodło się: " . mysqli_connect_error()); //
        }

        $executed_query = rtrim(trim($sql_query), ';'); //
        $result = mysqli_query($conn, $executed_query); //

        if ($result === false) { //
            echo "<p style='color:red;'>Błąd zapytania: " . mysqli_error($conn) . "</p>"; //
        } elseif ($result === true) { //
            echo "<p style='color:green;'>Zapytanie wykonane pomyślnie.</p>"; //
        } else { //
            echo "<h3>Wyniki zapytania:</h3>"; //
            echo "<table border='1'><tr>"; //

            while ($field_info = mysqli_fetch_field($result)) { //
                echo "<th>" . htmlspecialchars($field_info->name) . "</th>"; //
            }
            echo "</tr>"; //

            while ($row = mysqli_fetch_assoc($result)) { //
                echo "<tr>"; //
                foreach ($row as $cell) { //
                    echo "<td>" . htmlspecialchars($cell ?? '') . "</td>"; //
                }
                echo "</tr>"; //
            }
            echo "</table>"; //

            mysqli_free_result($result); //
        }

        mysqli_close($conn); //
    }
    ?>

    <br>
    <form action="db_view.php" method="POST" style="display:inline;"> <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($database); ?>"> <button type="submit">Wróć do widoku bazy</button> </form>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/sql/sql.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/hint/show-hint.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/hint/sql-hint.min.js"></script>

    <script src="sql_script.js"></script>
</body>

</html>
</body>

</html>