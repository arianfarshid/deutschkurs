function markIndex() {
    let index = document.getElementById('index');
    if (index && index.value == 'artikel') {
        let artikel = document.getElementById('navArtikel');
        if (artikel) {
            artikel.style.color = "#4CAF50";
        }
    }
    if(index && index.value == 'uebersetzer'){
        let uebersetzer = document.getElementById('navUebersetzer');
        if(uebersetzer){
            uebersetzer.style.color = "#4CAF50";
        }
    }
}

document.addEventListener("DOMContentLoaded", function () {
    let indexElement = document.getElementById('index');
    if (indexElement) {
        indexElement.addEventListener('change', markIndex);
        // Ruft die Funktion einmal auf, um den Zustand beim Laden der Seite zu überprüfen
        markIndex();
    }
});
