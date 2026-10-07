import os
import requests
import json

groq_key = None
with open('.env', 'r') as f:
    for line in f:
        if line.startswith('GROQ_API_KEY='):
            groq_key = line.split('=', 1)[1].strip()
            break

url = "https://api.groq.com/openai/v1/chat/completions"
headers = {"Authorization": f"Bearer {groq_key}", "Content-Type": "application/json"}
payload = {
    "model": "llama-3.1-8b-instant",
    "messages": [
        {"role": "system", "content": "You are a helpful assistant."},
        {"role": "user", "content": "Halo"},
        {"role": "user", "content": "Permisi"},
        {"role": "user", "content": "Halo ka"}
    ]
}

response = requests.post(url, headers=headers, json=payload)
print(response.status_code)
print(response.text)
