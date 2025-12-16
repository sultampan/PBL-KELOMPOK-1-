// admin/assets/js/produk.js

/* =========================================
   1. FUNGSI GAMBAR & FILE UPLOAD
   ========================================= */
function previewProductImage(event) {
    const input = event.target;
    const imgPreview = document.getElementById("imgPreview");
    const previewContainer = document.getElementById("previewContainer");
    const errorContainer = document.getElementById("fileError");
    
    // Config
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // Reset Error
    if (errorContainer) {
        errorContainer.textContent = "";
        errorContainer.style.display = "none";
    }

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();

        // Validasi Ekstensi
        if (!ALLOWED_EXT.includes(fileExt)) {
            showFileError(input, previewContainer, errorContainer, `Ekstensi .${fileExt} tidak diizinkan.`);
            return;
        }
        // Validasi Ukuran
        if (file.size > MAX_FILE_SIZE) {
            showFileError(input, previewContainer, errorContainer, "File terlalu besar (Max 5MB).");
            return;
        }

        // Tampilkan Preview
        const reader = new FileReader();
        reader.onload = function (e) {
            if (imgPreview) imgPreview.src = e.target.result;
            if (previewContainer) previewContainer.style.display = "flex"; 
        };
        reader.readAsDataURL(file);
    } else {
        // Jika cancel dialog file
        if (imgPreview) imgPreview.src = "";
        if (previewContainer) previewContainer.style.display = "none";
    }
    
    // Trigger update nama file & validasi tombol
    updateProductFileName(input);
}

function showFileError(input, container, errorBox, msg) {
    if (errorBox) {
        errorBox.textContent = msg;
        errorBox.style.display = "block";
    }
    input.value = ""; // Reset input
    if (container) container.style.display = "none";
    updateProductFileName(input);
}

function removeProductImage() {
    const input = document.getElementById("inputGambar");
    const img = document.getElementById("imgPreview");
    const previewContainer = document.getElementById("previewContainer");
    
    if (input) input.value = "";
    if (img) img.src = "";
    if (previewContainer) previewContainer.style.display = "none";
    
    // Set flag hapus gambar lama (hidden input di form-fields.php)
    const removeExisting = document.getElementById("removeExistingImage");
    if (removeExisting) removeExisting.value = "1";
    
    updateProductFileName(input);
}

function updateProductFileName(input) {
    const fileNameText = document.getElementById("fileNameText");
    const removeBtn = document.getElementById("removeImageBtn");
    const removeExisting = document.getElementById("removeExistingImage");

    if (input && input.files && input.files.length > 0) {
        // Ada file baru dipilih
        if (fileNameText) fileNameText.textContent = input.files[0].name;
        if (removeBtn) removeBtn.style.display = "block";
        if (removeExisting) removeExisting.value = "0"; // Jangan hapus, karena mau diganti baru
    } else {
        // Tidak ada file baru (bisa jadi kosong, atau masih pakai gambar lama)
        // Kita cek apakah user sebelumnya klik tombol hapus (removeExisting == 1)
        if (removeExisting && removeExisting.value === "1") {
            if (fileNameText) fileNameText.textContent = "Tidak ada file yang dipilih...";
            if (removeBtn) removeBtn.style.display = "none";
        } else {
            // Kembalikan ke state awal (mungkin gambar lama masih ada)
            // Logic ini opsional, tergantung UX yg dimau. 
            // Defaultnya biarkan teks placeholder.
        }
    }
    validateFormState();
}

/* =========================================
   2. FUNGSI DYNAMIC ROW (TIM PENGEMBANG)
   ========================================= */
function addTeamToTable() {
    const select = document.getElementById("memberSelect");
    const roleInput = document.getElementById("roleInput");
    const tableBody = document.querySelector("#teamTable tbody");

    if (!select || !roleInput || !tableBody) return;

    const memberId = select.value;
    const memberName = select.options[select.selectedIndex].text;
    const role = roleInput.value.trim();

    if (!memberId) { alert("Pilih member terlebih dahulu!"); return; }
    if (role === "") { alert("Role tidak boleh kosong!"); return; }

    // Cek duplikasi ID
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
        <td style="text-align:center;">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeTeamRowTable(this)">✕</button>
        </td>
    `;
    tableBody.appendChild(row);
    
    // Reset input
    select.value = ""; 
    roleInput.value = "";
    
    validateFormState();
}

function removeTeamRowTable(btn) {
    btn.closest("tr").remove();
    validateFormState();
}

/* =========================================
   3. FUNGSI VALIDASI TOMBOL (SIMPAN / BATAL)
   ========================================= */
function validateFormState() {
    const submitBtn = document.getElementById("submitBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const form = document.getElementById("productForm");

    if (!submitBtn || !cancelBtn || !form) return;

    // A. Validasi Tombol SIMPAN (Required Fields)
    const requiredInputs = form.querySelectorAll('[required]');
    let allFilled = true;
    requiredInputs.forEach(input => {
        if (!input.value.trim()) allFilled = false;
    });
    submitBtn.disabled = !allFilled;

    // B. Validasi Tombol BATAL (Dirty State)
    const isEditMode = document.querySelector('input[name="id_produk"]');
    
    if (isEditMode) {
        cancelBtn.disabled = false; // Mode Edit: Batal selalu aktif
    } else {
        // Mode Tambah: Cek apakah user sudah isi sesuatu
        let isDirty = false;
        
        // Cek Text Inputs
        const inputs = form.querySelectorAll('input[type="text"], textarea');
        inputs.forEach(inp => { if(inp.value.trim() !== "") isDirty = true; });
        
        // Cek File Input
        const fileInput = document.getElementById("inputGambar");
        if (fileInput && fileInput.files.length > 0) isDirty = true;
        
        // Cek Tabel Tim (Baris > 0)
        const tableBody = document.querySelector("#teamTable tbody");
        if (tableBody && tableBody.children.length > 0) isDirty = true;

        cancelBtn.disabled = !isDirty;
    }
}

function attachFormValidators() {
    const form = document.getElementById("productForm");
    if (form) {
        // Hapus listener lama biar gak numpuk (safety)
        form.removeEventListener("input", validateFormState);
        form.removeEventListener("change", validateFormState);
        
        // Pasang listener baru
        form.addEventListener("input", validateFormState);
        form.addEventListener("change", validateFormState);
        
        // Cek kondisi awal
        validateFormState();
    }
}

/* =========================================
   4. AJAX CRUD (LOAD, SAVE, DELETE)
   ========================================= */
function loadProductList() {
    const listContainer = document.getElementById("product-list-container");
    if (!listContainer) return;

    const url = "module/produk/table-load.php" + window.location.search + "&_t=" + new Date().getTime();
    
    listContainer.style.opacity = "0.6";
    fetch(url)
        .then(r => r.text())
        .then(html => {
            const temp = document.createElement("div");
            temp.innerHTML = html;
            const content = temp.querySelector("#product-list-container");
            listContainer.innerHTML = content ? content.innerHTML : html;
            listContainer.style.opacity = "1";
            attachSearchListener(); // Re-attach search event
        })
        .catch(e => console.error("Load Table Error:", e));
}

function loadEmptyProductForm(msg) {
    const formContainer = document.getElementById("form-content-wrapper");
    // Load form kosong baru
    fetch("module/produk/form-load.php?_t=" + new Date().getTime())
        .then(r => r.text())
        .then(html => {
            if(formContainer) formContainer.innerHTML = html;
            if(msg) displayAlert(msg, "success");
            attachFormValidators(); // PENTING: Pasang ulang validator
        });
}

function cancelProductForm() {
    const formContainer = document.getElementById("form-content-wrapper");
    formContainer.innerHTML = '<div class="text-center p-3">Resetting...</div>';

    fetch("module/produk/form-load.php")
        .then(r => r.text())
        .then(html => {
            formContainer.innerHTML = html;
            // Bersihkan URL parameter ?edit=...
            const url = new URL(window.location);
            url.searchParams.delete('edit');
            window.history.pushState({}, '', url);
            
            attachFormValidators();
        });
}

function deleteProduct(id) {
    if(!confirm("Yakin hapus produk ini?")) return;
    
    const formData = new FormData();
    formData.append("id", id);
    
    fetch("module/produk/delete.php", { method: "POST", body: formData })
        .then(r => r.json())
        .then(data => {
            if(data.status === 'success') {
                loadProductList();
                displayAlert(data.message, "success");
            } else {
                displayAlert(data.message, "error");
            }
        })
        .catch(() => displayAlert("Gagal menghapus data.", "error"));
}

/* =========================================
   5. SEARCH & UTILS
   ========================================= */
function displayAlert(msg, type) {
    // Buat container toast kalau belum ada
    let container = document.getElementById("toast-container");
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `alert toast ${type}`;
    toast.innerHTML = msg;
    container.appendChild(toast);
    
    // Auto hide
    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function searchProduct() {
    const input = document.getElementById('searchProductInput');
    if (!input) return;
    
    const url = new URL(window.location.href);
    url.searchParams.set('p', '1');
    if (input.value) url.searchParams.set('keyword', input.value);
    else url.searchParams.delete('keyword');
    
    window.history.pushState(null, "", url.toString());
    loadProductList();
}

function attachSearchListener() {
    const input = document.getElementById('searchProductInput');
    if (!input) return;
    
    // Debounce logic
    let timeout;
    
    // Ganti element biar listener lama hilang (clone method)
    const newInput = input.cloneNode(true);
    input.parentNode.replaceChild(newInput, input);
    
    newInput.addEventListener('input', () => {
        clearTimeout(timeout);
        timeout = setTimeout(searchProduct, 500);
    });
    
    newInput.addEventListener('keypress', (e) => {
        if(e.key === 'Enter') {
            clearTimeout(timeout);
            searchProduct();
        }
    });
    
    // Fokus balik (opsional)
    newInput.focus();
    // Taruh kursor di akhir teks
    const val = newInput.value;
    newInput.value = '';
    newInput.value = val;
}

/* =========================================
   6. PAGINATION CLICK
   ========================================= */
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('page-link')) {
        e.preventDefault();
        const href = e.target.getAttribute('href');
        if (href && !e.target.classList.contains('disabled')) {
            window.history.pushState(null, "", href);
            loadProductList();
            
            // Scroll ke atas list
            const list = document.getElementById("product-list-container");
            if(list) list.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
});

/* =========================================
   7. INITIALIZATION (MAIN EVENT)
   ========================================= */
document.addEventListener("DOMContentLoaded", function () {
    
    // A. Setup Awal
    attachSearchListener();
    attachFormValidators(); // <--- WAJIB DIPANGGIL DI SINI

    // B. Handle Browser Back/Forward
    window.addEventListener('popstate', () => loadProductList());

    // C. Handle Form Submit (SINGLE LISTENER)
    document.addEventListener("submit", function (e) {
        if (e.target && e.target.id === "productForm") {
            e.preventDefault();
            
            const form = e.target;
            const btn = document.getElementById("submitBtn");
            const originalText = btn.textContent;
            
            // Disable tombol biar gak bisa klik 2x
            btn.disabled = true;
            btn.textContent = "Menyimpan...";

            const formData = new FormData(form);
            const isUpdate = formData.get("id_produk");

            fetch("module/produk/save.php", { method: "POST", body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.status === "success") {
                        loadProductList();
                        loadEmptyProductForm(data.message);
                        
                        // Bersihkan URL jika mode edit
                        if (isUpdate) {
                            const cleanUrl = window.location.pathname + "?page=produk";
                            window.history.pushState({}, document.title, cleanUrl);
                        }
                    } else {
                        displayAlert(data.message, "error");
                        // Balikin tombol jadi aktif kalau error, biar bisa diedit user
                        btn.disabled = false;
                        btn.textContent = originalText;
                    }
                })
                .catch(err => {
                    console.error(err);
                    displayAlert("Terjadi kesalahan server.", "error");
                    btn.disabled = false;
                    btn.textContent = originalText;
                });
        }
    });
});