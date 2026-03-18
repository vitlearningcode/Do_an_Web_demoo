<?php

readonly class Sach_TheLoai_DTO 
{
    public function __construct(
        public string $maSach, 
        public int $maTL
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maSach: (string) $data['maSach'], 
            maTL: (int) $data['maTL']
        );
    }
}
?>