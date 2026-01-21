<?php
// Auto-create uploads folder
$upload_dir = __DIR__ . "/uploads";
if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0755, true);
}

// Connect to database
$conn = new mysqli("localhost","DB_USER","DB_PASS","DB_NAME");
if($conn->connect_error){
    die("Database connection failed: " . $conn->connect_error);
}

// DELETE
if(isset($_GET['delete'])){
    $conn->query("DELETE FROM news WHERE id=".$_GET['delete']);
    header("Location: admin.php"); exit;
}

// ADD / UPDATE
if(isset($_POST['save'])){
    $title = $_POST['title'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    $id = $_POST['id'];

    $mediaName = "";
    $mediaType = "";

    if(!empty($_FILES['media']['name'])){
        $mediaName = time() . "_" . $_FILES['media']['name'];
        move_uploaded_file($_FILES['media']['tmp_name'], $upload_dir . "/" . $mediaName);
        $mediaType = strpos($_FILES['media']['type'], "video") !== false ? "video" : "image";
    }

    if($id==""){
        $conn->query("INSERT INTO news (title,content,media,media_type,status)
        VALUES ('$title','$content','$mediaName','$mediaType','$status')");
    } else {
        $q = "UPDATE news SET title='$title', content='$content', status='$status'";
        if($mediaName != "") { $q .= ", media='$mediaName', media_type='$mediaType'"; }
        $q .= " WHERE id=$id";
        $conn->query($q);
    }
    header("Location: admin.php"); exit;
}

// EDIT
$edit = null;
if(isset($_GET['edit'])){
    $edit = $conn->query("SELECT * FROM news WHERE id=".$_GET['edit'])->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin | Big Quams Media</title>
<style>
body{font-family:Segoe UI, sans-serif;margin:0;padding:15px;background:#f4f6f9;}
.card{background:#fff;padding:15px;border-radius:12px;margin-bottom:20px;box-shadow:0 4px 10px rgba(0,0,0,.05);}
input,textarea,select,button{width:100%;padding:12px;margin-top:10px;border-radius:8px;border:1px solid #ddd;font-size:16px;}
button{background:#2563eb;color:#fff;border:none;}
.preview img, .preview video{width:100%;border-radius:10px;margin-top:10px;}
.news-item{margin-bottom:20px;}
.badge{font-size:12px;padding:3px 8px;border-radius:6px;}
.published{background:#d1fae5;color:#065f46;}
.draft{background:#fee2e2;color:#991b1b;}
.actions{text-align:right;}
.actions span{font-size:18px;margin-left:12px;cursor:pointer;}
</style>
</head>
<body>

<div class="card">
<h3><?= $edit ? "Edit" : "Add" ?> News</h3>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
<input name="title" placeholder="News Title" value="<?= $edit['title'] ?? '' ?>" required>
<textarea name="content" placeholder="News Content" required><?= $edit['content'] ?? '' ?></textarea>

<select name="status">
<option value="published" <?= isset($edit) && $edit['status']=="published"?"selected":"" ?>>Publish</option>
<option value="draft" <?= isset($edit) && $edit['status']=="draft"?"selected":"" ?>>Draft</option>
</select>

<input type="file" name="media" accept="image/*,video/*">
<div class="preview">
<?php
if(isset($edit['media']) && $edit['media'] != ""){
    if($edit['media_type']=="video"){
        echo "<video controls src='uploads/{$edit['media']}'></video>";
    } else {
        echo "<img src='uploads/{$edit['media']}'>";
    }
}
?>
</div>

<button name="save"><?= $edit ? "Update" : "Save" ?> News</button>
</form>
</div>

<div class="card">
<h3>All News</h3>
<?php
$r = $conn->query("SELECT * FROM news ORDER BY id DESC");
while($n = $r->fetch_assoc()){
    echo "<div class='news-item'>
    <b>{$n['title']}</b> <span class='badge {$n['status']}'>{$n['status']}</span>
    <p>{$n['content']}</p>";
    if($n['media'] != ""){
        if($n['media_type']=="video"){
            echo "<video controls src='uploads/{$n['media']}'></video>";
        } else {
            echo "<img src='uploads/{$n['media']}'>";
        }
    }
    echo "<div class='actions'>
    <a href='admin.php?edit={$n['id']}'>✏️ Edit</a> | 
    <a href='admin.php?delete={$n['id']}' onclick='return confirm(\"Delete?\")'>🗑️ Delete</a>
    </div><hr></div>";
}
?>
</div>

</body>
</html>
