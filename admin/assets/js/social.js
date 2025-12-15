// admin/assets/js/social.js

// === FUNGSI ALERT/TOAST (Copy dari member.js) ===
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
    
    // Animasi masuk
    setTimeout(() => toast.classList.add('show'), 10); // Asumsi ada CSS .toast.show { transform: ... }

    setTimeout(() => {
        toast.style.opacity = "0"; // Animasi keluar manual
        setTimeout(() => {
            toast.remove();
            if (toastContainer.children.length === 0) toastContainer.remove();
        }, 300);
    }, 4000);
}

// === EVENT LISTENER UTAMA ===
document.addEventListener("DOMContentLoaded", function () {
    
    document.addEventListener("submit", function (e) {
        // Cek ID Form
        if (e.target && e.target.id === "socialForm") {
            e.preventDefault(); 
            
            const form = e.target;
            const formData = new FormData(form);
            const url = "module/social/save.php"; 
            
            const submitBtn = document.getElementById("submitBtn");
            const originalText = submitBtn.innerHTML; // Simpan teks asli (ikon + tulisan)
            
            // State Loading
            submitBtn.disabled = true; 
            submitBtn.textContent = "Menyimpan...";
      
            fetch(url, { method: "POST", body: formData })
              .then((response) => response.json())
              .then((data) => {
                if (data.status === "success") {
                    displayAlert(data.message, "success");
                    // Tidak perlu reload atau reset form karena ini settings page
                } else {
                    displayAlert(data.message, "error");
                }
              })
              .catch((error) => { 
                  console.error("AJAX Error:", error); 
                  displayAlert("Terjadi kesalahan jaringan/server.", "error"); 
              })
              .finally(() => { 
                  // Kembalikan Tombol
                  if (submitBtn) {
                      submitBtn.disabled = false;
                      submitBtn.innerHTML = originalText;
                  }
              });
        }
    });

});