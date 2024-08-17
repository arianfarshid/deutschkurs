let request = new XMLHttpRequest();

document.addEventListener("DOMContentLoaded", function (){
    document.getElementById("userInput").addEventListener("input", requestData);
})

function requestData() { // fordert die Daten asynchron an
    "use strict";
    let input = document.getElementById("userInput");
    request.open("GET", "sucheAPI.php?word=" + encodeURI(input.value.trim()));
    request.onreadystatechange = processData;
    request.send(null);
}

function processData() {
    "use strict";
    if (request.readyState === 4) { // Uebertragung = DONE
        if (request.status === 200) { // HTTP-Status = OK
            if (request.responseText != null) {
                const data = JSON.parse(request.responseText);
                process(data);
            }
            else console.error("Dokument ist leer");
        } else console.error("Uebertragung fehlgeschlagen");
    } // else; // Uebertragung laeuft noch
}

function process(intext){
    "use strict";
    if (!intext || typeof intext !== 'object') {
        console.error("Ungültige Daten übergeben:", intext);
        return;
    }

    let artikel = intext.Artikel || "";
    let begriff = intext.Begriff + ": " || "";
    let satz = intext.Satz || "";

    let artikelH = document.getElementById("artikel");
    artikelH.textContent = artikel;
    let begriffH = document.getElementById("begriff");
    begriffH.textContent = begriff;
    let satzH = document.getElementById("satz");
    satzH.textContent = satz;


    if(artikel == "die"){
        artikelH.style.color = "red";
    } else if(artikel == "der"){
        artikelH.style.color = "blue";
    } else {
        artikelH.style.color = "green";
    }
}


