<html>
<head>
	<title>登录页面</title>
	<script src="http://recaptcha.net/recaptcha/api.js" async defer></script>
	<link rel="stylesheet" type="text/css" href="/css/login.css">
</head>
<body>
	<div class="login-container">
		<h1>登录</h1>
		<form method="post" action="">
			<input type="text" placeholder="用户名" id="username" name="username">
			<input type="password" placeholder="密码" id="password" name="password">
            <div name="g-recaptcha" id="g-recaptcha" class="g-recaptcha" data-sitekey="密钥"></div>
			<input class="button" type="submit"></input>
			<a href="signup.php">还有没账号？去注册</a>
			<?php
			$mysql_ip = "localhost";
			$mysql_username = "chuzouX";
			$mysql_password = "password";
			$mysql_databass = "HelloWorld";
		
			$conn = mysqli_connect("$mysql_ip", "$mysql_username", "$mysql_password", "$mysql_databass");
			// 检查连接是否成功
			if (!$conn) {
				die("数据库连接失败: " . mysqli_connect_error());
			}

			// 开始验证
			$secret_key = "密钥";
			@$response = $_POST["g-recaptcha-response"];
			$remote_ip = $_SERVER["REMOTE_ADDR"];

			$url = 'https://recaptcha.tsinbei.com/recaptcha/api/siteverify';
			$data = array(
			'secret' => $secret_key,
			'response' => $response,
			'remoteip' => $remote_ip
			);

			$options = array(
			'http' => array (
				'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
				'method' => 'POST',
				'content' => http_build_query($data)
			)
			);

			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
			$response_data = json_decode($result);

			// 检查用户是否进行输入
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if ($response_data->success) {
					// 验证成功，执行其他操作
					$username = $_POST["username"];
					$password = $_POST["password"];
					$password_hash = hash('sha512', $password);
					if ($username == "" or $password == ""){
						$info = "用户名或密码不能为空";
					}elseif (4>strlen($username) or 16<strlen($username)){
						$info = "用户名长度应在4-16位之间";
					}elseif (8>strlen($password) or 16<strlen($password)){
						$info = "密码长度应在8-16位之间";
					}else{
						$info = "";
						$login = 1;	// 状态值
					}
					echo "<error>".$info."</error>";
					if ($login == 1){
						// echo "开始登录测试<br>";
						$sql = "SELECT * FROM userinfo WHERE username = '".$username."';";
						// echo $sql."<br>";
						$result = mysqli_query($conn, $sql);
						if ($result->num_rows > 0) {
							// echo "用户存在<br>";
							$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
							if ($password_hash == $row['password']){
								echo "登录成功，欢迎".$row['username']."，id为".$row['id'];
							}else{
								echo "<error>账号或密码错误</error>";
							}
						}else{
							echo "<error>用户不存在<error><br>";
						}
					}
				}else {
					// 验证失败，显示错误信息
					echo "reCAPTCHA 验证失败，请重试。";
					}
				}
				?>
		</form>
	</div>
</body>
</html>
