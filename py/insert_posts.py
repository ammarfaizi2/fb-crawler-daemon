from init import *
import json

from bson.objectid import ObjectId

def upsert_to_post(json_input):
    queue_id = json_input["_queue_id"]
    search_query = {
        "_queue_id": queue_id,
    }
    db.crawling_post.update(
        search_query,
        json_input,
        **{
            "upsert": True,
        }
    )

realinput = process_stdin()

json_input = json.loads(realinput)
upsert_to_post(json_input)

