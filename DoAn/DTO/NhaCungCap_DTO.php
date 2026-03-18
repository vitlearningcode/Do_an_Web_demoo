<?php
readonly class NhaCungCap_DTO 
{
    public function __construct(
        public int $maNCC, 
        public string $tenNCC, 
        public ?string $sdt, 
        public ?string $email, 
        public float $chietKhauMacDinh
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maNCC: (int) $data['maNCC'], 
            tenNCC: (string) $data['tenNCC'], 
            sdt: $data['sdt'] ?? null, 
            email: $data['email'] ?? null, 
            chietKhauMacDinh: isset($data['chietKhauMacDinh']) ? (float) $data['chietKhauMacDinh'] : 0.0
        );
    }
}
?>