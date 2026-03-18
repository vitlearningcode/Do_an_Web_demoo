<?php

readonly class DonHang_DTO 
{
    public function __construct(
        public string $maDH, 
        public int $maND, 
        public int $maDC, 
        public int $maPT,   
        public ?string $ngayDat, 
        public float $tongTien, 
        public string $trangThai
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maDH: (string) $data['maDH'], 
            maND: (int) $data['maND'], 
            maDC: (int) $data['maDC'], 
            maPT: (int) $data['maPT'], 
            ngayDat: $data['ngayDat'] ?? null, 
            tongTien: (float) $data['tongTien'], 
            trangThai: $data['trangThai'] ?? 'ChoDuyet'
        );
    }
}
?>