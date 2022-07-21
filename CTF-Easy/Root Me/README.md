Root Me
===

Link: https://tryhackme.com/room/rrootme

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

![](https://i.imgur.com/0OPAx4S.png)

![](https://i.imgur.com/am3S3WY.png)

Tất cả các port udp đều bị filtered, tcp có 2 port mở:

* Port-22: OpenSSH 7.6p1 Ubuntu 4ubuntu0.3 (SSH)
* Port-80: Apache httpd 2.4.29 (HTTP)

Mình thử truy cập vào http://10.10.40.41:80 , xem qua source, lướt front vẫn không thấy có gì thú vị nên mình sẽ dùng gobuster để crawl ra các directories ẩn:

![](https://i.imgur.com/Y02bfO5.png)

Tìm thấy 3 path ẩn.

---
## Xác định lỗ hổng bảo mật <a name="vuln"></a>

Thử truy cập vào 3 path tìm được ở trên, mình dự đoán là server dính lỗi uploads file:

![](https://i.imgur.com/bzYjr0j.png)

![](https://i.imgur.com/VkhgBrO.png)

Mình thử upload 1 file jpg lên rồi execute thử:

![](https://i.imgur.com/TlEoSSN.png)

Thành công:

![](https://i.imgur.com/diBl5MW.jpg)

> *(All hail Lelouch)*

Bây giờ mình cần tạo file backdoor rồi tìm cách execute nó trên server để tạo kết nối ngược về máy mình và lấy reverse shell. 

---
## Tiến hành xâm nhập <a name="exploit"></a>

Trước hết, mình sẽ dùng nc lắng nghe tại port bất kì (> 1000):

```
nc -vlnp 'port'
```

Tiếp theo, mình lên mạng tìm php reverse shell và cách bypass upload file theo hint. Các bạn có thể tham khảo 2 nguồn này: 
* https://github.com/pentestmonkey/php-reverse-shell/blob/master/php-reverse-shell.php
* https://vulp3cula.gitbook.io/hackers-grimoire/exploitation/web-application/file-upload-bypass

Mình tạo 1 file tên là backdoor2.php5 với nội dung như link 1, đuôi php5 là để bypass filter của server, sau đó vào http://10.10.40.41/uploads/ bấm vào để thực thi nó và mình thành công lấy được reverse shell:

![](https://i.imgur.com/XqWhYKI.png)

Bây giờ mình đã có thể cat file user.txt. Kế tiếp, mình thử 1 số cách để nâng cấp quyền root. Dựa vào hint của task4, mình biết được câu lệnh để find. Cụ thể câu lệnh này dùng để find tất cả những file mà có phân quyền s (set UID) là file bạn có thể thực thi với quyền của owner file đó. Vì nó tìm ra nhiều kết quả quá nên mình sẽ grep lại cho gọn:

```
find / -user root -perm /4000 | grep /usr/bin
```

Nhìn vào kết quả, mình tìm được 1 path khá thú vị là: /usr/bin/python, mình lên mạng search theo hint thì tìm được payload nâng cấp quyền bằng SUID:

```
./python -c 'import os; os.execl("/bin/sh", "sh", "-p")'
```

Sau khi thực thi câu lệnh, mình thành công nâng quyền lên root:

![](https://i.imgur.com/TK1V823.png)

Việc cuối cùng chỉ là tìm file flag thôi.

---
## Kết quả <a name="result"></a>

user.txt: /var/www/user.txt

![](https://i.imgur.com/Qil0AZa.png)

root.txt: /root

![](https://i.imgur.com/Xjv0qre.png)
