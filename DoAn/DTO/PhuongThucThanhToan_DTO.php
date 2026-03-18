<?php
readonly class PhuongThucThanhToan_DTO 
{
    public function __construct(
        public int $maPT, 
        public string $tenPT
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maPT: (int) $data['maPT'], 
            tenPT: (string) $data['tenPT']
        );
    }
}
?>