// admin/assets/js/fasilitas.js

// GANTI NAMA: previewImage -> previewFasilitasImage
function previewFasilitasImage(event) {
    const input = event.target;
    const imgPreview = document.getElementById("imgPreview");
    const MAX_FILE_SIZE = 5 * 1024 * 1024; 
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
            errorContainer.textContent = `Ekstensi file .${fileExt} tidak diizinkan.`;
            errorContainer.style.display = "block";
            input.value = ""; 
            imgPreview.style.display = "none";
            updateFasilitasFileName(input); 
            return;
        }

        if (file.size > MAX_FILE_SIZE) {
            errorContainer.textContent = "Ukuran file melebihi batas 5MB.";
            errorContainer.style.display = "block";
            input.value = ""; 
            imgPreview.style.display = "none";
            updateFasilitasFileName(input);
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

// GANTI NAMA: removeImage -> removeFasilitasImage
function removeFasilitasImage() {
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

// GANTI NAMA: updateFileName -> updateFasilitasFileName
function updateFasilitasFileName(input) {
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

function displayAlert(message, type) {
    // Fungsi ini aman dibiarkan sama karena logicnya generic
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

function loadFasilitasList() {
  const listContainer = document.getElementById("fasilitas-list-container");
  if (!listContainer) return;
  const currentParams = new URLSearchParams(window.location.search);
  const url = "module/fasilitas/table-load.php" + window.location.search;

  listContainer.innerHTML = '<div style="text-align:center; padding:20px;">Memuat data...</div>';

  fetch(url)
    .then((response) => response.text())
    .then((html) => {
      const tempDiv = document.createElement("div");
      tempDiv.innerHTML = html;
      const currentPage = parseInt(currentParams.get("p")) || 1;
      const tableBody = tempDiv.querySelector(".table tbody");
      if (currentPage > 1 && tableBody && tableBody.children.length === 1 && tableBody.querySelector("td[colspan]")) {
        currentParams.set("p", currentPage - 1); 
        window.history.pushState(null, "", window.location.pathname + "?" + currentParams.toString());
        loadFasilitasList();
        return;
      }
      listContainer.innerHTML = html;
    })
    .catch((error) => {
      console.error("Error loading table:", error);
      listContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat tabel.</div>';
    });
}

// GANTI NAMA: loadEmptyForm -> loadEmptyFasilitasForm
function loadEmptyFasilitasForm(successMessage) {
    const formContainer = document.getElementById("form-content-wrapper"); 
    if (!formContainer) return;

    // Pastikan URL ke fasilitas
    const url = "module/fasilitas/form-load.php?success_msg=" + encodeURIComponent(successMessage);

    formContainer.innerHTML = '<div style="text-align:center; padding:20px;">Memuat form...</div>';
    
    displayAlert(successMessage, "success");

    fetch(url)
        .then((response) => response.text())
        .then((html) => {
            formContainer.innerHTML = html;
            const newNameInput = document.querySelector('#fasilitasForm input[name="judul"]');
            if (newNameInput) {
                setTimeout(() => { newNameInput.focus(); }, 50); 
            }
        })
        .catch((error) => {
            console.error("Error loading form:", error);
            formContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat form.</div>';
        });
}

function deleteFasilitas(id) {
  if (!confirm("Anda yakin ingin menghapus fasilitas ini?")) return;
  const url = "module/fasilitas/delete.php"; 
  const formData = new FormData();
  formData.append("id", id); 
  displayAlert("Menghapus data...", "warning");
  fetch(url, { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        loadFasilitasList(); 
      } else {
        displayAlert(data.message, "error");
      }
    })
    .catch((error) => {
      console.error("AJAX Delete Error:", error);
      displayAlert("Terjadi kesalahan jaringan.", "error");
    });
}

// --- FUNGSI BARU: BATAL / RESET FORM ---
function cancelFasilitasForm() {
    const formContainer = document.getElementById("form-content-wrapper");
    if (!formContainer) return;

    formContainer.innerHTML = '<div style="text-align:center; padding:20px;">Mereset form...</div>';

    fetch("module/fasilitas/form-load.php")
        .then((response) => response.text())
        .then((html) => {
            // 1. Ganti isi form
            formContainer.innerHTML = html;

            // 2. Bersihkan URL dari ?edit=...
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.delete('edit'); 
            window.history.pushState({}, '', currentUrl);

            // 3. --- LOGIKA BARU: AUTO FOCUS ---
            // Cari input dengan name="judul"
            const judulInput = document.querySelector('#fasilitasForm input[name="judul"]');
            
            // Jika ketemu, langsung sorot (focus)
            if (judulInput) {
                // Kasih jeda 50ms biar browser selesai menggambar form dulu
                setTimeout(() => {
                    judulInput.focus();
                }, 50);
            }
            // ----------------------------------

            // 4. Scroll smooth ke atas (opsional)
            document.querySelector('.card h2').scrollIntoView({ behavior: 'smooth' });
        })
        .catch((error) => {
            console.error("Error resetting form:", error);
            formContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal mereset form.</div>';
        });
}

document.addEventListener("DOMContentLoaded", function () {
  document.addEventListener("submit", function (e) {
    // Pastikan ID form sesuai dengan yang di form-fields.php (fasilitasForm)
    if (e.target && e.target.id === "fasilitasForm") {
      e.preventDefault(); 

      const form = e.target;
      const formData = new FormData(form);
      const url = "module/fasilitas/save.php"; 

      const submitBtn = document.getElementById("submitBtn");
      submitBtn.disabled = true;
      submitBtn.textContent = "Memproses...";

      fetch(url, { method: "POST", body: formData })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            loadFasilitasList();

            const isUpdate = formData.get("id_galery"); // Cek Primary Key Fasilitas
            
            // PANGGIL FUNGSI YANG SUDAH DIGANTI NAMANYA
            loadEmptyFasilitasForm(data.message);

            if (isUpdate) {
              window.history.pushState({}, document.title, window.location.pathname + "?page=fasilitas");
            }
          } else {
            displayAlert(data.message, "error");
            
            const input = document.getElementById('inputGambar');
            const img = document.getElementById('imgPreview');
            if (input) input.value = ''; 
            if (img) img.style.display = 'none'; 
            
            // PANGGIL FUNGSI YANG SUDAH DIGANTI NAMANYA
            updateFasilitasFileName(input);
          }
        })
        .catch((error) => {
          console.error("AJAX Error:", error);
          displayAlert("Terjadi kesalahan jaringan/server.", "error");
        })
        .finally(() => {
          // BAGIAN INI YANG DIPERBAIKI
          const finalBtn = document.getElementById("submitBtn");
          if (finalBtn) {
            finalBtn.disabled = false;
            
            // Cek apakah ini mode Edit (ada id_galery) atau Simpan Baru
            const isEditMode = formData.get("id_galery"); 
            
            // Kembalikan teks tombol sesuai modenya
            finalBtn.textContent = isEditMode ? "Update" : "Simpan";
          }
        });
    }
  });
});