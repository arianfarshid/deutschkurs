let translateRequest = new XMLHttpRequest();

document.addEventListener("DOMContentLoaded", function (){
    document.getElementById("gesuchtesWort").addEventListener("input", translateRequestData);
})

function translateRequestData() { // fordert die Daten asynchron an
    "use strict";
    let input = document.getElementById("gesuchtesWort");
    translateRequest.open("GET", "ubersetzerAPI.php?searched=" + encodeURI(input.value.trim()));
    translateRequest.onreadystatechange = processTranslateData;
    translateRequest.send(null);
}

function processTranslateData() {
    "use strict";
    if (translateRequest.readyState === 4) { // Uebertragung = DONE
        if (translateRequest.status === 200) { // HTTP-Status = OK
            if (translateRequest.responseText != null) {
                const data = JSON.parse(translateRequest.responseText);
                processTranslate(data);
            }
            else console.error("Dokument ist leer");
        } else console.error("Uebertragung fehlgeschlagen");
    } // else; // Uebertragung laeuft noch
}

function processTranslate(data){
    let uebersetzung = document.getElementById('uebersetzung');
    let satz = document.getElementById('deutscherSatz');
    let persischerSatz = document.getElementById('persischerSatz');

    uebersetzung.textContent = data.Persisch;
    satz.textContent = data.Satz;
    persischerSatz.textContent = data.PersischerSatz;
}