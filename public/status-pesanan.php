<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
      <link rel="stylesheet" href="./css/styles.css">
   <link href="../node_modules/remixicon/fonts/remixicon.css" rel="stylesheet">
   <link rel="icon" href="img" type="image/png">
</head>
<body>
   <?php include __DIR__ . '/../src/php/includes/header.php' ?>

   <section class="ezy__about3 light py-14 md:py-24 bg-[#A8BBA3]">
  <div class="container px-4">
    <div class="text-center">
      <h1 class="text-3xl lg:text-5xl leading-none font-bold mb-8 lg:mb-10">Bagaimana Pesanan Saya?</h1>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 text-center">
      <div class="flex flex-col mt-10 px-8">
        <i class="fab fa-500px text-6xl text-blue-600 font-bold"></i>
        <h5 class="text-3xl font-medium my-4">Metode Pembayaran</h5>
        <p class="text-base opacity-80">Lakukan pembayaran dengan mudah melalui transfer bank dan e-wallet.
        Setiap transaksi aman dan tercatat untuk kenyamanan Anda.</p>
      </div>

      <div class="flex flex-col mt-10 px-8">
        <i class="fas fa-comments text-6xl text-blue-600 font-bold"></i>
        <h5 class="text-3xl font-medium my-4">Status Pemesanan</h5>
        <p class="text-base opacity-80">
          Status pemesanan dapat anda cek melalui email ketika sudah melakukan pemesanan.
        </p>
      </div>

      <div class="flex flex-col mt-10 px-8">
        <i class="fas fa-compass text-6xl text-blue-600 font-bold"></i>
        <h5 class="text-3xl font-medium my-4">Layanan</h5>
        <p class="text-base opacity-80">Untuk informasi lebih lanjut silahkan hubungi kontak kami di halaman <a href="contact.php" class="underline">kontak</a> </p>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../src/php/includes/footer.php' ?>
</body>
</html>