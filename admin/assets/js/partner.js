// admin/assets/js/activity.js

// =========================================================
// 1. STARTUP & EVENT LISTENERS
// =========================================================
document.addEventListener("DOMContentLoaded", function () {
    
    // Inisialisasi Paginasi saat halaman dimuat
    initActivityPagination();

    // Listener untuk Search (Enter Key)
    attachSearchListener();
    
    // Listener Submit Form
    document.addEventListener("submit", function (e) {
        if (e.target && e.target.id === "activityForm") {
            handleActivitySubmit(e);
        }
    });
});


// =========================================================
// 2. CLIENT-SIDE PAGINATION (ADAPTASI DARI PARTNER)
// =========================================================

function initActivityPagination() {
    // Cari semua grid yang punya class 'paginated-grid'
    const grids = document.querySelectorAll('.paginated-grid');

    if (grids.length === 0) return;

    grids.forEach(grid => {
        // Reset state jika perlu
        const items = grid.querySelectorAll('.grid-item');
        const limit = parseInt(grid.dataset.itemsPerPage) || 6; // Default 6 item per halaman
        const totalItems = items.length;
        const totalPages = Math.ceil(totalItems / limit);
        
        // Ambil ID unik untuk container paginasi
        // Asumsi ID grid: grid-md5hash -> Pagination ID: pagination-md5hash
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
                    // Pastikan display sesuai CSS asli activity-item (flex)
                    item.style.display = 'flex'; 
                    // Tambahkan animasi fade-in halus (opsional)
                    item.style.animation = 'fadeIn 0.3s ease';
                } else {
                    item.style.display = 'none'; 
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
    
    // Jika halaman cuma 1, sembunyikan paginasi
    if (totalPages <= 1) {
        container.innerHTML = ""; 
        return;
    }

    // --- LOGIKA SLIDING WINDOW (Mirip Partner) ---
    const maxVisible = 5;
    let startPage = currentPage - 2;
    
    if (startPage < 1) startPage = 1;
    
    let endPage = startPage + maxVisible - 1;
    
    if (endPage > totalPages) {
        endPage = totalPages;
        startPage = endPage - maxVisible + 1;
        if (startPage < 1) startPage = 1;
    }

    let html = `<div class="pagination" style="margin-top: 20px; justify-content: center; gap: 5px;">`;

    // Helper Class Styles (Sesuaikan dengan CSS Activity/Partner)
    const btnBase = "min-width: 40px; height: 40px; border: 1px solid #ddd; background: #fff; color: #333; cursor: pointer; border-radius: 4px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-weight: 600;";
    const btnActive = "background-color: #02406C; color: white; border-color: #02406C;";
    const btnDisabled = "background-color: #f5f5f5; color: #ccc; cursor: not-allowed;";

    const getStyle = (isActive, isDisabled) => {
        if (isActive) return `${btnBase} ${btnActive}`;
        if (isDisabled) return `${btnBase} ${btnDisabled}`;
        return btnBase;
    };

    // 1. PREV
    html += `<a href="javascript:void(0)" style="${getStyle(false, currentPage === 1)}" data-page="${currentPage - 1}">&lsaquo;</a>`;

    // 2. ANGKA
    for (let i = startPage; i <= endPage; i++) {
        html += `<a href="javascript:void(0)" style="${getStyle(i === currentPage, false)}" data-page="${i}">${i}</a>`;
    }

    // 3. NEXT
    html += `<a href="javascript:void(0)" style="${getStyle(false, currentPage === totalPages)}" data-page="${currentPage + 1}">&rsaquo;</a>`;

    html += `</div>`;
    container.innerHTML = html;

    // Pasang Event Listener
    container.querySelectorAll('a').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            // Cek apakah disabled atau active (cek style text color atau background)
            if (btn.style.color === 'rgb(204, 204, 204)' || btn.style.backgroundColor === 'rgb(2, 64, 108)') return;
            
            const targetPage = parseInt(btn.dataset.page);
            if (!isNaN(targetPage) && targetPage > 0 && targetPage <= totalPages) {
                onPageClick(targetPage);
            }
        });
    });
}


// =========================================================
// 3. IMAGE PREVIEW & FILE VALIDATION
// =========================================================
function previewActivityImage(event) {
    const input = event.target;
    const imgPreview = document.getElementById("imgPreview");
    const previewBox = document.getElementById("previewBox"); 
    const errorContainer = document.getElementById("fileError");
    
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (errorContainer) {
        errorContainer.textContent = "";
        errorContainer.style.display = "none";
    }

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();

        if (!ALLOWED_EXT.includes(fileExt)) {
            showFileError(input, previewBox, "Ekstensi file harus JPG, PNG, GIF, atau WEBP.");
            updateActivityFileName(input);
            return;
        }
        if (file.size > MAX_FILE_SIZE) {
            showFileError(input, previewBox, "Ukuran file terlalu besar (Max 5MB).");
            updateActivityFileName(input);
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            imgPreview.src = e.target.result;
            if(previewBox) previewBox.style.display = "flex";
        };
        reader.readAsDataURL(file);
    } else {
        if(imgPreview) imgPreview.src = "";
        if(previewBox) previewBox.style.display = "none";
    }
    updateActivityFileName(input);
}

function showFileError(input, previewBox, msg) {
    const errorContainer = document.getElementById("fileError");
    if(errorContainer) {
        errorContainer.textContent = msg;
        errorContainer.style.display = "block";
    }
    input.value = "";
    if(previewBox) previewBox.style.display = "none";
}

function removeActivityImage() {
    const input = document.getElementById("inputGambar");
    const img = document.getElementById("imgPreview");
    const previewBox = document.getElementById("previewBox");
    const removeBtn = document.getElementById("removeImageBtn");
    const fileNameText = document.getElementById("fileNameText");

    if (input) input.value = "";
    if (img) img.src = "";
    if (previewBox) previewBox.style.display = "none";

    if (fileNameText) fileNameText.textContent = "Tidak ada file yang dipilih...";
    if (removeBtn) removeBtn.style.display = "none";

    const removeExisting = document.getElementById("removeExistingImage");
    if (removeExisting) removeExisting.value = "1";
}

function updateActivityFileName(input) {
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
}


// =========================================================
// 4. DYNAMIC ROWS (TEAM MEMBERS)
// =========================================================
function addTeamRow() {
    const container = document.getElementById('team-container');
    const template = document.getElementById('teamRowTemplate');
    if (container && template) {
        const clone = template.content.cloneNode(true);
        container.appendChild(clone);
    }
}

function removeTeamRow(btn) {
    const row = btn.closest('.link-row');
    if (row) row.remove();
}


// =========================================================
// 5. SEARCH & AJAX LOAD (MIRIP PARTNER)
// =========================================================

function searchActivity() {
    const input = document.getElementById('searchActivityInput');
    const keyword = input ? input.value.trim() : '';
    
    const url = new URL(window.location.href);
    if (keyword) {
        url.searchParams.set('keyword', keyword);
    } else {
        url.searchParams.delete('keyword');
    }
    
    // Update URL tanpa reload
    window.history.pushState(null, "", url);
    
    // Reload Tabel via AJAX
    loadActivityList();
}

function resetSearchActivity() {
    const url = new URL(window.location.href);
    url.searchParams.delete('keyword');
    window.history.pushState(null, "", url);
    
    const input = document.getElementById('searchActivityInput');
    if(input) input.value = "";
    
    loadActivityList();
}

function attachSearchListener() {
    const searchInput = document.getElementById('searchActivityInput');
    if (searchInput) {
        // Hapus listener lama (cloning trick)
        const newInput = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(newInput, searchInput);
        
        newInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchActivity();
        });
    }
}

function loadActivityList() {
    const listContainer = document.getElementById("activity-list-container");
    if (!listContainer) return;

    listContainer.style.opacity = "0.5"; // Efek loading

    // Tambahkan timestamp agar tidak cache
    const timestamp = new Date().getTime();
    const url = "module/activity/table-load.php" + window.location.search + "&_t=" + timestamp;

    fetch(url)
    .then(response => response.text())
    .then(html => {
        listContainer.innerHTML = html;
        listContainer.style.opacity = "1";
        
        // --- PENTING: Inisialisasi Ulang Paginasi Setelah Konten Berubah ---
        initActivityPagination();
        
        // Pasang ulang listener search di elemen baru
        attachSearchListener();
    })
    .catch(error => {
        console.error("Error loading table:", error);
        listContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat tabel.</div>';
        listContainer.style.opacity = "1";
    });
}


// =========================================================
// 6. FORM HANDLERS (SAVE, EDIT, DELETE)
// =========================================================

function handleActivitySubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const url = "module/activity/save.php";

    const submitBtn = document.getElementById("submitBtn");
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = "Memproses...";

    fetch(url, {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            displayAlert(data.message, "success");
            
            // Refresh Tabel & Paginasi
            loadActivityList();

            // Reset Form ke mode tambah
            cancelMemberForm(); // Nama fungsi existing kamu
            
            // Scroll ke atas
            const tableArea = document.getElementById("activity-list-container");
            if (tableArea) tableArea.scrollIntoView({ behavior: 'smooth', block: 'start' });

        } else {
            displayAlert(data.message, "error");
            
            // Reset input file jika error upload
            const input = document.getElementById('inputGambar');
            if (input && data.message.includes("upload")) {
                 removeActivityImage();
            }
        }
    })
    .catch(error => {
        console.error("AJAX Error:", error);
        displayAlert("Terjadi kesalahan jaringan.", "error");
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

function deleteActivity(id) {
    if (!confirm("Anda yakin ingin menghapus activity ini?")) return;
    
    const url = "module/activity/delete.php";
    const formData = new FormData();
    formData.append("id", id);

    fetch(url, { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            displayAlert(data.message, "success");
            loadActivityList();
            
            // Reset form jika sedang mengedit item yang dihapus
            const currentEditId = document.querySelector('input[name="id_activity"]');
            if (currentEditId && currentEditId.value == id) {
                cancelMemberForm();
            }
        } else {
            displayAlert(data.message, "error");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        displayAlert("Gagal menghapus data.", "error");
    });
}

function cancelMemberForm() {
    const formContainer = document.getElementById("form-content-wrapper");
    if (!formContainer) return;
    
    // Hapus parameter edit dari URL
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.delete('edit');
    window.history.pushState({}, '', currentUrl);

    fetch("module/activity/form-load.php")
    .then(response => response.text())
    .then(html => {
        formContainer.innerHTML = html;
        // Scroll ke form
        document.querySelector('.card h2').scrollIntoView({ behavior: 'smooth' });
    });
}


// =========================================================
// 7. UTILITIES
// =========================================================
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
    
    // Animasi masuk
    toast.style.opacity = "0";
    toast.style.transform = "translateX(100%)";
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transition = "all 0.3s ease";
        toast.style.opacity = "1";
        toast.style.transform = "translateX(0)";
    }, 10);

    setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transform = "translateX(100%)";
        setTimeout(() => {
            toast.remove();
            if (toastContainer.children.length === 0) toastContainer.remove();
        }, 300);
    }, 4000);
}