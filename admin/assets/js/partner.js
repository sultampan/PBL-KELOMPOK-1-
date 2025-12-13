// admin/assets/js/partner.js

// Variabel Global untuk menyimpan status awal form
let initialFormState = "";

// =========================================================
// 0. STARTUP (JALANKAN SAAT HALAMAN DIMUAT)
// =========================================================
document.addEventListener("DOMContentLoaded", function() {
    // 1. Inisialisasi Form (Logic tombol simpan/batal)
    initPartnerForm();

    // 2. Inisialisasi Paginasi (Agar muncul saat pindah menu) -> INI YANG KURANG SEBELUMNYA
    initPartnerPagination();
});


// =========================================================
// 1. INITIALIZER FORM
// =========================================================
function initPartnerForm() {
    const form = document.getElementById("partnerForm");
    if (!form) return;

    // Reset listener submit agar tidak double
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);
    
    // Pasang submit listener
    newForm.addEventListener("submit", handlePartnerSubmit);

    // Pasang listener input/change untuk validasi tombol
    const inputs = newForm.querySelectorAll('input, select, textarea');
    inputs.forEach(el => {
        el.addEventListener('input', validatePartnerButtons);
        el.addEventListener('change', validatePartnerButtons);
    });

    // Snapshot data awal untuk fitur "Batal"
    const formData = new FormData(newForm);
    formData.delete("gambar"); 
    initialFormState = new URLSearchParams(formData).toString();

    // Cek status tombol di awal load
    validatePartnerButtons();
}


// =========================================================
// 2. LOGIC TOMBOL & VALIDASI
// =========================================================
function validatePartnerButtons() {
    const form = document.getElementById("partnerForm");
    const btnSimpan = document.getElementById("submitBtn");
    const btnBatal = document.getElementById("btnCancel");
    
    const namaInput = document.getElementById("namaInput");
    const kategoriInput = document.getElementById("kategoriInput");
    const fileInput = document.getElementById("inputGambar");
    const isEditMode = document.querySelector('input[name="id_partner"]'); 

    if(!form || !btnSimpan || !btnBatal || !namaInput || !kategoriInput) return;

    // Ambil Data Dasar
    const nama = namaInput.value.trim();
    const kategori = kategoriInput.value;
    const hasFile = fileInput && fileInput.value !== "";

    // 1. Apakah Data Wajib Terisi?
    const isRequiredFilled = (nama !== "" && kategori !== "");

    // 2. Apakah Ada Perubahan? (Khusus Edit Mode)
    let hasChanges = true; 
    
    if (isEditMode) {
        const currentFormData = new FormData(form);
        currentFormData.delete("gambar"); 
        const currentState = new URLSearchParams(currentFormData).toString();
        
        if (currentState === initialFormState && !hasFile) {
            hasChanges = false;
        }
    }

    // A. LOGIKA TOMBOL SIMPAN
    if(isRequiredFilled && hasChanges) {
        btnSimpan.disabled = false;
        btnSimpan.style.opacity = "1";
        btnSimpan.style.cursor = "pointer";
    } else {
        btnSimpan.disabled = true;
        btnSimpan.style.opacity = "0.6";
        btnSimpan.style.cursor = "not-allowed";
    }

    // B. LOGIKA TOMBOL BATAL
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
// 3. ACTION HANDLERS (SUBMIT, CANCEL, DELETE)
// =========================================================

function cancelPartnerForm() {
    const isEditMode = document.querySelector('input[name="id_partner"]');
    
    if (isEditMode) {
        // Mode Edit: Kembali ke Mode Tambah tanpa refresh
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.delete('edit'); 
        window.history.pushState({}, '', currentUrl);
        loadEmptyPartnerForm(null); 
    } else {
        // Mode Tambah: Reset Form
        const form = document.getElementById("partnerForm");
        if(form) form.reset();
        removePartnerImage(); 
        
        const kategori = document.getElementById("kategoriInput");
        if(kategori) kategori.value = ""; 

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
            loadPartnerList(); // Refresh Tabel
            
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

function deletePartner(id) {
    if(!confirm("Hapus partner ini?")) return;
    const fd = new FormData(); fd.append("id", id);
    fetch("module/partner/delete.php", {method:"POST", body:fd})
    .then(r => r.json())
    .then(data => {
        if (data.status === "success") {
            loadPartnerList(); 
            displayAlert(data.message, "success");
        } else { displayAlert(data.message, "error"); }
    });
}


// =========================================================
// 4. IMAGE FUNCTIONS
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
    if(fileNameText) fileNameText.textContent = "Tidak ada file yang dipilih..."; 
    if(removeExisting) removeExisting.value = "1";
    
    validatePartnerButtons();
}


// =========================================================
// 5. LOADERS (AJAX)
// =========================================================

function loadEmptyPartnerForm(successMessage) {
    const formContainer = document.getElementById("form-content-wrapper"); 
    if (!formContainer) return;

    if(successMessage && successMessage !== "") {
        displayAlert(successMessage, "success");
    }

    fetch("module/partner/form-load.php")
        .then(r => r.text())
        .then(html => {
            formContainer.innerHTML = html;
            initPartnerForm(); 
        });
}

function loadPartnerList() {
    const container = document.getElementById("partner-list-container");
    if (!container) return;
    
    container.style.opacity = "0.5";
    
    // --- PERUBAHAN DISINI: Tambahkan timestamp (&_t=...) ---
    // Ini memaksa browser mengambil data terbaru, bukan cache
    const timestamp = new Date().getTime();
    const url = "module/partner/table-load.php" + window.location.search + "&_t=" + timestamp;

    fetch(url)
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
            container.style.opacity = "1";
            
            // Re-init paginasi
            initPartnerPagination(); 

            // (OPSIONAL) AUTO SCROLL KE BAWAH SETELAH NAMBAH DATA
            // Jika kamu mau setelah nambah data langsung scroll ke bawah kategori, bisa aktifkan ini:
            // const grids = document.querySelectorAll('.partner-grid');
            // if(grids.length > 0) grids[grids.length - 1].scrollIntoView({ behavior: 'smooth' });
        });
}

function searchPartner() {
    const input = document.getElementById('searchPartnerInput');
    const keyword = input ? input.value.trim() : '';
    const url = new URL(window.location.href);
    
    if (keyword) url.searchParams.set('keyword', keyword);
    else url.searchParams.delete('keyword');
    
    // Reset parameter lain
    // (Kita tidak perlu reset ?p=1 karena paginasi diurus JS Client Side)
    
    window.history.pushState(null, "", url);
    loadPartnerList();
}


// =========================================================
// 6. CLIENT-SIDE PAGINATION PER KATEGORI (FIX LENGKAP)
// =========================================================

function initPartnerPagination() {
    // Cari semua grid yang punya class 'paginated-grid'
    const grids = document.querySelectorAll('.paginated-grid');

    if (grids.length === 0) return;

    grids.forEach(grid => {
        const items = grid.querySelectorAll('.grid-item');
        const limit = parseInt(grid.dataset.itemsPerPage) || 6;
        const totalItems = items.length;
        const totalPages = Math.ceil(totalItems / limit);
        
        // Ambil ID unik untuk container paginasi
        const paginationContainerId = grid.id.replace('grid-', 'pagination-');
        const paginationContainer = document.getElementById(paginationContainerId);

        if (totalItems === 0) return;

        // Fungsi Render Halaman Lokal
        const showPage = (page) => {
            grid.dataset.currentPage = page;

            const start = (page - 1) * limit;
            const end = start + limit;

            items.forEach((item, index) => {
                if (index >= start && index < end) {
                    item.style.display = 'flex'; // Tampilkan (flex utk card)
                } else {
                    item.style.display = 'none'; // Sembunyikan
                }
            });

            // Render Tombol Navigasi
            renderPaginationControls(paginationContainer, page, totalPages, showPage);
        };

        // Mulai dari halaman 1
        showPage(1);
    });
}

function renderPaginationControls(container, currentPage, totalPages, onPageClick) {
    if (!container) return;
    if (totalPages <= 1) {
        container.innerHTML = ""; 
        return;
    }

    // --- LOGIKA LIMIT 6 HALAMAN (SLIDING WINDOW) ---
    const maxVisible = 6;
    let startPage = currentPage - 2;
    
    // Koreksi batas bawah
    if (startPage < 1) startPage = 1;
    
    // Koreksi batas atas berdasarkan maxVisible
    let endPage = startPage + maxVisible - 1;
    
    // Jika endPage melebihi total, geser startPage mundur
    if (endPage > totalPages) {
        endPage = totalPages;
        startPage = endPage - maxVisible + 1;
        if (startPage < 1) startPage = 1;
    }

    let html = `<div class="pagination" style="margin-top: 10px; justify-content: center;">`;

    // Helper Class
    const getLinkClass = (active, disabled) => {
        if (active) return 'page-link page-num active';
        if (disabled) return 'page-link page-arrow disabled';
        return 'page-link page-num'; 
    };
    const getArrowClass = (disabled) => disabled ? 'page-link page-arrow disabled' : 'page-link page-arrow';

    // URUTAN SESUAI MEMBER: [Prev] [First] ... [Last] [Next]
    
    // 1. TOMBOL PREV (<) - Paling Kiri
    html += `<a href="javascript:void(0)" class="${getArrowClass(currentPage === 1)}" data-page="${currentPage - 1}" title="Sebelumnya">&lsaquo;</a>`;

    // 2. TOMBOL FIRST PAGE (<<) - Setelah Prev
    html += `<a href="javascript:void(0)" class="${getArrowClass(currentPage === 1)}" data-page="1" title="Halaman Pertama">&laquo;</a>`;

    // 3. ANGKA HALAMAN (Dibatasi Logic Diatas)
    for (let i = startPage; i <= endPage; i++) {
        html += `<a href="javascript:void(0)" class="${getLinkClass(i === currentPage, false)}" data-page="${i}">${i}</a>`;
    }

    // 4. TOMBOL LAST PAGE (>>) - Sebelum Next
    html += `<a href="javascript:void(0)" class="${getArrowClass(currentPage === totalPages)}" data-page="${totalPages}" title="Halaman Terakhir">&raquo;</a>`;

    // 5. TOMBOL NEXT (>) - Paling Kanan
    html += `<a href="javascript:void(0)" class="${getArrowClass(currentPage === totalPages)}" data-page="${currentPage + 1}" title="Selanjutnya">&rsaquo;</a>`;

    html += `</div>`;
    container.innerHTML = html;

    // Pasang Event Listener
    container.querySelectorAll('a').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (btn.classList.contains('disabled') || btn.classList.contains('active')) return;
            
            const targetPage = parseInt(btn.dataset.page);
            onPageClick(targetPage);
        });
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