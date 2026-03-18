<?php

readonly class TacGia_DTO 
{
    public function __construct(
        public int $maTG, 
        public string $tenTG, 
        public ?string $tieuSu
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maTG: (int) $data['maTG'], 
            tenTG: (string) $data['tenTG'], 
            tieuSu: $data['tieuSu'] ?? null
        );
    }
}
?>