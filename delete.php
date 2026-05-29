<?php
$database = $_POST['db_name'] ?? '';
$table = $_POST['table'] ?? '';
$id = $_POST['id'] ?? '';
$col = $_POST['col'] ?? '';

require_once 'conn_config.php';
$conn = get_db_connection($database);
$sql = "DELETE FROM $table WHERE $col = '$id'";

if (mysqli_query($conn, $sql)) {
    // Automatyczny powrót przez POST
    echo "<form id='back' action='table_view.php' method='POST'>
            <input type='hidden' name='db_name' value='$database'>
            <input type='hidden' name='table' value='$table'>
          </form>
          <script>document.getElementById('back').submit();</script>";
} else {
    echo "Błąd: " . mysqli_error($conn);
}
?>