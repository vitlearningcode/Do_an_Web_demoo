<?php
/**
 * ============================================================
 * LUỒNG: LẤY GIỎ HÀNG + BẢN ĐỒ GIÁ + TỒN KHO TỪ DB
 *
 * GỌI BỞI: index.php, layChiTietSach.php, trangTheLoai.php
 *   chỉ khi $isLoggedIn = true
 *   require_once '...PhuongThuc/layGioHangCoGia.php';
 *
 * YÊU CẦU: $pdo đã khởi tạo, session_start() đã gọi.
 *
 * OUTPUT (3 biến PHP sẵn sàng dùng ngay sau require):
 *
 *   $cartServerDataArr  — Mảng PHP các sách trong giỏ (kèm giá từ DB)
 *   $cartServerDataJson — JSON của $cartServerDataArr (inject vào <head>)
 *   $giaSachMapJson     — JSON {maSach: giaChinh} của TOÀN BỘ sách (inject vào <head>)
 *   $tonKhoMapJson      — JSON {maSach: soLuongTon} của TOÀN BỘ sách (inject vào <head>)
 *
 * VÌ SAO CẦN 2 PHẦN?
 *
 *   PHẦN 1 ($cartServerDataArr):
 *     Cart drawer cần biết NGAY chi tiết sách trong giỏ (tên, ảnh, giá hiện tại)
 *     để render danh sách khi mở trang (không đợi user mở drawer)
 *
 *   PHẦN 2 ($giaSachMapJson + $tonKhoMapJson):
 *     btnThemGioHang.js cần BIẾT GIÁ và TỒN KHO của BẤT KỲ sách nào
 *     khi user nhấn nút thêm — không thể nhúng giá vào data-price (dễ bị F12 sửa)
 *     => Giải pháp: PHP inject toàn bộ map vào <script>, JS dùng lookup
 *
 * BẢO MẬT:
 *   GIÁ luôn đến từ DB (COALESCE(giaSau_flashSale, giaBan_goc))
 *   Số lượng giỏ hàng bị cập nhật xuống nếu TonKho < soLuong cart
 * ============================================================
 */

// =============================================================
// PHẦN 1: XÂY DỰNG $cartServerDataArr — GIỏ HÀNG VỚI GIÁ TỪ DB
// Mục đích: cart.js đọc biến này ngay khi trang load
// (thay vì đọc localStorage — giá trong localStorage có thể bị chỉnh sửa)
// =============================================================
$cartServerDataArr = [];

// Chỉ xử lý khi user đã đăng nhập và có giỏ hàng trong session
// $_SESSION['cart'] được set bởi: xuly_dangnhap.php (khi login) + luuGioHang.php (mọi thao tác)
if (!empty($_SESSION['cart'])) {
    $maSachCartList = []; // Danh sách maSach — dùng cho IN (?)
    $soLuongCartMap = []; // maSach => soLuong — từ session (KHÔNG tin giả từ session)

    // Trích xuất maSach và soLuong từ session
    // max(1, ...) đảm bảo soLuong luôn íd tửa là 1
    foreach ($_SESSION['cart'] as $item) {
        $ms = trim($item['maSach'] ?? '');
        $sl = max(1, (int)($item['soLuong'] ?? 1));
        if ($ms !== '') {
            $maSachCartList[] = $ms;
            $soLuongCartMap[$ms] = $sl;
        }
    }

    if (!empty($maSachCartList)) {
        // -----------------------------------------------------------
        // QUỜY `N? 1: LẤY THÔNG TIN SÁCH TRONG GIỏ HÀNG TỪ DB
        // JOIN: Sach + HinhAnhSach (1 ảnh nhỏ nhất) + TacGia (ghep chuỗi)
        // Subquery giaSau: áp dụng flash sale nếu còn hiệu lực
        //   NOW() BETWEEN ngayBatDau AND ngayKetThuc (timezone +07:00 từ db.php)
        //   ORDER BY phanTramGiam DESC: lấy khuyến mãi giảm nhiều nhất
        //   LIMIT 1: chỉ áp 1 khuyến mãi tại một thời điểm
        // IN ($inPh): placeholder dạng "?,?,?" — PDO quản lý an toàn
        // -----------------------------------------------------------
        $inPh = implode(',', array_fill(0, count($maSachCartList), '?'));
        $stmtCart = $pdo->prepare("
            SELECT
                s.maSach,
                s.tenSach,
                s.giaBan,
                s.soLuongTon,
                COALESCE(ha.urlAnh, '') AS hinhAnh,
                COALESCE(
                    GROUP_CONCAT(DISTINCT tg.tenTG ORDER BY tg.maTG SEPARATOR ', '),
                    'Đang cập nhật'
                ) AS tacGia,
                (
                    -- Flash sale: giảm % nếu còn trong thời gian hiệu lực
                    SELECT ROUND(s.giaBan * (1 - ckm.phanTramGiam / 100))
                    FROM ChiTietKhuyenMai ckm
                    JOIN KhuyenMai km ON km.maKM = ckm.maKM
                    WHERE ckm.maSach = s.maSach
                      AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
                    ORDER BY ckm.phanTramGiam DESC
                    LIMIT 1
                ) AS giaSau
            FROM Sach s
            LEFT JOIN (
                SELECT maSach, MIN(urlAnh) AS urlAnh -- Chỉ lấy 1 ảnh (ảnh nhỏ nhất theo alphabet)
                FROM HinhAnhSach GROUP BY maSach
            ) ha ON ha.maSach = s.maSach
            LEFT JOIN Sach_TacGia stg ON stg.maSach = s.maSach
            LEFT JOIN TacGia tg ON tg.maTG = stg.maTG
            WHERE s.maSach IN ($inPh)
            GROUP BY s.maSach, s.tenSach, s.giaBan, s.soLuongTon, ha.urlAnh
        ");
        $stmtCart->execute($maSachCartList); // Truyền mảng maSach làm tham số

        // Build map: maSach => dong_DB (để lookup nhanh bước tiếp theo)
        $sachCartInfo = [];
        foreach ($stmtCart->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $sachCartInfo[$row['maSach']] = $row;
        }

        foreach ($maSachCartList as $ms) {
            // Bỏ qua sách bị xóa trong DB (khống có trong kết quả query)
            if (!isset($sachCartInfo[$ms])) continue;
            $info = $sachCartInfo[$ms];

            // -----------------------------------------------------------
            // TÍNH GIÁ THỰC Tế: ƯU TIÊN flash sale nếu còn hiệu lực
            // BẢO MẬT: GIÁ LẤY TỪ DB, không từ $_SESSION['cart'][i]['giaBan']
            // -----------------------------------------------------------
            $giaChinh = ($info['giaSau'] !== null)
                ? (float)$info['giaSau']  // Giá sau flash sale
                : (float)$info['giaBan']; // Giá gốc nếu không có flash sale

            // -----------------------------------------------------------
            // ĐẨU TẨY SỐ LƯỢNG: không cho vượt tồn kho
            // Ví dụ: user có 5 cuốn trong giỏ, kho chỉ còn 3
            //   $slHopLe = min(5, max(0, 3)) = 3
            // max(0, tonKho): tronộn g trường hợp tonKho âm (lỗi dữ liệu)
            // -----------------------------------------------------------
            $tonKho  = (int)$info['soLuongTon'];
            $slHopLe = min($soLuongCartMap[$ms], max(0, $tonKho));

            // Xây dựng mặt hàng đầy đủ — sẽ được JSON encode và inject vào <head>
            $cartServerDataArr[] = [
                'maSach'     => $ms,
                'tenSach'    => $info['tenSach'],
                'giaBan'     => $giaChinh,    // ← giá thật từ DB (có áp dụng flash sale)
                'hinhAnh'    => $info['hinhAnh'],
                'tacGia'     => $info['tacGia'],
                'soLuong'    => $slHopLe,      // ← đã điều chỉnh xuống nếu vượt kho
                'soLuongTon' => $tonKho,       // ← cart.js dùng để khoa nút +
            ];
        }
    }
}

// =============================================================
// PHẦN 2: XÂY DỰNG $giaSachMapArr + $tonKhoMapArr
// TOÀN BỘ sách đang kinh doanh (không chỉ trong giỏ)
// Mục đích: JS dùng để lookup giá/tồn kho khi user nhấn "Thêm giỏ hàng"
// =============================================================

// Query nhẹ: chỉ SELECT maSach + soLuongTon + giaChinh (không JOIN nhập/tác giả)
// WHERE trangThai = 'DangKD': bỏ qua sách ngừng kinh doanh / hết hạn
// COALESCE: nếu có flash sale dùng giá flashSale; không thì dùng giá gốc
$giaSachMapArr = [];
$tonKhoMapArr  = [];
try {
    $stmtMap = $pdo->query("
        SELECT
            s.maSach,
            s.soLuongTon,
            COALESCE(
                (
                    -- Tương tự subquery trong phần 1, nhưng chỉ lấy giá — không cần tên/ảnh
                    SELECT ROUND(s.giaBan * (1 - ckm.phanTramGiam / 100))
                    FROM ChiTietKhuyenMai ckm
                    JOIN KhuyenMai km ON km.maKM = ckm.maKM
                    WHERE ckm.maSach = s.maSach
                      AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
                    ORDER BY ckm.phanTramGiam DESC
                    LIMIT 1
                ),
                s.giaBan -- Fallback: giá gốc nếu không có flash sale
            ) AS giaChinh
        FROM Sach s
        WHERE s.trangThai = 'DangKD' -- Chỉ lấy sách đang kinh doanh
    ");

    // Build 2 map riêng biệt từ cùng 1 result set
    // {maSach: giá} + {maSach: tồnKho}
    foreach ($stmtMap->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $giaSachMapArr[$row['maSach']] = (float)$row['giaChinh'];   // window.__giaSach
        $tonKhoMapArr[$row['maSach']]  = (int)$row['soLuongTon'];   // window.__tonKhoMap
    }
} catch (PDOException $e) {
    // Fallback an toàn: mảng rỗng — JS sẽ dùng giá 0 và không giới hạn tồn kho
    $giaSachMapArr = [];
    $tonKhoMapArr  = [];
}

// =============================================================
// PHẦN 3: JSON ENCODE — sẵn sàng để PHP inject vào <head>
//
// Những biến này được dùng ngay sau require trong index.php:
//   <script>
//     var cartServerData = [giá trị $cartServerDataJson]; // cart.js đọc trong init()
//     var __giaSach      = [giá trị $giaSachMapJson];     // btnThemGioHang.js đọc
//     var __tonKhoMap    = [giá trị $tonKhoMapJson];      // cart.js + btnThemGioHang.js đọc
//   </script>
//
// JSON_UNESCAPED_UNICODE: tiếng Việt hiển thị đúng, không bị encode thành \uXXXX
// =============================================================
$giaSachMapJson     = json_encode($giaSachMapArr, JSON_UNESCAPED_UNICODE);
$tonKhoMapJson      = json_encode($tonKhoMapArr,  JSON_UNESCAPED_UNICODE);
$cartServerDataJson = json_encode($cartServerDataArr, JSON_UNESCAPED_UNICODE);
