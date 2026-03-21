<?php
require_once '../DAO/TheLoai_DAO.php';

class TheLoai_BUS 
{
    private TheLoai_DAO $theLoai_DAO;

    public function __construct() 
    {
        $this->theLoai_DAO = new TheLoai_DAO();
    }

    public function getDanhSachTheLoai(): array 
    {
        return $this->theLoai_DAO->getAll();
    }
}
?>