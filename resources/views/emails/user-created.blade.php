<html>
<head><title>{{ config('app.name') }}</title><meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" /></head>
<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="font: 14px "Lucida Grande", Helvetica, Arial, sans-serif;">
	<div style="width: 100%; background: transparent; display: table; background: #2D4259;">
		<div style="width: 75%; background: #FFF; margin: 20px 10% 20px 10%; padding: 10px;">
			<h2>{{ $subject }}</h2>
			<br/>
			
			Please verify your email address by using the following 6-digit code below.
			<center>
				<br/>
				<h3><strong>{{ $user->email_verification_code }}</strong></h3>
				<br/>
			</center>
			<br/><br/>
			<br/>
		</div>
	</div>
</body>
</html>