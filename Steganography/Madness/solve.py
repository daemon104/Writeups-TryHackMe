import requests

url = 'http://10.10.188.120/th1s_1s_h1dd3n/?secret='
secret = 1


while(secret < 100):
    r = requests.get(url + str(secret))
    string = r.text
    if (string.find('That is wrong!') == -1):
        print(string)
    secret += 1
