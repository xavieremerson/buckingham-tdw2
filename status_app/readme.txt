Note: Delete updatepassword.php when you are done with either installation or upgrade to hashed passwords

How to install Chipmunk Chatbox:
First modify connect.php and admin/connect.php and put your username, password, and databasename in the corressponding fields.

Then upload everything and run install.php
Delete install.php(*Important)
Run admin/register.php and register yourself an admin name
DELETE admin/register.php and admin/reguser.php but do not delete the register.php and reguser.php outside the admin folder

Note: The admin name and chat name is not the same so if you want to chat as registered, you still have to go to the register.php outside the admin folder to create a chat account for yourself.

Now the chat should be up and functional
To modify other things such as colors of registered and unregistered users and if guests can post or not edit admin/var.php which should be self explanatory

You may use this chat and modify it anyway you want as long as you do not take the copyright of chipmunk-scripts off the bottom of the page.

At the bottom post.php, there is a bad words filter, there are a few examples there, just follow the examples to add your own words to the filter.

From none-hashed to hashed passwords
---------------------------------------
1. upload getpass.php, setpass.php
2. Upload reguser.php, authenticate.php, admin/authenticate.php
3. Upload updatepassword.php and run it in a browser then delete this file
4. Thats it