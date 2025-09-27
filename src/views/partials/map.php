<section class="ezy__location py-14 md:py-24 bg-[#F3F4F6]">
    <div class="container px-4">
        <div class="grid grid-cols-12 justify-center text-center mb-12">
            <div class="col-span-12 md:col-span-8 md:col-start-3">
                <h2 class="text-4xl leading-snug md:text-5xl md:leading-snug font-bold mb-6">Lokasi Kami</h2>
                <p class="text-lg opacity-80 mb-4">
                    Kunjungi kantor kami atau hubungi kami untuk konsultasi pernikahan. 
                    Kami siap membantu mewujudkan pernikahan impian Anda di lokasi yang strategis dan mudah diakses.
                </p>
            </div>
        </div>
        <div class="grid grid-cols-12 justify-center">
            <div class="col-span-12 md:col-span-10 md:col-start-2">
                <div class="w-full h-96 rounded-2xl overflow-hidden shadow-lg mb-8">
                    <iframe 
                        src="<?php echo htmlspecialchars($settings['address']); ?>" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                <!-- Kontak -->
                <div class="flex flex-col md:flex-row justify-center items-center gap-8">
                    <a href="<?php echo htmlspecialchars($settings['email1']); ?>" class="flex items-center gap-2 text-gray-700 hover:text-gray-900">
                        <i class="ri-mail-line text-2xl"></i>
                        <span>taufikhan17@gmail.com</span>
                    </a>
                    <a href="<?php echo htmlspecialchars($settings['instagram_url']); ?>" target="_blank" class="flex items-center gap-2 text-gray-700 hover:text-gray-900">
                        <i class="ri-instagram-line text-2xl"></i>
                        <span>@pikopik__</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
