from init import *

realinput = process_stdin()

json_input = json.loads(realinput)
db.crawler_info.insert_one(json_input)
