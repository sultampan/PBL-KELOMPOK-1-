// admin/assets/js/produk.js

/* =========================================
   1. FUNGSI GAMBAR & FILE UPLOAD (LOGIK ACTIVITY ADAPTED)
   ========================================= */
function previewProductImage(event) {
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
            updateProductFileName(input);
            return;
        }
        if (file.size > MAX_FILE_SIZE) {
            errorContainer.textContent = "File terlalu besar (Max 5MB).";
            errorContainer.style.display = "block";
            input.value = "";
            imgPreview.style.display = "none";
            updateProductFileName(input);
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

function removeProductImage() {
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
}

function removeTeamRowTable(btn) {
    btn.closest("tr").remove();
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

        window.location.href = currentUrl.toString();
    }
}

function resetSearchProduct() {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.delete('keyword');
    currentUrl.searchParams.set('p', '1');
    window.location.href = currentUrl.toString();
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

    listContainer.innerHTML = '<div style="text-align:center; padding:20px;">Memuat data...</div>';

    fetch(url).then((response) => response.text()).then((html) => {
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = html;
        const listContent = tempDiv.querySelector("#product-list-container");
        
        // Ambil isi baru, atau fallback ke full html jika selector ga ketemu
        listContainer.innerHTML = listContent ? listContent.innerHTML : html;
        
        attachSearchListener(); // Pasang ulang listener Enter
    }).catch((error) => {
        console.error("Error loading table:", error);
        listContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat tabel.</div>';
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

function attachSearchListener() {
    const searchInput = document.getElementById('searchProductInput');
    if (searchInput) {
        const newInput = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(newInput, searchInput);
        newInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchProduct();
        });
        newInput.focus();
    }
}

/* =========================================
   6. EVENT LISTENER UTAMA
   ========================================= */
document.addEventListener("DOMContentLoaded", function () {
    attachSearchListener();

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
                        // Kembalikan text tombol sesuai kondisi form yang baru diload (biasanya Simpan)
                        finalBtn.textContent = "Simpan"; 
                    }
                });
        }
    });
});