document.addEventListener('DOMContentLoaded', function() {
    const selected = document.getElementById('selectedImage').parentElement;
    const items = document.getElementsByClassName('select-items')[0];
    const options = items.getElementsByTagName('section');

    selected.addEventListener('click', function() {
        items.classList.toggle('select-hide');
        selected.classList.toggle('select-arrow-active');
    });

    for (let i = 0; i < options.length; i++) {
        options[i].addEventListener('click', function() {
            const imageSrc = this.getAttribute('data-image');
            const value = this.getAttribute('data-value');

            selected.getElementsByTagName('img')[0].src = imageSrc;

            const selectElement = document.getElementById('avatarSelect');
            selectElement.value = value;

            items.classList.add('select-hide');
            selected.classList.remove('select-arrow-active');
        });
    }


    document.addEventListener('click', function(e) {
        if (!selected.contains(e.target)) {
            items.classList.add('select-hide');
            selected.classList.remove('select-arrow-active');
        }
    });
});
