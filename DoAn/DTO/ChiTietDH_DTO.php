<?php

readonly class ChiTietDH_DTO 
{
    public function __construct(
        public string $maDH, 
        public string $maSach, 
        public int $soLuong, 
        public float $giaBan, 
        public ?string $maKM, 
        public float $thanhTien
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maDH: (string) $data['maDH'], 
            maSach: (string) $data['maSach'], 
            soLuong: (int) $data['soLuong'], 
            giaBan: (float) $data['giaBan'], 
            maKM: $data['maKM'] ?? null, 
            thanhTien: (float) $data['thanhTien']
        );
    }
}
?>