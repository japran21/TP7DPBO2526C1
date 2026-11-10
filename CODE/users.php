<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/user.php';

$database = new Database();
$db = $database->getConnection();
$userObj = new User($db);

$editUser = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $status = '';
        if ($_POST['action'] === 'create') {
            $userObj->create($_POST['nickname'], $_POST['email'], $_POST['age']);
            $status = 'created';
        } elseif ($_POST['action'] === 'update') {
            $userObj->update($_POST['id'], $_POST['nickname'], $_POST['email'], $_POST['age']);
            $status = 'updated';
        } elseif ($_POST['action'] === 'delete') {
            $userObj->delete($_POST['id']);
            $status = 'deleted';
        }
        header('Location: users.php' . ($status !== '' ? '?status=' . $status : ''));
        exit();
    }
}

if (isset($_GET['edit'])) {
    $editUser = $userObj->getById($_GET['edit']);
}

$users = $userObj->readAll();
?>

<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#fffaf7">
  <title>Komunitas HeartTalk</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header class="site-header">
    <a class="brand" href="index.php"><span class="brand-mark" aria-hidden="true">♥</span>HeartTalk</a>
    <nav class="site-nav" aria-label="Navigasi utama">
      <a href="index.php">Beranda</a>
      <a href="users.php" aria-current="page">Komunitas</a>
    </nav>
  </header>

<main class="users-page">
  <section class="users-heading">
    <div>
      <span class="users-eyebrow">RUANG KOMUNITAS</span>
      <h1>Data anggota</h1>
      <p>Kelola anggota HeartTalk dengan mudah dan rapi.</p>
    </div>
    <div class="users-count"><strong><?= count($users) ?></strong> anggota terdaftar</div>
  </section>

  <?php
  $statusMessages = [
      'created' => 'Anggota baru berhasil ditambahkan.',
      'updated' => 'Perubahan data anggota berhasil disimpan.',
      'deleted' => 'Anggota berhasil dihapus.',
  ];
  $status = $_GET['status'] ?? '';
  if (isset($statusMessages[$status])):
  ?>
  <p class="users-notice" role="status"><?= htmlspecialchars($statusMessages[$status], ENT_QUOTES, 'UTF-8') ?></p>
  <?php endif; ?>

  <div class="users-layout">
    <section class="users-card" aria-labelledby="user-form-title">
      <div class="users-card-heading">
        <span class="users-card-icon" aria-hidden="true"><?= $editUser ? '✎' : '+' ?></span>
        <div>
          <h2 id="user-form-title"><?= $editUser ? 'Edit anggota' : 'Tambah anggota' ?></h2>
          <p><?= $editUser ? 'Perbarui informasi anggota.' : 'Isi detail anggota baru.' ?></p>
        </div>
      </div>

      <form class="users-form" action="users.php" method="POST">
        <input type="hidden" name="action" value="<?= $editUser ? 'update' : 'create' ?>">
        <?php if ($editUser): ?>
        <input type="hidden" name="id" value="<?= (int) $editUser['id'] ?>">
        <?php endif; ?>

        <div class="users-field">
          <label for="nickname">Nama panggilan</label>
          <input id="nickname" type="text" name="nickname" placeholder="Contoh: Senja"
            value="<?= htmlspecialchars($editUser['nickname'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="users-field">
          <label for="email">Alamat email</label>
          <input id="email" type="email" name="email" placeholder="nama@email.com"
            value="<?= htmlspecialchars($editUser['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="users-field">
          <label for="age">Umur</label>
          <input id="age" type="number" name="age" min="1" max="120" placeholder="Contoh: 21"
            value="<?= htmlspecialchars((string) ($editUser['age'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="users-actions">
          <button class="users-button users-button-primary" type="submit">
            <?= $editUser ? 'Simpan perubahan' : 'Tambah anggota' ?>
          </button>
          <?php if ($editUser): ?>
          <a class="users-button users-button-cancel" href="users.php">Batal</a>
          <?php endif; ?>
        </div>
      </form>
    </section>

    <section class="users-card" aria-labelledby="users-list-title">
      <div class="users-card-heading">
        <span class="users-card-icon" aria-hidden="true">☷</span>
        <div>
          <h2 id="users-list-title">Daftar anggota</h2>
          <p>Informasi komunitas yang terdaftar.</p>
        </div>
      </div>

      <?php if ($users === []): ?>
      <div class="users-empty">
        <strong>Belum ada anggota</strong>
        Tambahkan anggota pertama menggunakan formulir ini.
      </div>
      <?php else: ?>
      <div class="users-table-wrap">
        <table class="users-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Anggota</th>
              <th>Umur</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr class="<?= $editUser && (int) $editUser['id'] === (int) $u['id'] ? 'user-row-editing' : '' ?>">
              <td class="user-id">#<?= (int) $u['id'] ?></td>
              <td>
                <div class="user-name"><?= htmlspecialchars($u['nickname'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="user-email"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></div>
              </td>
              <td class="user-age"><?= (int) $u['age'] ?> tahun</td>
              <td>
                <div class="user-row-actions">
                  <a class="user-action user-action-edit" href="users.php?edit=<?= (int) $u['id'] ?>">Edit</a>
                  <form class="user-delete-form" action="users.php" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                    <button class="user-action user-action-delete" type="submit"
                      onclick="return confirm('Hapus anggota ini?')">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </section>
  </div>
</main>

<footer class="site-footer">
  <p>&copy; <?= date('Y') ?> HeartTalk · Ruang aman untuk bertumbuh bersama.</p>
</footer>
</body>

</html>