<?php

readonly class DanhGiaSach_DTO 
{
    public function __construct(
        public int $maDG, 
        public string $maSach, 
        public int $maND, 
        public ?int $diemDG, 
        public ?string $nhanXet, 
        public ?string $ngayDG
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maDG: (int) $data['maDG'], 
            maSach: (string) $data['maSach'], 
            maND: (int) $data['maND'], 
            diemDG: isset($data['diemDG']) ? (int) $data['diemDG'] : null, 
            nhanXet: $data['nhanXet'] ?? null, 
            ngayDG: $data['ngayDG'] ?? null
        );
    }
}
?>