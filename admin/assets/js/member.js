// admin/assets/js/member.js

// =========================================================
// 1. REAL-TIME SEARCH (Live Typing - Anti Putus)
// =========================================================
let memberSearchTimeout = null;

// Event Listener saat mengetik
document.addEventListener('input', function(e) {
    if (e.target && e.target.id === 'searchMemberInput') {
        clearTimeout(memberSearchTimeout);
        // Delay 300ms: Tunggu user selesai mengetik sebentar baru cari
        // Ini bikin UI gak nge-lag tapi tetap terasa real-time
        memberSearchTimeout = setTimeout(() => {
            const keyword = e.target.value.trim(); // Ambil teks tanpa spasi ujung
            executeMemberSearch(keyword);
        }, 300); 
    }
});

function executeMemberSearch(keyword) {
    const currentUrl = new URL(window.location.href);
    
    if (keyword) {
        currentUrl.searchParams.set('keyword', keyword);
    } else {
        currentUrl.searchParams.delete('keyword'); 
    }
    
    // Reset ke halaman 1 setiap kali mengetik pencarian baru
    currentUrl.searchParams.set('p', 1); 
    
    // Update URL di browser tanpa reload halaman (biar rapi)
    window.history.pushState(null, "", currentUrl);
    
    // Panggil fungsi load data
    loadMemberList(); 
}

// Mencegah tombol Enter me-refresh halaman (karena sudah otomatis)
document.addEventListener('keydown', function(e) {
    if (e.target && e.target.id === 'searchMemberInput' && e.key === 'Enter') {
        e.preventDefault(); 
    }
});

function resetSearchMember() {
    const input = document.getElementById('searchMemberInput');
    if (input) input.value = ''; 
    executeMemberSearch(''); // Cari string kosong (reset)
}


// =========================================================
// 2. CORE AJAX LOGIC (SMART RELOAD)
// =========================================================

// Handle Tombol Back/Forward Browser
window.addEventListener('popstate', function(event) {
    loadMemberList();
});

// Handle Klik Pagination
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('page-link')) {
        e.preventDefault(); 
        const href = e.target.getAttribute('href'); 
        if (href && !e.target.classList.contains('disabled')) {
            window.history.pushState(null, "", href);
            loadMemberList(true); // true = scroll ke atas
        }
    }
});

function loadMemberList(scrollToTop = false) {
  // Tambah timestamp agar browser tidak mengambil data cache lama
  const url = "module/member/table-load.php" + window.location.search + "&_t=" + new Date().getTime();

  // Indikator loading halus (opacity) pada bagian konten saja
  const contentArea = document.querySelector('.member-content-area');
  if (contentArea) contentArea.style.opacity = "0.5"; 

  fetch(url)
    .then((response) => response.text())
    .then((html) => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      
      // --- LOGIKA PENTING DI SINI ---
      // Kita HANYA mengambil bagian '.member-content-area' dari data baru
      // dan menimpanya ke '.member-content-area' yang lama.
      // HEADER & SEARCH BAR TIDAK DISENTUH -> JADI KURSOR TIDAK HILANG
      
      const newContent = doc.querySelector('.member-content-area');
      const oldContent = document.querySelector('.member-content-area');
      
      if (newContent && oldContent) {
          oldContent.innerHTML = newContent.innerHTML;
      } else {
          // Fallback jika struktur HTML berubah drastis
          const container = document.getElementById("member-list-container");
          if (container) container.innerHTML = html;
      }
      
      // Kembalikan opacity
      if (oldContent) oldContent.style.opacity = "1";
      else if (contentArea) contentArea.style.opacity = "1";

      // Cek Halaman Kosong & Redirect Otomatis (Jika hapus item terakhir di hal 2)
      const currentParams = new URLSearchParams(window.location.search);
      const currentPage = parseInt(currentParams.get("p")) || 1;
      const totalCards = document.querySelectorAll(".mit-card").length;
      const isEmptyMessage = document.body.innerText.includes("Belum ada data");

      if (currentPage > 1 && (totalCards === 0 || isEmptyMessage)) {
        currentParams.set("p", currentPage - 1); 
        const newUrl = window.location.pathname + "?" + currentParams.toString();
        window.history.replaceState(null, "", newUrl); 
        loadMemberList(); // Reload lagi ke halaman sebelumnya
      }
      
      // Scroll ke atas hanya jika pindah halaman (bukan saat searching)
      if (scrollToTop) {
         const wrapper = document.querySelector('.member-wrapper');
         if(wrapper) wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    })
    .catch((error) => {
      console.error("Error loading grid:", error);
      if (contentArea) contentArea.style.opacity = "1";
    });
}


// =========================================================
// 3. IMAGE HANDLING (Preview & Validasi)
// =========================================================

function previewMemberImage(event) {
    const input = event.target;
    const imgPreview = document.getElementById("imgPreview");
    const previewBox = document.getElementById("previewBox");
    const errorContainer = document.getElementById("fileError");
    
    const MAX_FILE_SIZE = 5 * 1024 * 1024; 
    const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp']; 

    if (errorContainer) { errorContainer.textContent = ""; errorContainer.style.display = "none"; }

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();

        if (!ALLOWED_EXT.includes(fileExt)) {
            errorContainer.textContent = `Ekstensi tidak diizinkan.`; errorContainer.style.display = "block";
            input.value = ""; 
            if(previewBox) previewBox.style.display = "none"; 
            updateMemberFileName(input); return;
        }
        if (file.size > MAX_FILE_SIZE) {
            errorContainer.textContent = "File terlalu besar (Max 5MB)."; errorContainer.style.display = "block";
            input.value = ""; 
            if(previewBox) previewBox.style.display = "none"; 
            updateMemberFileName(input); return;
        }

        const reader = new FileReader();
        reader.onload = function (e) { 
            imgPreview.src = e.target.result; 
            if(previewBox) previewBox.style.display = "flex"; 
            validateFormState(); 
        };
        reader.readAsDataURL(file); 
    } else {
        imgPreview.src = ""; 
        if(previewBox) previewBox.style.display = "none"; 
        validateFormState();
    }
}

function removeMemberImage() {
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

  validateFormState();
}

function updateMemberFileName(input) {
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
  validateFormState();
}


// =========================================================
// 4. MODAL DETAIL
// =========================================================
function showMemberDetail(data) {
    const modal = document.getElementById("memberDetailModal");
    if(!modal) return; // Safety check
    
    document.getElementById("detailNama").textContent = data.nama_member;
    document.getElementById("detailJabatan").textContent = data.jabatan || '-';
    document.getElementById("detailNidn").textContent = data.nidn || '-';
    document.getElementById("detailDeskripsi").textContent = data.deskripsi || '-';

    const imgElement = document.getElementById("detailFoto");
    if (data.gambar) {
        imgElement.src = `../public/uploads/member/${data.gambar}`;
        imgElement.style.display = 'inline-block';
    } else {
        imgElement.style.display = 'none';
    }

    const linkContainer = document.getElementById("linkContainer");
    linkContainer.innerHTML = ''; 
    
    if (data.links && data.links.length > 0) {
        data.links.forEach(link => {
            const a = document.createElement('a');
            const url = (link.url_link.startsWith('http')) ? link.url_link : 'https://' + link.url_link;
            a.href = url;
            a.target = "_blank";
            a.className = "link-btn";
            a.textContent = link.judul_link;
            a.style.cssText = "display:inline-block; padding:5px 10px; background:#02406C; color:#fff; border-radius:4px; text-decoration:none; margin-right:5px; margin-bottom:5px; font-size:12px;";
            linkContainer.appendChild(a);
        });
    } else {
        linkContainer.innerHTML = '<span style="color:#999; font-style:italic;">Tidak ada link.</span>';
    }

    modal.style.display = "block";
}

function closeMemberModal() { 
    const modal = document.getElementById("memberDetailModal");
    if(modal) modal.style.display = "none"; 
}
window.onclick = function(event) {
    const modal = document.getElementById("memberDetailModal");
    if (event.target == modal) modal.style.display = "none";
}


// =========================================================
// 5. FORM & CRUD (VALIDATION, SUBMIT, DELETE)
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
    toastContainer.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('hide'); 
        setTimeout(() => {
            toast.remove();
            if (toastContainer.children.length === 0) toastContainer.remove();
        }, 300); 
    }, 4000); 
}

function loadEmptyMemberForm(successMessage) {
    const formContainer = document.getElementById("form-content-wrapper"); 
    if (!formContainer) return;
    const url = "module/member/form-load.php?success_msg=" + encodeURIComponent(successMessage);
    displayAlert(successMessage, "success");
    fetch(url).then((response) => response.text()).then((html) => {
        formContainer.innerHTML = html;
        setupFormValidation(); 
        const newNameInput = document.querySelector('#memberForm input[name="nama_member"]');
        if (newNameInput) setTimeout(() => { newNameInput.focus(); }, 50); 
    });
}

function cancelMemberForm() {
    const formContainer = document.getElementById("form-content-wrapper");
    if (!formContainer) return;
    formContainer.innerHTML = '<div style="text-align:center; padding:20px;">Mereset form...</div>';
    fetch("module/member/form-load.php").then((response) => response.text()).then((html) => {
            formContainer.innerHTML = html;
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.delete('edit'); 
            window.history.pushState({}, '', currentUrl);
            setupFormValidation();
            const namaInput = document.querySelector('#memberForm input[name="nama_member"]');
            if (namaInput) setTimeout(() => { namaInput.focus(); }, 50);
            const card = document.querySelector('.card');
            if(card) card.scrollIntoView({ behavior: 'smooth' });
    });
}

function deleteMember(id) {
    if (!confirm("Anda yakin ingin menghapus member ini?")) return;
    const url = "module/member/delete.php";
    const formData = new FormData();
    formData.append("id", id);

    fetch(url, { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
        if (data.status === "success") {
            displayAlert(data.message || "Member berhasil dihapus.", "success");
            loadMemberList();
        } else {
            displayAlert(data.message, "error");
        }
    })
    .catch((error) => {
        console.error("AJAX Delete Error:", error);
        displayAlert("Terjadi kesalahan sistem saat menghapus member.", "error");
    });
}

// --- FORM VALIDATION ---
let initialFormString = ""; 
function getFormString() {
    const form = document.getElementById("memberForm");
    if (!form) return "";
    const formData = new FormData(form);
    formData.delete("gambar"); 
    return new URLSearchParams(formData).toString();
}
function captureInitialState() { initialFormString = getFormString(); }

function validateFormState() {
    const btnSimpan = document.getElementById("submitBtn");
    const btnBatal = document.querySelector(".button-group .btn-secondary");
    if (!btnSimpan) return;

    const idMemberInput = document.querySelector('input[name="id_member"]');
    const isEditMode = idMemberInput && idMemberInput.value !== "";
    let hasChanges = false;
    const currentString = getFormString();
    if (currentString !== initialFormString) hasChanges = true;
    const fileInput = document.getElementById('inputGambar');
    if (fileInput && fileInput.files.length > 0) hasChanges = true;

    if (btnBatal) {
        if (isEditMode) enableBtn(btnBatal);
        else {
            if (hasChanges) enableBtn(btnBatal);
            else disableBtn(btnBatal);
        }
    }

    const namaInput = document.querySelector('input[name="nama_member"]');
    const nidnInput = document.querySelector('input[name="nidn"]');
    const jabatanInput = document.querySelector('[name="jabatan"]');
    const nama = namaInput ? namaInput.value.trim() : "";
    const nidn = nidnInput ? nidnInput.value.trim() : "";
    const jabatan = jabatanInput ? jabatanInput.value.trim() : "";
    const isRequiredFilled = (nama !== "" && nidn !== "" && jabatan !== "");

    if (isRequiredFilled && hasChanges) {
        enableBtn(btnSimpan);
        btnSimpan.textContent = isEditMode ? "Update" : "Simpan";
    } else {
        disableBtn(btnSimpan);
        btnSimpan.textContent = isEditMode ? "Update" : "Simpan";
    }
}
function enableBtn(btn) { btn.disabled = false; btn.style.opacity = "1"; btn.style.cursor = "pointer"; }
function disableBtn(btn) { btn.disabled = true; btn.style.opacity = "0.6"; btn.style.cursor = "not-allowed"; }
function debounce(func, delay) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}
function setupFormValidation() {
    const inputs = document.querySelectorAll('#memberForm input, #memberForm textarea, #memberForm select');
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

// STARTUP
document.addEventListener("DOMContentLoaded", function () {
  setupFormValidation();
  
  document.addEventListener("submit", function (e) {
      if (e.target && e.target.id === "memberForm") {
        e.preventDefault(); 
        const form = e.target;
        const formData = new FormData(form);
        const url = "module/member/save.php"; 
        
        const submitBtn = document.getElementById("submitBtn");
        submitBtn.disabled = true; 
        submitBtn.textContent = "Memproses...";
  
        fetch(url, { method: "POST", body: formData })
          .then((response) => response.json())
          .then((data) => {
            if (data.status === "success") {
              loadMemberList();
              const isUpdate = formData.get("id_member"); 
              if (isUpdate) {
                  const currentUrl = new URL(window.location);
                  currentUrl.searchParams.delete('edit'); 
                  window.history.pushState({}, document.title, currentUrl);
                  loadEmptyMemberForm("Update Berhasil! Data telah disimpan.");
              } else {
                  loadEmptyMemberForm(data.message);
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
              if (finalBtn) { finalBtn.disabled = false; finalBtn.textContent = "Simpan"; }
          });
      }
  });
});