// admin/assets/js/produk.js

// --- 1. FUNGSI DINAMIS TIM (MEMBER + ROLE) ---
function addTeamRow() {
    const container = document.getElementById('team-container');
    const template = document.getElementById('teamRowTemplate');
    
    if (container && template) {
        // Clone isi template
        const clone = template.content.cloneNode(true);
        container.appendChild(clone);
        
        // Trigger validasi biar tombol simpan nyala (karena ada baris baru)
        validateFormState();
    }
}

function removeTeamRow(btn) {
    // Hapus elemen parent (div.link-row)
    btn.parentElement.remove();
    // Trigger validasi
    validateFormState();
}


// --- 2. FUNGSI GAMBAR (PREVIEW & REMOVE) ---
function previewImage(event) {
    const input = event.target;
    const imgPreview = document.getElementById("imgPreview");
    const MAX_FILE_SIZE = 5 * 1024 * 1024; 
    const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp']; 
    const errorContainer = document.getElementById("fileError");

    if (errorContainer) { errorContainer.textContent = ""; errorContainer.style.display = "none"; }

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();

        if (!ALLOWED_EXT.includes(fileExt)) {
            errorContainer.textContent = `Ekstensi tidak diizinkan.`; 
            errorContainer.style.display = "block";
            input.value = ""; 
            imgPreview.style.display = "none"; 
            updateFileName(input); 
            return;
        }
        if (file.size > MAX_FILE_SIZE) {
            errorContainer.textContent = "File terlalu besar (Max 5MB)."; 
            errorContainer.style.display = "block";
            input.value = ""; 
            imgPreview.style.display = "none"; 
            updateFileName(input); 
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            imgPreview.src = e.target.result;
            imgPreview.style.display = "block";
            validateFormState(); // Cek tombol simpan
        };
        reader.readAsDataURL(file); 

    } else {
        imgPreview.src = "";
        imgPreview.style.display = "none";
        validateFormState();
    }
}

function removeImage() {
    const input = document.getElementById("inputGambar");
    const img = document.getElementById("imgPreview");
    const removeBtn = document.getElementById("removeImageBtn");
    const fileNameText = document.getElementById("fileNameText");
    const removeExisting = document.getElementById("removeExistingImage");

    if (input) input.value = "";
    if (img) { img.src = ""; img.style.display = "none"; }
    if (fileNameText) fileNameText.textContent = "Tidak ada file yang dipilih...";
    if (removeBtn) removeBtn.style.display = "none";
    if (removeExisting) removeExisting.value = "1";

    validateFormState(); // Cek tombol simpan
}

function updateFileName(input) {
    const fileNameText = document.getElementById("fileNameText");
    const removeBtn = document.getElementById("removeImageBtn");
    const removeExisting = document.getElementById("removeExistingImage");

    if (input.files && input.files.length > 0) {
        fileNameText.textContent = input.files[0].name;
        if (removeBtn) removeBtn.style.display = "block";
    } else {
        fileNameText.textContent = "Tidak ada file yang dipilih...";
        if (removeBtn) removeBtn.style.display = "none";
    }
    if (removeExisting) removeExisting.value = "0"; // Reset flag hapus
    
    validateFormState();
}


// --- 3. VALIDASI FORM & STATE CHECK (SNAPSHOT) ---

// Menyimpan kondisi awal form untuk dibandingkan nanti
let initialFormString = ""; 

function getFormString() {
    const form = document.getElementById("productForm"); // Pastikan ID form benar
    if (!form) return "";
    
    const formData = new FormData(form);
    // Hapus input file dari string (karena file object tidak bisa dibanding string)
    formData.delete("gambar"); 
    
    // Trik: Convert ke URLSearchParams biar jadi string rapi
    // Ini otomatis menghandle array seperti member_ids[] dan member_roles[]
    return new URLSearchParams(formData).toString();
}

function captureInitialState() {
    initialFormString = getFormString();
}

function validateFormState() {
    const btnSimpan = document.getElementById("submitBtn");
    const btnBatal = document.querySelector(".button-group .btn-secondary");
    if (!btnSimpan) return;

    // 1. Ambil Input Wajib (Nama & Deskripsi)
    const nama = document.querySelector('input[name="nama"]').value.trim();
    const deskripsi = document.querySelector('textarea[name="deskripsi"]').value.trim();

    // 2. Cek Mode (Edit atau Tambah)
    const idInput = document.querySelector('input[name="id_produk"]');
    const isEditMode = idInput && idInput.value !== "";

    // 3. Deteksi Perubahan (Dirty Check)
    let hasChanges = false;
    
    if (isEditMode) {
        // Mode Edit: Cek Gambar & Teks
        const fileInput = document.getElementById('inputGambar');
        if (fileInput && fileInput.files.length > 0) hasChanges = true;
        
        const removeExisting = document.getElementById("removeExistingImage");
        if (removeExisting && removeExisting.value === "1") hasChanges = true;

        if (!hasChanges) {
            if (getFormString() !== initialFormString) hasChanges = true;
        }
    } else {
        // Mode Tambah: Dianggap berubah kalau field wajib diisi
        hasChanges = true; 
    }

    // 4. Atur Tombol Simpan
    const isRequiredFilled = (nama !== "" && deskripsi !== "");
    
    if (isRequiredFilled && hasChanges) {
        btnSimpan.disabled = false;
        btnSimpan.style.opacity = "1";
        btnSimpan.style.cursor = "pointer";
        btnSimpan.textContent = isEditMode ? "Update" : "Simpan";
    } else {
        btnSimpan.disabled = true;
        btnSimpan.style.opacity = "0.6";
        btnSimpan.style.cursor = "not-allowed";
        if (isEditMode && !hasChanges) btnSimpan.textContent = "Tidak ada perubahan";
    }

    // 5. Atur Tombol Batal (Opsional)
    if (btnBatal) {
        if (isEditMode) {
            btnBatal.disabled = false;
            btnBatal.style.opacity = "1";
            btnBatal.style.cursor = "pointer";
        } else {
            const isDirty = (nama || deskripsi);
            if (isDirty) {
                btnBatal.disabled = false;
                btnBatal.style.opacity = "1";
                btnBatal.style.cursor = "pointer";
            } else {
                btnBatal.disabled = true;
                btnBatal.style.opacity = "0.6";
                btnBatal.style.cursor = "not-allowed";
            }
        }
    }
}

// Fungsi Debounce (Biar gak berat saat ngetik)
function debounce(func, delay) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

function setupFormValidation() {
    const inputs = document.querySelectorAll('#productForm input, #productForm textarea, #productForm select');
    
    if(inputs.length > 0) {
        captureInitialState();
        validateFormState();

        const validateSabar = debounce(validateFormState, 200);

        inputs.forEach(input => {
            if (input.type === 'text' || input.tagName === 'TEXTAREA') {
                input.addEventListener('input', validateSabar);
            } else {
                input.addEventListener('change', validateFormState);
            }
        });
    }
}


// --- 4. CORE LOGIC (LOAD, SUBMIT, DELETE) ---

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

function loadProductList() {
    const listContainer = document.getElementById("product-list-container");
    if (!listContainer) return;

    const url = "module/produk/table-load.php" + window.location.search;
    listContainer.style.opacity = "0.5"; 

    fetch(url)
        .then((response) => response.text())
        .then((html) => {
            // Teknik ganti isi tanpa kedip
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.getElementById('product-list-container');
            
            if (newContent) listContainer.innerHTML = newContent.innerHTML;
            else listContainer.innerHTML = html;
            
            listContainer.style.opacity = "1";
        })
        .catch((error) => {
            console.error("Error loading table:", error);
            listContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat tabel.</div>';
            listContainer.style.opacity = "1";
        });
}

function loadEmptyForm() { // Hapus parameter successMessage
    const formContainer = document.getElementById("form-content-wrapper"); 
    if (!formContainer) return;

    const url = "module/produk/form-load.php"; 
    
    fetch(url).then((response) => response.text()).then((html) => {
        formContainer.innerHTML = html;
        setupFormValidation(); 
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
        setupFormValidation();
    });
}

function deleteProduct(productId) {
    if (!confirm("Anda yakin ingin menghapus produk ini?")) return;
    const url = "module/produk/delete.php";
    const formData = new FormData();
    formData.append("id", productId); 
    displayAlert("Menghapus produk...", "warning");

    fetch(url, { method: "POST", body: formData })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") loadProductList(); 
            else displayAlert(data.message, "error");
        });
}

// --- 5. INITIALIZATION ---
document.addEventListener("DOMContentLoaded", function () {
    setupFormValidation();

    document.addEventListener("submit", function (e) {
        if (e.target && e.target.id === "productForm") {
            
            e.preventDefault(); // 🔥 SELALU CEGAH SUBMIT BAWAAN (AJAX ONLY) 🔥
            
            const form = e.target;
            const formData = new FormData(form);
            const isUpdateMode = formData.get("id_produk") && formData.get("id_produk") !== "";

            const submitBtn = document.getElementById("submitBtn");
            submitBtn.disabled = true;
            submitBtn.textContent = "Memproses...";
            
            const url = "module/produk/save.php";

            fetch(url, { method: "POST", body: formData })
                .then((response) => response.json())
                .then((data) => {
                    
                    if (data && data.status === "success") {
                        
                        // 🔥 LOGIKA REDIRECT: Jika server kirim redirect, ikuti. 🔥
                        if (data.redirect) {
                            displayAlert(data.message, "success");
                            setTimeout(() => {
                                window.location.href = data.redirect; 
                            }, 300);
                        } else {
                            // Mode Tambah Baru
                            displayAlert(data.message, "success");
                            loadProductList();
                            loadEmptyForm();
                        }
                    } 
                    else if (data && data.status === "error") {
                        
                        displayAlert(data.message, "error");
                        
                        // Jika server mengirim error dengan instruksi redirect (Misal: validasi gagal saat update)
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect; 
                            }, 300);
                        }
                    }
                })
                .catch((error) => {
                    console.error("AJAX Error:", error);
                    displayAlert("Terjadi kesalahan sistem.", "error");
                })
                .finally(() => {
                    validateFormState();
                });

        }
    });
});

// ... (Kode lama biarkan) ...

// --- FUNGSI TAMBAHAN UNTUK TIM ---
function addTeamRow() {
    const container = document.getElementById('team-container');
    const template = document.getElementById('teamRowTemplate');
    
    if (container && template) {
        const clone = template.content.cloneNode(true);
        container.appendChild(clone);
        // Validasi form agar tombol simpan aktif jika ada perubahan
        if (typeof validateFormState === 'function') validateFormState();
    }
}

function removeTeamRow(btn) {
    btn.parentElement.remove();
    // Validasi form agar tombol simpan aktif
    if (typeof validateFormState === 'function') validateFormState();
}

// ... (Update juga fungsi getFormString di validateFormState) ...

function getFormString() {
    const form = document.getElementById("productForm");
    if (!form) return "";
    
    const formData = new FormData(form);
    formData.delete("gambar"); 
    
    // FormData otomatis menghandle array member_ids[] dan member_roles[]
    // Jadi kalau ada perubahan dropdown/role, string ini akan berubah
    return new URLSearchParams(formData).toString();
}

function addTeamToTable() {
    const select = document.getElementById("memberSelect");
    const roleInput = document.getElementById("roleInput");
    const tableBody = document.querySelector("#teamTable tbody");

    const memberId = select.value;
    const memberName = select.options[select.selectedIndex].text;
    const role = roleInput.value.trim();

    if (!memberId) {
        alert("Pilih member terlebih dahulu!");
        return;
    }
    if (role === "") {
        alert("Role tidak boleh kosong!");
        return;
    }

    // Tambahkan baris ke tabel
    const row = document.createElement("tr");
    row.innerHTML = `
        <td>
            ${memberName}
            <input type="hidden" name="member_ids[]" value="${memberId}">
        </td>
        <td>
            ${role}
            <input type="hidden" name="member_roles[]" value="${role}">
        </td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeTeamRowTable(this)">✕</button>
        </td>
    `;

    tableBody.appendChild(row);

    // Reset input
    select.value = "";
    roleInput.value = "";

    // Supaya tombol simpan aktif
    if (typeof validateFormState === "function") validateFormState();
}

function removeTeamRowTable(btn) {
    btn.closest("tr").remove();

    if (typeof validateFormState === "function") validateFormState();
}
