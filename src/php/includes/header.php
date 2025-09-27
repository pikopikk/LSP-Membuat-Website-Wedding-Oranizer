<?php
require __DIR__ . '/../config/settings.php';
?>

<div
  class="ezy__nav2_2lNcSgXG mx-auto px-14 w-full py-6 bg-white text-stone-800 relative"
>
  <nav>
    <div class="px-4">
      <div class="flex justify-between items-center">
        <a class="font-black text-3xl" href="index.php">
          <?php if(!empty($settings['logo'])): ?>
            <img src="<?php echo $settings['logo']; ?>" alt="Logo" width="120">
        <?php else: ?>
            <h1>WO.pik</h1>
        <?php endif; ?>
        </a>
        <button
          class="block lg:hidden cursor-pointer h-10 z-20"
          type="button"
          id="hamburger"
        >
          <div
            class="h-0.5 w-7 bg-black dark:bg-white -translate-y-2"
          ></div>
          <div class="h-0.5 w-7 bg-black dark:bg-white"></div>
          <div class="h-0.5 w-7 bg-black dark:bg-white translate-y-2"></div>
        </button>
        <ul
          class="flex flex-col lg:flex-row justify-center items-center text-3xl gap-6 lg:text-base lg:gap-2 absolute h-screen w-screen top-0 left-full lg:left-0 lg:relative lg:h-auto lg:w-auto bg-[#A8BBA3] lg:bg-transparent"
          id="navbar"
        >
          <li class="">
            <a class="px-4 font-bold" href="index.php">Home</a>
          </li>
          <li class="">
            <a class="px-4 font-bold hover:opacity-100" href="catalog.php"
              >Katalog</a
            >
          </li>
          <li class="">
            <a class="px-4 font-bold hover:opacity-100" href="contact.php"
              >Kontak</a
            >
          </li>
          <li class="">
            <a class="px-4 font-bold hover:opacity-100" href="status-pesanan.php"
              >Cek Pesanan</a
            >
          </li>
          
          <li>
            <a
              href="dashboard.php" class="font-bold flex items-center bg-[#B87C4C] px-4 py-2 text-white outline-[#A8BBA3] focus:outline-2 hover:bg-stone-100 hover:cursor-pointer hover:text-[#B87C4C] focus:outline-[#A8BBA3] rounded"
            >
              Login
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</div>