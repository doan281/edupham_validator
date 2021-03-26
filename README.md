## EDUPHAM VALIDATOR
Package hỗ trợ validate trong Laravel
### I. Các validate hỗ trợ
##### Nhóm kiểm tra đầu số di động/cố định:
- Validate đầu số mạng mobifone: 090, 093, ...
- Validate đầu số mạng viettel: 097, 098, ...
- Validate đầu số mạng vinaphone: 091, 094, ...
- Validate đầu số mạng vietnamobile: 092, ...
- Validate đầu số điện thoại cố định các Tỉnh/TP
##### Nhóm kiểm tra mã số thuế:
- Validate mã số thuế Việt Nam
- Validate ký hiệu hóa đơn
##### Nhóm kiểm tra tài khoản:
- Validate tên tài khoản thường dùng
- Validate mật khẩu thường dùng
- Validate mật khẩu mạnh
##### Nhóm kiểm tra khác:
- Validate chữ viết HOA
### II. Cài đặt
Cấu hình trong file composer.json
```
"require": 
{
     ...,
     "edupham/validator": "dev-master"
},
```
sau đó chạy lệnh:
```
composer install
```
hoặc chạy lệnh:
```
composer update
```

### III. Cách sử dụng
##### Danh sách rule

##### Danh sách rule

### Thông tin người viết
- [Phạm Văn Đoan](https://github.com/doan281?tab=repositories)
- Email: doan281@gmail.com
