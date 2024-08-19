function markIndex() {
    let index = document.getElementById('index');
    if (index && index.value == 'forum') {
        let forum = document.getElementById('navForum');
        if (forum) {
            forum.style.color = "#4CAF50";
        }
    }
    if(index && index.value == 'nutzer'){
        let nutzer = document.getElementById('navNutzer');
        if(nutzer){
            nutzer.style.color = "#4CAF50";
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
