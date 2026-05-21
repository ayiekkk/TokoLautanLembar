<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../admin/style/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <aside>
        <h2>Panel Admin</h2>

        <ul>
            <li><a href="index.php?page=dashboard">Dashboard</a></li>
            <li><a href="index.php?page=tambah-buku">Tambah Buku</a></li>
            <li><a href="index.php?page=tambah-kategori">Tambah Kategori</a></li>
        </ul>
    </aside>
    <main>
        <?php include __DIR__ . "/../../routes/admin.php"; ?>
    </main>
</body>

</html>