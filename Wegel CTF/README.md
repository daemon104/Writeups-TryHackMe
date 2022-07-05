Wgel
===

Link: https://tryhackme.com/room/wgelctf

---
## Mục lục <a name="menu"></a>

* [Mục lục](#menu)
* [Thu thập thông tin](#info)
* [Xác định lỗ hổng bảo mật](#vuln)
* [Tiến hành xâm nhập](#exploit)
* [Kết quả](#result)

---
## Thu thập thông tin <a name="info"></a>

Đầu tiên, mình dùng nmap scan tổng quan các port tcp, udp:

![](https://i.imgur.com/ch6aB1n.png)

![](https://i.imgur.com/DD2ldjt.png)

Các port udp thông dụng đều đã bị filtered, có 2 port TCP mở:
* Port-22: OpenSSH 7.2p2 Ubuntu 4ubuntu2.8 (SSH)
* Port-80: Apache httpd 2.4.18 (HTTP)

Thử truy cập vào http://10.10.31.23:80 ta thấy đây là trang mặc định của apache unbuntu, xem qua source thì thấy 1 dòng khá đặc biệt:

![](https://i.imgur.com/eOgJ5p0.png)

> Username: jessie

Mình tiếp tục crawl trang này bằng gobuster để xem có đường dẫn ẩn nào hay ho không. Mình tìm được đường dẫn sitemap, thử truy cập vào xem sao:

![](https://i.imgur.com/Tjo8MSc.png)

![](https://i.imgur.com/G0p4CBX.png)

Theo như hình, đây có thể là website chính của server wgel, mình tiếp tục dùng gobuster, 1 lúc sau mình crawl ra được trang .ssh, trang này chứa private key rsa dùng để verify user khi ssh đến server:

![](https://i.imgur.com/XFA0qmE.png)

![](https://i.imgur.com/zU5A3U8.png)

> *(Vấn đề này thuộc lĩnh vực crypto, bạn có thể search gg cách RSA encode/decode văn bản và cách server xác thực người dùng thông qua cặp public/private key,...)*


---
## Xác định lỗ hổng bảo mật <a name="vuln"></a>

Sau khi có được rsa private key, mình xác định server dính lỗi ssh vì để lộ key ở nơi user có thể dễ dàng thấy được. Mình sẽ tiến hành ssh tới server với username có được và key này:

![](https://i.imgur.com/XnFjCBU.png)

> *(Các bạn nhớ thay đổi quyền truy cập file key bằng chmod 777 hay 700 đều được, nếu không sẽ gặp lỗi)*

![](https://i.imgur.com/YNP3SXw.png)

Sau khi có được shell cơ bản, mình lấy được flag user, giờ mình sẽ tiến đến nâng cấp quyền root.

---
## Tiến hành xâm nhập <a name="exploit"></a>

Trước hết, mình check xem server có dùng các công cụ quen thuộc có thể giúp mình leo quyền hệ thống không:

![](https://i.imgur.com/87lCrix.png)

Mình thấy server có dùng python, perl và nc nên sẽ tiến hành thử nâng cấp quyền thông qua các công cụ này nhưng có vẻ đều thất bại nên mình sẽ tìm cách khác. Sau 1 lúc mò mẩm, mình tìm được 1 thông tin khá quan trọng:

![](https://i.imgur.com/U5YPGun.png)

Điều này nghĩa là user jessie sẽ được dùng wget dưới đặc quyền root, vậy chúng ta có thể lợi dụng lổ hổng này để nâng đặc quyền.

> Mình search gg cách nâng cấp quyền với wget thì tìm đang nguồn này: https://vk9-sec.com/wget-privilege-escalation/ Các bạn có thể dựa theo nó và search thêm kiến thức về passwd là có thể thành công lấy được quyền root.

Đầu tiên, mình dùng nc lắng nghe ở port 80:

![](https://i.imgur.com/HIeAgUF.png)

Tiếp đó, tại shell jessie, mình tiến hành post file /etc/passwd qua cho machine của mình:

![](https://i.imgur.com/FupEot2.png)

Kết quả hiển thị bên nc:

![](https://i.imgur.com/jP9kNv2.png)

File /etc/passwd của server đã được in ra màn hình, giờ mình chỉ cần tạo file tên passwd rồi chỉnh sửa file, bỏ dấu x ở account root (đó là mật khẩu đã bị ẩn đi) thay bằng bản hash của mật khẩu mình tự chọn:

![](https://i.imgur.com/JU8OhH5.png)

![](https://i.imgur.com/Nwf5O5L.png)

Kế đến, uploads file passwd vừa mới sửa lên server:

![](https://i.imgur.com/S5cyzWm.png)

Kiểm tra xem server đã ghi đè file passwd mới lên file cũ chưa bằng cách post file qua nc như ở trên:

![](https://i.imgur.com/q3Mc8by.png)

Thành công ghi đè file, bây giờ mình đăng nhập account root tại server bằng shell jessie:

![](https://i.imgur.com/PxRgYVm.png)

Cuối cùng, mình chỉ cần tìm flag để hoàn thành room!!

---
## Kết quả <a name="result"></a>

User flag: /home/jessie/Documents/user_flag.txt

![](https://i.imgur.com/zTPiFvW.png)

Root flag: /root/root_flag.txt

![](https://i.imgur.com/CNzJLma.png)

