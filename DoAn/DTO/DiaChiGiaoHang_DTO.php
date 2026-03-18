<?php

readonly class DiaChiGiaoHan_DTO 
{
    public function __construct(
        public int $maDC, 
        public int $maND, 
        public string $diaChiChiTiet, 
        public int $laMacDinh
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maDC: (int) $data['maDC'], 
            maND: (int) $data['maND'], 
            diaChiChiTiet: (string) $data['diaChiChiTiet'], 
            laMacDinh: isset($data['laMacDinh']) ? (int) $data['laMacDinh'] : 0
        );
    }
}
?>