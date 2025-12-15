// admin/assets/js/fasilitas.js

// =========================================================
// 1. PENCARIAN (SEARCH)
// =========================================================

function searchFasilitas() {
  const input = document.getElementById('searchFasilitasInput');
  if (!input) return;

  const keyword = input.value.trim();
  const currentUrl = new URL(window.location.href);
  
  // Set parameter keyword di URL
  if (keyword) {
      currentUrl.searchParams.set('keyword', keyword);
  } else {
      currentUrl.searchParams.delete('keyword'); 
  }
  
  // Reset ke halaman 1 saat mencari baru
  currentUrl.searchParams.set('p', 1);

  // Update URL Browser dan Load Data
  window.history.pushState(null, "", currentUrl);
  loadFasilitasList();
}

// Event Listener untuk tombol Enter di Search Box
document.addEventListener('keydown', function(e) {
  if (e.target && e.target.id === 'searchFasilitasInput' && e.key === 'Enter') {
      e.preventDefault(); 
      searchFasilitas();
  }
});


// =========================================================
// 2. VALIDASI FORM & LOGIKA TOMBOL (SIMPAN/BATAL)
// =========================================================

let initialFormString = ""; 

// Mengambil snapshot form untuk mendeteksi perubahan
function getFormString() {
  const form = document.getElementById("fasilitasForm");
  if (!form) return "";
  const formData = new FormData(form);
  formData.delete("gambar"); // File dicek terpisah
  return new URLSearchParams(formData).toString();
}

function captureInitialState() {
  initialFormString = getFormString();
}

function validateFormState() {
  const btnSimpan = document.getElementById("submitBtn");
  // Ambil tombol batal (bisa ID btnCancel atau class btn-secondary)
  const btnBatal = document.getElementById("btnCancel") || document.querySelector(".button-group .btn-secondary");

  if (!btnSimpan) return;

  // 1. CEK MODE (EDIT/TAMBAH)
  const idInput = document.querySelector('input[name="id_fasilitas"]');
  const isEditMode = idInput && idInput.value !== "";

  // 2. DETEKSI PERUBAHAN
  let hasChanges = false;
  const currentString = getFormString();
  if (currentString !== initialFormString) hasChanges = true;

  const fileInput = document.getElementById('inputGambar');
  if (fileInput && fileInput.files.length > 0) hasChanges = true;

  // 3. ATUR TOMBOL BATAL (KIRI)
  if (btnBatal) {
      if (isEditMode) {
          enableBtn(btnBatal); // Mode Edit: Batal selalu aktif
      } else {
          // Mode Tambah: Batal aktif jika form sudah terisi (kotor)
          if (hasChanges) enableBtn(btnBatal);
          else disableBtn(btnBatal);
      }
  }

  // 4. ATUR TOMBOL SIMPAN (KANAN)
  const judulInput = document.querySelector('input[name="judul"]'); 
  const deskripsiInput = document.querySelector('[name="deskripsi"]'); 
  
  const judul = judulInput ? judulInput.value.trim() : "";
  const deskripsi = deskripsiInput ? deskripsiInput.value.trim() : "";

  // SYARAT WAJIB: Judul & Deskripsi harus terisi
  const isRequiredFilled = (judul !== "" && deskripsi !== "");

  // Tombol Nyala Jika: (Data Lengkap) DAN (Ada Perubahan)
  if (isRequiredFilled && hasChanges) {
      enableBtn(btnSimpan);
      btnSimpan.textContent = isEditMode ? "Update" : "Simpan";
  } else {
      disableBtn(btnSimpan);
      if (isEditMode && !hasChanges) {
          btnSimpan.textContent = "Tidak ada perubahan";
      } else {
          btnSimpan.textContent = isEditMode ? "Update" : "Simpan";
      }
  }
}

function enableBtn(btn) {
  btn.disabled = false;
  btn.style.opacity = "1";
  btn.style.cursor = "pointer";
}

function disableBtn(btn) {
  btn.disabled = true;
  btn.style.opacity = "0.6";
  btn.style.cursor = "not-allowed";
}

function debounce(func, delay) {
  let timeout;
  return function(...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), delay);
  };
}

function setupFormValidation() {
  const inputs = document.querySelectorAll('#fasilitasForm input, #fasilitasForm textarea, #fasilitasForm select');
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


// =========================================================
// 3. IMAGE HANDLING
// =========================================================

function previewFasilitasImage(event) {
  const input = event.target;
  const previewContainer = document.getElementById("previewContainer");
  const imgPreview = document.getElementById("imgPreview");
  const errorContainer = document.getElementById("fileError");
  
  const MAX_FILE_SIZE = 5 * 1024 * 1024; 
  const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp']; 

  if (errorContainer) { errorContainer.textContent = ""; errorContainer.style.display = "none"; }

  if (input.files && input.files[0]) {
      const file = input.files[0];
      const fileName = file.name;
      const fileExt = fileName.split('.').pop().toLowerCase();

      if (!ALLOWED_EXT.includes(fileExt) || file.size > MAX_FILE_SIZE) {
          const msg = !ALLOWED_EXT.includes(fileExt) ? "Ekstensi tidak diizinkan." : "File max 5MB.";
          if (errorContainer) { errorContainer.textContent = msg; errorContainer.style.display = "block"; }
          
          input.value = ""; 
          if(previewContainer) previewContainer.style.display = "none";
          updateFasilitasFileName(input); 
          validateFormState();
          return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
          if(imgPreview && previewContainer) {
              imgPreview.src = e.target.result;
              previewContainer.style.display = "flex"; 
          }
          validateFormState();
      };
      reader.readAsDataURL(file); 
  } else {
      if(previewContainer) previewContainer.style.display = "none";
      validateFormState();
  }
}

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
  if (removeExisting && input.files.length > 0) removeExisting.value = "0";
  validateFormState(); 
}

function removeFasilitasImage() {
  const input = document.getElementById("inputGambar");
  const previewContainer = document.getElementById("previewContainer");
  const imgPreview = document.getElementById("imgPreview");
  const removeBtn = document.getElementById("removeImageBtn");
  const fileNameText = document.getElementById("fileNameText");

  if (input) input.value = "";
  if (imgPreview) imgPreview.src = "";
  if (previewContainer) previewContainer.style.display = "none";
  if (fileNameText) fileNameText.textContent = "Tidak ada file yang dipilih...";
  if (removeBtn) removeBtn.style.display = "none";

  const removeExisting = document.getElementById("removeExistingImage");
  if (removeExisting) removeExisting.value = "1";
  validateFormState();
}


// =========================================================
// 4. ACTION HANDLERS
// =========================================================

// Handle Klik Pagination (.page-link)
    document.addEventListener('click', function(e) {
    // Cek apakah yang diklik adalah elemen pagination atau anaknya
    if (e.target && e.target.classList.contains('page-link')) {
        e.preventDefault();
        
        // Ambil href dari tombol
        const href = e.target.getAttribute('href');
        
        // Jika href valid dan bukan disabled
        if (href && !e.target.classList.contains('disabled')) {
            window.history.pushState(null, "", href);
            loadFasilitasList();
            
            // Scroll sedikit ke atas agar user sadar halaman berubah
            const gridContainer = document.querySelector('.fasilitas-grid-container');
            if(gridContainer) gridContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
  });

function loadFasilitasList() {
  const listContainer = document.getElementById("fasilitas-list-container");
  if (!listContainer) return;
  const currentParams = new URLSearchParams(window.location.search);
  const url = "module/fasilitas/table-load.php" + window.location.search;

  listContainer.style.opacity = "0.5";

  fetch(url)
  .then((response) => response.text())
  .then((html) => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newContent = doc.getElementById('fasilitas-list-container');
      if (newContent) {
          listContainer.innerHTML = newContent.innerHTML;
      } else {
          listContainer.innerHTML = html;
      }
      listContainer.style.opacity = "1";
  })
  .catch((error) => {
      console.error("Error loading table:", error);
      listContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat tabel.</div>';
      listContainer.style.opacity = "1";
  });
}

function cancelFasilitasForm() {
  const isEditMode = document.querySelector('input[name="id_fasilitas"]');
  
  if (isEditMode) {
      const formContainer = document.getElementById("form-content-wrapper");
      if (!formContainer) return;
      
      formContainer.innerHTML = '<div style="text-align:center; padding:20px;">Mereset form...</div>';
      fetch("module/fasilitas/form-load.php")
          .then((response) => response.text())
          .then((html) => {
              formContainer.innerHTML = html;
              const currentUrl = new URL(window.location);
              currentUrl.searchParams.delete('edit'); 
              window.history.pushState({}, '', currentUrl);
              
              setupFormValidation();
              const judulInput = document.querySelector('#fasilitasForm input[name="judul"]');
              if (judulInput) setTimeout(() => { judulInput.focus(); }, 50);
              document.querySelector('.card h2').scrollIntoView({ behavior: 'smooth' });
          });
  } else {
      document.getElementById("fasilitasForm").reset();
      removeFasilitasImage(); 
      const removeExisting = document.getElementById("removeExistingImage");
      if (removeExisting) removeExisting.value = "0";
      captureInitialState(); 
      validateFormState();
  }
}

function loadEmptyFasilitasForm(successMessage) {
  const formContainer = document.getElementById("form-content-wrapper"); 
  if (!formContainer) return;

  const url = "module/fasilitas/form-load.php?success_msg=" + encodeURIComponent(successMessage);
  displayAlert(successMessage, "success");

  fetch(url)
      .then((response) => response.text())
      .then((html) => {
          formContainer.innerHTML = html;
          setupFormValidation();
          const newNameInput = document.querySelector('#fasilitasForm input[name="judul"]');
          if (newNameInput) setTimeout(() => { newNameInput.focus(); }, 50); 
      });
}

function deleteFasilitas(id) {
  if (!confirm("Anda yakin ingin menghapus fasilitas ini?")) return;
  const url = "module/fasilitas/delete.php"; 
  const formData = new FormData();
  formData.append("id", id); 

  fetch(url, { method: "POST", body: formData })
  .then((response) => response.json())
  .then((data) => {
      if (data.status === "success") {
          loadFasilitasList(); 
          displayAlert(data.message, "success"); 
      } else {
          displayAlert(data.message, "error");
      }
  })
  .catch((error) => {
      console.error("Delete Error:", error);
      displayAlert("Terjadi kesalahan jaringan.", "error");
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
  setTimeout(() => {
      toast.classList.add('hide'); 
      setTimeout(() => {
          toast.remove();
          if (toastContainer.children.length === 0) toastContainer.remove();
      }, 300); 
  }, 4000); 
}

// Startup
document.addEventListener("DOMContentLoaded", function () {
  setupFormValidation();

  document.addEventListener("submit", function (e) {
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
                  const isUpdate = formData.get("id_fasilitas"); 
                  if (isUpdate) {
                      const currentUrl = new URL(window.location);
                      currentUrl.searchParams.delete('edit'); 
                      window.history.pushState({}, document.title, currentUrl);
                      loadEmptyFasilitasForm("Update Berhasil! Data telah disimpan.");
                  } else {
                      loadEmptyFasilitasForm(data.message);
                  }
              } else {
                  displayAlert(data.message, "error");
                  const input = document.getElementById('inputGambar');
                  const previewContainer = document.getElementById('previewContainer');
                  if (input) input.value = ''; 
                  if (previewContainer) previewContainer.style.display = 'none'; 
                  updateFasilitasFileName(input);
              }
          })
          .catch((error) => {
              console.error("AJAX Error:", error);
              displayAlert("Terjadi kesalahan jaringan/server.", "error");
          })
          .finally(() => {
              const finalBtn = document.getElementById("submitBtn");
              if (finalBtn && finalBtn.disabled) {
                  finalBtn.disabled = false;
                  finalBtn.textContent = "Simpan";
              }
          });
      }
  });
});