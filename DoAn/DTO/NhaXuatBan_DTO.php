<?php

readonly class NhaXuatBan_DTO 
{
    public function __construct(
        public int $maNXB, 
        public string $tenNXB, 
        public ?string $diaChi
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maNXB: (int) $data['maNXB'], 
            tenNXB: (string) $data['tenNXB'], 
            diaChi: $data['diaChi'] ?? null
        );
    }
}
?>