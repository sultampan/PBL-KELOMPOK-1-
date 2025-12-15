// admin/assets/js/produk.js

/* =========================================
   1. FUNGSI GAMBAR & FILE UPLOAD
   ========================================= */
   function previewProductImage(event) {
    const input = event.target;
    const imgPreview = document.getElementById("imgPreview");
    const previewContainer = document.getElementById("previewContainer"); // Tambah ini
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    const errorContainer = document.getElementById("fileError");
    if (errorContainer) {
        errorContainer.textContent = "";
        errorContainer.style.display = "none";
    }

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();

        if (!ALLOWED_EXT.includes(fileExt)) {
            errorContainer.textContent = `Ekstensi tidak diizinkan.`;
            errorContainer.style.display = "block";
            input.value = "";
            
            // Sembunyikan container, bukan cuma img
            if(previewContainer) previewContainer.style.display = "none"; 
            
            updateProductFileName(input);
            return;
        }
        if (file.size > MAX_FILE_SIZE) {
            errorContainer.textContent = "File terlalu besar (Max 5MB).";
            errorContainer.style.display = "block";
            input.value = "";
            
            // Sembunyikan container
            if(previewContainer) previewContainer.style.display = "none";
            
            updateProductFileName(input);
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            imgPreview.src = e.target.result;
            // Tampilkan container
            if(previewContainer) previewContainer.style.display = "flex"; 
        };
        reader.readAsDataURL(file);
    } else {
        imgPreview.src = "";
        // Sembunyikan container
        if(previewContainer) previewContainer.style.display = "none";
    }
    
    // Panggil validasi tombol simpan/batal
    validateFormState();
}

function removeProductImage() {
    const input = document.getElementById("inputGambar");
    const img = document.getElementById("imgPreview");
    const previewContainer = document.getElementById("previewContainer"); // Tambah ini
    const removeBtn = document.getElementById("removeImageBtn");
    const fileNameText = document.getElementById("fileNameText");

    if (input) input.value = "";
    if (img) img.src = "";
    
    // Sembunyikan container kotak
    if (previewContainer) previewContainer.style.display = "none";
    
    if (fileNameText) fileNameText.textContent = "Tidak ada file yang dipilih...";
    if (removeBtn) removeBtn.style.display = "none";

    const removeExisting = document.getElementById("removeExistingImage");
    if (removeExisting) removeExisting.value = "1";
    
    validateFormState();
}

function updateProductFileName(input) {
    const fileNameText = document.getElementById("fileNameText");
    const removeBtn = document.getElementById("removeImageBtn");
    if (input.files && input.files.length > 0) {
        fileNameText.textContent = input.files[0].name;
        if (removeBtn) removeBtn.style.display = "block";
    } else {
        fileNameText.textContent = "Tidak ada file yang dipilih...";
        if (removeBtn) removeBtn.style.display = "none";
    }
    const removeExisting = document.getElementById("removeExistingImage");
    if (removeExisting) removeExisting.value = "0";
    validateFormState();
}

/* =========================================
   2. FUNGSI DYNAMIC ROW (TEAM - MEMBER + ROLE)
   ========================================= */
function addTeamToTable() {
    const select = document.getElementById("memberSelect");
    const roleInput = document.getElementById("roleInput");
    const tableBody = document.querySelector("#teamTable tbody");

    const memberId = select.value;
    const memberName = select.options[select.selectedIndex].text;
    const role = roleInput.value.trim();

    if (!memberId) { alert("Pilih member terlebih dahulu!"); return; }
    if (role === "") { alert("Role tidak boleh kosong!"); return; }

    // Cek duplikasi di tabel
    const existingInputs = tableBody.querySelectorAll('input[name="member_ids[]"]');
    for (let input of existingInputs) {
        if (input.value === memberId) {
            alert("Member ini sudah ada di daftar!");
            return;
        }
    }

    const row = document.createElement("tr");
    row.innerHTML = `
        <td>${memberName}<input type="hidden" name="member_ids[]" value="${memberId}"></td>
        <td>${role}<input type="hidden" name="member_roles[]" value="${role}"></td>
        <td style="text-align:center;"><button type="button" class="btn btn-danger btn-sm" onclick="removeTeamRowTable(this)">✕</button></td>
    `;
    tableBody.appendChild(row);
    select.value = ""; roleInput.value = "";
    validateFormState();
}

function removeTeamRowTable(btn) {
    btn.closest("tr").remove();
    validateFormState();
}

/* =========================================
   3. FUNGSI SEARCH (PENCARIAN)
   ========================================= */
function searchProduct() {
    const input = document.getElementById('searchProductInput');
    if (input) {
        const keyword = input.value;
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('p', '1');
        
        if (keyword) currentUrl.searchParams.set('keyword', keyword);
        else currentUrl.searchParams.delete('keyword');

        // Menggunakan pushState agar tidak reload halaman (AJAX Search)
        window.history.pushState(null, "", currentUrl.toString());
        loadProductList();
    }
}

function resetSearchProduct() {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.delete('keyword');
    currentUrl.searchParams.set('p', '1');
    
    // Menggunakan pushState agar tidak reload halaman
    window.history.pushState(null, "", currentUrl.toString());
    loadProductList();
}

/* =========================================
   4. FUNGSI ALERT & UTILITY
   ========================================= */
function displayAlert(message, type) {
    let toastContainer = document.getElementById("toast-container");
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }
    const toast = document.createElement('div');
    toast.className = `alert toast ${type}`;
    toast.innerHTML = message;
    toastContainer.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => {
            toast.remove();
            if (toastContainer.children.length === 0) toastContainer.remove();
        }, 300);
    }, 4000);
}

/* =========================================
   5. FUNGSI CRUD AJAX (LOAD, DELETE, SAVE)
   ========================================= */
function loadProductList() {
    const listContainer = document.getElementById("product-list-container");
    if (!listContainer) return;
    
    // Tambahkan timestamp (&_t) untuk anti-cache
    const url = "module/produk/table-load.php" + window.location.search + "&_t=" + new Date().getTime();

    listContainer.style.opacity = "0.6"; // Efek loading

    fetch(url).then((response) => response.text()).then((html) => {
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = html;
        const listContent = tempDiv.querySelector("#product-list-container");
        
        // Ambil isi baru, atau fallback ke full html jika selector ga ketemu
        listContainer.innerHTML = listContent ? listContent.innerHTML : html;
        listContainer.style.opacity = "1";
        
        attachSearchListener(); // Pasang ulang listener Enter
    }).catch((error) => {
        console.error("Error loading table:", error);
        listContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat tabel.</div>';
        listContainer.style.opacity = "1";
    });
}

function loadEmptyProductForm(successMessage) {
    const formContainer = document.getElementById("form-content-wrapper");
    if (!formContainer) return;
    
    // Tambahkan timestamp agar form benar-benar baru
    const url = "module/produk/form-load.php?_t=" + new Date().getTime();

    if(successMessage) displayAlert(successMessage, "success");

    fetch(url).then((response) => response.text()).then((html) => {
        formContainer.innerHTML = html;
        attachFormValidators();
    }).catch((error) => {
        console.error("Error loading form:", error);
    });
}

function cancelProductForm() {
    const formContainer = document.getElementById("form-content-wrapper");
    if (!formContainer) return;
    formContainer.innerHTML = '<div style="text-align:center; padding:20px;">Mereset form...</div>';

    fetch("module/produk/form-load.php").then((response) => response.text()).then((html) => {
        formContainer.innerHTML = html;
        
        // Hapus parameter edit dari URL tanpa reload
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.delete('edit');
        window.history.pushState({}, '', currentUrl);

        document.querySelector('.card h2').scrollIntoView({ behavior: 'smooth' });
        attachFormValidators();
    }).catch((error) => {
        console.error("Error resetting form:", error);
    });
}

function deleteProduct(id) {
    if (!confirm("Anda yakin ingin menghapus produk ini?")) return;
    const url = "module/produk/delete.php";
    const formData = new FormData();
    formData.append("id", id);

    fetch(url, { method: "POST", body: formData })
        .then((response) => response.json()).then((data) => {
            if (data.status === "success") {
                loadProductList(); // Refresh list tanpa reload
                displayAlert(data.message, "success");
            } else {
                displayAlert(data.message, "error");
            }
        }).catch((error) => {
            displayAlert("Terjadi kesalahan jaringan.", "error");
        });
}

/* =========================================
   FUNGSI LIVE SEARCH DENGAN DEBOUNCE
   ========================================= */
function attachSearchListener() {
    const searchInput = document.getElementById('searchProductInput');
    let debounceTimeout;

    if (searchInput) {
        // Clone node untuk membersihkan listener lama (biar gak numpuk)
        const newInput = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(newInput, searchInput);

        // EVENT 1: INPUT (Saat user mengetik)
        newInput.addEventListener('input', function(e) {
            // Reset timer setiap kali user ngetik huruf baru
            clearTimeout(debounceTimeout);
            
            // Tunggu 500ms (setengah detik) setelah user BERHENTI ngetik
            debounceTimeout = setTimeout(() => {
                searchProduct();
            }, 500); 
        });

        // EVENT 2: ENTER (Biar pencarian instan kalau user neken Enter)
        newInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(debounceTimeout); // Batalkan timer debounce
                searchProduct(); // Cari langsung
            }
        });
        
        // PENTING: Fokus dikembalikan ke input agar kursor tidak hilang (opsional)
        // newInput.focus(); 
    }
}

/* =========================================
   6. HANDLE PAGINATION CLICKS (AJAX) - BARU!
   ========================================= */
document.addEventListener('click', function(e) {
    // Cek jika elemen yang diklik memiliki class 'page-link'
    if (e.target && e.target.classList.contains('page-link')) {
        e.preventDefault(); // Stop reload halaman
        
        const href = e.target.getAttribute('href'); // Ambil URL target (?page=produk&p=2)
        
        // Jika URL valid dan tombol tidak disabled
        if (href && !e.target.classList.contains('disabled')) {
            // Update URL browser
            window.history.pushState(null, "", href);
            
            // Panggil fungsi load AJAX
            loadProductList();
            
            // Scroll halus ke atas daftar produk
            const listContainer = document.getElementById("product-list-container");
            if(listContainer) {
                listContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }
});

/* =========================================
   7. EVENT LISTENER UTAMA
   ========================================= */
document.addEventListener("DOMContentLoaded", function () {
    attachSearchListener();

    // Handle Back/Forward Browser Button
    window.addEventListener('popstate', function(event) {
        loadProductList();
    });

    document.addEventListener("submit", function (e) {
        // ID Form HARUS 'productForm'
        if (e.target && e.target.id === "productForm") {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const url = "module/produk/save.php";

            const submitBtn = document.getElementById("submitBtn");
            submitBtn.disabled = true;
            submitBtn.textContent = "Memproses...";

            fetch(url, { method: "POST", body: formData })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === "success") {
                        
                        // 1. REFRESH TABEL (AJAX)
                        loadProductList(); 

                        // 2. CEK APAKAH UPDATE ATAU INSERT
                        const isUpdate = formData.get("id_produk"); 
                        
                        // 3. RESET FORM
                        loadEmptyProductForm(data.message);

                        // 4. JIKA UPDATE, BERSIHKAN URL (HILANGKAN ?edit=...)
                        if (isUpdate) {
                            const cleanUrl = window.location.pathname + "?page=produk";
                            window.history.pushState({}, document.title, cleanUrl);
                        }

                    } else {
                        displayAlert(data.message, "error");
                    }
                })
                .catch((error) => {
                    console.error("AJAX Error:", error);
                    displayAlert("Terjadi kesalahan jaringan/server.", "error");
                })
                .finally(() => {
                    const finalBtn = document.getElementById("submitBtn");
                    if (finalBtn) {
                        finalBtn.disabled = false;
                        finalBtn.textContent = "Simpan"; 
                    }
                });
        }
    });
});

/* =========================================
   8. LOGIKA TOMBOL BATAL & SIMPAN (AUTO DISABLE)
   ========================================= */

function validateFormState() {
    const submitBtn = document.getElementById("submitBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const form = document.getElementById("productForm");
    
    if (!submitBtn || !cancelBtn || !form) return;

    // 1. CEK UNTUK TOMBOL SIMPAN (MANDATORY CHECK)
    // Ambil input yang punya atribut 'required'
    const requiredInputs = form.querySelectorAll('[required]');
    let allFilled = true;

    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            allFilled = false;
        }
    });

    // Aktifkan/Matikan Tombol Simpan
    submitBtn.disabled = !allFilled;


    // 2. CEK UNTUK TOMBOL BATAL (DIRTY/EMPTY CHECK)
    // Tombol Batal nyala jika:
    // a. Mode Edit (karena form pasti terisi data lama)
    // b. Mode Tambah TAPI user sudah mengetik/upload sesuatu
    
    const isEditMode = document.querySelector('input[name="id_produk"]');
    
    if (isEditMode) {
        // Kalau mode edit, tombol batal SELALU NYALA (biar bisa cancel edit)
        cancelBtn.disabled = false;
    } else {
        // Kalau mode tambah, cek apakah form kotor (ada isinya)
        let isDirty = false;

        // Cek semua input text/textarea
        const allInputs = form.querySelectorAll('input[type="text"], textarea');
        allInputs.forEach(input => {
            if (input.value.trim() !== "") isDirty = true;
        });

        // Cek input file
        const fileInput = document.getElementById("inputGambar");
        if (fileInput && fileInput.files.length > 0) isDirty = true;

        // Cek tabel member (apakah ada baris di tbody)
        const tableBody = document.querySelector("#teamTable tbody");
        if (tableBody && tableBody.children.length > 0) isDirty = true;

        // Aktifkan/Matikan Tombol Batal
        cancelBtn.disabled = !isDirty;
    }
}

/* =========================================
   FUNGSI RE-ATTACH LISTENER (SOLUSI AJAX)
   ========================================= */
function attachFormValidators() {
    const form = document.getElementById("productForm");
    
    if (form) {
        // Hapus listener lama (opsional, untuk kebersihan memori)
        form.removeEventListener("input", validateFormState);
        form.removeEventListener("change", validateFormState);

        // Pasang listener BARU
        form.addEventListener("input", validateFormState);
        form.addEventListener("change", validateFormState);
        
        // Cek kondisi awal form baru
        validateFormState();
    }
}

// PASANG EVENT LISTENER AGAR REALTIME
// PASANG EVENT LISTENER AGAR REALTIME
document.addEventListener("DOMContentLoaded", function() {
    attachSearchListener();

    // Handle Back/Forward Browser Button
    window.addEventListener('popstate', function(event) {
        loadProductList();
    });

    document.addEventListener("submit", function (e) {
        // ... (KODE SUBMIT TETAP SAMA JANGAN DIUBAH) ...
        // ID Form HARUS 'productForm'
        if (e.target && e.target.id === "productForm") {
             // ... (Isi logika submit kamu yang panjang tadi biarkan saja) ...
             e.preventDefault();
             // ... dst ...
        }
    });

    // PANGGIL FUNGSI INI SAAT PERTAMA KALI LOAD
    attachFormValidators(); 
});