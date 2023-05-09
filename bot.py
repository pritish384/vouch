import discord
from discord.ext import commands
import api


def embed(data):
    embed1 = discord.Embed(title="New Vouch Form Submission!", color=0xebe70c)
    embed1.add_field(name="User who is giving vouch:-", value=f"Username:- {data['discord_username']}#{data['discord_discriminator']} \nDiscord ID:- {data['discord_id']}",inline=False)
    embed1.add_field(name="" , value=f"Vouches Left:- {data['vouches_left']+1}",inline=False)
    embed1.add_field(name="User who is being vouch:-", value=f"Discord Username:- {data['vouch_username']}#{data['vouch_discriminator']} \nDiscord ID:- {data['vouch_user_id']} ",inline=False)
    embed1.add_field(name="Vouch ID", value=f"{data['vouch_id']}",inline=False)

    return embed1

def embed_approved(data, action_by):
    embed_approved = discord.Embed(title="Vouch Approved!", color=0x1eeb0c)
    embed_approved.add_field(name="User who is giving vouch:-", value=f"Username:- {data['discord_username']}#{data['discord_discriminator']}\nDiscord ID:- {data['discord_id']} ",inline=False)
    embed_approved.add_field(name="User who is being vouch:-", value=f"Discord Username:- {data['vouch_username']}#{data['vouch_discriminator']}\n Discord ID:- {data['vouch_user_id']}",inline=False)
    embed_approved.add_field(name="Approved by", value=f"Discord Username:- {action_by['user']} \nDiscord ID:- {action_by['id']}",inline=False)
    embed_approved.add_field(name="Vouch ID", value=f"{data['vouch_id']}",inline=False)

    return embed_approved

def embed_denied(data, action_by):
    embed_denied = discord.Embed(title="Vouch Denied!", color=0xff0000)
    embed_denied.add_field(name="User who is giving vouch:-", value=f"Username:- {data['discord_username']}#{data['discord_discriminator']} \nDiscord ID:- {data['discord_id']}",inline=False)
    embed_denied.add_field(name="User who is being vouch:-", value=f"Discord Username:- {data['vouch_username']}#{data['vouch_discriminator']} \nDiscord ID:- {data['vouch_user_id']}",inline=False)
    embed_denied.add_field(name="Denied by", value=f"Discord Username:- {action_by['user']} \nDiscord ID:- {action_by['id']}",inline=False)
    embed_denied.add_field(name="Vouch ID", value=f"{data['vouch_id']}",inline=False)
    
    return embed_denied

# make button for embed approved and denied add role to user if approved
class vouchbtn(discord.ui.View):
    def __init__(self):
        super().__init__(timeout=None)
        self.value = None

    @discord.ui.button(label="Approve", style=discord.ButtonStyle.green , custom_id='persistent_view:approve7410btn')
    async def approve(self,interaction: discord.Interaction , button: discord.ui.Button):
        
        guild = client.get_guild(1074296554149654580)
        role = guild.get_role(1098306389237055579)

        vouch_id = api.collection.find_one({"vouch_id": interaction.message.embeds[0].fields[3].value})

        async def try_member(id: int, /, *, guild: discord.Guild) -> discord.Member:
          return guild.get_member(id) or await guild.fetch_member(id)
         
        member = await try_member(int(vouch_id['vouch_user_id']), guild=guild)

        await member.add_roles(role)

        await member.send(f"Your vouch from {vouch_id['discord_username']}#{vouch_id['discord_discriminator']} has been approved and Mystic role has been added to you.")
        action_by = {
            "user": interaction.user.name,
            "id": interaction.user.id
        }
        await interaction.message.edit(embed=embed_approved(vouch_id, action_by) , view=None)

    @discord.ui.button(label="Deny", style=discord.ButtonStyle.red , custom_id='persistent_view:deny7410btn')
    async def deny(self,interaction: discord.Interaction , button: discord.ui.Button):
        vouch_id = api.collection.find_one({"vouch_id": interaction.message.embeds[0].fields[3].value})
        action_by = {
            "user": interaction.user.name,
            "id": interaction.user.id
        }
        await interaction.message.edit(embed=embed_denied(vouch_id, action_by) , view=None)
        
        guild = client.get_guild(1074296554149654580)
        

        async def try_member(id: int, /, *, guild: discord.Guild) -> discord.Member:
          return guild.get_member(id) or await guild.fetch_member(id)
         
        member = await try_member(int(vouch_id['vouch_user_id']), guild=guild)
        await member.send(f"Your vouch from {vouch_id['discord_username']}#{vouch_id['discord_discriminator']} has been denied.")
        update= {
            "discord_id": vouch_id['discord_id'],
        }
        # run function
        api.update_vouches_left(update)

       
            
        
        





class PersistentViewBot(commands.Bot):
    def __init__(self):
        intents = discord.Intents.default()
        intents.message_content = True

        super().__init__(command_prefix=commands.when_mentioned_or('>'), intents=intents)

    async def setup_hook(self) -> None:
        self.add_view(vouchbtn())
   

    async def on_ready(self):
        print(f'Logged in as {self.user} (ID: {self.user.id})')
        print('------')


client = PersistentViewBot()
guild = client.get_guild(1074296554149654580)




def run_discord():
    client.run('MTA4NzM5NjQwOTU5MjAwMDYxMg.GqwHMC.IOPKuPRYwzMp2W3tVRb1PYbiz-nczBCwxgACHA')

