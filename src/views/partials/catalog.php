<?php
include __DIR__ . '/../../php/config/database.php';

// Fetch catalogs with status_publish = 'Y'
$stmt = $pdo->prepare("SELECT * FROM tb_catalogues_opik WHERE status_publish = 'Y'");
$stmt->execute();
$catalogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section id="catalog" class="ezy__portfolio12_ZIeG92wp bg-white text-stone-800">
  <div class="container mx-auto px-4">
    <div class="flex flex-col items-center text-center">
      <h1 class="text-3xl md:text-[45px] font-bold mb-2 py-6 text-stone-800">Paket Kami</h1>
    </div>

    <div>
      <!-- tab contents -->
      <div class="grid grid-cols-12 gap-6 mt-6">
        <?php if (empty($catalogs)): ?>
          <div class="col-span-12 text-center text-gray-500">
            <p>Tidak ada paket yang tersedia saat ini.</p>
          </div>
        <?php else: ?>
          <?php foreach ($catalogs as $cat): ?>
            <!-- card -->
            <a href="/proyek-lsp-wo/public/details-product.php?id=<?php echo $cat['catalogue_id']; ?>" class="col-span-12 sm:col-span-6 lg:col-span-3 hover:shadow-lg hover:scale-105 transition duration-300">
              <div class="h-full rounded overflow-hidden">
                <img 
                  src="<?php 
                    $imagePath = !empty($cat['image']) && file_exists(__DIR__ . '/../../../public/' . $cat['image']) 
                      ? '/proyek-lsp-wo/public/' . htmlspecialchars($cat['image']) 
                      : '/proyek-lsp-wo/public/img/placeholder.png'; 
                    echo $imagePath;
                  ?>" 
                  class="w-full" 
                  alt="<?php echo htmlspecialchars($cat['package_name']); ?>" 
                />
                <div class="p-4">
                  <p class="text-[15px] text-stone-800 mb-2"><?php echo htmlspecialchars($cat['package_name']); ?></p>
                  <h5 class="text-[19px] text-stone-800 font-medium leading-snug mb-2">
                    Rp <?php echo number_format($cat['price'], 0, ',', '.'); ?>
                  </h5>
                  <p class="text-[15px] text-stone-800"><?php echo htmlspecialchars($cat['description']); ?></p>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>