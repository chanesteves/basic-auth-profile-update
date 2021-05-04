<html>
<head><title>[<i>App Title Here</i>]</title><meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" /></head>
<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="font: 14px "Lucida Grande", Helvetica, Arial, sans-serif;">
	<div style="width: 100%; background: transparent; display: table; background: #2D4259;">
		<div style="text-align: center; margin-top: 20px;">
            [<i>App Logo Here</i>]
		</div>
		<div style="width: 75%; background: #FFF; margin: 20px 10% 20px 10%; padding: 10px;">
			<h2>{{ $subject }}</h2>
			<br/>
			@php $inviter = \App\User::find($invitation->inviter_id); @endphp
			
			<strong>{{ $inviter->name }}</strong> invited you to join [<i>App Title Here</i>]
			<center>
				<br/>
				<a href="{{ $base_url }}/auth/register?code={{ $inviter->invitation_code }}" style="cursor: pointer; text-decoration: none;">
					<div style="width: 75%; border-radius: 2px; background: #398BCE; padding: 20px; color: #FFF; cursor: pointer;">
						REGISTER NOW
					</div>
				</a>
				<br/>
			</center>
			<br/><br/>
			<br/>
			Team [<i>App Title Here</i>]
		</div>
		<div style="width: 75%: margin: 20px 10% 20px 10%; font: 11px Tahoma, Arial;">
			<center>
				<span style="color: #FFF;">&copy; [<i>App Title Here</i>]</span>
				<br/><br/>
			</center>
		</div>
	</div>
</body>
</html>