<?php
include_once 'config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// 1) استعلام محضّر لجلب كل الفيديوهات بترتيب تنازلي
$stmt = $connect->prepare(
  "SELECT
  id,
  title,
  subject AS description,
  name    AS location
FROM videos
ORDER BY id DESC
     "
);
$stmt->execute();
$result = $stmt->get_result();
$videos = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Video App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="app-video">
    <?php foreach ($videos as $row):
      // 2) حماية المخرجات لمنع XSS
      $loc   = htmlspecialchars($row['location'],    ENT_QUOTES, 'UTF-8');
      $title = htmlspecialchars($row['title'],       ENT_QUOTES, 'UTF-8');
      $desc  = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
    ?>
    <div class="video-container">
      <video controls muted autoplay loop class="video__player">
        <source src="<?= $loc ?>" type="video/mp4">
        Your browser does not support the video tag.
      </video>
      <footer class="video-footer">
        <div class="footer-content">
          <h3><?= $title ?></h3>
          <p class="description"><?= $desc ?></p>
          <div class="img-called">
            <img src="images/Music_29918.png" alt="Music icon">
            <div class="ticker">
              <span><?= $title ?></span>
            </div>
          </div>
        </div>
        <button class="play-button" aria-label="Play/Pause video">
          <img src="images/1486486316-arrow-film-movie-play-player-start-video_81236.png" alt="Play icon">
        </button>
      </footer>
    </div>
    <?php endforeach; ?>
  </div>

  <script>
  // 3) جلب كل عناصر الفيديو والتفاعل بالنقر
  const videos = document.querySelectorAll('video.video__player');
  videos.forEach(video => {
    video.addEventListener('click', () => {
      video.paused ? video.play() : video.pause();
    });
  });
  </script>
</body>

</html>