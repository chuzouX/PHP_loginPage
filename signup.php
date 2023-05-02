<html>
<head>
	<title>注册页面</title>
    <script src="http://recaptcha.net/recaptcha/api.js" async defer></script>
	<link rel="stylesheet" type="text/css" href="/css/signup.css">
</head>
<body>
	<div class="login-container">
		<h1>注册</h1>
		<form method="post" action="">
			<input type="text" placeholder="用户名" id="username" name="username">
			<input type="password" placeholder="密码" id="password" name="password">
            <input type="email" placeholder="电子邮箱" id="email" name="email">
            <div name="g-recaptcha" id="g-recaptcha" class="g-recaptcha" data-sitekey="密钥"></div>
			<input class="button" type="submit"></input>
            <a href="login.php">已有账号？去登录！</a>
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

                    // 判断验证
                    if ($response_data->success) {
                        // 验证成功，执行其他操作
                        $username = $_POST["username"];
                        $password = $_POST["password"];
                        $password_hash = hash('sha512', $password);
                        $email = $_POST["email"];
                        // 规范用户输入内容
                        if ($username == "" or $password == "" ){
                            $info = "用户名或密码不能为空";
                        }elseif (4>strlen($username) or 16<strlen($username)){
                            $info = "用户名长度应在4-16位之间";
                        }elseif (8>strlen($password) or 16<strlen($password)){
                            $info = "密码长度应在8-16位之间";
                        }else{
                            $info = "";
                            if ($email == ""){
                                $info = "电子邮箱不能为空";
                                $signup_email = 0;
                            }
                            $signup_email = 1;
                            $signup_user = 1;	// 状态值
                        }
                        echo "<error>".$info."</error>";
                        if ($signup_user == 1 and $signup_email == 1){
                            $sql_user = "SELECT * FROM userinfo WHERE username = '".$username."';";
                            $result_user = mysqli_query($conn, $sql_user);
                            if ($result_user->num_rows > 0) {
                                echo "<error>此用户名已被占用！<error><br>";
                            }else{
                                // echo "此用户名可用<br>";
                                $signup_user = 2;
                            }
                            $sql_email = "SELECT * FROM userinfo WHERE email = '".$email."';";
                            $result_email = mysqli_query($conn, $sql_email);
                            if ($result_email->num_rows > 0) {
                                echo "<error>此邮箱已被占用！<error><br>";
                            }else{
                                // echo "此邮箱可用<br>";
                                $signup_email = 2;
                            }
                            if ($signup_user == 2 and $signup_email == 2){
                                $action_signup = "INSERT INTO `userinfo` (`username`, `password`, `email`) VALUES ('{$username}', '{$password_hash}', '{$email}');";
                                $result_action_signup = mysqli_query($conn, $action_signup);
                                if ($result_action_signup){
                                    echo "注册成功";
                                }
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
