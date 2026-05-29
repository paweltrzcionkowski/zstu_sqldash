<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>ZSTU_sqlDash | Menedżer Baz Danych</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>ZSTU_sqlDash | Menedżer Baz Danych</h1>
    <p>Wybierz bazę danych z listy: </p>
    
    <form action="db_view.php" method="POST">
        <select name="db_name">
            <?php 
            require_once 'conn_config.php';
            
            // Połączenie bez konkretnej bazy, żeby pobrać listę
            $conn = get_db_connection();

            $sql = "SHOW DATABASES";
            $databases = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_array($databases)) {
                $db = $row[0];
                echo "<option value='$db'>$db</option>";
            }
            mysqli_close($conn);
            ?>
        </select>
        <button type="submit">Zarządzaj</button>
    </form>
</body>
</html>