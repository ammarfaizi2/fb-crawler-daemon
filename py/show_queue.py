try:
	from init import *
except:
	from py.init import *

import json

out = []

search_query = [
    {
        "not_found": False, "crawling": False
    }, {
        "not_found": False, "crawling": True, "crawling_type": 'sustainable'
    }
]

for data in db.crawling_target.find({"$or": search_query}):
	data["_id"] = str(data["_id"])
	data["insert_date"] = str(data["insert_date"])
	out.append(data)

print(json.dumps(out))
