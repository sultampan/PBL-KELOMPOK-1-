// admin/assets/js/fasilitas.js

// =========================================================
// 1. REAL-TIME SEARCH (Live Typing)
// =========================================================
let searchTimeout = null;

document.addEventListener('input', function(e) {
    if (e.target && e.target.id === 'searchFasilitasInput') {
        clearTimeout(searchTimeout);
        // Delay 300ms (Debounce standard)
        searchTimeout = setTimeout(() => {
            const keyword = e.target.value.trim();
            executeSearch(keyword);
        }, 300); 
    }
});

function executeSearch(keyword) {
    const currentUrl = new URL(window.location.href);
    if (keyword) {
        currentUrl.searchParams.set('keyword', keyword);
    } else {
        currentUrl.searchParams.delete('keyword'); 
    }
    currentUrl.searchParams.set('p', 1); // Reset ke hal 1
    window.history.pushState(null, "", currentUrl);
    
    loadFasilitasList(); 
}

// Mencegah Enter refresh halaman
document.addEventListener('keydown', function(e) {
    if (e.target && e.target.id === 'searchFasilitasInput' && e.key === 'Enter') {
        e.preventDefault(); 
    }
});

// =========================================================
// 2. LOAD TABLE AJAX & PAGINATION
// =========================================================

function loadFasilitasList() {
  const listContainer = document.getElementById("fasilitas-data-content");
  if (!listContainer) return;
  
  // Tambahkan timestamp untuk mencegah cache browser
  const url = "module/fasilitas/table-load.php" + window.location.search + "&_t=" + new Date().getTime();
  
  listContainer.style.opacity = "0.5";

  fetch(url)
  .then((response) => response.text())
  .then((html) => {
      listContainer.innerHTML = html;
      listContainer.style.opacity = "1";
  })
  .catch((error) => {
      console.error("Error:", error);
      listContainer.style.opacity = "1";
  });
}

// Handle Pagination Klik
document.addEventListener('click', function(e) {
    // Cek apakah klik pada elemen page-link
    if (e.target && e.target.classList.contains('page-link')) {
        e.preventDefault();
        
        const href = e.target.getAttribute('href');
        
        // Pastikan href valid dan bukan tombol disabled
        if (href && !e.target.classList.contains('disabled')) {
            window.history.pushState(null, "", href);
            
            // Muat data baru
            loadFasilitasList();
            
            // Scroll halus ke bagian atas list
            const header = document.querySelector('.toolbar-header');
            if(header) {
                header.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }
});

// =========================================================
// 3. VALIDASI FORM & LOGIKA TOMBOL (SIMPAN/BATAL)
// =========================================================

let initialFormString = ""; 

function getFormString() {
  const form = document.getElementById("fasilitasForm");
  if (!form) return "";
  const formData = new FormData(form);
  formData.delete("gambar"); 
  return new URLSearchParams(formData).toString();
}

function captureInitialState() {
  initialFormString = getFormString();
}

function validateFormState() {
  const btnSimpan = document.getElementById("submitBtn");
  const btnBatal = document.getElementById("btnCancel") || document.querySelector(".button-group .btn-secondary");

  if (!btnSimpan) return;

  const idInput = document.querySelector('input[name="id_fasilitas"]');
  const isEditMode = idInput && idInput.value !== "";

  let hasChanges = false;
  const currentString = getFormString();
  if (currentString !== initialFormString) hasChanges = true;

  const fileInput = document.getElementById('inputGambar');
  if (fileInput && fileInput.files.length > 0) hasChanges = true;

  const removeExisting = document.getElementById("removeExistingImage");
  if (removeExisting && removeExisting.value === "1") hasChanges = true;

  if (btnBatal) {
      if (isEditMode) {
          enableBtn(btnBatal); 
      } else {
          if (hasChanges) enableBtn(btnBatal);
          else disableBtn(btnBatal);
      }
  }

  const judulInput = document.querySelector('input[name="judul"]'); 
  const deskripsiInput = document.querySelector('[name="deskripsi"]'); 
  
  const judul = judulInput ? judulInput.value.trim() : "";
  const deskripsi = deskripsiInput ? deskripsiInput.value.trim() : "";
  const isRequiredFilled = (judul !== "" && deskripsi !== "");

  if (isRequiredFilled && hasChanges) {
      enableBtn(btnSimpan);
      btnSimpan.textContent = isEditMode ? "Update" : "Simpan";
  } else {
      disableBtn(btnSimpan);
      btnSimpan.textContent = isEditMode ? "Update" : "Simpan";
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
// 4. IMAGE HANDLING
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
// 5. CRUD ACTION HANDLERS
// =========================================================

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
              const card = document.querySelector('.card');
              if(card) card.scrollIntoView({ behavior: 'smooth' });
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

  // Tambahkan timestamp di request form juga
  const url = "module/fasilitas/form-load.php?success_msg=" + encodeURIComponent(successMessage) + "&_t=" + new Date().getTime();
  displayAlert(successMessage, "success");

  fetch(url)
      .then((response) => response.text())
      .then((html) => {
          formContainer.innerHTML = html;
          setupFormValidation();
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

// STARTUP
document.addEventListener("DOMContentLoaded", function () {
  setupFormValidation();

  // Handle Back/Forward Browser
  window.addEventListener('popstate', function(event) {
    loadFasilitasList();
  });

  // Handle Submit Form
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