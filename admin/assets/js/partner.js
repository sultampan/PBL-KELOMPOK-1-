// admin/assets/js/partner.js

// Jalankan saat halaman pertama kali dimuat
document.addEventListener("DOMContentLoaded", function() {
    initPartnerForm();
});

// =========================================================
// 1. INITIALIZER (PENTING: DIPANGGIL SETIAP FORM DILOAD)
// =========================================================
function initPartnerForm() {
    const form = document.getElementById("partnerForm");
    if (!form) return;

    // Reset listener submit (clone node)
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);
    
    // Pasang submit listener
    newForm.addEventListener("submit", handlePartnerSubmit);

    // Pasang listener input (ketik/pilih) -> Trigger validasi tombol
    const inputs = newForm.querySelectorAll('input, select, textarea');
    inputs.forEach(el => {
        el.addEventListener('input', validatePartnerButtons);
        el.addEventListener('change', validatePartnerButtons);
    });

    // Cek status tombol sekarang juga
    validatePartnerButtons();
}

// =========================================================
// 2. LOGIC TOMBOL (VALIDASI)
// =========================================================
function validatePartnerButtons() {
    const btnSimpan = document.getElementById("submitBtn");
    const btnBatal = document.getElementById("btnCancel");
    
    const namaInput = document.getElementById("namaInput");
    const kategoriInput = document.getElementById("kategoriInput");
    const fileInput = document.getElementById("inputGambar");
    const isEditMode = document.querySelector('input[name="id_partner"]'); 

    if(!btnSimpan || !btnBatal || !namaInput || !kategoriInput) return;

    // Ambil Data
    const nama = namaInput.value.trim();
    const kategori = kategoriInput.value;
    const hasFile = fileInput && fileInput.value !== "";

    // A. TOMBOL SIMPAN (Wajib Nama & Kategori)
    if(nama !== "" && kategori !== "") {
        btnSimpan.disabled = false;
        btnSimpan.style.opacity = "1";
        btnSimpan.style.cursor = "pointer";
    } else {
        btnSimpan.disabled = true;
        btnSimpan.style.opacity = "0.6";
        btnSimpan.style.cursor = "not-allowed";
    }

    // B. TOMBOL BATAL
    // Nyala jika: Mode Edit ATAU Form Kotor
    const isFormDirty = (nama !== "" || (kategori !== "" && kategori !== null) || hasFile);
    
    if(isEditMode || isFormDirty) {
        btnBatal.disabled = false;
        btnBatal.style.opacity = "1";
        btnBatal.style.cursor = "pointer";
    } else {
        btnBatal.disabled = true;
        btnBatal.style.opacity = "0.6";
        btnBatal.style.cursor = "not-allowed";
    }
}

// =========================================================
// 3. ACTION HANDLERS (CANCEL & SUBMIT)
// =========================================================

// Fungsi dipanggil oleh onclick="cancelPartnerForm()" di HTML
function cancelPartnerForm() {
    const isEditMode = document.querySelector('input[name="id_partner"]');
    
    if (isEditMode) {
        // Mode Edit -> Keluar ke mode tambah
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.delete('edit'); 
        window.history.pushState({}, '', currentUrl);
        loadEmptyPartnerForm("Mode edit dibatalkan.");
    } else {
        // Mode Tambah -> Reset Form
        const form = document.getElementById("partnerForm");
        if(form) form.reset();
        
        removePartnerImage(); 
        
        // Reset Kategori
        const kategori = document.getElementById("kategoriInput");
        if(kategori) kategori.value = ""; 

        // Validasi ulang (PENTING: Ini yang bikin tombol Batal mati lagi)
        validatePartnerButtons(); 
    }
}

function handlePartnerSubmit(e) {
    e.preventDefault(); 
    const form = e.target;
    const btn = document.getElementById("submitBtn");
    const originalText = btn.textContent;
    
    btn.disabled = true; btn.textContent = "Memproses...";
    const formData = new FormData(form);

    fetch("module/partner/save.php", { method: "POST", body: formData })
    .then(r => r.text())
    .then(text => {
        try { return JSON.parse(text); } 
        catch (err) { throw new Error("Server Error."); }
    })
    .then(data => {
        if(data.status === 'success') {
            loadPartnerList();
            const isUpdate = formData.get("id_partner");
            if (isUpdate) {
                const url = new URL(window.location);
                url.searchParams.delete('edit');
                window.history.pushState({}, document.title, url);
                loadEmptyPartnerForm("Update Berhasil!");
            } else {
                loadEmptyPartnerForm(data.message);
            }
        } else {
            displayAlert(data.message, "error");
            btn.disabled = false; btn.textContent = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        displayAlert("Terjadi kesalahan.", "error");
        btn.disabled = false; btn.textContent = originalText;
    });
}

// =========================================================
// 4. IMAGE FUNCTIONS (GLOBAL)
// =========================================================

function previewPartnerImage(e) {
    const file = e.target.files[0];
    const imgPreview = document.getElementById("imgPreview");
    const previewContainer = document.getElementById("previewContainer");
    const removeBtn = document.getElementById("removeImageBtn");
    const fileNameText = document.getElementById("fileNameText");
    const removeExisting = document.getElementById("removeExistingImage");
    const errorContainer = document.getElementById("fileError");

    if(errorContainer) errorContainer.style.display = 'none';

    if(file) {
        if (file.size > 5 * 1024 * 1024) {
            if(errorContainer) {
                errorContainer.textContent = "Ukuran file maksimal 5MB.";
                errorContainer.style.display = 'block';
            }
            e.target.value = ""; 
            validatePartnerButtons();
            return;
        }

        if(imgPreview) imgPreview.src = URL.createObjectURL(file);
        if(previewContainer) previewContainer.style.display = "flex";
        if(removeBtn) removeBtn.style.display = "block";
        if(fileNameText) fileNameText.textContent = file.name;
        if(removeExisting) removeExisting.value = "0";
    }
    validatePartnerButtons();
}

function updatePartnerFileName() { validatePartnerButtons(); }

function removePartnerImage() {
    const input = document.getElementById("inputGambar");
    const previewContainer = document.getElementById("previewContainer");
    const removeBtn = document.getElementById("removeImageBtn");
    const fileNameText = document.getElementById("fileNameText");
    const removeExisting = document.getElementById("removeExistingImage");

    if(input) input.value = "";
    if(previewContainer) previewContainer.style.display = "none";
    if(removeBtn) removeBtn.style.display = "none";
    
    // Kembalikan teks default
    if(fileNameText) fileNameText.textContent = "Tidak ada file yang dipilih..."; 
    
    if(removeExisting) removeExisting.value = "1";
    validatePartnerButtons();
}

// =========================================================
// 5. LOADERS & HELPERS
// =========================================================

function loadEmptyPartnerForm(successMessage) {
    const formContainer = document.getElementById("form-content-wrapper"); 
    if (!formContainer) return;
    if(successMessage) displayAlert(successMessage, "success");

    fetch("module/partner/form-load.php")
        .then(r => r.text())
        .then(html => {
            formContainer.innerHTML = html;
            // Panggil init lagi untuk pasang listener di form baru
            initPartnerForm(); 
        });
}

function loadPartnerList() {
    const container = document.getElementById("partner-list-container");
    if (!container) return;
    container.style.opacity = "0.5";
    fetch("module/partner/table-load.php" + window.location.search)
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
            container.style.opacity = "1";
        });
}

function searchPartner() {
    const input = document.getElementById('searchPartnerInput');
    const keyword = input ? input.value.trim() : '';
    const url = new URL(window.location.href);
    if (keyword) url.searchParams.set('keyword', keyword);
    else url.searchParams.delete('keyword');
    url.searchParams.set('p', 1);
    window.history.pushState(null, "", url);
    loadPartnerList();
}

function deletePartner(id) {
    if(!confirm("Hapus partner ini?")) return;
    const fd = new FormData(); fd.append("id", id);
    fetch("module/partner/delete.php", {method:"POST", body:fd})
    .then(r => r.json())
    .then(data => {
        if (data.status === "success") {
            loadPartnerList(); displayAlert(data.message, "success");
        } else { displayAlert(data.message, "error"); }
    });
}

function displayAlert(message, type) {
    let container = document.getElementById("toast-container");
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `alert toast ${type}`;
    toast.innerHTML = message;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = "1"; toast.style.transform = "translateX(0)"; }, 10);
    setTimeout(() => { 
        toast.classList.add('hide'); 
        setTimeout(() => { toast.remove(); if(container.children.length===0) container.remove(); }, 300); 
    }, 4000);
}