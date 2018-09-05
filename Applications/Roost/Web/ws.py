import sys
from websocket import create_connection
ws = create_connection("ws://api.edisonx.cn:3340")
ws.send(sys.argv[1])
ws.close()