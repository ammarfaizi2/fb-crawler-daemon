from init import *

realinput = ""
for line in sys.stdin:
	realinput += line
realinput = realinput.rstrip()

print(realinput)
