<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Line Pharm') }} | منصة الأدوية والصيدلية الذكية</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #16A085 0%, #087F5B 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-[#F7FAF9] text-[#263238]">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-[#16A085] flex items-center gap-2">
                        <svg class="w-8 h-8 text-[#16A085]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Line Pharm
                    </a>
                </div>

                <!-- Nav Links -->
                <div class="hidden md:flex items-center space-x-8 space-x-reverse">
                    <a href="#" class="text-gray-700 hover:text-[#16A085] font-medium transition">الرئيسية</a>
                    <a href="#" class="text-gray-700 hover:text-[#16A085] font-medium transition">تصفح الأدوية</a>
                    <a href="#" class="text-gray-700 hover:text-[#16A085] font-medium transition">استشارة صيدلي</a>
                    <a href="#" class="text-gray-700 hover:text-[#16A085] font-medium transition">مقالات صحية</a>
                </div>

                <!-- Right Action Items (Search & Auth) -->
                <div class="flex items-center gap-4">
                    <!-- Search Icon / Quick Input -->
                    <div class="relative hidden sm:block">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" placeholder="ابحث عن دواء أو علاج..." class="w-48 lg:w-64 pr-9 pl-4 py-1.5 text-sm bg-[#F7FAF9] border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#16A085] focus:bg-white transition">
                    </div>

                    @auth
                        <a href="{{ url('/admin/loginType') }}" class="px-4 py-2 bg-[#16A085] text-white rounded-lg hover:bg-[#087F5B] transition">
                            لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 text-gray-700 hover:text-[#16A085] transition font-medium">
                            تسجيل دخول
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-[#16A085] text-white rounded-lg hover:bg-[#087F5B] transition">
                            حساب جديد
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-gradient text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold mb-6">
                    مرحباً بك في <span class="text-[#E8F5F2]">Line Pharm</span>
                </h1>
                <p class="text-xl md:text-2xl text-emerald-100 mb-8 max-w-3xl mx-auto">
                    منصتك الطبية الشاملة للبحث عن الأدوية، استشارة الصيادلة، والحصول على الرعاية الصحية الموثوقة بكل سهولة
                </p>
                <div class="flex gap-4 justify-center">
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-white text-[#16A085] rounded-lg font-semibold hover:bg-[#E8F5F2] transition transform hover:scale-105 shadow-md">
                        استكشف الأدوية الآن
                    </a>
                    <a href="#" class="px-8 py-3 border-2 border-white text-white rounded-lg font-semibold hover:bg-white hover:text-[#16A085] transition">
                        استشارة صيدلي
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    لماذا تختار Line Pharm؟
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    نقدم لك أعلى معايير الجودة والأمان الدوائي لتلبية احتياجاتك الصحية
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="card-hover bg-[#F7FAF9] border border-gray-100 rounded-xl p-6 text-center">
                    <div class="w-16 h-16 bg-[#E8F5F2] rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#16A085]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">أدوية أصلية ومعتمدة</h3>
                    <p class="text-gray-600">نوفر قاعدة بيانات واسعة ودقيقة للأدوية والمركبات الطبية المرخصة بالكامل</p>
                </div>

                <!-- Feature 2 -->
                <div class="card-hover bg-[#F7FAF9] border border-gray-100 rounded-xl p-6 text-center">
                    <div class="w-16 h-16 bg-[#E8F5F2] rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#16A085]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">استشارات فورية 24/7</h3>
                    <p class="text-gray-600">فريق صيدلاني متخصص جاهز للرد على استفساراتك الدوائية والجرعات في أي وقت</p>
                </div>

                <!-- Feature 3 -->
                <div class="card-hover bg-[#F7FAF9] border border-gray-100 rounded-xl p-6 text-center">
                    <div class="w-16 h-16 bg-[#E8F5F2] rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#16A085]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">سرعة وسهولة الطلب</h3>
                    <p class="text-gray-600">ابحث عن علاجك المفضّل وقم بشرائه أو حجزه من أقرب صيدلية بكل راحة وسرعة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-[#087F5B] py-16 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold">10K+</div>
                    <div class="mt-2 text-emerald-100">دواء وعلاج مسجل</div>
                </div>
                <div>
                    <div class="text-4xl font-bold">500+</div>
                    <div class="mt-2 text-emerald-100">صيدلية شريكة</div>
                </div>
                <div>
                    <div class="text-4xl font-bold">99%</div>
                    <div class="mt-2 text-emerald-100">دقة المعلومات الدوائية</div>
                </div>
                <div>
                    <div class="text-4xl font-bold">24/7</div>
                    <div class="mt-2 text-emerald-100">استشارات صيدلانية</div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-20 bg-[#F7FAF9]">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                هل تبحث عن دواء معين أو استشارة طبية؟
            </h2>
            <p class="text-gray-600 mb-8 text-lg">
                انضم الآن إلى منصة Line Pharm واحصل على أفضل الخدمات الصحية الدوائية
            </p>
            <a href="{{ route('register') }}" class="inline-block px-8 py-3 bg-[#16A085] text-white rounded-lg font-semibold hover:bg-[#087F5B] transition transform hover:scale-105 shadow-md">
                إنشاء حساب جديد
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#263238] text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-white font-bold mb-4 text-lg">Line Pharm</h3>
                    <p class="text-sm text-gray-400">منصتك الطبية الذكية للبحث عن الأدوية وإدارة الرعاية الصحية</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">روابط سريعة</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-[#16A085] transition">الرئيسية</a></li>
                        <li><a href="#" class="hover:text-[#16A085] transition">من نحن</a></li>
                        <li><a href="#" class="hover:text-[#16A085] transition">الأدوية الشائعة</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">الدعم والسياسات</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-[#16A085] transition">مركز المساعدة</a></li>
                        <li><a href="#" class="hover:text-[#16A085] transition">سياسة الخصوصية</a></li>
                        <li><a href="#" class="hover:text-[#16A085] transition">الشروط والأحكام</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">تواصل معنا</h4>
                    <p class="text-sm text-gray-400">البريد: info@linepharm.com</p>
                    <p class="text-sm text-gray-400 mt-2">الدعم الطبي: +966 5XXXXXX</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} Line Pharm. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

</body>
</html>
