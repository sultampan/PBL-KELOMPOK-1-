// admin/assets/js/member.js

// --- FUNGSI PENCARIAN MEMBER ---
function searchMember() {
    const input = document.getElementById('searchMemberInput');
    if (!input) return;

    const keyword = input.value.trim();
    const currentUrl = new URL(window.location.href);
    
    // Set parameter keyword di URL
    if (keyword) {
        currentUrl.searchParams.set('keyword', keyword);
    } else {
        currentUrl.searchParams.delete('keyword'); // Kalau kosong, hapus param
    }
    
    // Reset ke halaman 1 saat mencari baru
    currentUrl.searchParams.set('p', 1);

    // Update URL dan Load Data
    window.history.pushState(null, "", currentUrl);
    loadMemberList();
}

// Fitur tambahan: Tekan Enter di input langsung cari
document.addEventListener('keydown', function(e) {
    if (e.target && e.target.id === 'searchMemberInput' && e.key === 'Enter') {
        searchMember();
    }
});

// --- FUNGSI RESET PENCARIAN ---
function resetSearchMember() {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.delete('keyword');
    currentUrl.searchParams.set('p', 1); // Reset ke hal 1
    
    window.history.pushState(null, "", currentUrl);
    loadMemberList();
}

// 1. PREVIEW GAMBAR
function previewMemberImage(event) {
    const input = event.target;
    const imgPreview = document.getElementById("imgPreview");
    const previewBox = document.getElementById("previewBox"); // <--- AMBIL ELEMENT KOTAK
    
    const MAX_FILE_SIZE = 5 * 1024 * 1024; 
    const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp']; 

    const errorContainer = document.getElementById("fileError");
    if (errorContainer) { errorContainer.textContent = ""; errorContainer.style.display = "none"; }

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();

        // Validasi Ekstensi
        if (!ALLOWED_EXT.includes(fileExt)) {
            errorContainer.textContent = `Ekstensi tidak diizinkan.`; errorContainer.style.display = "block";
            input.value = ""; 
            
            // Sembunyikan kotak kalau error
            if(previewBox) previewBox.style.display = "none"; 
            
            updateMemberFileName(input); return;
        }
        
        // Validasi Size
        if (file.size > MAX_FILE_SIZE) {
            errorContainer.textContent = "File terlalu besar (Max 5MB)."; errorContainer.style.display = "block";
            input.value = ""; 
            
            // Sembunyikan kotak kalau error
            if(previewBox) previewBox.style.display = "none"; 
            
            updateMemberFileName(input); return;
        }

        const reader = new FileReader();
        reader.onload = function (e) { 
            imgPreview.src = e.target.result; 
            
            // MUNCULKAN KOTAK SAAT BERHASIL LOAD
            // Pakai 'flex' karena biasanya di CSS .form-preview-box pakai display: flex untuk center
            if(previewBox) previewBox.style.display = "flex"; 
            
            validateFormState(); 
        };
        reader.readAsDataURL(file); 
    } else {
        // Kalau batal pilih file
        imgPreview.src = ""; 
        if(previewBox) previewBox.style.display = "none"; // Sembunyikan lagi
        validateFormState();
    }
}

function removeMemberImage() {
  const input = document.getElementById("inputGambar");
  const img = document.getElementById("imgPreview");
  const previewBox = document.getElementById("previewBox"); // <--- AMBIL ELEMENT KOTAK
  const removeBtn = document.getElementById("removeImageBtn");
  const fileNameText = document.getElementById("fileNameText");

  if (input) input.value = "";
  if (img) img.src = "";
  
  // SEMBUNYIKAN KOTAK SAAT DIHAPUS
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
  
  // [BARU] Validasi nama file
  validateFormState();
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
    
    // Logic Link Array
    if (data.links && data.links.length > 0) {
        data.links.forEach(link => {
            const a = document.createElement('a');
            // Pastikan URL ada http/https
            const url = (link.url_link.startsWith('http')) ? link.url_link : 'https://' + link.url_link;
            a.href = url;
            a.target = "_blank";
            a.className = "link-btn";
            a.textContent = link.judul_link;
            // Styling inline sementara biar rapi
            a.style.cssText = "display:inline-block; padding:5px 10px; background:#02406C; color:#fff; border-radius:4px; text-decoration:none; margin-right:5px; margin-bottom:5px; font-size:12px;";
            linkContainer.appendChild(a);
        });
    } else {
        linkContainer.innerHTML = '<span style="color:#999; font-style:italic;">Tidak ada link.</span>';
    }

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

// --- GANTI FUNGSI deleteMember DENGAN INI ---

function deleteMember(id) {
    // 1. Konfirmasi
    if (!confirm("Anda yakin ingin menghapus member ini?")) return;

    const url = "module/member/delete.php";
    const formData = new FormData();
    formData.append("id", id);

    // (Opsional) Tampilkan pesan loading kuning/abu
    // displayAlert("Memproses penghapusan...", "warning");

    fetch(url, {
        method: "POST",
        body: formData
    })
    .then((response) => response.json())
    .then((data) => {
        if (data.status === "success") {
            // [FIX] Tampilkan Notif Hijau (Sukses)
            displayAlert(data.message || "Member berhasil dihapus.", "success");

            // Refresh tampilan grid
            loadMemberList();
        } else {
            // Error dari PHP (Merah)
            displayAlert(data.message, "error");
        }
    })
    .catch((error) => {
        console.error("AJAX Delete Error:", error);
        // Pesan error jaringan (Merah)
        displayAlert("Terjadi kesalahan sistem saat menghapus member.", "error");
    });
}

// =========================================================
// 4. VALIDASI FORM & STATE CHECK (BARU)
// =========================================================

// Kita pakai String untuk menyimpan snapshot seluruh form
// Ini lebih aman untuk array input seperti judul_link[]
let initialFormString = ""; 

// Fungsi Helper untuk mengambil "Foto Copy" Form saat ini
function getFormString() {
    const form = document.getElementById("memberForm");
    if (!form) return "";
    
    // FormData otomatis menangkap semua input teks, select, textarea, hidden, dan array
    const formData = new FormData(form);
    
    // Hapus input file dari string (karena file object berubah-ubah dan tidak bisa dibanding string)
    // Kita handle file terpisah lewat cek files.length
    formData.delete("gambar"); 

    // Trik: Convert ke URLSearchParams biar jadi string rapi "nama=Budi&jabatan=Head..."
    return new URLSearchParams(formData).toString();
}

// Fungsi rekam data awal (dijalankan saat form load/reset)
function captureInitialState() {
    initialFormString = getFormString();
}

// FUNGSI UTAMA: CEK TOMBOL
// admin/assets/js/member.js
function validateFormState() {
    const btnSimpan = document.getElementById("submitBtn");
    const btnBatal = document.querySelector(".button-group .btn-secondary");

    if (!btnSimpan) return;

    // 1. CEK MODE (EDIT ATAU TAMBAH) -- (Kita pindah ke atas biar bisa dipake di logika Batal)
    const idMemberInput = document.querySelector('input[name="id_member"]');
    // Dianggap Mode Edit kalau input ID ada isinya
    const isEditMode = idMemberInput && idMemberInput.value !== "";

    // 2. DETEKSI PERUBAHAN (DIRTY CHECK)
    let hasChanges = false;

    // A. Cek Input Teks/Select/Hidden
    const currentString = getFormString();
    if (currentString !== initialFormString) {
        hasChanges = true;
    }

    // B. Cek Input File
    const fileInput = document.getElementById('inputGambar');
    if (fileInput && fileInput.files.length > 0) {
        hasChanges = true;
    }

    // 3. ATUR TOMBOL BATAL (LOGIKA BARU)
    if (btnBatal) {
        if (isEditMode) {
            // [FIX] Kalau Mode Edit, Batal HARUS SELALU NYALA
            // (Supaya user bisa cancel editing walaupun belum ubah apa-apa)
            enableBtn(btnBatal);
        } else {
            // Kalau Mode Tambah, Batal baru nyala kalau form sudah "kotor" (ada isinya)
            if (hasChanges) enableBtn(btnBatal);
            else disableBtn(btnBatal);
        }
    }

    // 4. ATUR TOMBOL SIMPAN (LOGIKA WAJIB 3 KOLOM)
    const namaInput = document.querySelector('input[name="nama_member"]');
    const nidnInput = document.querySelector('input[name="nidn"]');
    const jabatanInput = document.querySelector('[name="jabatan"]');
    
    const nama = namaInput ? namaInput.value.trim() : "";
    const nidn = nidnInput ? nidnInput.value.trim() : "";
    const jabatan = jabatanInput ? jabatanInput.value.trim() : "";
    
    // Syarat Wajib: 3 Kolom ini harus terisi
    const isRequiredFilled = (nama !== "" && nidn !== "" && jabatan !== "");

    // Tombol Simpan Nyala Jika: (Data Lengkap) DAN (Ada Perubahan)
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

// Fungsi Debounce (Biar gak berat saat ngetik)
function debounce(func, delay) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

// Pasang Event Listener ke SEMUA input
function setupFormValidation() {
    const inputs = document.querySelectorAll('#memberForm input, #memberForm textarea, #memberForm select');
    
    if(inputs.length > 0) {
        // 1. Rekam Data Awal
        captureInitialState();

        // 2. Cek kondisi awal
        validateFormState();

        // 3. Pasang Listener
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
              // 1. Refresh Tabel Member
              loadMemberList();
              
              const isUpdate = formData.get("id_member"); 

              // 2. LOGIKA BARU SETELAH SUKSES
              if (isUpdate) {
                  // === KASUS UPDATE ===
                  
                  // A. Bersihkan URL (Hapus ?edit=123) biar gak nyangkut
                  const currentUrl = new URL(window.location);
                  currentUrl.searchParams.delete('edit'); 
                  window.history.pushState({}, document.title, currentUrl);

                  // B. Reset Form jadi Kosong (Mode Tambah) + Tampilkan Notif Toast
                  loadEmptyMemberForm("Update Berhasil! Data telah disimpan.");
                  
              } else {
                  // === KASUS TAMBAH BARU ===
                  // Reset form jadi kosong + Tampilkan Notif Toast
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
              // Tombol akan di-reset otomatis saat loadEmptyMemberForm selesai merender ulang form
              // Tapi untuk jaga-jaga jika error:
              const finalBtn = document.getElementById("submitBtn");
              if (finalBtn && finalBtn.disabled) {
                  finalBtn.disabled = false;
                  finalBtn.textContent = "Simpan"; // Default balik ke Simpan
              }
          });
      }
});
});