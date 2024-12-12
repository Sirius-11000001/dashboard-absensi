// toggle password
function togglePassword() {
  var passwordField = document.getElementById("password");
  var checkbox = document.getElementById("show-password");

  if (checkbox.checked) {
      passwordField.type = "text";
  } else {

      passwordField.type = "password";
  }
}
// fungsi scan barcode


// fungsi untuk mengubah sidebar menjadi navbar pada  layar kecil
const sidebar = document.querySelector(".sidebar");
const sidebarToggler = document.querySelector(".sidebar-toggler");
const menuToggler = document.querySelector(".menu-toggler");

let collapsedSidebarHeight = "56px";
let fullSidebarHeight = "calc(100vh - 32px)";
sidebarToggler.addEventListener("click", () => {
  sidebar.classList.toggle("collapsed");
});
const toggleMenu = (isMenuActive) => {
  sidebar.style.height = isMenuActive ? `${sidebar.scrollHeight}px` : collapsedSidebarHeight;
  menuToggler.querySelector("span").innerText = isMenuActive ? "close" : "menu";
}
menuToggler.addEventListener("click", () => {
  toggleMenu(sidebar.classList.toggle("menu-active"));
});
window.addEventListener("resize", () => {
  if (window.innerWidth >= 1024) {
    sidebar.style.height = fullSidebarHeight;
  } else {
    sidebar.classList.remove("collapsed");
    sidebar.style.height = "auto";
    toggleMenu(sidebar.classList.contains("menu-active"));
  }
});



// datetime header
  function updateTime() {

    const now = new Date();
    
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = now.getFullYear();
    
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    const formattedTime = `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
    
    document.getElementById('current-time').textContent = formattedTime;
  }

  setInterval(updateTime, 1000);
  
  updateTime();