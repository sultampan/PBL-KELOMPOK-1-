// admin/assets/js/produk.js

function previewImage(event) {
    const input = event.target;
    const imgPreview = document.getElementById("imgPreview");
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB 
    const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp']; 

    const errorContainer = document.getElementById("fileError");
    if (errorContainer) {
        errorContainer.textContent = "";
        errorContainer.style.display = "none";
    }

    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // 1. VALIDASI EKSTENSI & UKURAN (Tetap dipertahankan)
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();

        if (!ALLOWED_EXT.includes(fileExt)) {
            // ... (Logika penolakan ekstensi) ...
            errorContainer.textContent = `Ekstensi file .${fileExt} tidak diizinkan.`;
            errorContainer.style.display = "block";
            input.value = ""; 
            imgPreview.style.display = "none";
            updateFileName(input); 
            return;
        }

        if (file.size > MAX_FILE_SIZE) {
            // ... (Logika penolakan ukuran) ...
            errorContainer.textContent = "Ukuran file melebihi batas 5MB.";
            errorContainer.style.display = "block";
            input.value = ""; 
            imgPreview.style.display = "none";
            updateFileName(input);
            return;
        }

        // 2. Tampilkan File ASLI (menggunakan FileReader)
        const reader = new FileReader();
        reader.onload = function (e) {
            // Langsung set src ke Data URL asli yang dibaca oleh FileReader
            imgPreview.src = e.target.result;
            imgPreview.style.display = "block";
        };
        // Membaca file sebagai Data URL (base64)
        reader.readAsDataURL(file); 

    } else {
        imgPreview.src = "";
        imgPreview.style.display = "none";
    }
}

/**
 * Fungsi untuk menghapus gambar dari preview (tombol 'X')
 * @param {boolean} isEditMode - True jika saat ini berada di mode Edit.
 */
function removeImagePreview(isEditMode) {
  const input = document.getElementById("inputGambar");
  const img = document.getElementById("imgPreview");
  const displayContainer = document.getElementById("image-display-container");
  const hiddenInputDelete = document.getElementById("hapusGambarLama"); // Hidden input untuk PHP

  // 1. Bersihkan Input File (agar form tidak mengirim file)
  input.value = "";

  // 2. Kosongkan Visual
  img.src = "";
  displayContainer.style.display = "none";

  // 3. LOGIC KHUSUS MODE EDIT
  if (isEditMode && hiddenInputDelete) {
    // Jika mode Edit, atur hidden field ke 1.
    // Ini memberitahu PHP untuk menghapus file yang namanya ada di gambar_lama (DB).
    hiddenInputDelete.value = "1";
  }

  // Opsional: Bersihkan pesan error file
  const errorContainer = document.getElementById("fileError");
  if (errorContainer) {
    errorContainer.textContent = "";
    errorContainer.style.display = "none";
  }
}

// --- 2. FUNGSI PEMBANTU (Alert Pop-up) ---
// Perlu dipanggil di AJAX success/error
function displayAlert(message, type) {
    // 1. Pastikan container global ada di DOM
    let toastContainer = document.getElementById("toast-container");
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }
    
    // 2. Buat elemen toast baru
    const toast = document.createElement('div');
    // Tambahkan kelas 'toast' dan kelas 'hide' di awal (untuk animasi masuk)
    toast.className = `alert toast ${type}`;
    toast.innerHTML = message;
    
    // Masukkan ke container dan pastikan terlihat
    toastContainer.appendChild(toast);
    
    // 3. Atur timer untuk menghilangkan notifikasi
    setTimeout(() => {
        // Mulai transisi untuk menghapus
        toast.classList.add('hide'); 
        
        // Setelah transisi, hapus elemen sepenuhnya
        setTimeout(() => {
            toast.remove();
            // Opsional: Hapus container jika sudah kosong
            if (toastContainer.children.length === 0) {
                 toastContainer.remove();
            }
        }, 300); // Harus sama dengan durasi transisi CSS
    }, 4000); // Notifikasi tampil selama 4 detik
}

// --- 3. FUNGSI MUAT ULANG TABEL VIA AJAX ---
// admin/assets/js/produk.js (Modifikasi fungsi loadProductList)

function loadProductList() {
  const listContainer = document.getElementById("product-list-container");
  if (!listContainer) return;

  // 1. Ambil parameter URL saat ini (misal: p=2)
  const currentParams = new URLSearchParams(window.location.search);

  // Tentukan URL AJAX
  const url = "module/produk/table-load.php" + window.location.search;

  listContainer.innerHTML =
    '<div style="text-align:center; padding:20px;">Memuat data...</div>';

  fetch(url)
    .then((response) => response.text())
    .then((html) => {
      // 2. EKSTRAK TOTAL REKOR DARI HTML YANG BARU DIMUAT
      // Kita perlu mengambil total data dari DOM yang baru (walaupun tidak terlihat)

      // Masukkan HTML ke DOM sementara untuk diekstrak
      const tempDiv = document.createElement("div");
      tempDiv.innerHTML = html;

      // Asumsi: Total record (untuk paginasi) dihitung di PHP dan disimpan di suatu tempat
      // Jika Anda tidak menyimpannya, kita harus hitung manual di PHP.
      // UNTUK KODE INI: Kita asumsikan ada elemen tersembunyi yang menyimpan total halaman.

      // CARA YANG LEBIH AMAN: Asumsikan PHP mengembalikan totalPages dalam array terpisah
      // Karena ini tidak bisa, kita akan mengandalkan PHP untuk mengirim HTML yang benar

      // Solusi Sederhana: Cek apakah tabel KOSONG setelah penghapusan
      // Jika halaman saat ini (> 1) dan tabel kosong, pindah ke halaman 1
      const currentPage = parseInt(currentParams.get("p")) || 1;
      const tableBody = tempDiv.querySelector(".table tbody");

      if (
        currentPage > 1 &&
        tableBody &&
        tableBody.children.length === 1 &&
        tableBody.querySelector("td[colspan]")
      ) {
        // Kondisi: Kita berada di halaman > 1, dan tabel hanya berisi baris 'Belum ada produk'

        // 3. JIKA KOSONG, GANTI URL KE HALAMAN 1 (ATAU HALAMAN SEBELUMNYA)
        currentParams.set("p", currentPage - 1); // Mundur satu halaman

        // 4. Update URL di browser tanpa reload
        window.history.pushState(
          null,
          "",
          window.location.pathname + "?" + currentParams.toString()
        );

        // 5. Muat ulang tabel lagi dengan URL yang baru
        loadProductList();
        return;
      }

      // Jika tabel tidak kosong atau sudah halaman 1, tampilkan HTML normal
      listContainer.innerHTML = html;
    })
    .catch((error) => {
      console.error("Error loading table:", error);
      listContainer.innerHTML =
        '<div style="text-align:center; color:red;">Gagal memuat tabel.</div>';
    });
}

// --- 4. LOGIKA UTAMA (DOM LOADED) ---
document.addEventListener("DOMContentLoaded", function () {
  // Logika Tambahan: Delegasi Event untuk Form Submit
  // Kita pasang listener ke dokumen (atau body) karena elemen form akan diganti oleh AJAX.
  document.addEventListener("submit", function (e) {
    // Cek apakah event submit berasal dari form dengan ID productForm
    if (e.target && e.target.id === "productForm") {
      e.preventDefault(); // Mencegah reload halaman

      const form = e.target;
      const formData = new FormData(form);
      const url = "module/produk/save.php";

      const submitBtn = document.getElementById("submitBtn");
      submitBtn.disabled = true;
      submitBtn.textContent = "Memproses...";

      fetch(url, { method: "POST", body: formData })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            // LOGIKA SUKSES
            loadProductList();

            const isUpdate = formData.get("id_produk");

            // Selalu muat ulang form card untuk mereset state dan menampilkan pesan sukses
            loadEmptyForm(data.message);

            if (isUpdate) {
              // Hapus ID Edit dari URL browser
              window.history.pushState(
                {},
                document.title,
                window.location.pathname + "?page=produk"
              );
            }
          } else {
            //LOGIKA ERROR DARI SERVER (saat upload gagal, dll.)
            displayAlert(data.message, "error");

            // 🔑 KUNCI: Reset input file setelah error dari server!
                        const input = document.getElementById('inputGambar');
                        const img = document.getElementById('imgPreview');
                        if (input) input.value = ''; // Hapus file dari input
                        if (img) img.style.display = 'none'; // Sembunyikan preview
                        
                        // Opsional: Perbarui teks label
                        updateFileName(input);
          }
        })
        .catch((error) => {
          // ... (Logika error Jaringan/Parsing) ...
          console.error("AJAX Error:", error);
          displayAlert(
            "Terjadi kesalahan jaringan atau server. Cek Konsol (F12).",
            "error"
          );
        })
        .finally(() => {
          const finalBtn = document.getElementById("submitBtn");
          // Pastikan tombol masih ada sebelum diakses
          if (finalBtn) {
            finalBtn.disabled = false;
            // Tombol akan direset text-nya oleh loadEmptyForm (yang memuat HTML baru)
          }
        });
    }
  });

  // Logika Pembersihan Alert Awal
  // setupInitialAlertCleanup();
});

/**
 * Fungsi untuk menghapus produk via AJAX
 */
function deleteProduct(productId) {
  if (
    !confirm(
      "Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan."
    )
  ) {
    return;
  }

  const url = "module/produk/delete.php";
  const formData = new FormData();
  formData.append("id", productId); // Kirim ID produk sebagai POST data

  // Tampilkan pesan loading di console atau di atas form
  displayAlert("Menghapus produk.", "warning");

  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        loadProductList(); // Muat ulang tabel setelah sukses
      } else {
        displayAlert(data.message, "error");
      }
    })
    .catch((error) => {
      console.error("AJAX Delete Error:", error);
      displayAlert("Terjadi kesalahan jaringan saat menghapus.", "error");
    });
}

// function setupInitialAlertCleanup() {
//   // Target alert yang dimuat langsung dari PHP (bukan dari AJAX)
//   const initialAlert = document.querySelector(".alert");

//   if (initialAlert) {
//     setTimeout(() => {
//       initialAlert.style.transition = "opacity 0.5s ease-out";
//       initialAlert.style.opacity = "0";
//       setTimeout(() => initialAlert.remove(), 500);
//     }, 4000);
//   }
// }

function loadEmptyForm(successMessage) {
    const formContainer = document.getElementById("form-content-wrapper"); 
    if (!formContainer) {
        console.error("ID #form-content-wrapper tidak ditemukan!");
        return;
    }

    // 🚨 KOREKSI PATH: Karena AJAX dipanggil dari base admin/index.php,
    // kita gunakan path relatif ke module/produk/form-load.php
    const url =
        "module/produk/form-load.php?success_msg=" +
        encodeURIComponent(successMessage);

    formContainer.innerHTML =
        '<div style="text-align:center; padding:20px;">Memuat form...</div>';
    
    // 🚨 Tampilkan toast sukses di sini, karena error 'Gagal memuat form' sudah diatasi
    displayAlert(successMessage, "success");

    fetch(url)
        .then((response) => response.text())
        .then((html) => {
            // Hapus semua panggilan setupInitialAlertCleanup()
            // setupInitialAlertCleanup(); // <--- HAPUS
            
            formContainer.innerHTML = html;
            // setupInitialAlertCleanup(); // <--- HAPUS

            const newNameInput = document.querySelector(
                '#productForm input[name="nama"]'
            );

            if (newNameInput) {
                setTimeout(() => {
                    newNameInput.focus();
                }, 50); 
            }
        })
        .catch((error) => {
            console.error("Error loading form:", error);
            formContainer.innerHTML =
                '<div style="text-align:center; color:red;">Gagal memuat form.</div>';
        });
}

/**
 * Mereset input file, menyembunyikan preview, dan menyembunyikan tombol X.
 */
function removeImage() {
  const input = document.getElementById("inputGambar");
  const img = document.getElementById("imgPreview");
  const removeBtn = document.getElementById("removeImageBtn");
  const fileNameText = document.getElementById("fileNameText");

  // Reset input file (ini menghapus file baru yang dipilih)
  if (input) input.value = "";

  // Sembunyikan preview
  if (img) {
    img.src = "";
    img.style.display = "none";
  }

  // Reset teks placeholder
  if (fileNameText) fileNameText.textContent = "Tidak ada file yang dipilih...";

  // Sembunyikan tombol X
  if (removeBtn) removeBtn.style.display = "none";

  // Jika ada gambar lama dari DB, tandai untuk dihapus di server saat submit
  const removeExisting = document.getElementById("removeExistingImage");
  if (removeExisting) removeExisting.value = "1";
  // Anda harus menambahkan logic di save.php untuk menghapus gambar jika remove_existing_image == 1
}

/**
 * Mengubah teks label (dipanggil onchange)
 */
function updateFileName(input) {
  const fileNameText = document.getElementById("fileNameText");
  const removeBtn = document.getElementById("removeImageBtn");

  if (input.files && input.files.length > 0) {
    // Tampilkan nama file dan tombol X
    fileNameText.textContent = input.files[0].name;
    if (removeBtn) removeBtn.style.display = "block";
  } else {
    // Jika tidak ada file, sembunyikan tombol X
    fileNameText.textContent = "Tidak ada file yang dipilih...";
    if (removeBtn) removeBtn.style.display = "none";
  }
  // Pastikan nilai hidden field remove_existing_image direset
  const removeExisting = document.getElementById("removeExistingImage");
  if (removeExisting) removeExisting.value = "0";
}
