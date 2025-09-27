<?php
include __DIR__ . '/../src/php/config/database.php';

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    die('Produk tidak ditemukan.');
}

$id = (int) $_GET['id'];

// Query produk berdasarkan id
$stmt = $pdo->prepare("SELECT * FROM tb_catalogues_opik WHERE catalogue_id = ? AND status_publish = 'Y'");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die('Produk tidak ditemukan atau belum dipublish.');
}

// Path server untuk file_exists
$serverImagePath = __DIR__ . '/img/catalog/' . basename($product['image']);

// Path URL untuk src
$imagePath = !empty($product['image']) && file_exists($serverImagePath)
    ? '/proyek-lsp-wo/public/img/catalog/' . htmlspecialchars(basename($product['image']))
    : '/proyek-lsp-wo/public/img/placeholder.png';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($product['package_name']); ?> | Detail Produk</title>
     <link rel="stylesheet" href="./css/styles.css">
   <link href="../node_modules/remixicon/fonts/remixicon.css" rel="stylesheet">
   <link rel="icon" href="img" type="image/png">
</head>
<body>
<?php include __DIR__ . '/../src/php/includes/header.php' ?>

<section class="ezy__about9 light py-14 md:py-24 bg-[#A8BBA3]">
  <div class="container px-4">
    <div class="grid grid-cols-12 items-center gap-4 mb-12">
      <div class="col-span-12 lg:col-span-6">
        <h6 class="font-medium opacity-70 mb-2"><?php echo htmlspecialchars($product['package_name']); ?></h6>
        <h1 class="text-3xl leading-none font-bold uppercase tracking-wider mb-2">
          Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
        </h1>
        <hr class="bg-[#B87C4C] h-1 rounded-[3px] w-12 opacity-100 my-6" />
        <p class="opacity-70 mb-2">
          <?php echo nl2br(htmlspecialchars($product['description'])); ?>
        </p>
      
        <div class="mt-12">
          <a href="form-pesan.php?catalogue_id=<?php echo $product['catalogue_id']; ?>" 
             class="bg-gray-900 text-white dark:bg-white dark:text-black hover:bg-opacity-90 rounded-md px-5 py-2 transition">
            Pesan Sekarang
          </a>
        </div>
      </div>
      <div class="col-span-12 lg:col-span-6">
        <div class="mt-12 lg:mt-0">
          <img
    src="<?php echo $imagePath; ?>"
    alt="<?php echo htmlspecialchars($product['package_name']); ?>"
    class="max-w-full h-auto rounded-2xl"
/>

        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../src/php/includes/footer.php' ?>
</body>
</html>
