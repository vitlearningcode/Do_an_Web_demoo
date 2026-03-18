<?php
// DTO/PhieuNhapDTO.php

readonly class PhieuNhap_DTO 
{
    public function __construct(
        public string $maPN, 
        public int $tongLuongNhap, 
        public ?string $ngayLap, 
        public float $soTienDaThanhToan, 
        public float $tongTien, 
        public string $trangThai, 
        public int $maNCC
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maPN: (string) $data['maPN'], 
            tongLuongNhap: isset($data['tongLuongNhap']) ? (int) $data['tongLuongNhap'] : 0, 
            ngayLap: $data['ngayLap'] ?? null, 
            soTienDaThanhToan: isset($data['soTienDaThanhToan']) ? (float) $data['soTienDaThanhToan'] : 0.0, 
            tongTien: isset($data['tongTien']) ? (float) $data['tongTien'] : 0.0, 
            trangThai: $data['trangThai'] ?? 'Waiting', 
            maNCC: (int) $data['maNCC']
        );
    }
}
?>