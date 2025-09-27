<?php
session_start(); // mulai session untuk flash message

require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include __DIR__ . '/../src/php/config/database.php'; // koneksi DB

$catalogue_id = isset($_GET['catalogue_id']) ? (int)$_GET['catalogue_id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM tb_catalogues_opik WHERE catalogue_id = ? AND status_publish='Y'");
$stmt->execute([$catalogue_id]);
$catalog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$catalog) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $wedding_date = $_POST['wedding_date'] ?? '';

    if ($name && $email && $phone && $wedding_date) {
        $stmt = $pdo->prepare("INSERT INTO tb_orders_opik (catalogue_id, name, email, phone_number, wedding_date, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 'requested', NOW(), NOW())");
        $stmt->execute([$catalogue_id, $name, $email, $phone, $wedding_date]);

        // Kirim email
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
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Pesanan Paket Anda Berhasil Dibuat';
            $mail->Body    = "
                <h3>Terima kasih telah memesan paket kami!</h3>
                <p>Berikut detail pesanan Anda:</p>
                <ul>
                    <li>Nama Paket: {$catalog['package_name']}</li>
                    <li>Harga: Rp " . number_format($catalog['price'],0,',','.') . "</li>
                    <li>Tanggal Wedding: {$wedding_date}</li>
                </ul>
                <p>Silahkan lakukan pembayaran melalui: 
                <br>
                Seabank: 0123456789
                <br>
                Dana: 0123456789
                <br>
                Ovo: 0123456789
                <br>
                Gopay: 0123456789
                <br>
                Shopeepay: 0123456789
                </p>
                <p>
                Setelah membayar, pesanan anda akan kami konfirmasi dan informasikan melalui email kembali, terimakasih.
                </p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
        }

        // Set flash message dan redirect
        $_SESSION['flash_message'] = "Pesanan anda berhasil dibuat, silahkan cek email untuk informasi pembayaran.";
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Form Pesan</title>
   <link rel="stylesheet" href="./css/styles.css">
   <link href="../node_modules/remixicon/fonts/remixicon.css" rel="stylesheet">
   <link rel="icon" href="img" type="image/png">
</head>
<body>

<section class="ezy__contact8 light py-14 md:py-24 bg-[#A8BBA3] text-zinc-900 dark:text-white relative overflow-hidden">
  <div class="container px-4">
    <div class="grid grid-cols-12">
      <div class="col-span-12 lg:col-span-6 lg:col-start-4 lg:px-16 xl:px-20">
        <div class="bg-white bg-opacity-20 rounded-xl relative p-6 md:p-12">
          <h2 class="text-2xl text-stone-800 md:text-[45px] leading-none font-bold mb-4">Pesan Paket</h2>
          <p class="text-lg mb-12 text-stone-800">Isi data Anda untuk memesan paket berikut:</p>

          <form method="POST">
            <div class="mb-4">
              <input
                type="text"
                class="text-stone-800 min-h-[48px] leading-[48px] bg-[#F7F4EA] border border-transparent rounded-xl focus:outline-none focus:border focus:border-[#86b7fe] w-full px-5"
                value="<?php echo htmlspecialchars($catalog['package_name']); ?>" 
                readonly
              />
            </div>
            <div class="mb-4">
              <input
                type="text"
                class="text-stone-800 min-h-[48px] leading-[48px] bg-[#F7F4EA] border border-transparent rounded-xl focus:outline-none focus:border focus:border-[#86b7fe] w-full px-5"
                value="Rp <?php echo number_format($catalog['price'], 0, ',', '.'); ?>" 
                readonly
              />
            </div>
            <div class="mb-4">
              <input
                type="text"
                name="name"
                class="text-stone-800 min-h-[48px] leading-[48px] bg-[#F7F4EA] border border-transparent rounded-xl focus:outline-none focus:border focus:border-[#86b7fe] w-full px-5"
                placeholder="Nama Anda"
                required
              />
            </div>
            <div class="mb-4">
              <input
                type="email"
                name="email"
                class="text-stone-800 min-h-[48px] leading-[48px] bg-[#F7F4EA] border border-transparent rounded-xl focus:outline-none focus:border focus:border-[#86b7fe] w-full px-5"
                placeholder="Email Anda"
                required
              />
            </div>
            <div class="mb-4">
              <input
                type="text"
                name="phone"
                class="text-stone-800 min-h-[48px] leading-[48px] bg-[#F7F4EA] border border-transparent rounded-xl focus:outline-none focus:border focus:border-[#86b7fe] w-full px-5"
                placeholder="Nomor Telepon"
                required
              />
            </div>
            <div class="mb-4">
              <input
                type="date"
                name="wedding_date"
                class="text-stone-800 min-h-[48px] leading-[48px] bg-[#F7F4EA] border border-transparent rounded-xl focus:outline-none focus:border focus:border-[#86b7fe] w-full px-5"
                required
              />
            </div>
            <div class="text-end">
              <button type="submit" class="bg-[#B87C4C] text-stone-800 hover:bg-opacity-90 text-white px-8 py-3 rounded mb-4">
                Submit
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

</body>
</html>
