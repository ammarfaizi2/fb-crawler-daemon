from init import *
import json

from bson.objectid import ObjectId

def upsert_to_post(json_input):
    queue_id = json_input["_queue_id"]
    collection = db.crawling_post
    search_query = {
        "_queue_id": queue_id,
    }

    current_document = collection.find_one(search_query)

    if current_document is not None:
        current_document['scraped_at'] = json_input['scraped_at']
        posts = {
            item['story_fbid']: item
            for item in current_document['user_posts']
        }

        new_posts = {
            item['story_fbid']: item
            for item in json_input['user_posts']
        }
        posts.update(new_posts)
        json_input['user_posts'] = posts

        db.crawling_post.update(
            search_query,
            json_input,
            **{
                "upsert": True,
            }
        )
    else:
        db.crawling_post.insert_one(json_input)

realinput = process_stdin()

json_input = json.loads(realinput)
upsert_to_post(json_input)

