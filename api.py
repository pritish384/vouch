from flask import Flask, request
from bot import client , embed , vouchbtn
import requests
import pymongo

uri = "mongodb+srv://admin:auiadmin1234@cluster0.5kthlmv.mongodb.net/?retryWrites=true&w=majority"

# Connect to MongoDB
try:
    mongoclient = pymongo.MongoClient(uri)
    print("Connected to MongoDB!")
except Exception as e:
    print(e)


# Get the database
db = mongoclient['vouch']
collection = db['userinfo']


app = Flask(__name__)

@app.route('/api', methods=['POST' , 'GET'])


def handle_request():
    if request.method == 'GET':
        return 'Alive'
    
    data = request.get_json()
    if data['api_token'] != '42bd60d3-01b2-4c2a-b55b-9459b35dcc89':
        return 'Invalid API Token'
    else:
        document = {
        "discord_id": data['discord_id'],
        "discord_username": data['discord_username'],
        "discord_discriminator": data['discord_discriminator'],

        "vouches_left": data['vouches_left'],
        "vouch_user_id": data['vouch_user_id'],
        "vouch_username": data['vouch_username'],
        "vouch_discriminator": data['vouch_discriminator'],
        "vouch_id": data['vouch_id']
    }

    
    collection.insert_one(document)
    
    vouchdata = collection.find_one({"vouch_id": data['vouch_id']})


    async def send_embed():
        # vouchbtn_instance = vouchbtn(vouchdata)
        embed_instance = embed(vouchdata)
        vouchbtn_instance = vouchbtn()

        channel = client.get_channel(1086651597708333117)
        await channel.send(embed=embed_instance , view=vouchbtn_instance)

    client.loop.create_task(send_embed())

    return data

# post request to api.php
def update_vouches_left(update):

    return requests.post('http://localhost/mystic-vouch/api.php' , json=update)

# run 
def run_flask():
    app.run()

