<?php

readonly class Sach_TacGia_DTO 
{
    public function __construct(
        public string $maSach, 
        public int $maTG
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maSach: (string) $data['maSach'], 
            maTG: (int) $data['maTG']
        );
    }
}
?>