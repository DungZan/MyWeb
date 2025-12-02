-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 01, 2025 lúc 04:02 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `salesmanagement_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ctdd`
--

CREATE TABLE `ctdd` (
  `MaDon` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `GiaBan` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ctdd`
--

INSERT INTO `ctdd` (`MaDon`, `MaSP`, `SoLuong`, `GiaBan`) VALUES
(1, 1, 2, 350000.00),
(1, 3, 1, 2500000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ctpnk`
--

CREATE TABLE `ctpnk` (
  `MaPNK` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `GiaNhap` decimal(12,2) DEFAULT NULL,
  `MaVach` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ctpnk`
--

INSERT INTO `ctpnk` (`MaPNK`, `MaSP`, `SoLuong`, `GiaNhap`, `MaVach`) VALUES
(1, 1, 50, 250000.00, 'BATCH01-AO'),
(1, 2, 30, 400000.00, 'BATCH01-QUAN');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ctsanpham`
--

CREATE TABLE `ctsanpham` (
  `STT` int(11) NOT NULL,
  `MaSP` int(11) DEFAULT NULL,
  `MaVach` varchar(20) DEFAULT NULL,
  `TrangThaiBan` char(1) DEFAULT NULL,
  `GhiChu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ctsanpham`
--

INSERT INTO `ctsanpham` (`STT`, `MaSP`, `MaVach`, `TrangThaiBan`, `GhiChu`) VALUES
(1, 1, 'NK-TSHIRT-001', 'A', 'Hàng mới về'),
(2, 1, 'NK-TSHIRT-002', 'A', 'Hàng mới về'),
(3, 2, 'UQ-JEAN-001', 'A', 'Hàng trưng bày');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dgia`
--

CREATE TABLE `dgia` (
  `STT` int(11) NOT NULL,
  `MaSP` int(11) DEFAULT NULL,
  `MaKH` int(11) DEFAULT NULL,
  `DiemSao` int(11) DEFAULT NULL,
  `NhanXet` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `dgia`
--

INSERT INTO `dgia` (`STT`, `MaSP`, `MaKH`, `DiemSao`, `NhanXet`) VALUES
(1, 1, 3, 5, 'Áo mặc rất mát, vải đẹp, giao hàng nhanh!');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dondat`
--

CREATE TABLE `dondat` (
  `STT` int(11) NOT NULL,
  `NgayDat` datetime DEFAULT current_timestamp(),
  `TrangThai` varchar(20) DEFAULT NULL,
  `MaKH` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `dondat`
--

INSERT INTO `dondat` (`STT`, `NgayDat`, `TrangThai`, `MaKH`) VALUES
(1, '2025-12-01 21:44:51', 'MoiTao', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaisp`
--

CREATE TABLE `loaisp` (
  `STT` int(11) NOT NULL,
  `Ten` varchar(100) DEFAULT NULL,
  `MoTa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `loaisp`
--

INSERT INTO `loaisp` (`STT`, `Ten`, `MoTa`) VALUES
(1, 'Áo Thun', 'Các loại áo thun nam nữ'),
(2, 'Quần Jean', 'Quần bò các kiểu dáng'),
(3, 'Giày Sneaker', 'Giày thể thao năng động'),
(4, 'Áo Thun', 'Các loại áo thun nam nữ'),
(5, 'Quần Jean', 'Quần bò các kiểu dáng'),
(6, 'Giày Sneaker', 'Giày thể thao năng động');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lov`
--

CREATE TABLE `lov` (
  `STT` int(11) NOT NULL,
  `TenThamSo` varchar(50) DEFAULT NULL,
  `MoTa` text DEFAULT NULL,
  `GiaTri` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lov`
--

INSERT INTO `lov` (`STT`, `TenThamSo`, `MoTa`, `GiaTri`) VALUES
(1, 'VAT', 'Thuế giá trị gia tăng', '10'),
(2, 'FreeShipLimit', 'Giá trị đơn hàng tối thiểu để freeship', '500000');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pnk`
--

CREATE TABLE `pnk` (
  `STT` int(11) NOT NULL,
  `NgayGioNhap` datetime DEFAULT current_timestamp(),
  `TrangThai` varchar(50) DEFAULT NULL,
  `MaNV` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `pnk`
--

INSERT INTO `pnk` (`STT`, `NgayGioNhap`, `TrangThai`, `MaNV`) VALUES
(1, '2025-12-01 21:44:51', 'DaNhap', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `STT` int(11) NOT NULL,
  `TenSP` varchar(100) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `HinhAnh` varchar(255) DEFAULT NULL,
  `GiaMuaCoBan` decimal(12,2) DEFAULT NULL,
  `SoLuongTon` int(11) DEFAULT 0,
  `MaLoai` int(11) DEFAULT NULL,
  `MaTH` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`STT`, `TenSP`, `MoTa`, `HinhAnh`, `GiaMuaCoBan`, `SoLuongTon`, `MaLoai`, `MaTH`) VALUES
(1, 'Áo Thun Nike Pro', 'Áo thun thể thao thoáng khí', 'nike_tshirt.jpg', 300000.00, 100, 1, 1),
(2, 'Quần Jean Slimfit', 'Quần jean co giãn', 'jeans.jpg', 450000.00, 50, 2, 3),
(3, 'Giày Ultraboost', 'Giày chạy bộ cao cấp', 'das_shoes.jpg', 2000000.00, 20, 3, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

CREATE TABLE `taikhoan` (
  `STT` int(11) NOT NULL,
  `TenDangNhap` varchar(50) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `HoTen` varchar(100) DEFAULT NULL,
  `LoaiTK` varchar(10) DEFAULT NULL,
  `GhiChu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`STT`, `TenDangNhap`, `MatKhau`, `HoTen`, `LoaiTK`, `GhiChu`) VALUES
(1, 'admin', '123456', 'Nguyễn Quản Trị', 'ADMIN', 'Quản lý hệ thống'),
(2, 'nhanvien1', '123456', 'Trần Bán Hàng', 'STAFF', 'Nhân viên bán hàng ca sáng'),
(3, 'khachhang1', '123456', 'Lê Mua Sắm', 'CUST', 'Khách hàng thân thiết');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `th`
--

CREATE TABLE `th` (
  `STT` int(11) NOT NULL,
  `Ten` varchar(100) DEFAULT NULL,
  `MoTa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `th`
--

INSERT INTO `th` (`STT`, `Ten`, `MoTa`) VALUES
(1, 'Nike', 'Thương hiệu thể thao toàn cầu'),
(2, 'Adidas', 'Thương hiệu đến từ Đức'),
(3, 'Uniqlo', 'Thương hiệu thời trang Nhật Bản');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `ctdd`
--
ALTER TABLE `ctdd`
  ADD PRIMARY KEY (`MaDon`,`MaSP`),
  ADD KEY `FK_CTDD_SANPHAM` (`MaSP`);

--
-- Chỉ mục cho bảng `ctpnk`
--
ALTER TABLE `ctpnk`
  ADD PRIMARY KEY (`MaPNK`,`MaSP`),
  ADD KEY `FK_CTPNK_SANPHAM` (`MaSP`);

--
-- Chỉ mục cho bảng `ctsanpham`
--
ALTER TABLE `ctsanpham`
  ADD PRIMARY KEY (`STT`),
  ADD KEY `FK_CTSANPHAM_SANPHAM` (`MaSP`);

--
-- Chỉ mục cho bảng `dgia`
--
ALTER TABLE `dgia`
  ADD PRIMARY KEY (`STT`),
  ADD KEY `FK_DGIA_SANPHAM` (`MaSP`),
  ADD KEY `FK_DGIA_KHACHHANG` (`MaKH`);

--
-- Chỉ mục cho bảng `dondat`
--
ALTER TABLE `dondat`
  ADD PRIMARY KEY (`STT`),
  ADD KEY `FK_DONDAT_KHACHHANG` (`MaKH`);

--
-- Chỉ mục cho bảng `loaisp`
--
ALTER TABLE `loaisp`
  ADD PRIMARY KEY (`STT`);

--
-- Chỉ mục cho bảng `lov`
--
ALTER TABLE `lov`
  ADD PRIMARY KEY (`STT`);

--
-- Chỉ mục cho bảng `pnk`
--
ALTER TABLE `pnk`
  ADD PRIMARY KEY (`STT`),
  ADD KEY `FK_PNK_NHANVIEN` (`MaNV`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`STT`),
  ADD KEY `FK_SANPHAM_LOAISP` (`MaLoai`),
  ADD KEY `FK_SANPHAM_TH` (`MaTH`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`STT`);

--
-- Chỉ mục cho bảng `th`
--
ALTER TABLE `th`
  ADD PRIMARY KEY (`STT`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `ctsanpham`
--
ALTER TABLE `ctsanpham`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `dgia`
--
ALTER TABLE `dgia`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `dondat`
--
ALTER TABLE `dondat`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `loaisp`
--
ALTER TABLE `loaisp`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `lov`
--
ALTER TABLE `lov`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `pnk`
--
ALTER TABLE `pnk`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `th`
--
ALTER TABLE `th`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `ctdd`
--
ALTER TABLE `ctdd`
  ADD CONSTRAINT `FK_CTDD_DONDAT` FOREIGN KEY (`MaDon`) REFERENCES `dondat` (`STT`),
  ADD CONSTRAINT `FK_CTDD_SANPHAM` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`STT`);

--
-- Các ràng buộc cho bảng `ctpnk`
--
ALTER TABLE `ctpnk`
  ADD CONSTRAINT `FK_CTPNK_PNK` FOREIGN KEY (`MaPNK`) REFERENCES `pnk` (`STT`),
  ADD CONSTRAINT `FK_CTPNK_SANPHAM` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`STT`);

--
-- Các ràng buộc cho bảng `ctsanpham`
--
ALTER TABLE `ctsanpham`
  ADD CONSTRAINT `FK_CTSANPHAM_SANPHAM` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`STT`);

--
-- Các ràng buộc cho bảng `dgia`
--
ALTER TABLE `dgia`
  ADD CONSTRAINT `FK_DGIA_KHACHHANG` FOREIGN KEY (`MaKH`) REFERENCES `taikhoan` (`STT`),
  ADD CONSTRAINT `FK_DGIA_SANPHAM` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`STT`);

--
-- Các ràng buộc cho bảng `dondat`
--
ALTER TABLE `dondat`
  ADD CONSTRAINT `FK_DONDAT_KHACHHANG` FOREIGN KEY (`MaKH`) REFERENCES `taikhoan` (`STT`);

--
-- Các ràng buộc cho bảng `pnk`
--
ALTER TABLE `pnk`
  ADD CONSTRAINT `FK_PNK_NHANVIEN` FOREIGN KEY (`MaNV`) REFERENCES `taikhoan` (`STT`);

--
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `FK_SANPHAM_LOAISP` FOREIGN KEY (`MaLoai`) REFERENCES `loaisp` (`STT`),
  ADD CONSTRAINT `FK_SANPHAM_TH` FOREIGN KEY (`MaTH`) REFERENCES `th` (`STT`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
