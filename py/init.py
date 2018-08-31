import sys

sys.path.append('../')

from config.mongo import *
from pymongo import MongoClient

client = MongoClient(HOST, PORT)
db = client["privos"]

def process_stdin():
    realinput = ""
    for line in sys.stdin:
        realinput += line
    realinput = realinput.rstrip()
    return realinput
