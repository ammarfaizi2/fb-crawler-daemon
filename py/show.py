import sys
import json
from pymongo import MongoClient

sys.path.append('../')
from config.mongo import *

client = MongoClient(HOST, PORT)

db = client["privos"]

i = 1
for x in db.crawling_target.find({"account_type": "facebook-account"}):
	x["_id"] = str(x["_id"])
	x["insert_date"] = str(x["insert_date"])
	print(json.dumps(x))
