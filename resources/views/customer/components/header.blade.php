<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="{{ route('customer.categories') }}" class="flex items-center space-x-2">
                    <i class="fas fa-plane text-2xl text-orange-500"></i>
                    <span class="text-xl font-bold text-gray-900">Traveloka Tour</span>
                </a>
            </div>

            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('customer.categories') }}" 
                   class="text-gray-700 hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('customer.categories') ? 'text-orange-500' : '' }}">
                    Danh mục Tour
                </a>
                <a href="#" class="text-gray-700 hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium">
                    Về chúng tôi
                </a>
                <a href="#" class="text-gray-700 hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium">
                    Liên hệ
                </a>
            </nav>

            <div class="flex items-center space-x-4">
                @auth
                    <div class="relative user-dropdown">
                        <button id="userDropdown" class="flex items-center space-x-2 text-gray-700 hover:text-orange-500 focus:outline-none">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Hồ sơ
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="bg-orange-500 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-orange-600">
                        Đăng ký
                    </a>
                @endauth

                <a href="#" class="text-gray-700 hover:text-orange-500">
                    <i class="fas fa-search text-xl"></i>
                </a>
                <a href="#" class="text-gray-700 hover:text-orange-500">
                    <i class="fas fa-heart text-xl"></i>
                </a>
            </div>
        </div>
    </div>
</header>

<style>
.user-dropdown {
    position: relative;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userDropdown = document.getElementById('userDropdown');
    const userMenu = document.getElementById('userMenu');
    
    if (userDropdown && userMenu) {
        userDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            userMenu.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
            }
        });
    }
});
</script>

