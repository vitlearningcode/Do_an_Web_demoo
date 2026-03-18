<?php

readonly class TaiKhoan_DTO 
{
    public function __construct(
        public string $tenDN, 
        public string $matKhau, 
        public int $maND, 
        public int $maVT, 
        public string $trangThai
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            tenDN: (string) $data['tenDN'], 
            matKhau: (string) $data['matKhau'], 
            maND: (int) $data['maND'], 
            maVT: (int) $data['maVT'], 
            trangThai: $data['trangThai'] ?? 'on'
        );
    }
}
?>