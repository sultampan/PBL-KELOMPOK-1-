// admin/assets/js/produk.js

/* =========================================
   1. FUNGSI GAMBAR & FILE UPLOAD (LOGIKA SAMA DENGAN FASILITAS)
   ========================================= */

function previewProductImage(event) {
    const input = event.target;
    const previewContainer = document.getElementById("previewContainer");
    const imgPreview = document.getElementById("imgPreview");
    const errorContainer = document.getElementById("fileError");

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

        // Validasi Ekstensi & Ukuran
        if (!ALLOWED_EXT.includes(fileExt) || file.size > MAX_FILE_SIZE) {
            const msg = !ALLOWED_EXT.includes(fileExt) ? "Ekstensi tidak diizinkan." : "File max 5MB.";
            
            if (errorContainer) {
                errorContainer.textContent = msg;
                errorContainer.style.display = "block";
            }
            
            input.value = ""; // Reset input
            if (previewContainer) previewContainer.style.display = "none";
            updateProductFileName(input);
            return;
        }

        // Tampilkan Preview
        const reader = new FileReader();
        reader.onload = function (e) {
            if (imgPreview && previewContainer) {
                imgPreview.src = e.target.result;
                imgPreview.style.display = "block";
                previewContainer.style.display = "flex"; // Gunakan flex untuk centering
            }
        };
        reader.readAsDataURL(file);
    } else {
        if (previewContainer) previewContainer.style.display = "none";
    }
}

function updateProductFileName(input) {
    const fileNameText = document.getElementById("fileNameText");
    const removeBtn = document.getElementById("removeImageBtn");
    
    if (input.files && input.files.length > 0) {
        fileNameText.textContent = input.files[0].name;
        fileNameText.style.color = "#333"; // Warna teks aktif
        if (removeBtn) removeBtn.style.display = "block";
    } else {
        fileNameText.textContent = "Tidak ada file yang dipilih...";
        fileNameText.style.color = "#aaa"; // Warna placeholder
        if (removeBtn) removeBtn.style.display = "none";
    }
    
    // Jika user memilih file baru, batalkan flag hapus gambar lama
    const removeExisting = document.getElementById("removeExistingImage");
    if (removeExisting && input.files.length > 0) {
        removeExisting.value = "0";
    }
}

function removeProductImage() {
    const input = document.getElementById("inputGambar");
    const previewContainer = document.getElementById("previewContainer");
    const imgPreview = document.getElementById("imgPreview");
    const removeBtn = document.getElementById("removeImageBtn");
    const fileNameText = document.getElementById("fileNameText");

    // Reset Input File
    if (input) input.value = "";
    
    // Sembunyikan Preview
    if (imgPreview) imgPreview.src = "";
    if (previewContainer) previewContainer.style.display = "none";
    
    // Reset Text Label
    if (fileNameText) {
        fileNameText.textContent = "Tidak ada file yang dipilih...";
        fileNameText.style.color = "#aaa";
    }
    
    // Sembunyikan Tombol X
    if (removeBtn) removeBtn.style.display = "none";

    // Set Flag Hapus Gambar Lama (untuk Backend)
    const removeExisting = document.getElementById("removeExistingImage");
    if (removeExisting) removeExisting.value = "1";
}

/* =========================================
   2. FUNGSI DYNAMIC ROW (TEAM - MEMBER + ROLE)
   ========================================= */
function addTeamToTable() {
    const select = document.getElementById("memberSelect");
    const roleInput = document.getElementById("roleInput");
    const tableBody = document.querySelector("#teamTable tbody");

    // Validasi input
    if (!select || !roleInput) return;

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
    
    // Reset input setelah tambah
    select.value = ""; 
    roleInput.value = "";
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

function attachSearchListener() {
    const searchInput = document.getElementById('searchProductInput');
    if (searchInput) {
        // Hindari duplikasi listener dengan clone
        const newInput = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(newInput, searchInput);
        newInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchProduct();
        });
        // Fokus otomatis jika ada nilai (optional)
        // newInput.focus();
    }
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
    
    const url = "module/produk/table-load.php" + window.location.search + "&_t=" + new Date().getTime();

    listContainer.style.opacity = "0.5";

    fetch(url).then((response) => response.text()).then((html) => {
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = html;
        const listContent = tempDiv.querySelector("#product-list-container");
        
        listContainer.innerHTML = listContent ? listContent.innerHTML : html;
        listContainer.style.opacity = "1";
        
        attachSearchListener(); 
    }).catch((error) => {
        console.error("Error loading table:", error);
        listContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat tabel.</div>';
    });
}

function loadEmptyProductForm(successMessage) {
    const formContainer = document.getElementById("form-content-wrapper");
    if (!formContainer) return;
    
    const url = "module/produk/form-load.php?_t=" + new Date().getTime();

    if(successMessage) displayAlert(successMessage, "success");

    fetch(url).then((response) => response.text()).then((html) => {
        formContainer.innerHTML = html;
        // Scroll ke atas sedikit jika perlu
        const header = document.querySelector('.card h2');
        if(header) header.scrollIntoView({ behavior: 'smooth' });
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
                loadProductList(); 
                displayAlert(data.message, "success");
            } else {
                displayAlert(data.message, "error");
            }
        }).catch((error) => {
            displayAlert("Terjadi kesalahan jaringan.", "error");
        });
}

/* =========================================
   6. EVENT LISTENER UTAMA
   ========================================= */
document.addEventListener("DOMContentLoaded", function () {
    attachSearchListener();

    document.addEventListener("submit", function (e) {
        if (e.target && e.target.id === "productForm") {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const url = "module/produk/save.php";

            const submitBtn = document.getElementById("submitBtn");
            if(submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = "Memproses...";
            }

            fetch(url, { method: "POST", body: formData })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === "success") {
                        loadProductList(); 
                        
                        // Cek update
                        const isUpdate = formData.get("id_produk"); 
                        
                        loadEmptyProductForm(data.message);

                        if (isUpdate) {
                            const cleanUrl = window.location.pathname + "?page=produk";
                            window.history.pushState({}, document.title, cleanUrl);
                        }
                    } else {
                        displayAlert(data.message, "error");
                        // Jika gagal, reset input file agar user bisa coba lagi
                        const input = document.getElementById('inputGambar');
                        if (input) {
                            input.value = '';
                            updateProductFileName(input);
                        }
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