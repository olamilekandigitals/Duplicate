<?php
$conn = new mysqli("localhost","DB_USER","DB_PASS","DB_NAME");
if($conn->connect_error){ die("DB Error"); }

/* DELETE */
if(isset($_GET['delete'])){
  $conn->query("DELETE FROM news WHERE id=".$_GET['delete']);
  header("Location: admin.php"); exit;
}

/* ADD / UPDATE */
if(isset($_POST['save'])){
  $title=$_POST['title'];
  $content=$_POST['content'];
  $status=$_POST['status'];
  $id=$_POST['id'];

  $mediaName="";
  $mediaType="";

  if(!empty($_FILES['media']['name'])){
    $mediaName=time().$_FILES['media']['name'];
    move_uploaded_file($_FILES['media']['tmp_name'],"uploads/".$mediaName);
    $mediaType=strpos($_FILES['media']['type'],"video")!==false?"video":"image";
  }

  if($id==""){
    $conn->query("INSERT INTO news (title,content,media,media_type,status)
    VALUES ('$title','$content','$mediaName','$mediaType','$status')");
  }else{
    $q="UPDATE news SET title='$title',content='$content',status='$status'";
    if($mediaName!=""){ $q.=",media='$mediaName',media_type='$mediaType'"; }
    $q.=" WHERE id=$id";
    $conn->query($q);
  }
  header("Location: admin.php"); exit;
}

/* EDIT */
$edit=null;
if(isset($_GET['edit'])){
  $edit=$conn->query("SELECT * FROM news WHERE id=".$_GET['edit'])->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin | Big Quams Media</title>
<style>
body{font-family:Segoe UI;background:#f4f6f9;margin:0;padding:15px}
.card{background:#fff;padding:15px;border-radius:12px;margin-bottom:20px}
input,textarea,select,button{
width:100%;padding:12px;margin-top:10px;border-radius:8px;border:1px solid #ddd}
button{background:#2563eb;color:#fff;border:none}
.badge{font-size:12px;padding:3px 8px;border-radius:6px}
.published{background:#d1fae5;color:#065f46}
.draft{background:#fee2e2;color:#991b1b}
</style>
</head>

<body>

<div class="card">
<h3><?= $edit?"Edit":"Add" ?> News</h3>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
<input name="title" placeholder="Title" value="<?= $edit['title'] ?? '' ?>" required>
<textarea name="content" placeholder="Content" required><?= $edit['content'] ?? '' ?></textarea>

<select name="status">
<option value="published">Publish</option>
<option value="draft" <?= isset($edit)&&$edit['status']=="draft"?"selected":"" ?>>Draft</option>
</select>

<input type="file" name="media" accept="image/*,video/*">
<button name="save">Save</button>
</form>
</div>

<div class="card">
<h3>All News</h3>
<?php
$r=$conn->query("SELECT * FROM news ORDER BY id DESC");
while($n=$r->fetch_assoc()){
echo "
<p>
<b>{$n['title']}</b>
<span class='badge {$n['status']}'>{$n['status']}</span><br>
<a href='admin.php?edit={$n['id']}'>Edit</a> |
<a href='admin.php?delete={$n['id']}' onclick='return confirm(\"Delete?\")'>Delete</a>
</p><hr>";
}
?>
</div>

</body>
</html>
