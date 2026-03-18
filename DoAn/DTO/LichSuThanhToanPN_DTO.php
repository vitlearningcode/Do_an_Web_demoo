<?php

readonly class LichSuThanhToanPN_DTO 
{
    public function __construct(
        public int $maLSTT, 
        public string $maPN, 
        public ?string $ngayThanhToan, 
        public float $soTienTra, 
        public ?string $hinhThucTra, 
        public ?string $ghiChu
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maLSTT: (int) $data['maLSTT'], 
            maPN: (string) $data['maPN'], 
            ngayThanhToan: $data['ngayThanhToan'] ?? null, 
            soTienTra: (float) $data['soTienTra'], 
            hinhThucTra: $data['hinhThucTra'] ?? null, 
            ghiChu: $data['ghiChu'] ?? null
        );
    }
}
?>