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

function loadMemberList() {
  const listContainer = document.getElementById("member-list-container");
  if (!listContainer) return;
  const currentParams = new URLSearchParams(window.location.search);
  const url = "module/member/table-load.php" + window.location.search;

  listContainer.innerHTML = '<div style="text-align:center; padding:20px;">Memuat data...</div>';

  fetch(url).then((response) => response.text()).then((html) => {
      const tempDiv = document.createElement("div"); tempDiv.innerHTML = html;
      const currentPage = parseInt(currentParams.get("p")) || 1;
      const tableBody = tempDiv.querySelector(".table tbody");
      if (currentPage > 1 && tableBody && tableBody.children.length === 1 && tableBody.querySelector("td[colspan]")) {
        currentParams.set("p", currentPage - 1); 
        window.history.pushState(null, "", window.location.pathname + "?" + currentParams.toString());
        loadMemberList();
        return;
      }
      listContainer.innerHTML = html;
    }).catch((error) => {
      console.error("Error loading table:", error);
      listContainer.innerHTML = '<div style="text-align:center; color:red;">Gagal memuat tabel.</div>';
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

document.addEventListener("DOMContentLoaded", function () {
  document.addEventListener("submit", function (e) {
    if (e.target && e.target.id === "memberForm") {
      e.preventDefault(); 
      const form = e.target;
      const formData = new FormData(form);
      const url = "module/member/save.php"; 
      const submitBtn = document.getElementById("submitBtn");
      submitBtn.disabled = true; submitBtn.textContent = "Memproses...";

      fetch(url, { method: "POST", body: formData })
        .then((response) => response.json()).then((data) => {
          if (data.status === "success") {
            loadMemberList();
            const isUpdate = formData.get("id_member"); 
            loadEmptyMemberForm(data.message);
            if (isUpdate) window.history.pushState({}, document.title, window.location.pathname + "?page=member");
          } else {
            displayAlert(data.message, "error");
            const input = document.getElementById('inputGambar');
            const img = document.getElementById('imgPreview');
            if (input) input.value = ''; if (img) img.style.display = 'none'; 
            updateMemberFileName(input);
          }
        }).catch((error) => { console.error("AJAX Error:", error); displayAlert("Terjadi kesalahan jaringan/server.", "error"); })
        .finally(() => { const finalBtn = document.getElementById("submitBtn"); if (finalBtn) finalBtn.disabled = false; });
    }
  });
});