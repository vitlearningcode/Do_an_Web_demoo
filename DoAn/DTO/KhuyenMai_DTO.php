
<?php
readonly class KhuyenMai_DTO 
{
    public function __construct(
        public string $maKM, 
        public string $tenKM, 
        public string $ngayBatDau, 
        public string $ngayKetThuc
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maKM: (string) $data['maKM'], 
            tenKM: (string) $data['tenKM'], 
            ngayBatDau: (string) $data['ngayBatDau'], 
            ngayKetThuc: (string) $data['ngayKetThuc']
        );
    }
}
?>