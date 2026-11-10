<?php
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'db_hearttalk';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';

$stats = [
    'users' => 0,
    'counselors' => 0,
    'stories' => 0,
];
$stories = [];
$databaseError = false;

try {
    $connection = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    foreach (array_keys($stats) as $table) {
        $stats[$table] = (int) $connection->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
    }
    $storyQuery = $connection->query(
        'SELECT s.title, s.content, s.status, u.nickname AS user_nickname,
                c.name AS counselor_name
         FROM stories AS s
         INNER JOIN users AS u ON u.id = s.user_id
         INNER JOIN counselors AS c ON c.id = s.counselor_id
         ORDER BY s.id DESC
         LIMIT 6'
    );
    $stories = $storyQuery->fetchAll();
} catch (PDOException $exception) {
    error_log('HeartTalk database query failed: ' . $exception->getMessage());
    $databaseError = true;
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#fffaf7">
  <title>HeartTalk — Ruang Aman untuk Cerita</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header class="site-header">
    <a class="brand" href="index.php"><span class="brand-mark" aria-hidden="true">♥</span>HeartTalk</a>
    <nav class="site-nav" aria-label="Navigasi utama">
      <a href="index.php" aria-current="page">Beranda</a>
      <a href="users.php">Komunitas</a>
    </nav>
  </header>

  <main class="home">
    <section class="hero">
      <div class="hero-copy">
        <span class="eyebrow">RUANG AMAN UNTUK CERITA</span>
        <h2>Cerita kamu layak <span>didengarkan.</span></h2>
        <p>
          HeartTalk membantu kamu menata perasaan dan menemukan teman bicara
          yang tepat. Mulai dari satu cerita kecil hari ini.
        </p>
        <a class="button button-primary" href="users.php">Kelola komunitas <span aria-hidden="true">→</span></a>
      </div>
      <div class="hero-art" aria-hidden="true">
        <div class="orbit orbit-one"></div>
        <div class="orbit orbit-two"></div>
        <div class="heart">♥</div>
        <div class="art-note note-one">Kamu tidak sendiri</div>
        <div class="art-note note-two">Tarik napas dulu 🌿</div>
      </div>
    </section>

    <section class="section" aria-labelledby="overview-title">
      <div class="section-heading">
        <div>
          <span class="eyebrow">HEARTTALK HARI INI</span>
          <h2 id="overview-title">Ruang yang terus bertumbuh</h2>
        </div>
        <p>Setiap angka mewakili seseorang yang berani memulai.</p>
      </div>

      <?php if ($databaseError): ?>
      <p class="notice notice-error" role="status">
        Statistik belum dapat dimuat. Pastikan MySQL aktif dan database
        <strong>db_hearttalk</strong> sudah tersedia.
      </p>
      <?php else: ?>
      <div class="stats">
        <article class="stat-card">
          <span class="stat-icon icon-lilac" aria-hidden="true">☺</span>
          <p>Anggota komunitas</p>
          <strong><?= number_format($stats['users']) ?></strong>
        </article>
        <article class="stat-card">
          <span class="stat-icon icon-peach" aria-hidden="true">✦</span>
          <p>Konselor siap mendengar</p>
          <strong><?= number_format($stats['counselors']) ?></strong>
        </article>
        <article class="stat-card">
          <span class="stat-icon icon-mint" aria-hidden="true">♡</span>
          <p>Cerita yang dibagikan</p>
          <strong><?= number_format($stats['stories']) ?></strong>
        </article>
      </div>
      <?php endif; ?>
    </section>

    <section class="section stories-section" aria-labelledby="stories-title">
      <div class="section-heading">
        <div>
          <span class="eyebrow">CERITA KOMUNITAS</span>
          <h2 id="stories-title">Cerita yang dibagikan</h2>
        </div>
        <p>Ruang untuk saling mendengar dan bertumbuh.</p>
      </div>

      <?php if ($databaseError): ?>
      <p class="notice notice-error" role="status">Daftar cerita belum dapat dimuat. Periksa koneksi database.</p>
      <?php elseif ($stories === []): ?>
      <div class="stories-empty">
        <span aria-hidden="true">♡</span>
        <strong>Belum ada cerita</strong>
        <p>Cerita yang dibagikan akan tampil di sini.</p>
      </div>
      <?php else: ?>
      <div class="stories-grid">
        <?php
        $statusLabels = [
            'Pending' => 'Menunggu',
            'Responded' => 'Sudah ditanggapi',
            'Closed' => 'Selesai',
        ];
        ?>
        <?php foreach ($stories as $story): ?>
        <?php
        $statusClass = strtolower($story['status']);
        ?>
        <article class="story-card">
          <div class="story-card-top">
            <span class="story-category">CERITA CURHAT</span>
            <span class="story-status status-<?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($statusLabels[$story['status']] ?? $story['status'], ENT_QUOTES, 'UTF-8') ?>
            </span>
          </div>
          <h3><?= htmlspecialchars($story['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h3>
          <p class="story-content"><?= htmlspecialchars($story['content'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
          <div class="story-meta">
            <span>Oleh <strong><?= htmlspecialchars($story['user_nickname'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></strong></span>
            <span>Konselor <strong><?= htmlspecialchars($story['counselor_name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></strong></span>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </section>

    <section class="welcome-card">
      <div>
        <span class="eyebrow">MULAI DARI SINI</span>
        <h2>Pelan-pelan juga tetap maju.</h2>
        <p>Kelola anggota komunitas HeartTalk dalam satu tempat.</p>
      </div>
      <a class="button button-light" href="users.php">Buka data user <span aria-hidden="true">→</span></a>
    </section>
  </main>

  <footer class="site-footer">
    <p>&copy; <?= date('Y') ?> HeartTalk · Ruang aman untuk bertumbuh bersama.</p>
  </footer>
</body>

</html>
