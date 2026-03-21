<?php
require_once '../DAO/Sach_DAO.php';

class Sach_BUS 
{
    private Sach_DAO $sach_DAO;

    public function __construct() 
    {
        $this->sach_DAO = new Sach_DAO();
    }

    public function getDanhSachSach($mangTheLoai, $khoangGia, $sort) 
{
    return $this->sach_DAO->getSachTheoBoLoc($mangTheLoai, $khoangGia, $sort);
}
}
?>