<?php

readonly class ChiTietPN_DTO 
{
    public function __construct(
        public string $maPN, 
        public string $maSach, 
        public int $soLuongNhap, 
        public float $giaNhap, 
        public float $chietKhau, 
        public float $thanhTien
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maPN: (string) $data['maPN'], 
            maSach: (string) $data['maSach'], 
            soLuongNhap: (int) $data['soLuongNhap'], 
            giaNhap: (float) $data['giaNhap'], 
            chietKhau: isset($data['chietKhau']) ? (float) $data['chietKhau'] : 0.0, 
            thanhTien: (float) $data['thanhTien']
        );
    }
}
?>