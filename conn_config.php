<?php
// conn_config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

// Funkcja pomocnicza, żeby nie pisać mysqli_connect za każdym razem
function get_db_connection($database = null) {
    if ($database) {
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, $database);
    } else {
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
    }

    if (!$conn) {
        die("Błąd połączenia z bazą danych: " . mysqli_connect_error());
    }
    return $conn;
}
?>