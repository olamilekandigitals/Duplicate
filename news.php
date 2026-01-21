<?php
$conn = new mysqli("localhost","DB_USER","DB_PASS","DB_NAME");
$r = $conn->query("SELECT * FROM news WHERE status='published' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>News | Big Quams Media</title>
<style>
body{font-family:Segoe UI, sans-serif;margin:0;padding:15px;background:#f4f6f9;}
article{margin-bottom:30px;background:#fff;padding:15px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,.05);}
img,video{width:100%;border-radius:10px; margin-top:10px;}
h2{margin-bottom:20px;}
small{color:#555;}
</style>
</head>

<body>

<h2>Latest News</h2>

<?php
while($n=$r->fetch_assoc()){
echo "<article>
<h3>{$n['title']}</h3>";

if($n['media_type']=="video" && $n['media']!=""){
    echo "<video controls src='uploads/{$n['media']}'></video>";
}elseif($n['media_type']=="image" && $n['media']!=""){
    echo "<img src='uploads/{$n['media']}'>";
}

echo "<p>{$n['content']}</p>
<small>{$n['created_at']}</small>
</article>";
}
?>

</body>
</html>
