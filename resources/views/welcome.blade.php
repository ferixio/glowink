<!doctype html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @vite('resources/css/app.css')
        <style>
            .gradient-bg {
                background: linear-gradient(135deg, #a7f3d0 0%, #bfdbfe 50%, #86efac 100%);
            }

            .gradient-text {
                background: linear-gradient(135deg, #059669, #2563eb);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .coffee-bean {
                background: radial-gradient(ellipse at center, #8b4513 0%, #4a2c17 100%);
                border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            }

            .glass-effect {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
        </style>
    </head>

    <body class="gradient-bg min-h-screen">
        <!-- Navigation -->
        <nav class="fixed top-0 w-full z-50 glass-effect">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="coffee-bean w-8 h-8 animate-glow"></div>
                        <span class="text-2xl font-bold gradient-text">Glowing Network</span>
                    </div>
                    <div class="hidden md:flex space-x-8">
                        <a href="#home"
                            class="text-gray-700 hover:text-green-600 transition-colors font-medium">Beranda</a>
                        <a href="#about"
                            class="text-gray-700 hover:text-green-600 transition-colors font-medium">Tentang</a>
                        <a href="#products"
                            class="text-gray-700 hover:text-green-600 transition-colors font-medium">Produk</a>
                        <a href="#opportunity"
                            class="text-gray-700 hover:text-green-600 transition-colors font-medium">Peluang</a>
                        <a href="#contact"
                            class="text-gray-700 hover:text-green-600 transition-colors font-medium">Kontak</a>
                    </div>
                    <button class="md:hidden text-gray-700" onclick="toggleMobileMenu()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="home" class="pt-20 pb-16 min-h-screen flex items-center">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="animate-slide-up">
                        <h1 class="text-5xl md:text-6xl font-bold mb-6 gradient-text leading-tight">
                            Membangun Kesejahteraan Melalui Kopi Berkualitas
                        </h1>
                        <p class="text-xl text-gray-700 mb-8 leading-relaxed">
                            Bergabunglah dengan Glowing Network dan rasakan manfaat berimbang antara perusahaan, mitra,
                            dan konsumen melalui produk kopi premium dari PT. GOALS.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button
                                class="bg-gradient-to-r from-green-500 to-blue-500 text-white px-8 py-4 rounded-full font-semibold hover:scale-105 transition-transform shadow-lg animate-glow">
                                Mulai Sekarang
                            </button>
                            <button
                                class="border-2 border-green-500 text-green-600 px-8 py-4 rounded-full font-semibold hover:bg-green-50 transition-colors">
                                Pelajari Lebih Lanjut
                            </button>
                        </div>
                    </div>
                    <div class="relative animate-float">
                        <div class="relative z-10">
                            <div class="w-80 h-80 mx-auto relative">
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-green-400 to-blue-400 rounded-full opacity-20 animate-pulse">
                                </div>
                                <div
                                    class="absolute inset-4 bg-gradient-to-br from-green-300 to-blue-300 rounded-full opacity-30 animate-pulse delay-150">
                                </div>
                                <div
                                    class="absolute inset-8 bg-gradient-to-br from-green-200 to-blue-200 rounded-full opacity-40 animate-pulse delay-300">
                                </div>
                                <div
                                    class="absolute inset-12 bg-white rounded-full shadow-2xl flex items-center justify-center">
                                    <div class="text-6xl">☕</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-20 bg-white/20 backdrop-blur-sm">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold gradient-text mb-4">Tentang Glowing Network</h2>
                    <p class="text-xl text-gray-700 max-w-3xl mx-auto">
                        Membangun database dan marketbase produk-produk PT. GOALS berdasarkan asas manfaat berimbang
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="glass-effect p-8 rounded-2xl hover:scale-105 transition-transform">
                        <div class="text-4xl mb-4 text-center">🎯</div>
                        <h3 class="text-2xl font-bold text-center mb-4 gradient-text">Visi Kami</h3>
                        <p class="text-gray-700 text-center">
                            Menjadi platform direct selling terdepan yang menambah kesejahteraan masyarakat Indonesia
                            melalui produk konsumsi harian berkualitas.
                        </p>
                    </div>
                    <div class="glass-effect p-8 rounded-2xl hover:scale-105 transition-transform">
                        <div class="text-4xl mb-4 text-center">🤝</div>
                        <h3 class="text-2xl font-bold text-center mb-4 gradient-text">Misi Kami</h3>
                        <p class="text-gray-700 text-center">
                            Membangun ekosistem bisnis yang saling menguntungkan antara perusahaan, mitra, dan konsumen
                            dengan fokus pada produk kopi premium.
                        </p>
                    </div>
                    <div class="glass-effect p-8 rounded-2xl hover:scale-105 transition-transform">
                        <div class="text-4xl mb-4 text-center">💎</div>
                        <h3 class="text-2xl font-bold text-center mb-4 gradient-text">Nilai Kami</h3>
                        <p class="text-gray-700 text-center">
                            Transparansi, kualitas produk, dan pemberdayaan masyarakat melalui peluang bisnis yang
                            berkelanjutan dan menguntungkan.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Products Section -->
        <section id="products" class="py-20">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold gradient-text mb-4">Produk Unggulan</h2>
                    <p class="text-xl text-gray-700 max-w-3xl mx-auto">
                        Nikmati produk kopi premium dari PT. GOALS yang telah terpercaya kualitasnya
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div
                        class="bg-white/30 backdrop-blur-sm rounded-2xl p-6 hover:scale-105 transition-transform shadow-lg">
                        <div class="text-6xl text-center mb-4">☕</div>
                        <h3 class="text-2xl font-bold text-center mb-4 gradient-text">Kopi Arabica Premium</h3>
                        <p class="text-gray-700 text-center mb-6">
                            Kopi arabica pilihan dengan cita rasa yang kaya dan aroma yang memikat, langsung dari petani
                            terbaik Indonesia.
                        </p>
                        <div class="text-center">
                            <button
                                class="bg-gradient-to-r from-green-500 to-blue-500 text-white px-6 py-3 rounded-full font-semibold hover:scale-105 transition-transform">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                    <div
                        class="bg-white/30 backdrop-blur-sm rounded-2xl p-6 hover:scale-105 transition-transform shadow-lg">
                        <div class="text-6xl text-center mb-4">🌿</div>
                        <h3 class="text-2xl font-bold text-center mb-4 gradient-text">Kopi Herbal Sehat</h3>
                        <p class="text-gray-700 text-center mb-6">
                            Perpaduan sempurna antara kopi berkualitas dengan ekstrak herbal alami untuk kesehatan yang
                            optimal.
                        </p>
                        <div class="text-center">
                            <button
                                class="bg-gradient-to-r from-green-500 to-blue-500 text-white px-6 py-3 rounded-full font-semibold hover:scale-105 transition-transform">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                    <div
                        class="bg-white/30 backdrop-blur-sm rounded-2xl p-6 hover:scale-105 transition-transform shadow-lg">
                        <div class="text-6xl text-center mb-4">⭐</div>
                        <h3 class="text-2xl font-bold text-center mb-4 gradient-text">Kopi Spesial Blend</h3>
                        <p class="text-gray-700 text-center mb-6">
                            Racikan khusus yang menggabungkan berbagai jenis kopi terbaik untuk pengalaman rasa yang tak
                            terlupakan.
                        </p>
                        <div class="text-center">
                            <button
                                class="bg-gradient-to-r from-green-500 to-blue-500 text-white px-6 py-3 rounded-full font-semibold hover:scale-105 transition-transform">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Opportunity Section -->
        <section id="opportunity" class="py-20 bg-white/20 backdrop-blur-sm">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold gradient-text mb-4">Peluang Bisnis</h2>
                    <p class="text-xl text-gray-700 max-w-3xl mx-auto">
                        Bergabunglah dengan ribuan mitra sukses dan rasakan manfaat berimbang dalam bisnis direct
                        selling
                    </p>
                </div>
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <div
                                    class="bg-gradient-to-r from-green-500 to-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">
                                    1</div>
                                <div>
                                    <h3 class="text-xl font-bold gradient-text mb-2">Pendaftaran Mudah</h3>
                                    <p class="text-gray-700">Daftar menjadi mitra hanya dalam hitungan menit dengan
                                        proses yang sederhana dan transparan.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div
                                    class="bg-gradient-to-r from-green-500 to-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">
                                    2</div>
                                <div>
                                    <h3 class="text-xl font-bold gradient-text mb-2">Pelatihan Komprehensif</h3>
                                    <p class="text-gray-700">Dapatkan pelatihan lengkap tentang produk, strategi
                                        penjualan, dan pengembangan jaringan.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div
                                    class="bg-gradient-to-r from-green-500 to-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">
                                    3</div>
                                <div>
                                    <h3 class="text-xl font-bold gradient-text mb-2">Penghasilan Berkelanjutan</h3>
                                    <p class="text-gray-700">Nikmati penghasilan yang terus bertumbuh dari penjualan
                                        dan pengembangan jaringan mitra.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="glass-effect p-8 rounded-2xl">
                            <h3 class="text-2xl font-bold gradient-text mb-6 text-center">Keuntungan Mitra</h3>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3">
                                    <div class="text-green-500 text-xl">✓</div>
                                    <span class="text-gray-700">Komisi penjualan hingga 30%</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="text-green-500 text-xl">✓</div>
                                    <span class="text-gray-700">Bonus pengembangan jaringan</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="text-green-500 text-xl">✓</div>
                                    <span class="text-gray-700">Produk berkualitas tinggi</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="text-green-500 text-xl">✓</div>
                                    <span class="text-gray-700">Dukungan marketing penuh</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="text-green-500 text-xl">✓</div>
                                    <span class="text-gray-700">Pelatihan dan mentoring</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-4xl font-bold gradient-text mb-6">Siap Memulai Perjalanan Sukses Anda?</h2>
                <p class="text-xl text-gray-700 mb-8 max-w-2xl mx-auto">
                    Bergabunglah dengan Glowing Network hari ini dan rasakan manfaat berimbang dalam bisnis direct
                    selling yang menguntungkan.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button
                        class="bg-gradient-to-r from-green-500 to-blue-500 text-white px-8 py-4 rounded-full font-semibold hover:scale-105 transition-transform shadow-lg animate-glow">
                        Daftar Sebagai Mitra
                    </button>
                    <button
                        class="border-2 border-green-500 text-green-600 px-8 py-4 rounded-full font-semibold hover:bg-green-50 transition-colors">
                        Hubungi Kami
                    </button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer id="contact" class="bg-white/20 backdrop-blur-sm py-12">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="coffee-bean w-8 h-8"></div>
                            <span class="text-2xl font-bold gradient-text">Glowing Network</span>
                        </div>
                        <p class="text-gray-700 mb-4">
                            Membangun kesejahteraan masyarakat Indonesia melalui produk kopi berkualitas dari PT. GOALS.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold gradient-text mb-4">Produk</h3>
                        <ul class="space-y-2 text-gray-700">
                            <li><a href="#" class="hover:text-green-600 transition-colors">Kopi Arabica</a></li>
                            <li><a href="#" class="hover:text-green-600 transition-colors">Kopi Herbal</a></li>
                            <li><a href="#" class="hover:text-green-600 transition-colors">Kopi Spesial</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold gradient-text mb-4">Perusahaan</h3>
                        <ul class="space-y-2 text-gray-700">
                            <li><a href="#" class="hover:text-green-600 transition-colors">Tentang Kami</a></li>
                            <li><a href="#" class="hover:text-green-600 transition-colors">Karir</a></li>
                            <li><a href="#" class="hover:text-green-600 transition-colors">Kontak</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold gradient-text mb-4">Kontak</h3>
                        <div class="space-y-2 text-gray-700">
                            <p>📧 info@glowingnetwork.com</p>
                            <p>📱 +62 123-456-7890</p>
                            <p>📍 Jakarta, Indonesia</p>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-300 mt-8 pt-6 text-center text-gray-600">
                    <p>&copy; 2025 Glowing Network - PT. GOALS (Global Arsindo Lestari). All rights reserved.</p>
                </div>
            </div>
        </footer>

        <script>
            // Smooth scrolling for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Mobile menu toggle
            function toggleMobileMenu() {
                // Mobile menu implementation would go here
                console.log('Mobile menu toggled');
            }

            // Scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            }, observerOptions);

            // Observe all sections
            document.querySelectorAll('section').forEach(section => {
                observer.observe(section);
            });

            // Add parallax effect to hero section
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const hero = document.querySelector('#home');
                if (hero) {
                    hero.style.transform = `translateY(${scrolled * 0.5}px)`;
                }
            });

            // Interactive coffee bean animation
            document.querySelectorAll('.coffee-bean').forEach(bean => {
                bean.addEventListener('mouseenter', () => {
                    bean.style.transform = 'scale(1.2) rotate(360deg)';
                    bean.style.transition = 'transform 0.5s ease';
                });

                bean.addEventListener('mouseleave', () => {
                    bean.style.transform = 'scale(1) rotate(0deg)';
                });
            });

            // Button click effects
            document.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.classList.add('ripple');

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        </script>

        <style>
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                animation: ripple-animation 0.6s ease-out;
                pointer-events: none;
            }

            @keyframes ripple-animation {
                0% {
                    transform: scale(0);
                    opacity: 1;
                }

                100% {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        </style>
    </body>

</html>
