from init import *
import json

def set_not_found_to_true(queue_id):
    update_query = {
        "_id": ObjectId(queue_id)
    }

    set_query = {
        "$set": {
            "not_found": True
        }
    }

    db.crawling_target.update_one(update_query, set_query)

realinput = process_stdin()

json_input = json.loads(realinput)
queue_id = json_input["_queue_id"]
set_not_found_to_true(queue_id)
