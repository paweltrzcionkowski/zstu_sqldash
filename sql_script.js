document.addEventListener("DOMContentLoaded", function() {
    const container = document.getElementById("sql_editor_container");
    const form = document.getElementById("sql_form");
    const hiddenInput = document.getElementById("sql_query_hidden");

    if (!container || !form || !hiddenInput) return; 

    // Pobieramy zapytanie, które mogło już być wykonane (przechowamy je tymczasowo w pamięci PHP, jeśli chcesz je tam wstrzyknąć, lub odczytamy z ukrytego inputa)
    // Aby zachować zapytanie po przeładowaniu strony, przekażemy je z PHP do JS:
    const initialValue = form.dataset.query || "";

    // Inicjalizacja CodeMirror bezpośrednio wewnątrz diva
    const editor = CodeMirror(container, {
        value: initialValue,
        mode: "text/x-mysql",
        theme: "abbott",
        lineNumbers: true,
        indentWithTabs: false,
        smartIndent: false,
        matchBrackets: true,
        extraKeys: {
            "Ctrl-Space": "autocomplete"
        }
    });

    // Automatyczne pokazywanie podpowiedzi podczas pisania słów
    editor.on("inputRead", function(cm, change) {
        if (change.text[0].match(/[a-zA-Z._]/)) {
            cm.showHint({ completeSingle: false });
        }
    });

    // Obsługa wysyłania formularza
    form.addEventListener("submit", function(e) {
        const queryContent = editor.getValue().trim();

        if (queryContent === "") {
            e.preventDefault(); 
            editor.setValue(""); 
            alert("Wpisz jakieś zapytanie SQL przed wysłaniem!");
            return false;
        }

        // Pakujemy czysty string prosto do ukrytego inputa formularza
        hiddenInput.value = queryContent;
    });

    // Obsługa przycisku Reset
    const resetBtn = document.getElementById("reset_btn");
    if (resetBtn) {
        resetBtn.addEventListener("click", function() {
            editor.setValue("");
        });
    }
});

document.addEventListener("DOMContentLoaded", function() {
    const textarea = document.getElementById("sql_textarea");
    const form = document.getElementById("sql_form");

    if (!textarea || !form) return; 

    // Konfiguracja edytora CodeMirror
    const editor = CodeMirror.fromTextArea(textarea, {
        mode: "text/x-mysql",
        theme: "abbott",
        lineNumbers: true,
        indentWithTabs: false,
        smartIndent: false, // WYŁĄCZONE: CodeMirror nie próbuje być mądrzejszy od Ciebie
        matchBrackets: true,
        extraKeys: {
            "Ctrl-Space": "autocomplete"
        }
    });

    // Automatyczne pokazywanie podpowiedzi podczas pisania słów
    editor.on("inputRead", function(cm, change) {
        if (change.text[0].match(/[a-zA-Z._]/)) {
            cm.showHint({ completeSingle: false });
        }
    });

    // Obsługa wysyłania formularza
    form.addEventListener("submit", function(e) {
        const queryContent = editor.getValue().trim();

        if (queryContent === "") {
            e.preventDefault(); 
            editor.setValue(""); // Czyści edytor z ewentualnych niewidocznych śmieci visualnych
            alert("Wpisz jakieś zapytanie SQL przed wysłaniem!");
            return false;
        }

        editor.setValue(queryContent);
        editor.save(); 
    });

    // Obsługa przycisku Reset
    const resetBtn = document.getElementById("reset_btn");
    if (resetBtn) {
        resetBtn.addEventListener("click", function() {
            editor.setValue("");
        });
    }
});