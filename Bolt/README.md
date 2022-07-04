# Bolt

Link: https://tryhackme.com/room/bolt

---
## Mục lục <a name="menu"> </a>

* [Mục lục](#menu) 
* [Thu thập thông tin](#infor)
* [Xác định lỗ hổng bảo mật](#vuln)
* [Tiến hành xâm nhập](#exploit)
* [Kết quả](#result)


---
## Thu thập thông tin <a name="infor"> </a>

Đầu tiên, mình sẽ dùng nmap scan tổng quan qua các ports tcp, udp đang mở của server:

![](https://i.imgur.com/HxIV6Br.png)

> *(Lưu ý: Trong pentest thực tế, bạn cần scan hết 65535 ports để nắm hết các port và service đang chạy trên server, vì mình đã scan rồi và không thấy port nào khác nên khi viết wu mình chỉ scan tổng quan 1000 ports cho ngắn gọn và nhanh hơn)*

Tất cả port UDP hầu như đều bị filtered, ta tạm thời bỏ qua.
Kết quả scan tổng quan thu được 3 port đang mở. Tiếp tục dùng script scan 3 port này:

![](https://i.imgur.com/iFXc9zs.png)

Kết quả thu được như sau:
* Port 22: OpenSSH 7.6p1 Ubuntu 4ubuntu0.3 (SSH)
* Port 80: Apache httpd 2.4.29 (HTTP)
* Port 8000: HTTP service

Thử truy cập http://10.10.178.163:80/ thì không thấy gì thú vị chuyển qua http://10.10.178.163:8000/ 

![](https://i.imgur.com/CmlwgBs.png)

Xem qua các mục trên site, mình tìm thấy đoạn message chứa thông tin mềm:

![](https://i.imgur.com/ftO1TeI.png)

> Username: bolt | Password: boltadmin123

Sau 1 lúc tìm kiếm và truy cập các sub domain, mình tìm thấy url để đăng nhập: http://10.10.178.163:8000/bolt/login và đăng nhập vào bằng username và pass tìm được lúc nãy để vào được trang web của admin (Jake).

![](https://i.imgur.com/AfZPEGz.png)

---
## Xác định lỗ hổng bảo mật <a name="vuln"> </a>

Ở trang dashboard của admin Jake, chú ý góc dưới cùng bên trái, mình thấy được CMS Blot có version là 3.7.1. Mình sẽ thử dùng metasploit framework để search lỗ hổng của Bolt3.7.1:

![](https://i.imgur.com/yfJwZT7.png)

> Authenticated RCE có vẻ khả thi, mình sẽ thử exploit với module này. Nội dung và cơ chế hoạt động của module bạn có thể đọc ở phần info lỗi của metasploit:

![](https://i.imgur.com/mY5R9qY.png)

Về phần câu lệnh thao tác với metasploit, bạn có thể xem ở đây: 
> https://docs.rapid7.com/metasploit/

---
## Tiến hành xâm nhập <a name="exploit"> </a>

Đầu tiên, mình sẽ dùng câu lệnh options để xem toàn bộ option của module:

![](https://i.imgur.com/3WShR7Y.png)

Set các option cần thiết:

![](https://i.imgur.com/yxFo8gC.png)

Tiến hành xâm nhập: 

![](https://i.imgur.com/Th8GvAn.png)

Sau khi đã xâm nhập thành công và có được quyền root, mình sẽ tìm và cat file flag.txt để hoàn thành room:

---
## Kết quả: <a name="result"> </a>

File flag.txt sẽ nằm ở /home:

![](https://i.imgur.com/Iy21JtY.png)

Flag: THM{wh0_d035nt_l0ve5_b0l7_r1gh7?}


