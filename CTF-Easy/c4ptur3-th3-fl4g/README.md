c4ptur3-th3-fl4g
===

Link: https://tryhackme.com/room/c4ptur3th3fl4g

---
## Mục lục <a name="menu"></a>

* [Mục lục](#menu)
* [Intro](#intro)
* [Task1](#t1)
    * [Question 1](#q1)
    * [Question 2](#q2)
    * [Question 3](#q3)
    * [Question 4](#q4)
    * [Question 5](#q5)
    * [Question 6](#q6)
    * [Question 7](#q7)
    * [Question 8](#q8)
    * [Question 9](#q9)
    * [Question 10](#q10)
* [Task 2](#t2)
* [Task 3](#t3)
* [Task 4](#t4)

---
## Intro <a name="intro"></a>

Vì mình không phải là pro crypto mà chỉ có kiến thức cơ bản thôi nên tất cả câu hỏi trong room này mình đều dùng tool mạng, các decoder online.

Chủ yếu mình dùng tool của 2 trang sau:
* CyberChef: https://gchq.github.io/CyberChef/
* dcode: https://www.dcode.fr/en

Đây là 2 trang phổ biến chứa rất nhiều tool encrypt/decrypt cipher (dcode có cả các joke languages).

---
## Task 1 <a name="t1"></a>

**Translation & Shifting**

---
### Question 1 <a name="q1"></a>

Câu này là leet code: 

![](https://i.imgur.com/gmsYGkJ.png)

*Đáp án là: can you capture the flag?*

---
### Question 2 <a name="q2"></a>

Câu này là binary:

![](https://i.imgur.com/iELNWRZ.png)

*Đáp án là: lets try some binary out!*

---
### Question 3 <a name="q3"></a>

Base 32:

![](https://i.imgur.com/WndaQCg.png)


*Đáp án sẽ là: base32 is super common in CTF's*

---
### Question 4 <a name="q4"></a>

Base 64:

![](https://i.imgur.com/ARnVDyU.png)

*Đáp án sẽ là: Each Base64 digit represents exactly 6 bits of data.*

---
### Question 5 <a name="q5"></a>

Câu này là hệ thập lục (hexa):

![](https://i.imgur.com/xjxCKJ9.png)

*Đáp án sẽ là: hexadecimal or base16?*

---
### Question 6 <a name="q6"></a>

Đối với câu này, ta có thể dùng ROT13 hoặc Caesa Cipher với n = 13: 

![](https://i.imgur.com/pQ74czA.png)

*Đáp án là: Rotate me 13 places!*

---
### Question 7 <a name="q7"></a>

Đây là ROT47 cũng tương tự ROT13 nhưng áp dụng cho bảng mã ASCII có nhiều kí tự hơn:

![](https://i.imgur.com/3qGlo9H.png)

Đáp án là: You spin me right round baby right round (47 times)

---
### Question 8 <a name="q8"></a>

Câu này là mã morse thường được dùng trong quân đội, cần dịch 2 đoạn trên dưới:

![](https://i.imgur.com/Q1161H2.png)

![](https://i.imgur.com/XPzV8cC.png)

*Đáp án là: telecommunication encoding*

---
### Question 9 <a name="q9"></a>

From decimal to text:

![](https://i.imgur.com/ljnDjkV.png)

*Đáp án là: Unpack this BCD*

---
### Question 10 <a name="q10"></a>

Câu này hơi phức tạp hơn chút, nó giúp cho ta hiểu rằng cipher có thể được encode nhiều lần, giống mã hóa nhiều lớp vậy. Trình tự để giải đoạn mã là:

```
ciphertext -> base64 -> binary -> ROT47 -> decimal -> plaintext
```

* Base64:

![](https://i.imgur.com/vHC5ZV5.png)

* Binary:

![](https://i.imgur.com/woSkOfl.png)

* ROT47

![](https://i.imgur.com/2iw2yAC.png)

* Decimal:

![](https://i.imgur.com/gW8fiKG.png)

*Đáp án là: Let's make this a bit trickier...*

---
## Task 2 <a name="t2"></a>

**Spectrograms**

Task này sử dụng Spectrograms (quang phổ) để giấu flag trong file audio, kỹ thuật này rất hay, 1 số challenge khó sẽ giấu rất kỹ các thông điệp nhưng đối với câu này thì chỉ là cơ bản thôi. Mình sẽ dùng Audacity theo hint và import file đề vào:

![](https://i.imgur.com/X7KGuB2.png)

Tiếp theo, ở góc trái giữa, các bạn bấm vào chỗ secretaudio có mũi tên xổ xuống, chọn spectrograms thì sẽ ra ngay kết quả:

![](https://i.imgur.com/bVMBNCz.png)

*Đáp án là: super secret message*

---
## Task 3 <a name="t3"></a>

**Steganography**

Task này sử dụng kỹ thuật Steganography, đây là 1 kỹ thuật giấu cái này trong cái khác (hide something in something). Những thứ được giấu có thể là raw data, cipher text,... và chúng được giấu trong file, image, audio,.... Đối với kỹ thuật này, chúng ta có thể dùng 1 số tool như stego, steghide, stegcracker, binwalk, GHex, bless,... để edit, extract data được giấu đi.

Đầu tiên, mình dùng steghide để xem thông tin file down về, xem có ẩn data gì không:
```
steghide --info stegosteg.jpg
```

![](https://i.imgur.com/0dnx3hC.png)

Ok, phát hiện file ẩn, mình sẽ dùng steghide extract nó ra và cat file đó:

```
steghide --extract -sf stegosteg.jpg
```

![](https://i.imgur.com/b093R35.png)

*Đáp án là: SpaghettiSteg*

---
## Task 4 <a name="t4"></a>

Task này sử dụng kỹ thuật Security through obscurity hay gọi là "An ninh thông qua tối tăm" :smile: Các bạn có thể gg thêm nhé, đây là 1 kỹ thuật khá phổ biến.

Ở câu đầu, mình dùng binwalk để trích xuất file trong ảnh tải về, kết quả như sau:

```
binwalk -e meme.jpg
```

![](https://i.imgur.com/rmmZG6S.png)

Tìm thấy 1 directory ẩn, mình cd tới và thấy được file ảnh png, đó là đáp án cho câu này:

![](https://i.imgur.com/6bDJYHs.png)

![](https://i.imgur.com/eU6NoFM.png)

*Đáp án là: hackerchat.png*

---

Câu tiếp theo yêu cầu chúng ta đi sâu hơn nữa nên mình tiếp tục binwalk file 122A7.rar:

```
binwalk -e 122A7.rar
```

![](https://i.imgur.com/tC46VK5.png)

Đi tới thư mục extracted, lại thấy 1 file hackerchat.png, dựa vào hint, mình cần tìm đoạn text in hoa, mình thử mở file ảnh bằng GHex và kéo xuống xem thì ở cuối cùng của file tìm thấy hidden text:

![](https://i.imgur.com/bzPlB60.png)

![](https://i.imgur.com/LiUOOAV.png)

*Đáp án là: AHH_YOU_FOUND_ME!*

