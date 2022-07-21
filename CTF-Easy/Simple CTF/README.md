Simple CTF
===

Link: https://tryhackme.com/room/easyctf

---
## Mục lục <a name="menu"></a>

* [Mục lục](#menu)
* [Thu thập thông tin](#info)
* [Xác định lỗ hổng bảo mật](#vuln)
* [Tiến hành xâm nhập](#exploit)
* [Kết quả](#result)

---
## Thu thập thông tin <a name="info"></a>

Đầu tiên, mình scan tổng quan qua các port tcp, udp:

![](https://i.imgur.com/kGWvo3g.png)

![](https://i.imgur.com/wXtjpOj.png)

Các port udp hầu hết đều không khả dụng, tcp có 3 port mở:

* Port-21: vsftpd 3.0.3 (FTP)
* Port-80: Apache httpd 2.4.18 (HTTP)
* Port-2222: OpenSSH 7.2p2 Ubuntu 4ubuntu2.8 (SSH)

Xem qua port 80 thì mình thấy không có gì khả quan, đây là trang mặc định của ubuntu:

![](https://i.imgur.com/1Vwm3RK.png)

Sau đó, mình thử dùng gobuster crawl trang này ra, kết quả thu được:

![](https://i.imgur.com/X7794tX.png)

Tiến hành truy cập từng trang để tìm kiếm thông tin, robots.txt thì không thấy gì, còn http://10.10.190.55/simple/ thì mình đoán đây là trang web chính của SimpleCTF:

![](https://i.imgur.com/xVEa7Ws.png)

Tiếp tục dùng gobuster để crawl trang này, 1 lúc sau mình tìm được trang login:

![](https://i.imgur.com/RmqtTQc.png)

![](https://i.imgur.com/ljEtuGH.png)

OK, vậy thì username và password là gì? Chưa biết nên tạm thời để đó, mình thử kết nối ftp tới server bằng account anonymous thì thành công, sau đó mình tìm thấy 1 file tên ForMitch.txt, download nó về máy để xem:

![](https://i.imgur.com/ZalN0MR.png)

![](https://i.imgur.com/0cqJPFD.png)

![](https://i.imgur.com/RehA6F8.png)

> ForMitch.txt: đọc qua nội dung, mình đoán file này dành cho 1 dev tên Mitch và anh ta mắc lỗi đặt password yếu cho system user. 


---
## Xác định lỗ hổng bảo mật <a name="vuln"></a>

Mình đã có được kha khá thông tin, giờ bắt đầu xác định lỗ hổng bảo mật.

Mình sẽ thử với ssh. Mặc định username là mitch còn password thì tiến hành bruforce:

```
hydra -l mitch -P /home/kali/best110.txt 10.10.190.55 -t 4 ssh -vV -s 2222
```

![](https://i.imgur.com/gDmYBVj.png)

Thành công tìm được password, bây giờ mình sẽ tiến hành sử dụng username với password này để khai thác server.


---
## Tiến hành xâm nhập <a name="exploit"></a>

Trước tiên, mình dùng username với pass tìm được để log vào trang login lúc nãy:

![](https://i.imgur.com/V4zhEJk.png)

Thành công login như admin, bây giờ mình có thể kiểm soát main page và truy xuất được nhiều thông tin mềm của server dưới vai trò admin, quả là 1 lỗ hổng nguy hiểm!

Bây giờ mình tiến hành ssh tới server và thành công lấy được shell của mitch và cat được user.txt:

![](https://i.imgur.com/yJQSrTP.png)

Trong task có câu hỏi vể user khác trong home directory nên mình dùng cd để tìm:

![](https://i.imgur.com/n4OMW0m.png)

> User 2: sumbath

Tiếp theo, mình sử dụng sudo -l xem user mitch có các quyền gì:

![](https://i.imgur.com/vktDely.png)

Phát hiện user mitch có thể dùng vim dưới quyền root, do đó, mình tiến hành nâng cấp quyền bằng vim, câu lệnh như sau:

```
sudo vim -c ':!/bin/sh'
```

![](https://i.imgur.com/SDBMrud.png)

Thành công có được quyền root, bây giờ mình chỉ việc tìm root flag để hoàn thành room.

![](https://i.imgur.com/K0XO2si.png)

---
## Kết quả <a name="result"></a>

User flag: /home/mitch/user.txt

```
G00d j0b, keep up!
```

Root flag: /root/root.txt

```
W3ll d0n3. You made it!
```
