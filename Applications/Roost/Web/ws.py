
from websocket import create_connection

ws = create_connection("ws://api.edisonx.cn:3340")
ws.send("Hello, World")

result =  ws.recv()
print("Received '%s'" % result)
ws.close()