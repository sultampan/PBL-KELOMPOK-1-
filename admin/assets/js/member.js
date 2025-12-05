// admin/assets/js/member.js

// 1. PREVIEW GAMBAR
function previewMemberImage(event) {
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
            errorContainer.textContent = `Ekstensi tidak diizinkan.`; errorContainer.style.display = "block";
            input.value = ""; imgPreview.style.display = "none"; updateMemberFileName(input); return;
        }
        if (file.size > MAX_FILE_SIZE) {
            errorContainer.textContent = "File terlalu besar (Max 5MB)."; errorContainer.style.display = "block";
            input.value = ""; imgPreview.style.display = "none"; updateMemberFileName(input); return;
        }

        const reader = new FileReader();
        reader.onload = function (e) { imgPreview.src = e.target.result; imgPreview.style.display = "block"; };
        reader.readAsDataURL(file); 
    } else {
        imgPreview.src = ""; imgPreview.style.display = "none";
    }
}

function removeMemberImage() {
  const input = document.getElementById("inputGambar");
  const img = document.getElementById("imgPreview");
  const removeBtn = document.getElementById("removeImageBtn");
  const fileNameText = document.getElementById("fileNameText");

  if (input) input.value = "";
  if (img) { img.src = ""; img.style.display = "none"; }
  if (fileNameText) fileNameText.textContent = "Tidak ada file yang dipilih...";
  if (removeBtn) removeBtn.style.display = "none";

  const removeExisting = document.getElementById("removeExistingImage");
  if (removeExisting) removeExisting.value = "1";
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

// 2. MODAL LOGIC
function showMemberDetail(data) {
    const modal = document.getElementById("memberDetailModal");
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
    
    const fixUrlJs = (url) => {
        if (!url) return '';
        if (!url.startsWith('http://') && !url.startsWith('https://')) return 'https://' + url;
        return url;
    };

    let hasLink = false;
    if (data.google_scholar) { linkContainer.innerHTML += `<a href="${fixUrlJs(data.google_scholar)}" target="_blank" class="link-btn">Google Scholar</a>`; hasLink = true; }
    if (data.orcid) { linkContainer.innerHTML += `<a href="${fixUrlJs(data.orcid)}" target="_blank" class="link-btn">ORCID</a>`; hasLink = true; }
    if (data.sinta) { linkContainer.innerHTML += `<a href="${fixUrlJs(data.sinta)}" target="_blank" class="link-btn">Sinta</a>`; hasLink = true; }
    if (!hasLink) linkContainer.innerHTML = '<span style="color:#999; font-style:italic;">Tidak ada link.</span>';

    modal.style.display = "block";
}

function closeMemberModal() { document.getElementById("memberDetailModal").style.display = "none"; }
window.onclick = function(event) {
    const modal = document.getElementById("memberDetailModal");
    if (event.target == modal) modal.style.display = "none";
}

// 3. CORE AJAX LOGIC (PAGINATION & RELOAD)

// Handle Back/Forward Button
window.addEventListener('popstate', function(event) {
    loadMemberList();
});

// Handle Klik Pagination (.page-link) biar gak reload halaman
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('page-link')) {
        e.preventDefault(); // Stop reload
        const href = e.target.getAttribute('href'); // Ambil link (misal: ?page=member&p=2)
        if (href) {
            // Update URL browser tanpa reload
            window.history.pushState(null, "", href);
            // Panggil fungsi load ajax
            loadMemberList();
        }
    }
});

function loadMemberList() {
  const listContainer = document.getElementById("member-list-container");
  if (!listContainer) return;

  const currentParams = new URLSearchParams(window.location.search);
  const url = "module/member/table-load.php" + window.location.search;

  listContainer.style.opacity = "0.5"; 

  fetch(url)
    .then((response) => response.text())
    .then((html) => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      
      // Ambil isi baru
      const newContentWrapper = doc.getElementById('member-list-container');
      
      if (newContentWrapper) {
          // GANTI TOTAL isi container lama dengan yang baru (Anti Numpuk)
          listContainer.innerHTML = newContentWrapper.innerHTML;
      } else {
          listContainer.innerHTML = html;
      }
      
      listContainer.style.opacity = "1";

      // Cek Halaman Kosong (Redirect Otomatis)
      const currentPage = parseInt(currentParams.get("p")) || 1;
      const totalCards = listContainer.querySelectorAll(".mit-card").length;
      const isEmptyMessage = listContainer.innerHTML.includes("Belum ada data");

      if (currentPage > 1 && totalCards === 0 && (isEmptyMessage || totalCards === 0)) {
        currentParams.set("p", currentPage - 1); 
        const newUrl = window.location.pathname + "?" + currentParams.toString();
        window.history.replaceState(null, "", newUrl); 
        loadMemberList(); 
      }
    })
    .catch((error) => {
      console.error("Error loading grid:", error);
      listContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat data.</div>';
      listContainer.style.opacity = "1";
    });
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
            document.querySelector('.card h2').scrollIntoView({ behavior: 'smooth' });
    });
}

function deleteMember(id) {
  if (!confirm("Anda yakin ingin menghapus member ini?")) return;
  const url = "module/member/delete.php"; 
  const formData = new FormData();
  formData.append("id", id); 
  displayAlert("Menghapus data...", "warning");

  fetch(url, { method: "POST", body: formData })
    .then((response) => response.json()).then((data) => {
      if (data.status === "success") loadMemberList(); 
      else displayAlert(data.message, "error");
    }).catch((error) => { console.error("AJAX Delete Error:", error); displayAlert("Terjadi kesalahan jaringan.", "error"); });
}

// 4. VALIDASI FORM
function validateFormState() {
    const nama = document.querySelector('input[name="nama_member"]')?.value.trim();
    const nidn = document.querySelector('input[name="nidn"]')?.value.trim();
    const jabatanInput = document.querySelector('[name="jabatan"]');
    const jabatan = jabatanInput ? jabatanInput.value.trim() : '';
    const idMemberInput = document.querySelector('input[name="id_member"]');
    const isEditMode = idMemberInput && idMemberInput.value !== "";
    
    // Tambahan: Deteksi perubahan input apapun (untuk tombol Batal)
    const allInputs = document.querySelectorAll('#memberForm input:not([type=hidden]), #memberForm textarea, #memberForm select');
    let isDirty = false;
    allInputs.forEach(inp => { if(inp.value.trim() !== '') isDirty = true; });

    const btnSimpan = document.getElementById("submitBtn");
    const btnBatal = document.querySelector(".button-group .btn-secondary");

    if (btnSimpan) {
        if (nama && nidn && jabatan) {
            btnSimpan.disabled = false; btnSimpan.style.opacity = "1"; btnSimpan.style.cursor = "pointer";
        } else {
            btnSimpan.disabled = true; btnSimpan.style.opacity = "0.6"; btnSimpan.style.cursor = "not-allowed";
        }
    }

    if (btnBatal) {
        if (isEditMode || isDirty) {
            btnBatal.disabled = false; btnBatal.style.opacity = "1"; btnBatal.style.cursor = "pointer";
        } else {
            btnBatal.disabled = true; btnBatal.style.opacity = "0.6"; btnBatal.style.cursor = "not-allowed";
        }
    }
}

function setupFormValidation() {
    const inputs = document.querySelectorAll('#memberForm input, #memberForm textarea, #memberForm select');
    if(inputs.length > 0) {
        validateFormState();
        inputs.forEach(input => {
            input.addEventListener('input', validateFormState);
            input.addEventListener('change', validateFormState);
        });
    }
}

document.addEventListener("DOMContentLoaded", function () {
  setupFormValidation();
  document.addEventListener("submit", function (e) {
    if (e.target && e.target.id === "memberForm") {
      e.preventDefault(); 
      const form = e.target;
      const formData = new FormData(form);
      const url = "module/member/save.php"; 
      
      const submitBtn = document.getElementById("submitBtn");
      submitBtn.disabled = true; submitBtn.textContent = "Memproses...";

      fetch(url, { method: "POST", body: formData })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            loadMemberList();
            const isUpdate = formData.get("id_member"); 
            loadEmptyMemberForm(data.message);
            if (isUpdate) window.history.pushState({}, document.title, window.location.pathname + "?page=member");
          } else {
            displayAlert(data.message, "error");
            const input = document.getElementById('inputGambar');
            if (input) input.value = ''; 
            updateMemberFileName(input);
          }
        })
        .catch((error) => { console.error("AJAX Error:", error); displayAlert("Terjadi kesalahan jaringan.", "error"); })
        .finally(() => { 
            const finalBtn = document.getElementById("submitBtn");
            if (finalBtn) {
                finalBtn.disabled = false;
                const isEditMode = formData.get("id_member"); 
                finalBtn.textContent = isEditMode ? "Update" : "Simpan";
            }
        });
    }
  });
});