import requests
import time

url = "https://closemateai.onrender.com/api/debug/logs"

for _ in range(10):
    try:
        r = requests.get(url)
        if r.status_code == 200 and "Fonnte" in r.text:
            print("Logs found!")
            print(r.text[-2000:])
            break
        elif r.status_code == 200:
            print("Logs loaded but Fonnte might not be there.")
            print(r.text[-1000:])
            break
        else:
            print(f"Status {r.status_code}, waiting...")
    except Exception as e:
        print(f"Error: {e}")
    time.sleep(5)
