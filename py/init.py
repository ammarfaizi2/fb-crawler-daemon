import sys

sys.path.append('../')

from config.mongo import *
from pymongo import MongoClient

client = MongoClient(HOST, PORT)
db = client["privos"]
