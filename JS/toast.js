function showToast(msg = "เพิ่มเมนูแล้ว") {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = msg;
  toast.style.opacity = 1;
  setTimeout(() => {
    toast.style.opacity = 0;
  }, 2000);
}

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.add-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      showToast();
    });
  });
});