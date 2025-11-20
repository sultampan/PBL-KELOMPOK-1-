// admin/assets/js/produk.js

function previewImage(event) {
    const input = event.target;
    const img = document.getElementById('imgPreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            img.src = e.target.result;
            img.style.display = 'block';
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        img.src = '';
        img.style.display = 'none';
    }
}