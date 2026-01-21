<?php
$conn = new mysqli("localhost","DB_USER","DB_PASS","DB_NAME");
$r=$conn->query("SELECT * FROM news WHERE status='published' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>News | Big Quams Media</title>
<style>
body{font-family:Segoe UI;margin:0;padding:15px}
article{margin-bottom:30px}
img,video{width:100%;border-radius:10px}
</style>
</head>

<body>

<h2>Latest News</h2>

<?php
while($n=$r->fetch_assoc()){
echo "<article>
<h3>{$n['title']}</h3>";

if($n['media_type']=="video"){
echo "<video controls src='uploads/{$n['media']}'></video>";
}else{
echo "<img src='uploads/{$n['media']}'>";
}

echo "<p>{$n['content']}</p>
<small>{$n['created_at']}</small>
</article>";
}
?>

</body>
</html>
