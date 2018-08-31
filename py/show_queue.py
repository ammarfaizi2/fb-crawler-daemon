try:
	from init import *
except:
	from py.init import *

import json

i = 1
out = []
for x in db.crawling_target.find({"account_type": "facebook-account"}):
	x["_id"] = str(x["_id"])
	x["insert_date"] = str(x["insert_date"])
	out.append(x)

print(json.dumps(out))
