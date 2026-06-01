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
    <div id="changelogModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>ZSTU_sqlDash v.0.0.2</h2>
            <h3>Dziennik zmian:</h3>
            <ul>
                <li>Poprawiono błąd powodujący, że pole ENUM traktowane jest jako zwykłe pole tekstowe</li>
                <li>Dodano podpowiedzi dla pisania własnego kodu SQL</li>
            </ul>
            <div style="text-align: center; margin-top: 20px;">
                <button id="closeChangelogBtn">Rozumiem</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById("changelogModal");
            const closeSpan = document.querySelector(".close-modal");
            const closeBtn = document.getElementById("closeChangelogBtn");
            
            // Wersja aplikacji - jeśli w v0.0.3 zmienisz ten string, pop-up wyskoczy znowu
            const currentVersion = "v0.0.2";

            // Sprawdzamy localStorage – jeśli brak wpisu, ustawiamy flex (pokazuje okno)
            if (localStorage.getItem("viewed_changelog_" + currentVersion) !== "true") {
                modal.style.display = "flex";
            }

            // Funkcja ukrywająca okno i zapisująca flagę w przeglądarce
            function closeModal() {
                modal.style.display = "none";
                localStorage.setItem("viewed_changelog_" + currentVersion, "true");
            }

            closeSpan.onclick = closeModal;
            closeBtn.onclick = closeModal;

            // Kliknięcie poza szarym kontenerem też zamyka i zapisuje
            window.onclick = function(event) {
                if (event.target == modal) {
                    closeModal();
                }
            }
        });
    </script>
</body>
</html>