// admin/assets/js/activity.js

/* =========================================
   1. STARTUP & EVENT LISTENERS
   ========================================= */
document.addEventListener("DOMContentLoaded", function () {
    
    // 1. Inisialisasi Paginasi saat halaman dimuat
    initActivityPagination();

    // 2. Pasang Listener Search (Enter Key)
    attachSearchListener();
    
    // 3. Cek Scroll (jika habis reload)
    const scrollTarget = sessionStorage.getItem('scrollToCategory');
    if (scrollTarget) {
        setTimeout(() => {
            const categorySection = document.querySelector(`[data-category="${scrollTarget}"]`);
            if (categorySection) {
                categorySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            sessionStorage.removeItem('scrollToCategory');
        }, 100);
    }

    // 4. Listener Submit Form Global
    document.addEventListener("submit", function (e) {
        if (e.target && e.target.id === "activityForm") {
            handleActivitySubmit(e);
        }
    });
});


/* =========================================
   2. CLIENT-SIDE PAGINATION (STYLE PARTNER + FIRST/LAST)
   ========================================= */

function initActivityPagination() {
    const grids = document.querySelectorAll('.paginated-grid');

    if (grids.length === 0) return;

    grids.forEach(grid => {
        const items = grid.querySelectorAll('.grid-item');
        const limit = parseInt(grid.dataset.itemsPerPage) || 6; 
        const totalItems = items.length;
        const totalPages = Math.ceil(totalItems / limit);
        
        // Ambil ID container pagination
        const paginationContainerId = grid.id.replace('grid-', 'pagination-');
        const paginationContainer = document.getElementById(paginationContainerId);

        if (totalItems === 0) return;

        // Fungsi Tampilkan Halaman
        const showPage = (page) => {
            grid.dataset.currentPage = page;

            const start = (page - 1) * limit;
            const end = start + limit;

            items.forEach((item, index) => {
                if (index >= start && index < end) {
                    item.style.display = 'flex'; 
                    item.style.opacity = '0';
                    setTimeout(() => item.style.opacity = '1', 50);
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
    
    if (totalPages <= 1) {
        container.innerHTML = ""; 
        return;
    }

    // --- LOGIKA SLIDING WINDOW ---
    const maxVisible = 6;
    let startPage = currentPage - 2;
    
    if (startPage < 1) startPage = 1;
    
    let endPage = startPage + maxVisible - 1;
    
    if (endPage > totalPages) {
        endPage = totalPages;
        startPage = endPage - maxVisible + 1;
        if (startPage < 1) startPage = 1;
    }

    // --- HTML GENERATOR (STYLE SAMA DENGAN PARTNER) ---
    // Menggunakan class dari components.css (.page-link, .page-num, .page-arrow)
    let html = `<div class="pagination" style="margin-top: 20px; justify-content: center; display: flex; gap: 5px;">`;

    // Helper untuk generate class string
    const getClass = (type, active, disabled) => {
        let cls = 'page-link'; // Class dasar
        if (type === 'num') cls += ' page-num';
        if (type === 'arrow') cls += ' page-arrow';
        if (active) cls += ' active';
        if (disabled) cls += ' disabled';
        return cls;
    };

    // 1. TOMBOL PREV (<)
    html += `<a href="javascript:void(0)" class="${getClass('arrow', false, currentPage === 1)}" data-page="${currentPage - 1}" title="Sebelumnya">&lsaquo;</a>`;

    // 2. TOMBOL FIRST PAGE (<<) - INI YANG KAMU MINTA
    html += `<a href="javascript:void(0)" class="${getClass('arrow', false, currentPage === 1)}" data-page="1" title="Halaman Pertama">&laquo;</a>`;

    // 3. ANGKA HALAMAN
    for (let i = startPage; i <= endPage; i++) {
        html += `<a href="javascript:void(0)" class="${getClass('num', i === currentPage, false)}" data-page="${i}">${i}</a>`;
    }

    // 4. TOMBOL LAST PAGE (>>) - INI YANG KAMU MINTA
    html += `<a href="javascript:void(0)" class="${getClass('arrow', false, currentPage === totalPages)}" data-page="${totalPages}" title="Halaman Terakhir">&raquo;</a>`;

    // 5. TOMBOL NEXT (>)
    html += `<a href="javascript:void(0)" class="${getClass('arrow', false, currentPage === totalPages)}" data-page="${currentPage + 1}" title="Selanjutnya">&rsaquo;</a>`;

    html += `</div>`;
    container.innerHTML = html;

    // Pasang Event Listener
    container.querySelectorAll('a').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            // Cek apakah tombol disabled atau sedang aktif
            if (btn.classList.contains('disabled') || btn.classList.contains('active')) return;
            
            const targetPage = parseInt(btn.dataset.page);
            if (!isNaN(targetPage) && targetPage > 0 && targetPage <= totalPages) {
                onPageClick(targetPage);
            }
        });
    });
}


/* =========================================
   3. FUNGSI GAMBAR & FILE UPLOAD
   ========================================= */
function previewActivityImage(event) {
    const input = event.target;
    const imgPreview = document.getElementById("imgPreview");
    const previewBox = document.getElementById("previewBox"); 
    const errorContainer = document.getElementById("fileError");
    
    const MAX_FILE_SIZE = 5 * 1024 * 1024; 
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


/* =========================================
   4. FUNGSI TEAM MEMBER & FILTER
   ========================================= */
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

let memberSearchTimeout = null;
function filterMemberSelection() {
    clearTimeout(memberSearchTimeout);
    memberSearchTimeout = setTimeout(() => {
        const input = document.getElementById('searchMemberInput');
        const filter = input.value.toLowerCase();
        
        const container = document.getElementById('memberListContainer');
        const items = container.getElementsByClassName('member-item');
        const noResult = document.getElementById('noMemberFound');
        
        let visibleCount = 0;
        for (let i = 0; i < items.length; i++) {
            const label = items[i].getElementsByTagName("span")[0];
            const txtValue = label.textContent || label.innerText;
            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                items[i].style.display = ""; 
                visibleCount++;
            } else {
                items[i].style.display = "none";
            }
        }
        if (noResult) noResult.style.display = (visibleCount === 0) ? "block" : "none";
    }, 300);
}


/* =========================================
   5. SEARCH & AJAX LOAD (Tanpa Reload)
   ========================================= */

function searchActivity() {
    const input = document.getElementById('searchActivityInput');
    const keyword = input ? input.value.trim() : '';
    
    const url = new URL(window.location.href);
    if (keyword) {
        url.searchParams.set('keyword', keyword);
    } else {
        url.searchParams.delete('keyword');
    }
    
    // Update URL bar
    window.history.pushState(null, "", url);
    
    // Reload tabel
    loadActivityList();
}

function attachSearchListener() {
    const searchInput = document.getElementById('searchActivityInput');
    if (searchInput) {
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

    listContainer.style.opacity = "0.5";

    const timestamp = new Date().getTime();
    const url = "module/activity/table-load.php" + window.location.search + "&_t=" + timestamp;

    fetch(url)
    .then(response => response.text())
    .then(html => {
        listContainer.innerHTML = html;
        listContainer.style.opacity = "1";
        
        // Re-init Paginasi agar tombol muncul kembali
        initActivityPagination();
        
        attachSearchListener();
    })
    .catch(error => {
        console.error("Error loading table:", error);
        listContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat tabel.</div>';
        listContainer.style.opacity = "1";
    });
}


/* =========================================
   6. FORM HANDLERS (SAVE & DELETE)
   ========================================= */

function handleActivitySubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const url = "module/activity/save.php";

    const submitBtn = document.getElementById("submitBtn");
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = "Memproses...";

    fetch(url, { method: "POST", body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            displayAlert(data.message, "success");
            
            // 1. Refresh Table
            loadActivityList();

            // 2. Reset Form
            cancelMemberForm();
            
            // 3. Scroll ke atas
            const tableArea = document.getElementById("activity-list-container");
            if (tableArea) tableArea.scrollIntoView({ behavior: 'smooth', block: 'start' });

        } else {
            displayAlert(data.message, "error");
            
            // Reset gambar jika error upload
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
    
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.delete('edit');
    window.history.pushState({}, '', currentUrl);

    fetch("module/activity/form-load.php")
    .then(response => response.text())
    .then(html => {
        formContainer.innerHTML = html;
        document.querySelector('.card h2').scrollIntoView({ behavior: 'smooth' });
    });
}

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
    
    setTimeout(() => toast.classList.add('show'), 10);

    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => {
            toast.remove();
            if (toastContainer.children.length === 0) toastContainer.remove();
        }, 300);
    }, 4000);
}