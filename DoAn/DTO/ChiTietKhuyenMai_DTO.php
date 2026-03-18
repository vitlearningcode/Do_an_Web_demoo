<?php

readonly class ChiTietKhuyenMai_DTO 
{
    public function __construct(
        public string $maKM, 
        public string $maSach, 
        public int $phanTramGiam, 
        public int $soLuongKhuyenMai
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maKM: (string) $data['maKM'], 
            maSach: (string) $data['maSach'], 
            phanTramGiam: (int) $data['phanTramGiam'], 
            soLuongKhuyenMai: isset($data['soLuongKhuyenMai']) ? (int) $data['soLuongKhuyenMai'] : 0
        );
    }
}
?>