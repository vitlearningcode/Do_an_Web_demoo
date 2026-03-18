<?php

readonly class TheLoai_DTO 
{
    public function __construct(
        public int $maTL, 
        public string $tenTL
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maTL: (int) $data['maTL'], 
            tenTL: (string) $data['tenTL']
        );
    }
}
?>