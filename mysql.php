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
echo "数据库连接成功<br>";

$sql = "SELECT `id` , `username` , `password` , `email` FROM `userinfo` WHERE 1;";
$result = mysqli_query($conn, $sql);
echo '<table border="1" cellspacing="0" cellpadding="5"><tr><td>ID</td><td>username</td><td>email</td></tr>';
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
    echo "<tr><td> {$row['id']}</td> ".
         "<td>{$row['username']} </td> ".
         "<td>{$row['email']} </td> ".
         "</tr>";
}
// mysqli_close($conn);
?>