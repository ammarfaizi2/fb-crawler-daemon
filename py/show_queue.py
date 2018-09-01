try:
	from init import *
except:
	from py.init import *

import json

out = []

status_search_query = {
    "$or": [
        {
            "not_found": False, "crawling": False
        }, {
            "not_found": False, "crawling": True, "crawling_type": 'sustainable'
        }
    ]
}
platform_search_query = {"account_type": "facebook-account"}

for data in db.crawling_target.find({"$and": [status_search_query, platform_search_query]}):
	data["_id"] = str(data["_id"])
	data["insert_date"] = str(data["insert_date"])
	out.append(data)

print(json.dumps(out))
