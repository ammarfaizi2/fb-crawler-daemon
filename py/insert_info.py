from init import *
import json

from bson.objectid import ObjectId

def upsert_to_info(json_input):
    queue_id = json_input["_queue_id"]
    search_query = {
        "_queue_id": queue_id,
    }
    db.crawling_info.update(
        search_query,
        json_input,
        **{
            "upsert": True,
        }
    )

def set_crawl_to_true(queue_id):
    update_query = {
        "_id": ObjectId(queue_id)
    }

    set_query = {
        "$set": {
            "crawling": True
        }
    }

    db.crawling_target.update_one(update_query, set_query)

realinput = process_stdin()

json_input = json.loads(realinput)
upsert_to_info(json_input)

queue_id = json_input["_queue_id"]
set_crawl_to_true(queue_id)

