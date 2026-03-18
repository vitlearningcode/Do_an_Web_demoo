<?php

readonly class CongNo_DTO 
{
    public function __construct(
        public int $maCN, 
        public int $maNCC, 
        public float $tongNo, 
        public ?string $capNhatCuoi
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maCN: (int) $data['maCN'], 
            maNCC: (int) $data['maNCC'], 
            tongNo: (float) $data['tongNo'], 
            capNhatCuoi: $data['capNhatCuoi'] ?? null
        );
    }
}
?>