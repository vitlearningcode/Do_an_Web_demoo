<?php
/**
 * layChiTietSach.php
 * AJAX endpoint — trả về JSON thông tin chi tiết một cuốn sách.
 *
 * Tham số GET:
 *   maSach (string) — mã định danh sách cần truy vấn
 *
 * Phản hồi JSON:
 *   maSach, tenSach, giaBan, giaSau, phanTramGiam, namSX,
 *   moTa, soTrang, nhaXuatBan, hinhThucBia, kichThuoc,
 *   hinhAnh, tacGia, theLoai, diemTB, soReview, tongBan
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../../../KetNoi/config/db.php';

// ── Kiểm tra tham số đầu vào ──────────────────────────────────────────
$maSach = trim($_GET['maSach'] ?? '');
if ($maSach === '') {
    http_response_code(400);
    echo json_encode(['loi' => 'Thiếu mã sách'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── Query đầy đủ (kể cả các cột mở rộng của bảng Sach) ───────────────
try {
    $stmt = $pdo->prepare("
        SELECT
            s.maSach,
            s.tenSach,
            s.giaBan,
            s.namSX,
            s.moTa,
            s.soTrang,
            s.nhaXuatBan,
            s.hinhThucBia,
            s.kichThuoc,

            -- Ảnh bìa đầu tiên
            (SELECT urlAnh FROM HinhAnhSach
             WHERE maSach = s.maSach LIMIT 1)                                  AS hinhAnh,

            -- Tác giả (ghép nhiều tác giả bằng dấu phẩy)
            (SELECT GROUP_CONCAT(tg.tenTG ORDER BY tg.tenTG SEPARATOR ', ')
             FROM Sach_TacGia stg
             JOIN TacGia tg ON tg.maTG = stg.maTG
             WHERE stg.maSach = s.maSach)                                      AS tacGia,

            -- Thể loại đầu tiên
            (SELECT tl.tenTL
             FROM Sach_TheLoai stl
             JOIN TheLoai tl ON tl.maTL = stl.maTL
             WHERE stl.maSach = s.maSach LIMIT 1)                              AS theLoai,

            -- Điểm đánh giá trung bình
            (SELECT ROUND(AVG(diemDG), 1)
             FROM DanhGiaSach WHERE maSach = s.maSach)                         AS diemTB,

            -- Số lượt đánh giá
            (SELECT COUNT(*)
             FROM DanhGiaSach WHERE maSach = s.maSach)                         AS soReview,

            -- Tổng đã bán (tất cả thời gian, đơn Hoàn Thành)
            IFNULL((
                SELECT SUM(ct.soLuong)
                FROM ChiTietDH ct
                JOIN DonHang dh ON dh.maDH = ct.maDH
                WHERE ct.maSach = s.maSach AND dh.trangThai = 'HoanThanh'
            ), 0)                                                               AS tongBan,

            -- Phần trăm giảm Flash Sale đang áp dụng
            (SELECT ckm.phanTramGiam
             FROM ChiTietKhuyenMai ckm
             JOIN KhuyenMai km ON km.maKM = ckm.maKM
             WHERE ckm.maSach = s.maSach
               AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
             LIMIT 1)                                                           AS phanTramGiam

        FROM Sach s
        WHERE s.maSach = ?
    ");
    $stmt->execute([$maSach]);
    $sach = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    /*
     * Fallback: nếu bảng Sach chưa có các cột mở rộng (moTa, soTrang,
     * nhaXuatBan, hinhThucBia, kichThuoc), thử lại với query tối giản.
     * Các cột thiếu sẽ trả về NULL và JS hiển thị "Đang cập nhật".
     */
    try {
        $stmt = $pdo->prepare("
            SELECT
                s.maSach, s.tenSach, s.giaBan, s.namSX,
                NULL AS moTa,       NULL AS soTrang,
                NULL AS nhaXuatBan, NULL AS hinhThucBia, NULL AS kichThuoc,

                (SELECT urlAnh FROM HinhAnhSach
                 WHERE maSach = s.maSach LIMIT 1)                              AS hinhAnh,

                (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ')
                 FROM Sach_TacGia stg JOIN TacGia tg ON tg.maTG = stg.maTG
                 WHERE stg.maSach = s.maSach)                                  AS tacGia,

                (SELECT tl.tenTL FROM Sach_TheLoai stl
                 JOIN TheLoai tl ON tl.maTL = stl.maTL
                 WHERE stl.maSach = s.maSach LIMIT 1)                          AS theLoai,

                (SELECT ROUND(AVG(diemDG), 1)
                 FROM DanhGiaSach WHERE maSach = s.maSach)                     AS diemTB,

                (SELECT COUNT(*)
                 FROM DanhGiaSach WHERE maSach = s.maSach)                     AS soReview,

                0                                                              AS tongBan,

                (SELECT ckm.phanTramGiam
                 FROM ChiTietKhuyenMai ckm JOIN KhuyenMai km ON km.maKM = ckm.maKM
                 WHERE ckm.maSach = s.maSach
                   AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
                 LIMIT 1)                                                      AS phanTramGiam

            FROM Sach s WHERE s.maSach = ?
        ");
        $stmt->execute([$maSach]);
        $sach = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e2) {
        http_response_code(500);
        echo json_encode(['loi' => 'Lỗi hệ thống. Vui lòng thử lại sau.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!$sach) {
    http_response_code(404);
    echo json_encode(['loi' => 'Không tìm thấy sách với mã: ' . $maSach], JSON_UNESCAPED_UNICODE);
    exit;
}

// Tính giaSau dựa trên phanTramGiam (Flash Sale đang chạy)
if (!empty($sach['phanTramGiam'])) {
    $sach['giaSau'] = (int) round($sach['giaBan'] * (1 - $sach['phanTramGiam'] / 100));
} else {
    $sach['giaSau'] = null;
}

echo json_encode($sach, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
