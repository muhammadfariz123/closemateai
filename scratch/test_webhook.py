import requests
import json

url = "https://closemateai.onrender.com/api/public/wa/6YBwD4qiX4zrkLNNfV8omvoAI2Mb4nYH"
payload = {
    "device": "085878067644",
    "sender": "6285160037014",
    "message": "Halo test test test",
    "name": "Dinda Simulator"
}

try:
    response = requests.post(url, json=payload)
    print("Status Code:", response.status_code)
    print("Response text:", response.text)
except Exception as e:
    print("Error:", e)
