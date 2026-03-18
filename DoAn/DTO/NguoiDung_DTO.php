<?php

readonly class NguoiDung_DTO 
{
    public function __construct(
        public int $maND, 
        public string $tenND, 
        public ?int $sdt, 
        public ?string $email, 
        public ?string $ngayTao
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maND: (int) $data['maND'], 
            tenND: (string) $data['tenND'], 
            sdt: isset($data['sdt']) ? (int) $data['sdt'] : null, 
            email: $data['email'] ?? null, 
            ngayTao: $data['ngayTao'] ?? null
        );
    }
}
?>