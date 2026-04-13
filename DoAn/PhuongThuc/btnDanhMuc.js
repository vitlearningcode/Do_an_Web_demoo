// ---------------------------------------------------------
  // 1. LOGIC CHUYỂN ĐỔI GIAO DIỆN (Menu Trượt Bay)
  // ---------------------------------------------------------
  const nutDanhMuc = document.getElementById("nut-danh-muc-bay");
  const menuBay = document.getElementById("menu-bay-the-loai");

  if (nutDanhMuc && menuBay) {
    nutDanhMuc.addEventListener("click", (suKien) => {
      suKien.preventDefault();
      suKien.stopPropagation(); 
      
      // Bật/tắt class 'active'
      menuBay.classList.toggle("active");
      nutDanhMuc.classList.toggle("active");
    });

    // Tự động đóng menu nếu click ra ngoài vùng menu
    document.addEventListener("click", (suKien) => {
      if (menuBay.classList.contains("active") && !menuBay.contains(suKien.target)) {
        menuBay.classList.remove("active");
        nutDanhMuc.classList.remove("active");
      }
    });
    
    // Ngăn menu bị đóng khi click vào bên trong nó
    menuBay.addEventListener("click", (suKien) => {
       suKien.stopPropagation();
    });
  }