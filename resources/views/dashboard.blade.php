<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- بطاقة الترحيب -->
            <div class="bg-white shadow-lg sm:rounded-lg p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    مرحباً {{ Auth::user()->name }}! 👋
                </h1>
                <p class="text-gray-600 mb-6">لقد سجلت الدخول بنجاح إلى حسابك.</p>

                <!-- الإحصائيات -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- بطاقة المستخدمين -->
                    <div
                        class="flex items-center p-5 bg-gradient-to-r from-blue-100 to-blue-200 rounded-lg shadow hover:shadow-md transition duration-300">
                        <div class="p-3 bg-blue-500 rounded-full text-white mr-4">
                            <!-- أيقونة مستخدم -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A4 4 0 0110 16h4a4 4 0 014.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-700 font-medium">عدد المستخدمين</p>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\User::count() }}</p>
                        </div>
                    </div>

                    <!-- بطاقة الحالة -->
                    <div
                        class="flex items-center p-5 bg-gradient-to-r from-green-100 to-green-200 rounded-lg shadow hover:shadow-md transition duration-300">
                        <div class="p-3 bg-green-500 rounded-full text-white mr-4">
                            <!-- أيقونة الحالة -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-700 font-medium">حالتك</p>
                            <p class="text-2xl font-bold text-gray-900">نشط ✅</p>
                        </div>
                    </div>

                    <!-- بطاقة العضوية -->
                    <div
                        class="flex items-center p-5 bg-gradient-to-r from-purple-100 to-purple-200 rounded-lg shadow hover:shadow-md transition duration-300">
                        <div class="p-3 bg-purple-500 rounded-full text-white mr-4">
                            <!-- أيقونة التقويم -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-700 font-medium">عضو منذ</p>
                            <p class="text-gray-900">{{ Auth::user()->created_at->format('Y-m-d') }}</p>
                        </div>
                    </div>
                </div>

                <!-- زر لوحة التحكم -->
                <div class="mt-8 flex justify-center">
                    <a href="{{ route('admin.index') }}"
                        class="bg-gray-800 hover:bg-gray-900 text-white font-semibold text-lg px-8 py-3 rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                        🚀 الذهاب إلى لوحة التحكم
                    </a>
                </div>
                <div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
