<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


include __DIR__ . '/../src/php/config/database.php';

// Handle CRUD Catalog
if (isset($_POST['add_catalog'])) {
    $package_name = $_POST['package_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $status_publish = $_POST['status_publish'];
    $user_id = $_SESSION['user_id'];
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = 'img/catalog/';
        $image = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/' . $image);
    }

    $stmt = $pdo->prepare("INSERT INTO tb_catalogues_opik (image, package_name, description, price, status_publish, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$image, $package_name, $description, $price, $status_publish, $user_id]);
}

if (isset($_POST['edit_catalog'])) {
    $catalogue_id = $_POST['catalogue_id'];
    $package_name = $_POST['package_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $status_publish = $_POST['status_publish'];
    $image = $_POST['existing_image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = 'img/catalog/';
        $image = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/' . $image);
    }

    $stmt = $pdo->prepare("UPDATE tb_catalogues_opik SET image = ?, package_name = ?, description = ?, price = ?, status_publish = ? WHERE catalogue_id = ?");
    $stmt->execute([$image, $package_name, $description, $price, $status_publish, $catalogue_id]);
}

if (isset($_GET['delete_catalog'])) {
    $catalogue_id = $_GET['delete_catalog'];
    $stmt = $pdo->prepare("DELETE FROM tb_catalogues_opik WHERE catalogue_id = ?");
    $stmt->execute([$catalogue_id]);
}

// Handle Update Order Status
if (isset($_POST['update_order_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    if ($status === 'confirmed') {
        // Update status menjadi 'approved'
        $stmt = $pdo->prepare("UPDATE tb_orders_opik SET status = 'approved' WHERE order_id = ?");
        $stmt->execute([$order_id]);

        // Ambil data order beserta katalog untuk email
        $stmt = $pdo->prepare("
            SELECT o.*, c.package_name, c.price, c.catalogue_id
            FROM tb_orders_opik o
            JOIN tb_catalogues_opik c ON o.catalogue_id = c.catalogue_id
            WHERE o.order_id = ?
        ");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Kirim email notifikasi
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'taufikhan17@gmail.com';
                $mail->Password   = 'uztcpktukfmbpmqf';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('taufikhan17@gmail.com', 'Wedding Order');
                $mail->addAddress($order['email'], $order['name']);

                $mail->isHTML(true);
                $mail->Subject = 'Pesanan Anda Telah Disetujui';
                $mail->Body    = "
                    <h3>Terima kasih telah memesan paket kami!</h3>
                    <p>Pesanan Anda untuk paket <strong>{$order['package_name']}</strong> dengan harga Rp " . number_format($order['price'],0,',','.') . " telah disetujui.</p>
                    <p>Kami sangat menghargai kepercayaan Anda. Mohon rekomendasikan layanan kami kepada teman-teman Anda. Terima kasih telah memakai jasa kami.</p>
                ";

                $mail->send();
            } catch (Exception $e) {
                error_log("Mailer Error: {$mail->ErrorInfo}");
            }
        }

    } elseif ($status === 'canceled') {
        // Hapus data order
        $stmt = $pdo->prepare("DELETE FROM tb_orders_opik WHERE order_id = ?");
        $stmt->execute([$order_id]);
    }
    // Jika status pending/requested, tidak perlu diubah
}



// Handle Edit Settings
if (isset($_POST['edit_settings'])) {
    $instagram_url = $_POST['instagram_url'];
    $youtube_url = $_POST['youtube_url'];
    $phone_number1 = $_POST['phone_number1'];
    $email1 = $_POST['email1'];
    $header_bussines_hour = $_POST['header_bussines_hour'];
    $time_bussines_hour = $_POST['time_bussines_hour'];
    $address = $_POST['address'];

    // Handle logo upload
    $logo = $settings['logo'] ?? ''; // default: logo lama
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = 'img/';
        $logo = $target_dir . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__ . '/' . $logo);
    }

    $stmt = $pdo->prepare("UPDATE tb_settings_opik SET 
        instagram_url = ?, 
        youtube_url = ?, 
        phone_number1 = ?, 
        email1 = ?, 
        header_bussines_hour = ?, 
        time_bussines_hour = ?, 
        address = ?, 
        logo = ? 
        WHERE id = 1");
    $stmt->execute([
        $instagram_url, 
        $youtube_url, 
        $phone_number1, 
        $email1,
        $header_bussines_hour, 
        $time_bussines_hour, 
        $address, 
        $logo
    ]);

    // Refresh settings setelah update
    $settings = $pdo->query("SELECT * FROM tb_settings_opik WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
}


// Fetch Data
$catalogs = $pdo->query("SELECT * FROM tb_catalogues_opik")->fetchAll(PDO::FETCH_ASSOC);
$orders = $pdo->query("
    SELECT o.*, c.package_name 
    FROM tb_orders_opik o
    JOIN tb_catalogues_opik c ON o.catalogue_id = c.catalogue_id
")->fetchAll(PDO::FETCH_ASSOC);
$settings = $pdo->query("SELECT * FROM tb_settings_opik WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

// Laporan: Top 10 paket approved
$report_top_packages = $pdo->query("
    SELECT c.package_name, COUNT(o.order_id) AS total_orders, SUM(c.price) AS total_revenue
    FROM tb_orders_opik o
    JOIN tb_catalogues_opik c ON o.catalogue_id = c.catalogue_id
    WHERE o.status = 'approved'
    GROUP BY c.catalogue_id, c.package_name
    ORDER BY total_orders DESC
    LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);

// Total pendapatan keseluruhan
$total_revenue = $pdo->query("
    SELECT SUM(c.price) AS total_revenue
    FROM tb_orders_opik o
    JOIN tb_catalogues_opik c ON o.catalogue_id = c.catalogue_id
    WHERE o.status = 'approved'
")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-800 text-white h-screen fixed">
            <div class="p-6 flex flex-col">
    <h2 class="text-2xl font-bold mb-6">Admin Dashboard</h2>
    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded mb-4 text-center">Logout</a>
    <nav>
        <ul>
            <li>
                <button onclick="openTab(event, 'catalog')" class="tablink w-full text-left py-2 px-4 hover:bg-blue-700 rounded">Catalog</button>
            </li>
            <li>
                <button onclick="openTab(event, 'orders')" class="tablink w-full text-left py-2 px-4 hover:bg-blue-700 rounded">Orders</button>
            </li>
            <li>
                <button onclick="openTab(event, 'settings')" class="tablink w-full text-left py-2 px-4 hover:bg-blue-700 rounded">Settings</button>
            </li>
            <li>
    <button onclick="openTab(event, 'report')" class="tablink w-full text-left py-2 px-4 hover:bg-blue-700 rounded">Laporan</button>
</li>

        </ul>
    </nav>
</div>

        </aside>

        <!-- Main Content -->
        <main class="ml-64 p-6 w-full">
            <h1 class="text-3xl font-bold mb-6">Dashboard Admin</h1>

            <!-- Section Catalog -->
            <div id="catalog" class="tabcontent">
                <h2 class="text-2xl font-bold mb-4">Manage Catalog</h2>

                <!-- Form Add Catalog -->
                <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md mb-6 max-w-lg">
                    <input type="hidden" name="add_catalog" value="1">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Package Name</label>
                        <input type="text" name="package_name" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Price</label>
                        <input type="number" name="price" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status Publish</label>
                        <select name="status_publish" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="Y">Published</option>
                            <option value="N">Not Published</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Image</label>
                        <input type="file" name="image" class="mt-1 block w-full border border-gray-300 rounded-md p-2" accept="image/*">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Add Catalog</button>
                </form>

                <!-- List Catalog -->
                <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="p-3 text-left">ID</th>
                                <th class="p-3 text-left">Image</th>
                                <th class="p-3 text-left">Package Name</th>
                                <th class="p-3 text-left">Price</th>
                                <th class="p-3 text-left">Status</th>
                                <th class="p-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($catalogs as $cat): ?>
                            <tr class="border-b">
                                <td class="p-3"><?php echo $cat['catalogue_id']; ?></td>
                                <td class="p-3"><img src="<?php echo $cat['image']; ?>" width="50" alt=""></td>
                                <td class="p-3"><?php echo htmlspecialchars($cat['package_name']); ?></td>
                                <td class="p-3"><?php echo number_format($cat['price'], 0, ',', '.'); ?></td>
                                <td class="p-3"><?php echo $cat['status_publish'] === 'Y' ? 'Published' : 'Not Published'; ?></td>
                                <td class="p-3">
                                    <button onclick="openEditModal(<?php echo $cat['catalogue_id']; ?>, '<?php echo addslashes($cat['package_name']); ?>', '<?php echo addslashes($cat['description']); ?>', <?php echo $cat['price']; ?>, '<?php echo $cat['status_publish']; ?>', '<?php echo $cat['image']; ?>')" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Edit</button>
                                    <a href="?delete_catalog=<?php echo $cat['catalogue_id']; ?>" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700" onclick="return confirm('Yakin delete?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section Orders -->
            <div id="orders" class="tabcontent hidden">
                <h2 class="text-2xl font-bold mb-4">Manage Orders</h2>

                <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="p-3 text-left">ID</th>
                                <th class="p-3 text-left">Package</th>
                                <th class="p-3 text-left">Name</th>
                                <th class="p-3 text-left">Email</th>
                                <th class="p-3 text-left">Phone</th>
                                <th class="p-3 text-left">Wedding Date</th>
                                <th class="p-3 text-left">Status</th>
                                <th class="p-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr class="border-b">
                                <td class="p-3"><?php echo $order['order_id']; ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($order['package_name']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($order['name']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($order['email']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($order['phone_number']); ?></td>
                                <td class="p-3"><?php echo $order['wedding_date']; ?></td>
                                <td class="p-3"><?php echo $order['status']; ?></td>
                                <td class="p-3">
                                    <form method="POST" class="flex items-center space-x-2">
                                        <input type="hidden" name="update_order_status" value="1">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                       <select name="status" class="border p-1 rounded">
    <option value="requested" <?php if ($order['status'] == 'requested') echo 'selected'; ?>>Pending</option>
    <option value="confirmed">Confirmed</option>
    <option value="canceled">Cancel</option>
</select>

                                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Update</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section Settings Lengkap -->
<div id="settings" class="tabcontent hidden">
    <h2 class="text-2xl font-bold mb-4">Edit Kontak, Footer & Branding</h2>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md max-w-lg">
        <input type="hidden" name="edit_settings" value="1">

        <!-- Instagram & Youtube -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Instagram URL</label>
            <input type="text" name="instagram_url" value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Youtube URL</label>
            <input type="text" name="youtube_url" value="<?php echo htmlspecialchars($settings['youtube_url'] ?? ''); ?>" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- Phone & Email -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Phone Number 1</label>
            <input type="text" name="phone_number1" value="<?php echo htmlspecialchars($settings['phone_number1'] ?? ''); ?>" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Email 1</label>
            <input type="text" name="email1" value="<?php echo htmlspecialchars($settings['email1'] ?? ''); ?>" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        

        <!-- Header & Time Business Hour -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Header Business Hour</label>
            <input type="text" name="header_bussines_hour" value="<?php echo htmlspecialchars($settings['header_bussines_hour'] ?? ''); ?>" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Time Business Hour</label>
            <input type="text" name="time_bussines_hour" value="<?php echo htmlspecialchars($settings['time_bussines_hour'] ?? ''); ?>" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- Address -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Address</label>
            <textarea name="address" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($settings['address'] ?? ''); ?></textarea>
        </div>

        <!-- Logo -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Website Logo</label>
            <input type="file" name="logo" class="mt-1 block w-full border border-gray-300 rounded-md p-2" accept="image/*">
            <?php if(!empty($settings['logo'])): ?>
                <p class="mt-2">Existing Logo: <img src="<?php echo $settings['logo']; ?>" width="80" alt="Logo Preview"></p>
            <?php endif; ?>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Save Settings</button>
    </form>
</div>

<!-- Section Laporan -->
<div id="report" class="tabcontent hidden">
    <h2 class="text-2xl font-bold mb-4">Laporan Penjualan</h2>

    <!-- Total Pendapatan -->
    <div class="mb-4 p-4 bg-white rounded-lg shadow-md">
        <h3 class="text-xl font-semibold">Total Pendapatan Keseluruhan</h3>
        <p class="text-lg">Rp <?php echo number_format($total_revenue ?? 0, 0, ',', '.'); ?></p>
    </div>

    <!-- Top 10 Paket -->
    <div class="bg-white rounded-lg shadow-md overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">Package Name</th>
                    <th class="p-3 text-left">Total Orders</th>
                    <th class="p-3 text-left">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report_top_packages as $rep): ?>
                <tr class="border-b">
                    <td class="p-3"><?php echo htmlspecialchars($rep['package_name']); ?></td>
                    <td class="p-3"><?php echo $rep['total_orders']; ?></td>
                    <td class="p-3">Rp <?php echo number_format($rep['total_revenue'],0,',','.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>



    <!-- Modal untuk Edit Catalog -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit_catalog" value="1">
                <input type="hidden" name="catalogue_id" id="edit_id">
                <input type="hidden" name="existing_image" id="edit_image">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Package Name</label>
                    <input type="text" name="package_name" id="edit_name" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="edit_desc" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" name="price" id="edit_price" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Status Publish</label>
                    <select name="status_publish" id="edit_status" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Y">Published</option>
                        <option value="N">Not Published</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Image (Existing: <span id="edit_image_name"></span>)</label>
                    <input type="file" name="image" class="mt-1 block w-full border border-gray-300 rounded-md p-2" accept="image/*">
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Update</button>
                    <button type="button" onclick="closeEditModal()" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS untuk Tab dan Modal -->
    <script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablink;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablink = document.getElementsByClassName("tablink");
        for (i = 0; i < tablink.length; i++) {
            tablink[i].classList.remove("bg-blue-700");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.classList.add("bg-blue-700");
    }

    function openEditModal(id, name, desc, price, status, image) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_desc').value = desc;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_image').value = image;
        document.getElementById('edit_image_name').innerText = image.split('/').pop();
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Open default tab
    document.getElementsByClassName("tablink")[0].click();
    </script>
</body>
</html>