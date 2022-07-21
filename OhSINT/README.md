OhSINT
===

Link: https://tryhackme.com/room/ohsint

---
## Mục lục <a name="menu"></a>

* [Mục lục](#menu)
* [Question 1](#q1)
* [Question 2](#q2)
* [Question 3](#q3)
* [Question 4](#q4)
* [Question 5](#q5)
* [Question 6](#q6)
* [Question 7](#q7)

---
## Question 1 <a name="q1"></a>

Ở câu đầu tiên đòi hỏi chúng ta dùng tool phân tích ảnh, trong hint có hướng dẫn dùng Exiftool nên mình thử với câu lệnh sau:

```
exiftool WindowsXP.jpg 
```

![](https://i.imgur.com/8ddaQqq.png)

Từ kết quả trên, ta thấy được dòng copyright có tên của ai đó, mình lên search gg tên này thì tìm được twitter của hắn:

![](https://i.imgur.com/b3eLkAO.png)

![](https://i.imgur.com/wVz07Dh.png)

Ok vậy giờ ta đã có đáp án cho câu 1 là cat(mèo).

---
## Question 2 <a name="q2"></a>

Câu hỏi tiếp theo hỏi là kẻ này sống ở thành phố nào. Mình xem các tweet của hắn thì thấy hắn post lên BSSID của mình. BSSID hay MAC address là địa chỉ vật lí duy nhất cho mỗi thiết bị.

![](https://i.imgur.com/sWl8LrF.png)

Có được BSSID, mình xem hint thì được hướng dẫn dùng wigle.net để search nên mình thử theo các bước như sau:

* Truy cập wigle.net, đăng kí tài khoản, đăng nhập vào
* Chọn View -> Basic Search -> Wireless Network Mapping
* Nhập BSSID, tìm kiếm trên map bên cạnh

![](https://i.imgur.com/sFI3g9A.png)

Sau khi query xong, các bạn qua map và zoom out 1 tý sẽ thấy 1 đóm tím là vị trí chúng ta cần tìm, vị trí này là thành phố Luân Đôn thuộc nước Anh nên đáp án câu 2 sẽ là London:

![](https://i.imgur.com/QOUkiqp.png)

---
## Question 3 <a name="q3"></a>

Câu này khá đơn giản, đề hỏi SSID của WAP là gì (WAP là điểm truy cập không dây hay còn gọi là access point). Hay nói dễ hiểu hơn là tên wifi của kẻ này đang dùng là gì (giống tên wifi bạn thường đặt ở nhà). Với kết quả lúc nãy, chúng ta nhìn sang góc giữa sẽ thấy tên wifi ngay:

![](https://i.imgur.com/1Qeo5tD.png)

Đáp án sẽ là: unileverWifi

---
## Question 4 <a name="q4"></a>

Ở câu này, đề hỏi về mail của OWoodflint. Chúng ta search lại tên của hắn trên gg sẽ thấy được link github:

![](https://i.imgur.com/Zbl4G8K.png)

Bấm vào repo "people_finder", ta thấy ngay mail của hắn:

![](https://i.imgur.com/xUeL4Pf.png)

Đáp án sẽ là: OWoodflint@gmail.com

---
## Question 5 <a name="q5"></a>

Câu này hỏi chúng ta đã tìm thấy mail của hắn ở đâu, câu trả lời rất đơn giản là github.

> 1 trick khác để tìm mail của ai đó dựa vào github: https://www.nymeria.io/blog/how-to-manually-find-email-addresses-for-github-users

---
## Question 6 <a name="q6"></a>

Với câu hỏi này, mình gg tên hắn và truy cập vào wordpress của hắn, tại đây, mình thấy hắn đã đi đến New York, đây sẽ là đáp án của câu này:

![](https://i.imgur.com/sGzGN4m.png)

![](https://i.imgur.com/s4ei2r9.png)

---
## Question 7 <a name="q7"></a>

Câu này đề hỏi password của Owoodflint là gì, mình thử xem qua source code trang wordpress của hắn thì phát hiện 1 chuỗi có định dạng giống password:

![](https://i.imgur.com/xGg2hLB.png)

Ra là hắn đã dùng màu trắng để ẩn password khiến chúng ta không thấy được (color:#ffffff là màu trắng trong style html):

![](https://i.imgur.com/2Osgm3L.png)

Đáp án sẽ là: pennYDr0pper.!
