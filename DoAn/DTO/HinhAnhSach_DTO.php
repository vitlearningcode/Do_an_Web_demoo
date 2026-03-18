<?php

readonly class HinhAnhSach_DTO 
{
    public function __construct(
        public int $maHA, 
        public string $maSach, 
        public string $urlAnh
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maHA: (int) $data['maHA'], 
            maSach: (string) $data['maSach'], 
            urlAnh: (string) $data['urlAnh']
        );
    }
}
?>