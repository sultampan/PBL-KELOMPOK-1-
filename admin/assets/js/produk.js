// admin/assets/js/produk.js

// --- 1. FUNGSI UTAMA (Preview Gambar) ---
// Ini adalah fungsi yang dipanggil oleh atribut onchange="" di form.php
function previewImage(event) {
  const input = event.target;
  const img = document.getElementById("imgPreview");
  const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB (Harus sama dengan di PHP)

  const errorContainer = document.getElementById("fileError");
  if (errorContainer) {
    errorContainer.textContent = "";
    errorContainer.style.display = "none";
  }

  if (input.files && input.files[0]) {
    const file = input.files[0];

    // Cek Ukuran File (Client-side validation)
    if (file.size > MAX_FILE_SIZE) {
      if (errorContainer) {
        errorContainer.textContent =
          "Ukuran file melebihi batas 5MB. Silakan pilih file lain.";
        errorContainer.style.display = "block";
      }
      input.value = "";
      img.style.display = "none";
      return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      img.src = e.target.result;
      img.style.display = "block";
    };
    reader.readAsDataURL(input.files[0]);
  } else {
    img.src = "";
    img.style.display = "none";
  }
}

// --- 2. FUNGSI PEMBANTU (Alert Pop-up) ---
// Perlu dipanggil di AJAX success/error
function displayAlert(message, type) {
  // Gunakan container di atas form (misalnya body atau div utama)
  const container = document.querySelector(".card h2").parentNode;
  let alertHtml = `
        <div class="alert ${type}" style="
            background:${type === "success" ? "#e8f7e8" : "#fdd"}; 
            color:${type === "success" ? "#1a7d1a" : "#c00"}; 
            padding:10px; border-radius:5px; margin-bottom:15px; border: 1px solid ${
              type === "success" ? "#c9e8c9" : "#c00"
            };
        ">${message}</div>
    `;
  // Hapus alert lama sebelum menyisipkan yang baru (opsional)
  const oldAlert = container.querySelector(".alert");
  if (oldAlert) oldAlert.remove();

  container.insertAdjacentHTML("afterbegin", alertHtml);

  // Otomatis hilang setelah 4 detik
  const currentAlert = container.querySelector(".alert");
  if (currentAlert) {
    setTimeout(() => {
      currentAlert.style.transition = "opacity 0.5s ease-out";
      currentAlert.style.opacity = "0";
      setTimeout(() => currentAlert.remove(), 500);
    }, 4000);
  }
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
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Asumsi: Total record (untuk paginasi) dihitung di PHP dan disimpan di suatu tempat
            // Jika Anda tidak menyimpannya, kita harus hitung manual di PHP.
            // UNTUK KODE INI: Kita asumsikan ada elemen tersembunyi yang menyimpan total halaman.
            
            // CARA YANG LEBIH AMAN: Asumsikan PHP mengembalikan totalPages dalam array terpisah
            // Karena ini tidak bisa, kita akan mengandalkan PHP untuk mengirim HTML yang benar
            
            // Solusi Sederhana: Cek apakah tabel KOSONG setelah penghapusan
            // Jika halaman saat ini (> 1) dan tabel kosong, pindah ke halaman 1
            const currentPage = parseInt(currentParams.get('p')) || 1;
            const tableBody = tempDiv.querySelector('.table tbody');
            
            if (currentPage > 1 && tableBody && tableBody.children.length === 1 && tableBody.querySelector('td[colspan]')) {
                 // Kondisi: Kita berada di halaman > 1, dan tabel hanya berisi baris 'Belum ada produk'
                
                // 3. JIKA KOSONG, GANTI URL KE HALAMAN 1 (ATAU HALAMAN SEBELUMNYA)
                currentParams.set('p', (currentPage - 1)); // Mundur satu halaman
                
                // 4. Update URL di browser tanpa reload
                window.history.pushState(
                    null, 
                    '', 
                    window.location.pathname + '?' + currentParams.toString()
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
        if (e.target && e.target.id === 'productForm') {
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
                        displayAlert(data.message, "success");
                        loadProductList(); 

                        const isUpdate = formData.get("id_produk");

                        // PERBAIKAN KRITIS: Selalu muat ulang form card dari server untuk mereset state PHP
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
                        displayAlert(data.message, "error");
                    }
                })
                .catch((error) => {
                    console.error("AJAX Error:", error);
                    displayAlert(
                        "Terjadi kesalahan jaringan atau server. Cek Konsol (F12).",
                        "error"
                    );
                })
                .finally(() => {
                    const finalBtn = document.getElementById('submitBtn');
                    // Pastikan tombol masih ada sebelum diakses
                    if (finalBtn) { 
                        finalBtn.disabled = false;
                        // Tombol akan direset text-nya oleh loadEmptyForm (yang memuat HTML baru)
                    }
                });
        }
    });
    
    // Logika Pembersihan Alert Awal
    setupInitialAlertCleanup();
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
  displayAlert("Menghapus produk...", "warning");

  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        displayAlert(data.message, "success");
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

function setupInitialAlertCleanup() {
  // Target alert yang dimuat langsung dari PHP (bukan dari AJAX)
  const initialAlert = document.querySelector(".alert");

  if (initialAlert) {
    setTimeout(() => {
      initialAlert.style.transition = "opacity 0.5s ease-out";
      initialAlert.style.opacity = "0";
      setTimeout(() => initialAlert.remove(), 500);
    }, 4000);
  }
}

function loadEmptyForm(successMessage) {
  const formContainer = document.getElementById("form-content-wrapper"); // Ambil elemen card terluar
  if (!formContainer) {
        console.error("ID #form-content-wrapper tidak ditemukan!");
        return;
    }

  // Tambahkan pesan sukses ke URL agar bisa ditampilkan oleh form-load.php
  const url =
    "module/produk/form-load.php?success_msg=" +
    encodeURIComponent(successMessage);

  formContainer.innerHTML =
    '<div style="text-align:center; padding:20px;">Memuat form...</div>';

  fetch(url)
    .then((response) => response.text())
    .then((html) => {
      // Ganti seluruh isi card dengan HTML form baru (mode Tambah Baru)
      formContainer.innerHTML = html;

      const newNameInput = document.querySelector('#productForm input[name="nama"]');
            
            if (newNameInput) {
                // Gunakan setTimeout agar focus terjadi setelah browser selesai merender DOM
                setTimeout(() => {
                    newNameInput.focus();
                }, 50); // Jeda kecil (50ms) memastikan rendering selesai
            }
    })
    .catch((error) => {
      console.error("Error loading form:", error);
      formContainer.innerHTML =
        '<div style="text-align:center; color:red;">Gagal memuat form.</div>';
    });
}
