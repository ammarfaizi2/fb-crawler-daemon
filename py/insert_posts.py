from init import *
import json

realinput = process_stdin()

json_input = json.loads(realinput)
db.crawler_post.insert_one(json_input)
