<?php

readonly class Sach_DTO 
{
    public function __construct(
        public string $maSach, 
        public string $tenSach, 
        public int $maNXB, 
        public ?int $namSX,
        public string $loaiBia, 
        public float $giaBan, 
        public int $soLuongTon,
        public ?string $moTa, 
        public string $trangThai
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maSach: (string) $data['maSach'], 
            tenSach: (string) $data['tenSach'], 
            maNXB: (int) $data['maNXB'],
            namSX: isset($data['namSX']) ? (int) $data['namSX'] : null, 
            loaiBia: $data['loaiBia'] ?? 'Bìa Mềm',
            giaBan: (float) $data['giaBan'], 
            soLuongTon: isset($data['soLuongTon']) ? (int) $data['soLuongTon'] : 0,
            moTa: $data['moTa'] ?? null, 
            trangThai: $data['trangThai'] ?? 'DangKD'
        );
    }
}
?>