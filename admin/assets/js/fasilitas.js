// admin/assets/js/fasilitas.js

function initFormListener() {
  const judulInput = document.getElementById('judulInput');
  const deskripsiInput = document.getElementById('deskripsiInput');
  const fileInput = document.getElementById('inputGambar');
  
  const elements = [judulInput, deskripsiInput, fileInput];
  elements.forEach(el => {
      if(el) {
          el.addEventListener('input', checkFormState);
          el.addEventListener('change', checkFormState);
      }
  });
  checkFormState();
}

function checkFormState() {
  const judul = document.getElementById('judulInput')?.value.trim();
  const deskripsi = document.getElementById('deskripsiInput')?.value.trim();
  const fileInput = document.getElementById('inputGambar');
  const hasFile = fileInput?.files.length > 0;
  
  const isEditMode = document.querySelector('input[name="id_fasilitas"]');
  const btnCancel = document.getElementById('btnCancel');
  
  if (btnCancel) {
      if (isEditMode) {
          btnCancel.disabled = false; 
      } else {
          if (judul || deskripsi || hasFile) {
              btnCancel.disabled = false;
          } else {
              btnCancel.disabled = true;
          }
      }
  }
}

function previewFasilitasImage(event) {
  const input = event.target;
  // Target Pembungkus (Kotak) dan Gambarnya
  const previewContainer = document.getElementById("previewContainer");
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

      if (!ALLOWED_EXT.includes(fileExt) || file.size > MAX_FILE_SIZE) {
          const msg = !ALLOWED_EXT.includes(fileExt) 
              ? `Ekstensi file .${fileExt} tidak diizinkan.` 
              : "Ukuran file melebihi batas 5MB.";
          
          if (errorContainer) {
              errorContainer.textContent = msg;
              errorContainer.style.display = "block";
          }
          input.value = ""; 
          if(previewContainer) previewContainer.style.display = "none";
          updateFasilitasFileName(input); 
          checkFormState();
          return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
          if(imgPreview && previewContainer) {
              imgPreview.src = e.target.result;
              // Tampilkan Kotak
              previewContainer.style.display = "flex"; 
          }
      };
      reader.readAsDataURL(file); 
  } else {
      if(previewContainer) previewContainer.style.display = "none";
  }
  checkFormState();
}

function updateFasilitasFileName(input) {
const fileNameText = document.getElementById("fileNameText");
const removeBtn = document.getElementById("removeImageBtn");
const previewContainer = document.getElementById("previewContainer");

if (input.files && input.files.length > 0) {
  fileNameText.textContent = input.files[0].name;
  if (removeBtn) removeBtn.style.display = "block";
} else {
  // Jika tidak ada file baru, cek apakah kotak preview tampil (mode edit)
  if (previewContainer && previewContainer.style.display === 'none') {
      fileNameText.textContent = "Tidak ada file yang dipilih...";
      if (removeBtn) removeBtn.style.display = "none";
  }
}
const removeExisting = document.getElementById("removeExistingImage");
if (removeExisting && input.files.length > 0) removeExisting.value = "0";
}

function removeFasilitasImage() {
const input = document.getElementById("inputGambar");
const previewContainer = document.getElementById("previewContainer");
const imgPreview = document.getElementById("imgPreview");
const removeBtn = document.getElementById("removeImageBtn");
const fileNameText = document.getElementById("fileNameText");

if (input) input.value = "";
if (imgPreview) imgPreview.src = "";
if (previewContainer) previewContainer.style.display = "none"; // Sembunyikan kotak

if (fileNameText) fileNameText.textContent = "Tidak ada file yang dipilih...";
if (removeBtn) removeBtn.style.display = "none";

const removeExisting = document.getElementById("removeExistingImage");
if (removeExisting) removeExisting.value = "1";

checkFormState();
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
              initFormListener();
              const judulInput = document.querySelector('#fasilitasForm input[name="judul"]');
              if (judulInput) setTimeout(() => { judulInput.focus(); }, 50);
              document.querySelector('.card h2').scrollIntoView({ behavior: 'smooth' });
          });
  } else {
      document.getElementById("fasilitasForm").reset();
      removeFasilitasImage(); 
      const removeExisting = document.getElementById("removeExistingImage");
      if (removeExisting) removeExisting.value = "0";
      checkFormState();
  }
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
    const isEmpty = tempDiv.querySelector('.fasilitas-grid') && tempDiv.querySelector('.fasilitas-grid').children.length === 0;

    if (currentPage > 1 && isEmpty) {
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

function loadEmptyFasilitasForm(successMessage) {
  const formContainer = document.getElementById("form-content-wrapper"); 
  if (!formContainer) return;

  const url = "module/fasilitas/form-load.php?success_msg=" + encodeURIComponent(successMessage);

  formContainer.innerHTML = '<div style="text-align:center; padding:20px;">Memuat form...</div>';
  
  displayAlert(successMessage, "success");

  fetch(url)
      .then((response) => response.text())
      .then((html) => {
          formContainer.innerHTML = html;
          initFormListener(); 
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
      displayAlert(data.message, "success");
    } else {
      displayAlert(data.message, "error");
    }
  })
  .catch((error) => {
    console.error("AJAX Delete Error:", error);
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

document.addEventListener("DOMContentLoaded", function () {
initFormListener();

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
          loadEmptyFasilitasForm(data.message);
          if (isUpdate) {
            window.history.pushState({}, document.title, window.location.pathname + "?page=fasilitas");
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
        if (finalBtn) {
          finalBtn.disabled = false;
          const isEditMode = formData.get("id_fasilitas"); 
          finalBtn.textContent = isEditMode ? "Update" : "Simpan";
        }
      });
  }
});
});