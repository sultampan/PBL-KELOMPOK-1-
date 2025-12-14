/**
 * contact.js
 * - Handle Realtime Search
 * - Handle Delete Confirmation
 * - Handle Toast Notification (Sama persis dengan Fasilitas)
 */

document.addEventListener("DOMContentLoaded", function () {
    
    // ============================================================
    // 1. SEARCH TABLE
    // ============================================================
    const searchInput = document.getElementById("searchInput");
    const table = document.getElementById("contactTable");


    if (searchInput && table) {
        let timeout = null; 

        searchInput.addEventListener("keyup", function () {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                
                // --- LOGIKA PENCARIAN PINDAH KE DALAM SINI ---
                const filter = searchInput.value.toUpperCase();
                const tr = table.getElementsByTagName("tr");

                for (let i = 1; i < tr.length; i++) {
                    const tdSender = tr[i].getElementsByTagName("td")[2];
                    const tdSubject = tr[i].getElementsByTagName("td")[3];

                    if (tdSender || tdSubject) {
                        const txt = (tdSender.textContent || "") + (tdSubject.textContent || "");
                        
                        // Tampilkan atau Sembunyikan baris
                        tr[i].style.display = txt.toUpperCase().indexOf(filter) > -1 ? "" : "none";
                    }
                }

            }, 300);
        });
    }

    // ============================================================
    // 2. DELETE CONFIRMATION
    // ============================================================
    const deleteButtons = document.querySelectorAll(".btn-delete-msg");
    deleteButtons.forEach((btn) => {
        btn.addEventListener("click", function (e) {
            const name = this.getAttribute("data-name");
            if (!confirm(`Hapus pesan dari "${name}"? Data balasan juga akan terhapus.`)) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // 3. TOAST NOTIFICATION (Logic Copy dari Fasilitas)
    // ============================================================
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const msg = urlParams.get('msg');

    if (status) {
        let messageText = "";
        let type = "success"; // Default hijau (class .success di CSS)

        if (status === 'success') {
            messageText = "Berhasil! Balasan email telah terkirim.";
            type = "success";
        } else if (status === 'deleted') {
            messageText = "Berhasil! Data pesan berhasil dihapus.";
            type = "success"; 
        } else if (status === 'error') {
            messageText = msg ? decodeURIComponent(msg) : "Gagal! Terjadi kesalahan.";
            type = "error"; // Merah (class .error di CSS)
        }

        // Panggil fungsi displayAlert
        displayAlert(messageText, type);

        // Bersihkan URL
        const newUrl = window.location.pathname + "?page=contact";
        window.history.replaceState(null, null, newUrl);
    }
});

/**
 * FUNCTION: displayAlert
 * Persis sama dengan fasilitas.js
 */
function displayAlert(message, type) {
    // 1. Cek atau Buat Container (Penting agar posisi Fixed di pojok kanan)
    let toastContainer = document.getElementById("toast-container");
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container'; // Class ini yg bikin dia di pojok kanan atas
        document.body.appendChild(toastContainer);
    }

    // 2. Buat Element Toast
    const toast = document.createElement('div');
    // Tambahkan class 'toast' agar animasinya jalan
    // Tambahkan class type (success/error) agar warnanya muncul
    toast.className = `alert toast ${type}`; 
    
    // Tambahkan Icon (Opsional, biar makin mirip fasilitas)
    let icon = type === 'success' ? '<i class="fas fa-check-circle"></i> ' : '<i class="fas fa-exclamation-circle"></i> ';
    
    toast.innerHTML = icon + message;
    
    // 3. Masukkan ke Container
    toastContainer.appendChild(toast);

    // 4. Hapus otomatis setelah 4 detik
    setTimeout(() => {
        toast.classList.add('hide'); // Memicu transisi CSS opacity
        setTimeout(() => {
            toast.remove();
            // Bersihkan container jika kosong
            if (toastContainer.children.length === 0) toastContainer.remove();
        }, 300); // Tunggu animasi selesai
    }, 4000); 
}