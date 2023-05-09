
# # make button for embed approved and denied add role to user if approved

# class vouchbtn(discord.ui.View):
#     def __init__(self, data):
#         super().__init__(timeout=None)
#         self.data = data
#         self.value = None

   

#     @discord.ui.button(label="Approve", style=discord.ButtonStyle.green , custom_id='persistent_view:approve7410btn')
#     async def approve(self,interaction: discord.Interaction , button: discord.ui.Button):
#         self.value = "Approved"

#         guild = client.get_guild(1074296554149654580)
#         role = guild.get_role(1098306389237055579)
#         member = guild.get_member()
#         await member.add_roles(role)
#         user = client.get_user(int(fetch_data()['vouch_user_id']))
#         await user.send(f"Your vouch from {fetch_data['discord_username']}#{self.data['discord_discriminator']} has been approved and Mystic role has been added to you")
#         # action_by will be the user who clicked the button
#         action_by = {
#             'user': interaction.user.name,
#             'id': interaction.user.id
#         }
#         await interaction.response.edit_message(embed=embed_approved(self.data,action_by), view=None)
    
        

#     @discord.ui.button(label="Deny", style=discord.ButtonStyle.red , custom_id='persistent_view:deny7410btn')
#     async def deny(self,interaction: discord.Interaction , button: discord.ui.Button):
#         self.value = "Denied"
#         action_by = {
#             'user': interaction.user.name,
#             'id': interaction.user.id
#         }
#         await interaction.response.edit_message(embed=embed_denied(self.data ,action_by), view=None)
#         user = client.get_user(int(self.data['vouch_user_id']))
#         await user.send(f"Your vouch from {self.data['discord_username']}#{self.data['discord_discriminator']} has been denied")
#         update = {
#             'discord_id': self.data['discord_id'],
#         }
    
#         api.update_vouches_left(update)
