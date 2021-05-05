<p align="center">Basic Authentication and Profile Update</a></p>

## How to test

Use the requests below to test basic authentication and profile update features:

- Login: https://reqbin.com/tkyqb3ua (This request will call the /login API which will validate your credentials and return your API token.)
- Send Invitation: https://reqbin.com/0pggtao9 (This request will call the /users/{user}/send-invite API which will send an email with 'Register Now' link to your specified email address. It will also return the invititation code sent.)
- Registration: https://reqbin.com/vehm5oe0 (This request will call the /register API which register you if you provide the correct invitation code. This will also return your 6-digit email verification code.)
- Verify Email: https://reqbin.com/1maz8brw (This request will call the /verify API which will verify your account if your provide the correct 6-digit verification code)
- Update Profile: https://reqbin.com/1y8eyahv (This request will call the /users/{user} API which will update your profile)