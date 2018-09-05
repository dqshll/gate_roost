import sys
from websocket import create_connection

ws = create_connection("ws://api.edisonx.cn:3340")
ws.send("trg_" + sys.argv[1])
result =  ws.recv()
#print("Received '%s'" % result)
ws.close()