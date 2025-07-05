<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>NetworKit - Bangun Jaringan, Raih Kebebasan Finansial</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes slideInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-50px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(50px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes float {

                0%,
                100% {
                    transform: translateY(0px);
                }

                50% {
                    transform: translateY(-20px);
                }
            }

            .animate-fadeInUp {
                animation: fadeInUp 1s ease-out;
            }

            .animate-slideInLeft {
                animation: slideInLeft 1s ease-out;
            }

            .animate-slideInRight {
                animation: slideInRight 1s ease-out;
            }

            .animate-float {
                animation: float 3s ease-in-out infinite;
            }

            .gradient-bg {
                background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);
            }

            .card-hover {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .card-hover:hover {
                transform: translateY(-10px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            }

            .btn-primary {
                background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
                box-shadow: 0 4px 15px rgba(2, 132, 199, 0.3);
                transition: all 0.3s ease;
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
                box-shadow: 0 6px 25px rgba(2, 132, 199, 0.4);
                transform: translateY(-2px);
            }

            .parallax-bg {
                background-attachment: fixed;
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
            }
        </style>
    </head>

    <body class="bg-white">

        <!-- Navigation -->
        <nav class="bg-white shadow-lg fixed w-full top-0 z-50" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <i class="fas fa-network-wired text-2xl text-sky-600 mr-2"></i>
                            <span class="text-2xl font-bold text-gray-900">NetworKit</span>
                        </div>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#beranda" class="text-gray-700 hover:text-sky-600 transition-colors">Beranda</a>
                        <a href="#keunggulan" class="text-gray-700 hover:text-sky-600 transition-colors">Keunggulan</a>
                        <a href="#cara-kerja" class="text-gray-700 hover:text-sky-600 transition-colors">Cara Kerja</a>
                        <a href="#testimoni" class="text-gray-700 hover:text-sky-600 transition-colors">Testimoni</a>
                        <a href="/user"  class="btn-primary text-white px-6 py-2 rounded-full font-semibold">
                            Gabung Sekarang
                        </a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="md:hidden flex items-center">
                        <button @click="open = !open" class="text-gray-700 hover:text-sky-600">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="open" x-transition class="md:hidden bg-white border-t">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="#beranda" class="block px-3 py-2 text-gray-700 hover:text-sky-600">Beranda</a>
                    <a href="#keunggulan" class="block px-3 py-2 text-gray-700 hover:text-sky-600">Keunggulan</a>
                    <a href="#cara-kerja" class="block px-3 py-2 text-gray-700 hover:text-sky-600">Cara Kerja</a>
                    <a href="#testimoni" class="block px-3 py-2 text-gray-700 hover:text-sky-600">Testimoni</a>
                    <a href="/user" class="btn-primary text-white px-6 py-2 rounded-full font-semibold ml-3 mt-2">
                        Gabung Sekarang
                    </a>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="beranda" class="gradient-bg min-h-screen flex items-center pt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                    <!-- Hero Content -->
                    <div class="animate-slideInLeft">
                        <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                            Bangun Jaringan,
                            <span class="text-sky-600">Raih Kebebasan</span>
                            Finansial
                        </h1>

                        <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                            Gabung sekarang dan dapatkan akses ke sistem otomatisasi jaringan dan dukungan mentor 24/7.
                            Wujudkan impian finansial Anda bersama ribuan member sukses lainnya.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 mb-8">
                            <a href="/user" class="btn-primary text-white px-8 py-4 rounded-full font-semibold text-lg">
                                <i class="fas fa-rocket mr-2"></i>
                                Gabung Sekarang
                            </a>
                            <button
                                class="border-2 border-sky-600 text-sky-600 px-8 py-4 rounded-full font-semibold text-lg hover:bg-sky-600 hover:text-white transition-all">
                                <i class="fas fa-play mr-2"></i>
                                Pelajari Lebih Lanjut
                            </button>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-6 text-center">
                            <div>
                                <div class="text-3xl font-bold text-sky-600">10K+</div>
                                <div class="text-gray-600">Member Aktif</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-sky-600">50M+</div>
                                <div class="text-gray-600">Total Komisi</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-sky-600">98%</div>
                                <div class="text-gray-600">Kepuasan</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Illustration -->
                    <div class="animate-slideInRight">
                        <div class="relative">
                            <div class="animate-float">
                                <div class="w-96 h-96 mx-auto relative">
                                    <!-- Network Illustration -->
                                    <div
                                        class="absolute inset-0 bg-gradient-to-br from-sky-400 to-sky-600 rounded-full opacity-20 animate-pulse">
                                    </div>

                                    <!-- Central Node -->
                                    <div
                                        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-20 h-20 bg-sky-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-white text-2xl"></i>
                                    </div>

                                    <!-- Surrounding Nodes -->
                                    <div
                                        class="absolute top-16 left-1/2 transform -translate-x-1/2 w-12 h-12 bg-sky-400 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                    <div
                                        class="absolute top-1/2 right-16 transform -translate-y-1/2 w-12 h-12 bg-sky-400 rounded-full flex items-center justify-center">
                                        <i class="fas fa-chart-line text-white"></i>
                                    </div>
                                    <div
                                        class="absolute bottom-16 left-1/2 transform -translate-x-1/2 w-12 h-12 bg-sky-400 rounded-full flex items-center justify-center">
                                        <i class="fas fa-dollar-sign text-white"></i>
                                    </div>
                                    <div
                                        class="absolute top-1/2 left-16 transform -translate-y-1/2 w-12 h-12 bg-sky-400 rounded-full flex items-center justify-center">
                                        <i class="fas fa-handshake text-white"></i>
                                    </div>

                                    <!-- Connection Lines -->
                                    <svg class="absolute inset-0 w-full h-full" viewBox="0 0 384 384">
                                        <line x1="192" y1="192" x2="192" y2="80"
                                            stroke="#0284c7" stroke-width="2" opacity="0.6" />
                                        <line x1="192" y1="192" x2="304" y2="192"
                                            stroke="#0284c7" stroke-width="2" opacity="0.6" />
                                        <line x1="192" y1="192" x2="192" y2="304"
                                            stroke="#0284c7" stroke-width="2" opacity="0.6" />
                                        <line x1="192" y1="192" x2="80" y2="192"
                                            stroke="#0284c7" stroke-width="2" opacity="0.6" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Keunggulan Section -->
        <section id="keunggulan" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 animate-fadeInUp">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Mengapa Memilih NetworKit?</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Bergabunglah dengan platform MLM terdepan yang telah dipercaya ribuan member untuk meraih
                        kesuksesan finansial
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Keunggulan 1 -->
                    <div class="card-hover bg-white rounded-2xl p-8 text-center shadow-lg">
                        <div class="w-16 h-16 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-cogs text-sky-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Sistem Otomatis</h3>
                        <p class="text-gray-600 mb-6">
                            Platform canggih dengan otomatisasi penuh. Kelola jaringan, komisi, dan laporan dengan mudah
                            melalui dashboard terintegrasi.
                        </p>
                        <div class="flex justify-center">
                            <span class="text-sky-600 font-semibold">Hemat Waktu 80%</span>
                        </div>
                    </div>

                    <!-- Keunggulan 2 -->
                    <div class="card-hover bg-white rounded-2xl p-8 text-center shadow-lg">
                        <div class="w-16 h-16 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-infinity text-sky-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Pendapatan Tak Terbatas</h3>
                        <p class="text-gray-600 mb-6">
                            Sistem komisi berlapis dengan bonus leadership yang menguntungkan. Semakin besar jaringan,
                            semakin besar penghasilan.
                        </p>
                        <div class="flex justify-center">
                            <span class="text-sky-600 font-semibold">Hingga 40% Komisi</span>
                        </div>
                    </div>

                    <!-- Keunggulan 3 -->
                    <div class="card-hover bg-white rounded-2xl p-8 text-center shadow-lg">
                        <div class="w-16 h-16 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-users text-sky-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Komunitas Aktif</h3>
                        <p class="text-gray-600 mb-6">
                            Bergabung dengan komunitas solid yang saling mendukung. Akses training, webinar, dan
                            mentoring dari leader berpengalaman.
                        </p>
                        <div class="flex justify-center">
                            <span class="text-sky-600 font-semibold">Support 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cara Kerja Section -->
        <section id="cara-kerja" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Cara Kerja Mudah</h2>
                    <p class="text-xl text-gray-600">Hanya 3 langkah untuk memulai perjalanan kesuksesan Anda</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Step 1 -->
                    <div class="text-center relative">
                        <div class="relative z-10">
                            <div
                                class="w-20 h-20 bg-sky-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span class="text-white text-2xl font-bold">1</span>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Daftar Gratis</h3>
                            <p class="text-gray-600 mb-6">
                                Registrasi akun Anda dalam hitungan menit. Dapatkan akses ke dashboard dan material
                                training lengkap.
                            </p>
                        </div>
                        <!-- Arrow -->
                        <div class="hidden md:block absolute top-10 -right-4 text-sky-300">
                            <i class="fas fa-arrow-right text-3xl"></i>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="text-center relative">
                        <div class="relative z-10">
                            <div
                                class="w-20 h-20 bg-sky-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span class="text-white text-2xl font-bold">2</span>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Bangun Jaringan</h3>
                            <p class="text-gray-600 mb-6">
                                Ajak teman dan keluarga untuk bergabung. Gunakan tools marketing yang sudah disediakan
                                untuk mempermudah prospek.
                            </p>
                        </div>
                        <!-- Arrow -->
                        <div class="hidden md:block absolute top-10 -right-4 text-sky-300">
                            <i class="fas fa-arrow-right text-3xl"></i>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="text-center">
                        <div class="w-20 h-20 bg-sky-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <span class="text-white text-2xl font-bold">3</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Nikmati Komisi</h3>
                        <p class="text-gray-600 mb-6">
                            Dapatkan komisi otomatis dari setiap penjualan jaringan Anda. Penarikan dana dapat dilakukan
                            kapan saja.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimoni Section -->
        <section id="testimoni" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Testimoni Member Sukses</h2>
                    <p class="text-xl text-gray-600">Dengarkan kisah sukses dari member NetworKit</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" x-data="{
                    testimonials: [
                        { name: 'Sarah Putri', role: 'Diamond Leader', income: 'Rp 25 juta/bulan', quote: 'NetworKit mengubah hidup saya! Dari ibu rumah tangga biasa menjadi leader dengan penghasilan fantastis. Sistemnya sangat mudah dipahami.' },
                        { name: 'Ahmad Rizki', role: 'Platinum Member', income: 'Rp 18 juta/bulan', quote: 'Saya skeptis di awal, tapi setelah 6 bulan bergabung, hasilnya luar biasa. Sekarang saya bisa fokus pada keluarga tanpa khawatir finansial.' },
                        { name: 'Maya Sari', role: 'Gold Member', income: 'Rp 12 juta/bulan', quote: 'Training dan support dari tim sangat membantu. Dalam 3 bulan, saya sudah bisa mencapai target Gold Member. Terima kasih NetworKit!' }
                    ]
                }">
                    <template x-for="testimonial in testimonials" :key="testimonial.name">
                        <div class="bg-white rounded-2xl p-8 shadow-lg card-hover">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-user text-sky-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900" x-text="testimonial.name"></h4>
                                    <p class="text-sky-600" x-text="testimonial.role"></p>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-6" x-text="testimonial.quote"></p>
                            <div class="flex items-center">
                                <div class="text-yellow-400 mr-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-green-600 font-semibold" x-text="testimonial.income"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="gradient-bg py-20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Siap Memulai Perjalanan Menuju
                    <span class="text-sky-600">Kebebasan Finansial?</span>
                </h2>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                    Bergabunglah dengan ribuan member yang telah merasakan kesuksesan bersama NetworKit.
                    Jangan tunda lagi, mulai hari ini juga!
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                    <button class="btn-primary text-white px-10 py-4 rounded-full font-semibold text-lg">
                        <i class="fas fa-rocket mr-2"></i>
                        Daftar Sekarang - GRATIS
                    </button>
                    <button
                        class="border-2 border-sky-600 text-sky-600 px-10 py-4 rounded-full font-semibold text-lg hover:bg-sky-600 hover:text-white transition-all">
                        <i class="fas fa-phone mr-2"></i>
                        Hubungi Kami
                    </button>
                </div>

                <div class="flex items-center justify-center text-gray-600">
                    <i class="fas fa-shield-alt text-sky-600 mr-2"></i>
                    <span>100% Aman & Terpercaya • Tanpa Biaya Tersembunyi</span>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <i class="fas fa-network-wired text-2xl text-sky-400 mr-2"></i>
                            <span class="text-2xl font-bold">NetworKit</span>
                        </div>
                        <p class="text-gray-400 mb-4">
                            Platform MLM terdepan untuk membangun jaringan dan meraih kebebasan finansial.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-sky-400"><i
                                    class="fab fa-facebook"></i></a>
                            <a href="#" class="text-gray-400 hover:text-sky-400"><i
                                    class="fab fa-instagram"></i></a>
                            <a href="#" class="text-gray-400 hover:text-sky-400"><i
                                    class="fab fa-twitter"></i></a>
                            <a href="#" class="text-gray-400 hover:text-sky-400"><i
                                    class="fab fa-youtube"></i></a>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold mb-4">Perusahaan</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-sky-400">Tentang Kami</a></li>
                            <li><a href="#" class="hover:text-sky-400">Karir</a></li>
                            <li><a href="#" class="hover:text-sky-400">Blog</a></li>
                            <li><a href="#" class="hover:text-sky-400">Press</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold mb-4">Produk</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-sky-400">Fitur</a></li>
                            <li><a href="#" class="hover:text-sky-400">Pricing</a></li>
                            <li><a href="#" class="hover:text-sky-400">Training</a></li>
                            <li><a href="#" class="hover:text-sky-400">Support</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold mb-4">Kontak</h4>
                        <div class="space-y-2 text-gray-400">
                            <div class="flex items-center">
                                <i class="fas fa-envelope mr-2"></i>
                                <span>info@networkit.com</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone mr-2"></i>
                                <span>+62 812-3456-7890</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span>Jakarta, Indonesia</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; 2024 NetworKit. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Smooth Scrolling -->
        <script>
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        </script>
    </body>

</html>
