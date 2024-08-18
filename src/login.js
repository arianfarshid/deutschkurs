document.addEventListener('DOMContentLoaded', function() {
    const selected = document.querySelector('.select-selected');
    const items = document.querySelector('.select-items');
    const options = items.querySelectorAll('section');

    // Öffne/Schließe das Dropdown-Menü
    selected.addEventListener('click', function() {
        items.classList.toggle('select-hide');
        selected.classList.toggle('select-arrow-active');
    });

    // Aktualisiere das ausgewählte Bild
    options.forEach(function(option) {
        option.addEventListener('click', function() {
            const imageSrc = this.getAttribute('data-image');
            const value = this.getAttribute('data-value');
            selected.querySelector('img').src = imageSrc;

            // Setze den Wert im versteckten Dropdown-Menü (für Formulareinsendung)
            const selectElement = document.getElementById('avatarSelect');
            selectElement.value = value;

            // Schließe das Dropdown-Menü nach Auswahl
            items.classList.add('select-hide');
            selected.classList.remove('select-arrow-active');
        });
    });

    // Schließe das Dropdown, wenn man woanders hinklickt
    document.addEventListener('click', function(e) {
        if (!selected.contains(e.target)) {
            items.classList.add('select-hide');
            selected.classList.remove('select-arrow-active');
        }
    });
});
