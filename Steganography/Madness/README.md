Madness
===

Link: https://tryhackme.com/room/madness

---
## Mục lục <a name="menu"></a>

* [Mục lục](#menu)
* [Thu thập thông tin](#info)
* [Xác định lỗ hổng bảo mật](#vuln)
* [Tiến hành xâm nhập](#exploit)
* [Kết quả](#result)

---
## Thu thập thông tin <a name="info"></a>

Đầu tiên, mình sẽ scan port tcp, udp (những port phổ biến):

![](https://i.imgur.com/Vd3cFpy.png)

![](https://i.imgur.com/cOatEPW.png)

Các port udp hầu như đã bị chặn hoặc đóng, tcp có 3 port mở:

* Port-22: OpenSSH 7.2p2 Ubuntu 4ubuntu2.8 (SSH)
* Port-80: Apache httpd 2.4.18 (HTTP)

Mình thử truy cập vào page ở port 80 xem sao, ssh để đó:

![](https://i.imgur.com/2UMjtmo.png)

Xem qua source mình thấy có 1 dòng bất thường, còn lại đều không có gì khả quan:

![](https://i.imgur.com/ntM3oZ2.png)

Tiếp theo, mình thử gobuster để crawl trang này để tìm các directory bị ẩn:



Crawl đi crawl lại vẫn không thấy gì thú vị (mình đã thử 2 file directory phổ biến là common.txt và big.txt với các extension: php, txt, html, x,...) nên mình chuyển qua hướng khác là clone nguyên trang web về để tìm kiếm, mình dùng httrack để clone:

> httrack --mirror http://10.10.188.120/ -O /home/kali/temp0

![](https://i.imgur.com/mMvMWu6.png)

Xem qua các thư mục thì mình thấy file có thể chứa thông tin ẩn là file thm.jpg (temp0/10.10.188.120/thm.jpg) , file này đã bị hỏng (corrupted), chúng ta có thể sử dụng các công cụ edit để phục hồi nó xem sao:

![](https://i.imgur.com/Cp0C7nC.png)

Tool mình sử dụng là GHex, 1 hex editor có GUI dễ sử dụng, 1 số tool khác như bless hay hexeditor cũng rất tốt:

![](https://i.imgur.com/Xb8IVgA.png)

Có thể thấy file này có extension là .jpg tức file jpeg nhưng header extension của nó lại là .png, dó đó mình lên search jpg signature format và sửa lại header cho đúng:

|PNG|JPEG|
|-|-|
|`89 50 4E 47`|`FF D8 FF E0`|

> Link signature format: https://www.file-recovery.com/jpg-signature-format.htm#:~:text=JPEG%2FJFIF%2C%20it%20is%20the,hex%20values%20FF%20D8%20FF.

Sau khi chỉnh sửa thì file thm.jpg sẽ có giá trị hex trông như sau:

![](https://i.imgur.com/GIh7OcF.png)

> *(Lưu ý: phải chỉnh các bytes đầu sau cho file trở thành extension .jpg thì mới hết corrupted)* 

Kết quả thu được là 1 file ảnh chứa đường dẫn ẩn:

![](https://i.imgur.com/cBBlqNp.png)

Truy cập vào đường dẫn này và đọc qua source code, mình tìm được clue mới:

![](https://i.imgur.com/utfXY16.png)

![](https://i.imgur.com/ibvf8Iq.png)

Clue này cho biết chúng ta cần tìm ra secret của admin để lấy được id của hắn, secret này nằm trong khoảng từ 0-99, do đó mình cần thử lần lượt các secret để xem tìm được thông tin gì không:

![](https://i.imgur.com/aHv9816.png)

Script python bruteforce secret:

```python=
import requests

url = 'http://10.10.188.120/th1s_1s_h1dd3n/?secret='
secret = 1


while(secret < 100):
    r = requests.get(url + str(secret))
    string = r.text
    if (string.find('That is wrong!') == -1):
        print(string)
    secret += 1
```

Script này sẽ lần lượt request tới trang hidden và thử từng secret, nếu kết quả nào không có dòng 'That is wrong' thì là kết quả đúng và in ra. Kết quả thu được:

![](https://i.imgur.com/MSqp2cg.png)

Có vẻ mình đã tìm được đoạn password bị mã hóa nào đó, tiếp theo mình sẽ giải mã và tìm xem đoạn pass kia dùng ở đâu.

Sau khi search gg và tìm hiểu 1 lúc thì mình phát hiện ra trong cái ảnh thm.jpg có giấu data, đây là 1 kỹ thuật Steganography (hide something in something), kỹ thuật này khá hay, mình research và tìm các tool chuyên dụng để unhiding data trong file ảnh, ở đây mình chọn steghide là 1 tool khá phổ biến, cú pháp để trích xuất data như sau:

```
steghide --extract -sf thm.jpg
```

![](https://i.imgur.com/OVFi1Du.png)

> Passphrase: y2RPJ4QaPF!B

Thành công trích xuất vào file hidden.txt:

![](https://i.imgur.com/gv5z4lK.png)

> Tài liệu tham khảo về steganography ở đây: https://fareedfauzi.gitbook.io/ctf-checklist-for-beginner/steganography

Ok giờ mình đã dùng được password để lấy được username, nhưng cái username này chưa chính xác, dựa vào hint thì usename này bị mã hóa bằng ROT13, vậy nên chỉ cần giải mã để tìm ra username chính xác:

![](https://i.imgur.com/fMPJsvt.png)

> Đã có được username và password, mình thử ssh tới server nhưng không thành công, suy ra cái password trên kia chỉ dùng để extract username được giấu trong file thm.jpg thôi còn password thật thì chưa tìm được.

> Link cyberchef ROT13 decode: https://gchq.github.io/CyberChef/#recipe=ROT13(true,true,false,13)&input=NWlXN2tDOA

Sau 1 lúc tìm kiếm thì mình phát hiện ra 1 cái ảnh khác nữa có thể sẽ chứa password ta cần, đó là cái ảnh này:

![](https://i.imgur.com/vvR0HSX.png)

Download về và dùng steghide thôi:

![](https://i.imgur.com/25dbGDf.png)

> *(Lưu ý: mình đã bỏ ra kha khá thời gian để tìm và bruteforce cái password cho hidden data trong cái ảnh này nhưng các bạn biết sao không, password là rỗng, chỉ cần steghide và enter là extract thành công)*

Cuối cùng, tổng hợp lại username và password là:
* Username: joker
* Password: *axA&GF8dP

Tiếp theo, mình sẽ đi tới sử dụng cặp user/pass này xem sao.

---
## Xác định lỗ hổng bảo mật <a name="vuln"></a>

Trước hết, mình thử ssh tới server dùng username và password tìm được:

![](https://i.imgur.com/Zshs4PX.png)

Thành công trong lần thử đầu tiên, do đó mình sẽ tiến hành tìm user flag và root flag. Có thể nói server đã để port ssh cho mình kết nối vào, từ đó xâm nhập trích xuất thông tin hệ thống.

---
## Tiến hành xâm nhập <a name="exploit"></a>

Đầu tiên là user flag:

![](https://i.imgur.com/8JC73eq.png)

Tiếp theo, mình sẽ xem quyền của joker bằng 'sudo -l' nhưng chúng ta không có quyền dùng sudo với account này, mình thử xem qua suid bằng câu lệnh sau:

```
find / -user root -perm /4000 2>/dev/null
```

![](https://i.imgur.com/U2vhFd0.png)

Đây là list các app có phân quyền s, tức là ta có thể thực thi chúng dưới quyền của owner(root), sau khi thử gần search gần hết cái list này thì mình thấy có cái này có thể dùng được:

```
/bin/screen-4.5.0
```

Search exploit database cho cái screen phiên bản này thì mình tìm được lỗi khá khả quan:

![](https://i.imgur.com/DOrUpox.png)


> Link: https://www.exploit-db.com/exploits/41154

Đọc docs về lỗi này thì chúng ta cần phải thực thi 1 file script để khai thác lỗi (có sẵn trên exploit.db) tại thư mục /tmp của target machine. Do đó, mình cd tới tmp và dùng nano tạo script với nội dung lấy được từ link trên:

![](https://i.imgur.com/4l1dftq.png)

![](https://i.imgur.com/YIXAhxi.png)

Đừng quên cấp quyền cho file để có thể thực thi bằng chmod:

```
chmod 777 screenscript.sh
```

Cuối cùng là thực thi và chờ kết quả:

![](https://i.imgur.com/bPCekWX.png)

Sau khi có được quyền root, chỉ cần cd tới /root là có được root flag:

![](https://i.imgur.com/gKDd01P.png)

Đúng thật là điên rồ mà :smile: 

---
## Kết quả <a name="result"></a>

* User flag: /home/joker/user.txt

```
THM{d5781e53b130efe2f94f9b0354a5e4ea}
```
* Root flag: /root/root.txt

```
THM{5ecd98aa66a6abb670184d7547c8124a}
```

