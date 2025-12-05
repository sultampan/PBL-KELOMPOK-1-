// admin/assets/js/member.js

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

// --- FUNGSI MUAT ULANG GRID VIA AJAX (REVISI UNTUK GRID) ---
function loadMemberList() {
  const listContainer = document.getElementById("member-list-container");
  if (!listContainer) return;

  const currentParams = new URLSearchParams(window.location.search);
  const url = "module/member/table-load.php" + window.location.search;

  listContainer.innerHTML = '<div style="text-align:center; padding:20px;">Memuat data...</div>';

  fetch(url)
    .then((response) => response.text())
    .then((html) => {
      // Masukkan HTML ke DOM sementara
      const tempDiv = document.createElement("div");
      tempDiv.innerHTML = html;

      // --- LOGIKA BARU UNTUK GRID ---
      const currentPage = parseInt(currentParams.get("p")) || 1;
      
      // Hitung jumlah kartu (.mit-card) yang ada di HTML baru
      const totalCards = tempDiv.querySelectorAll(".mit-card").length;
      
      // Cek apakah ada pesan "Belum ada data member" (biasanya di tag <p> atau div)
      const isEmptyMessage = tempDiv.innerHTML.includes("Belum ada data member");

      // Jika kita di halaman > 1, tapi datanya 0 (kosong), mundur 1 halaman
      if (currentPage > 1 && totalCards === 0 && (isEmptyMessage || totalCards === 0)) {
        currentParams.set("p", currentPage - 1); 
        window.history.pushState(null, "", window.location.pathname + "?" + currentParams.toString());
        loadMemberList(); // Muat ulang halaman sebelumnya
        return;
      }
      // -----------------------------

      listContainer.innerHTML = html;
    })
    .catch((error) => {
      console.error("Error loading grid:", error);
      listContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat data.</div>';
    });
}

function loadEmptyMemberForm(successMessage) {
    const formContainer = document.getElementById("form-content-wrapper"); 
    if (!formContainer) return;
    const url = "module/member/form-load.php?success_msg=" + encodeURIComponent(successMessage);
    
    displayAlert(successMessage, "success");

    fetch(url).then((response) => response.text()).then((html) => {
        formContainer.innerHTML = html;
        const newNameInput = document.querySelector('#memberForm input[name="nama_member"]');
        if (newNameInput) setTimeout(() => { newNameInput.focus(); }, 50); 
    }).catch((error) => {
        console.error("Error loading form:", error);
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

            const namaInput = document.querySelector('#memberForm input[name="nama_member"]');
            if (namaInput) setTimeout(() => { namaInput.focus(); }, 50);
            
            document.querySelector('.card h2').scrollIntoView({ behavior: 'smooth' });
    }).catch((error) => { console.error("Error resetting form:", error); });
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

// ... (kode fungsi preview, remove, dll di atas BIARKAN SAJA) ...

document.addEventListener("DOMContentLoaded", function () {
  document.addEventListener("submit", function (e) {
    if (e.target && e.target.id === "memberForm") {
      e.preventDefault(); 
      const form = e.target;
      const formData = new FormData(form);
      const url = "module/member/save.php"; 
      
      const submitBtn = document.getElementById("submitBtn");
      // 1. Simpan teks asli tombol sebelum diubah (Simpan / Update)
      const originalBtnText = submitBtn.textContent; 
      
      submitBtn.disabled = true; 
      submitBtn.textContent = "Memproses...";

      fetch(url, { method: "POST", body: formData })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            loadMemberList();
            const isUpdate = formData.get("id_member"); 
            loadEmptyMemberForm(data.message);
            if (isUpdate) window.history.pushState({}, document.title, window.location.pathname + "?page=member");
          } else {
            // JIKA ERROR
            displayAlert(data.message, "error");
            
            // Reset input file visual jika error (opsional)
            const input = document.getElementById('inputGambar');
            const img = document.getElementById('imgPreview');
            if (input) input.value = ''; 
            if (img) img.style.display = 'none'; 
            updateMemberFileName(input);
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
                // Kembalikan teks tombol ke aslinya ("Simpan" atau "Update")
                // Kita cek apakah input id_member ada isinya di formData
                const isEditMode = formData.get("id_member"); 
                finalBtn.textContent = isEditMode ? "Update" : "Simpan";
            }
        });
    }
  });
});

// admin/assets/js/member.js

// --- FUNGSI MODAL DETAIL ---

function showMemberDetail(data) {
    const modal = document.getElementById("memberDetailModal");
    
    // 1. Isi Data Teks
    document.getElementById("detailNama").textContent = data.nama_member;
    document.getElementById("detailJabatan").textContent = data.jabatan || '-';
    document.getElementById("detailNidn").textContent = data.nidn || '-';
    document.getElementById("detailDeskripsi").textContent = data.deskripsi || '-';

    // 2. Isi Foto (Handle path thumbnail vs original)
    const imgElement = document.getElementById("detailFoto");
    if (data.gambar) {
        // Asumsi path gambar publik ada di ../public/uploads/member/
        // Kita pakai path relatif sederhana untuk display
        imgElement.src = `../public/uploads/member/${data.gambar}`;
        imgElement.style.display = 'inline-block';
    } else {
        imgElement.style.display = 'none';
    }

    // 3. Isi Link (Logic Fix URL di JS)
    const linkContainer = document.getElementById("linkContainer");
    linkContainer.innerHTML = ''; // Kosongkan dulu
    
    // Helper function buat fix URL
    const fixUrlJs = (url) => {
        if (!url) return '';
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            return 'https://' + url;
        }
        return url;
    };

    let hasLink = false;

    if (data.google_scholar) {
        linkContainer.innerHTML += `<a href="${fixUrlJs(data.google_scholar)}" target="_blank" class="link-btn">Google Scholar</a>`;
        hasLink = true;
    }
    if (data.orcid) {
        linkContainer.innerHTML += `<a href="${fixUrlJs(data.orcid)}" target="_blank" class="link-btn">ORCID</a>`;
        hasLink = true;
    }
    if (data.sinta) {
        linkContainer.innerHTML += `<a href="${fixUrlJs(data.sinta)}" target="_blank" class="link-btn">Sinta</a>`;
        hasLink = true;
    }

    if (!hasLink) {
        linkContainer.innerHTML = '<span style="color:#999; font-style:italic;">Tidak ada link.</span>';
    }

    // 4. Tampilkan Modal
    modal.style.display = "block";
}

function closeMemberModal() {
    const modal = document.getElementById("memberDetailModal");
    modal.style.display = "none";
}

// Tutup modal kalau klik di luar kotak (backdrop)
window.onclick = function(event) {
    const modal = document.getElementById("memberDetailModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}