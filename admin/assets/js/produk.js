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

document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.querySelector('.alert.success');
    if (successAlert) {
        // Setelah 4 detik, hilangkan alert secara perlahan
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.5s ease-out';
            successAlert.style.opacity = '0';
            // Hapus elemen setelah transisi selesai
            setTimeout(() => successAlert.remove(), 500); 
        }, 4000); 
    }
});