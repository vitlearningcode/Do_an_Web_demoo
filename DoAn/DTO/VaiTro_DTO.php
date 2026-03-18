<?php
readonly class VaiTro_DTO 
{
    public function __construct(
        public int $maVT, 
        public string $tenVT
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maVT: (int) $data['maVT'], 
            tenVT: (string) $data['tenVT']
        );
    }
}
?>