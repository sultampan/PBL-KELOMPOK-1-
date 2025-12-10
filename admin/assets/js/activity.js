// admin/assets/js/activity.js

/* =========================================
   1. FUNGSI GAMBAR & FILE UPLOAD
   ========================================= */
function previewActivityImage(event) {
    const input = event.target;
    const imgPreview = document.getElementById("imgPreview");
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
            imgPreview.style.display = "none";
            updateActivityFileName(input);
            return;
        }
        if (file.size > MAX_FILE_SIZE) {
            errorContainer.textContent = "File terlalu besar (Max 5MB).";
            errorContainer.style.display = "block";
            input.value = "";
            imgPreview.style.display = "none";
            updateActivityFileName(input);
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            imgPreview.src = e.target.result;
            imgPreview.style.display = "block";
        };
        reader.readAsDataURL(file);
    } else {
        imgPreview.src = "";
        imgPreview.style.display = "none";
    }
}

function removeActivityImage() {
    const input = document.getElementById("inputGambar");
    const img = document.getElementById("imgPreview");
    const removeBtn = document.getElementById("removeImageBtn");
    const fileNameText = document.getElementById("fileNameText");

    if (input) input.value = "";
    if (img) {
        img.src = "";
        img.style.display = "none";
    }
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
   2. FUNGSI DYNAMIC ROW (TEAM/MEMBER)
   ========================================= */
function addTeamRow() {
    const container = document.getElementById('team-container');
    const template = document.getElementById('teamRowTemplate');
    
    if (container && template) {
        // Clone isi template
        const clone = template.content.cloneNode(true);
        container.appendChild(clone);
    }
}

function removeTeamRow(btn) {
    // Cari elemen induk .link-row terdekat dan hapus
    const row = btn.closest('.link-row');
    if (row) {
        row.remove();
    }
}

/* =========================================
   3. FUNGSI SEARCH (PENCARIAN)
   ========================================= */
function searchActivity() {
    const input = document.getElementById('searchActivityInput');
    if (input) {
        const keyword = input.value;
        // Redirect GET standar agar halaman reload dengan parameter search
        window.location.href = '?page=activity&keyword=' + encodeURIComponent(keyword);
    }
}

function resetSearchActivity() {
    window.location.href = '?page=activity';
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
function loadActivityList() {
    const listContainer = document.getElementById("activity-list-container");
    if (!listContainer) return;
    const currentParams = new URLSearchParams(window.location.search);
    // Pastikan path ini benar sesuai struktur folder kamu
    const url = "module/activity/table-load.php" + window.location.search;

    listContainer.innerHTML = '<div style="text-align:center; padding:20px;">Memuat data...</div>';

    fetch(url).then((response) => response.text()).then((html) => {
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = html;
        const currentPage = parseInt(currentParams.get("p")) || 1;
        const tableBody = tempDiv.querySelector(".table tbody");
        
        // Logika mundur halaman jika data kosong setelah delete
        if (currentPage > 1 && tableBody && tableBody.children.length === 1 && tableBody.querySelector("td[colspan]")) {
            currentParams.set("p", currentPage - 1);
            window.history.pushState(null, "", window.location.pathname + "?" + currentParams.toString());
            loadActivityList();
            return;
        }
        listContainer.innerHTML = html;
        
        // Re-attach search listener karena HTML baru saja direplace
        attachSearchListener(); 

    }).catch((error) => {
        console.error("Error loading table:", error);
        listContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat tabel.</div>';
    });
}

function loadEmptyActivityForm(successMessage) {
    const formContainer = document.getElementById("form-content-wrapper");
    if (!formContainer) return;
    const url = "module/activity/form-load.php?success_msg=" + encodeURIComponent(successMessage);

    displayAlert(successMessage, "success");

    fetch(url).then((response) => response.text()).then((html) => {
        formContainer.innerHTML = html;
        const newNameInput = document.querySelector('#activityForm input[name="judul"]');
        if (newNameInput) setTimeout(() => {
            newNameInput.focus();
        }, 50);
    }).catch((error) => {
        console.error("Error loading form:", error);
    });
}

// Diganti namanya jadi cancelMemberForm agar sesuai dengan onclick di HTML
function cancelMemberForm() {
    const formContainer = document.getElementById("form-content-wrapper");
    if (!formContainer) return;
    formContainer.innerHTML = '<div style="text-align:center; padding:20px;">Mereset form...</div>';

    fetch("module/activity/form-load.php").then((response) => response.text()).then((html) => {
        formContainer.innerHTML = html;
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.delete('edit');
        window.history.pushState({}, '', currentUrl);

        const judulInput = document.querySelector('#activityForm input[name="judul"]');
        if (judulInput) setTimeout(() => {
            judulInput.focus();
        }, 50);

        document.querySelector('.card h2').scrollIntoView({
            behavior: 'smooth'
        });
    }).catch((error) => {
        console.error("Error resetting form:", error);
    });
}

function deleteActivity(id) {
    if (!confirm("Anda yakin ingin menghapus activity ini?")) return;
    const url = "module/activity/delete.php";
    const formData = new FormData();
    formData.append("id", id);
    displayAlert("Menghapus data...", "warning");

    fetch(url, {
        method: "POST",
        body: formData
    })
        .then((response) => response.json()).then((data) => {
            if (data.status === "success") loadActivityList();
            else displayAlert(data.message, "error");
        }).catch((error) => {
            console.error("AJAX Delete Error:", error);
            displayAlert("Terjadi kesalahan jaringan.", "error");
        });
}

// Fungsi helper untuk event listener search (Enter key)
function attachSearchListener() {
    const searchInput = document.getElementById('searchActivityInput');
    if (searchInput) {
        // Hapus listener lama (cloning element trick) biar ga double
        const newInput = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(newInput, searchInput);
        
        newInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchActivity();
        });
        
        // Kembalikan fokus jika hilang setelah replace
        newInput.focus();
    }
}

/* =========================================
   6. EVENT LISTENER UTAMA (DOM READY)
   ========================================= */
document.addEventListener("DOMContentLoaded", function () {
    
    // Pasang listener untuk search pertama kali load
    attachSearchListener();

    document.addEventListener("submit", function (e) {
        // Pastikan ID form sesuai dengan yang di form-fields.php (activityForm)
        if (e.target && e.target.id === "activityForm") {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const url = "module/activity/save.php";

            const submitBtn = document.getElementById("submitBtn");
            submitBtn.disabled = true;
            submitBtn.textContent = "Memproses...";

            fetch(url, {
                method: "POST",
                body: formData
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === "success") {
                        loadActivityList();

                        const isUpdate = formData.get("id_activity"); // Cek Primary Key
                        loadEmptyActivityForm(data.message);

                        if (isUpdate) {
                            window.history.pushState({}, document.title, window.location.pathname + "?page=activity");
                        }
                    } else {
                        // LOGIKA ERROR
                        displayAlert(data.message, "error");

                        const input = document.getElementById('inputGambar');
                        const img = document.getElementById('imgPreview');
                        if (input) input.value = '';
                        if (img) img.style.display = 'none';
                        updateActivityFileName(input);
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
                        const isEditMode = formData.get("id_activity");
                        finalBtn.textContent = isEditMode ? "Update" : "Simpan";
                    }
                });
        }
    });
});