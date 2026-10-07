import requests

url = 'https://api.fonnte.com/send'
headers = {'Authorization': 'INVALID_TOKEN_123'}

# Test JSON
print("Testing JSON:")
r_json = requests.post(url, headers=headers, json={'target': '08123456789', 'message': 'test'})
print(r_json.status_code, r_json.text)

# Test URL Encoded (asForm)
print("\nTesting URL Encoded:")
r_form = requests.post(url, headers=headers, data={'target': '08123456789', 'message': 'test'})
print(r_form.status_code, r_form.text)

# Test Multipart
print("\nTesting Multipart:")
r_multi = requests.post(url, headers=headers, files={'target': (None, '08123456789'), 'message': (None, 'test')})
print(r_multi.status_code, r_multi.text)
