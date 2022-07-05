Bounty Hacker
===

Link: https://tryhackme.com/room/cowboyhacker

---
## Mục lục <a name="menu"></a>

* [Mục lục](#menu)
* [Thu thập thông tin](#info)
* [Xác định lỗ hổng bảo mật](#vuln)
* [Tiến hành xâm nhập](#exploit)
* [Kết quả](#result)

---
## Thu thập thông tin <a name="info"></a>

Như thường lệ, mình scan tổng quan các port tcp, udp trước:

![](https://i.imgur.com/Qq1v5TI.png)

![](https://i.imgur.com/vcjw507.png)

Các port udp dường như đã bị filtered hết, có 3 port tcp đang mở:

* Port-21: vsftpd 3.0.3 (FPT)
* Port-22: OpenSSH 7.2p2 Ubuntu 4ubuntu2.8 (SSH)
* Port-80: Apache httpd 2.4.18 (HTTP)

Trước hết mình truy cập vào http://10.10.131.193:80 thì đây là trang web chính của bounty hacker:

![](https://i.imgur.com/w4VPVJB.jpg)

> Anime: Cowboy Bebop

Lướt sơ qua thì không thấy thông tin gì thú vị nên mình tạm skip và qua port 21 ftp:

![](https://i.imgur.com/aonUHbw.png)

> Kết nối với account anonymous thì thành công

Mình thử dùng dir list các file:

![](https://i.imgur.com/hXs7LtZ.png)

Thấy 2 file kia có thể chứa thông tin quan trọng nên mình tải cả 2 về máy xem:

![](https://i.imgur.com/KrIsZm5.png)

![](https://i.imgur.com/99em7ha.png)

File locks.txt sẽ chứa 1 list các password để đăng nhập vào đâu đó, file task.txt chứa tên người viết task.

![](https://i.imgur.com/MMdUqJH.png)

---
## Xác định lỗ hổng bảo mật <a name="vuln"></a>

Tiếp theo, mình bắt đầu tìm cách để sử dụng các pw kia để đăng nhập, gobuster main page của server không thu được gì nên mình chuyển qua ssh nhờ đọc câu hỏi và hint trong task. 

Để ssh tới server thì mình cần có username và password, mình dùng hydra để bruteforce username, password trong file locks.txt, câu lệnh sử dụng như sau:

```
hydra -l lin -P /home/kali/locks.txt 10.10.131.193 -t 4 ssh -vV
```

Username thử với root không được nên mình đổi sang lin. Bruteforce được 1 lúc thì mình thành công tìm ra password:

![](https://i.imgur.com/ZA7v4ep.png)

Sau khi có username và password, bước tiếp theo là ssh tới server.

---
## Tiến hành xâm nhập <a name="exploit"></a>

Mình ssh tới server thành công và lấy được flag user:

![](https://i.imgur.com/o3vmsDe.png)

Bây giờ, việc cần làm là tiến hành nâng cấp quyền để lấy được quyển root. Trước hết, mình dùng "sudo -l" để xem coi user lin có được dùng công cụ gì dưới quyền root không, rồi từ đó khai thác quyền:

![](https://i.imgur.com/xaW7uak.png)

> Password dùng lại cái lúc nãy

Vậy lin sẽ dùng tar dưới quyền root được, mình liền thực thi câu lệnh nâng cấp quyền bằng tar:

```
sudo tar -cf /dev/null /dev/null --checkpoint=1 --checkpoint-action=exec=/bin/sh
```
> Nguồn: https://gtfobins.github.io/gtfobins/tar/#sudo

Thành công nhặt được quyền root, việc cuối cùng là look around và tìm flag root thôi.

---
## Kết quả <a name="result"></a>

User flag: /home/lin/Desktop/user.txt

![](https://i.imgur.com/pQFRoRn.png)

Root flag: /root/root.txt

![](https://i.imgur.com/ljZxJgH.png)

