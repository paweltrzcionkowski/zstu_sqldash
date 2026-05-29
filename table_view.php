<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>ZSTU_sqlDash | Widok tabeli</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    require_once 'conn_config.php';
    // Pobieramy dane z POST
    $database = $_POST['db_name'] ?? '';
    $table = $_POST['table'] ?? '';

    // Sprawdzamy, czy mamy komplet informacji
    if (empty($database) || empty($table)) {
        die("Błąd: Brak nazwy bazy lub tabeli. Wróć do <a href='index.php'>wyboru bazy</a>.");
    }
    // --- LOGIKA PAGINACJI ---
    $limit = 20; // Liczba rekordów na stronę
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    // Pobranie całkowitej liczby rekordów bezpiecznym zapytaniem
    $conn = get_db_connection($database);
    $escaped_table = mysqli_real_escape_string($conn, $table);
    $count_query = "SELECT COUNT(*) as total FROM `$escaped_table`";
    $count_result = mysqli_query($conn, $count_query);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_records / $limit);

    echo "<h1>Dane z tabeli: " . htmlspecialchars($table) . "</h1>";

    // Menu nawigacyjne / Twoje karty i przyciski akcji
    echo "<div class='nav-buttons'>
            <form action='db_view.php' method='POST' style='display:inline;'>
                <input type='hidden' name='db_name' value='" . htmlspecialchars($database) . "'>
                <button type='submit' id='back-button'>Powrót do listy tabel</button>
            </form> 
            <form action='add.php' method='POST' style='display:inline;'>
                <input type='hidden' name='db_name' value='" . htmlspecialchars($database) . "'>
                <input type='hidden' name='table' value='" . htmlspecialchars($table) . "'>
                <button type='submit' id='add-record'>Dodaj nowy rekord</button>
            </form>
          </div>";

    // Wyciągamy porcję danych dla aktualnej strony
    $sql = "SELECT * FROM `$escaped_table` LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $fields = mysqli_fetch_fields($result);

        echo "<table>";
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "<th>Akcje</th>";
        echo "</tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }

            // Szukanie klucza głównego do edycji/usuwania
            $id = null;
            $idColumn = '';
            foreach ($fields as $field) {
                if ($field->flags & MYSQLI_PRI_KEY_FLAG) {
                    $idColumn = $field->name;
                    $id = $row[$idColumn];
                    break;
                }
            }
            if (empty($idColumn) && !empty($fields)) {
                $idColumn = $fields[0]->name;
                $id = reset($row);
            }

            echo "<td>
                    <div style='display:flex; gap:5px;'>
                        <form action='edit.php' method='POST'>
                            <input type='hidden' name='db_name' value='" . htmlspecialchars($database) . "'>
                            <input type='hidden' name='table' value='" . htmlspecialchars($table) . "'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($id) . "'>
                            <input type='hidden' name='col' value='" . htmlspecialchars($idColumn) . "'>
                            <button type='submit'>Edytuj</button>
                        </form>

                        <form action='delete.php' method='POST' onsubmit=\"return confirm('Na pewno chcesz usunąć ten rekord?');\">
                            <input type='hidden' name='db_name' value='" . htmlspecialchars($database) . "'>
                            <input type='hidden' name='table' value='" . htmlspecialchars($table) . "'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($id) . "'>
                            <input type='hidden' name='col' value='" . htmlspecialchars($idColumn) . "'>
                            <button type='submit' style='color:red;'>Usuń</button>
                        </form>
                    </div>
                  </td>";
            echo "</tr>";
        }
        echo "</table>";

        // --- PASEK NAWIGACJI PAGINACJI ---
        if ($total_pages > 1) {
            echo "<div class='pagination-container'>";
            for ($i = 1; $i <= $total_pages; $i++) {
                $class = ($i == $page) ? "class='active-page'" : "";
                echo "<form action='table_view.php' method='POST' style='display:inline;'>
                        <input type='hidden' name='db_name' value='" . htmlspecialchars($database) . "'>
                        <input type='hidden' name='table' value='" . htmlspecialchars($table) . "'>
                        <input type='hidden' name='page' value='$i'>
                        <button type='submit' $class>$i</button>
                      </form>";
            }
            echo "</div>";
        }
    } else {
        echo "Błąd zapytania: " . mysqli_error($conn);
    }

    mysqli_close($conn);
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const activePage = document.querySelector('.active-page');
        const container = document.querySelector('.pagination-container');
        
        if (activePage && container) {
            const scrollPos = activePage.offsetLeft - (container.offsetWidth / 2) + (activePage.offsetWidth / 2);
            container.scrollLeft = scrollPos;
        }
    });
    </script>
</body>
</html>