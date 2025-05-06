<?php
// upload.php
include_once 'config.php';
mysqli_set_charset($connect, 'utf8mb4');

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
  $title       = trim($_POST['title']);
  $description = trim($_POST['description']);
  $file        = $_FILES['file'];

  if ($file['error'] !== UPLOAD_ERR_OK) {
    $message = 'Upload error code: ' . $file['error'];
  } elseif ($file['size'] > 10 * 1024 * 1024) {
    $message = 'File is too large (max 10 MB).';
  } else {


    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed = [
      'audio/mpeg',
      'audio/wav',
      'audio/ogg',
      'video/mp4',
      'video/webm',
      'video/ogg',
      'image/jpeg',
      'image/png'
    ];
    if (!in_array($mimeType, $allowed, true)) {
      $message = "Invalid file type: $mimeType";
    } else {
      $ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
      $newName   = bin2hex(random_bytes(8)) . '.' . $ext;
      $targetDir = __DIR__ . '/videos/';
      if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
      $targetFile = $targetDir . $newName;

      if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // Prepared statement
        $stmt = mysqli_prepare(
          $connect,
          "INSERT INTO videos (title, subject, location) VALUES (?, ?, ?)"

        );
        mysqli_stmt_bind_param($stmt, 'sss', $title, $description, $newName);
        if (mysqli_stmt_execute($stmt)) {
          $message = 'Upload successful!';
        } else {
          $message = 'DB error: ' . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
      } else {
        $message = 'Failed to move uploaded file.';
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Upload Media</title>
  <link rel="stylesheet" href="upload.css">
</head>

<body>
  <main class="upload-container">
    <h1>Upload Your Media</h1>

    <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="file" class="file-label">📁 Choose File</label>
        <input type="file" name="file" id="file" required>
        <span id="file-name" class="file-name">No file chosen</span>
      </div>

      <div class="form-group">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" placeholder="Enter a title" required>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="3" placeholder="Enter a description" required></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" name="submit" class="btn-primary">Upload</button>
        <a href="readVodeos.php" class="btn-secondary">Home</a>
      </div>
    </form>
  </main>

  <script>
  // Display chosen file name
  document.getElementById('file').addEventListener('change', function(e) {
    const name = e.target.files[0]?.name || 'No file chosen';
    document.getElementById('file-name').textContent = name;
  });
  </script>
</body>

</html>