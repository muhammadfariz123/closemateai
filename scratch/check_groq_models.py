import os
import requests

groq_key = None
with open('.env', 'r') as f:
    for line in f:
        if line.startswith('GROQ_API_KEY='):
            groq_key = line.split('=', 1)[1].strip()
            break

url = "https://api.groq.com/openai/v1/models"
headers = {"Authorization": f"Bearer {groq_key}", "Content-Type": "application/json"}

try:
    response = requests.get(url, headers=headers)
    print(response.json())
except Exception as e:
    print("Error:", e)
