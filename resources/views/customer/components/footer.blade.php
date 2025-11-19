<footer class="bg-gray-50 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-orange-500 text-2xl font-bold mb-4 flex items-center">
                    <i class="fas fa-plane mr-2"></i>
                    Traveloka Tour
                </h3>
                <p class="text-gray-600 text-sm">Khám phá thế giới với những chuyến du lịch tuyệt vời nhất</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-4">Liên kết nhanh</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><a href="{{ route('customer.categories') }}" class="hover:text-orange-500">Danh mục Tour</a></li>
                    <li><a href="#" class="hover:text-orange-500">Về chúng tôi</a></li>
                    <li><a href="#" class="hover:text-orange-500">Liên hệ</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-4">Hỗ trợ</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><a href="#" class="hover:text-orange-500">Câu hỏi thường gặp</a></li>
                    <li><a href="#" class="hover:text-orange-500">Chính sách hủy</a></li>
                    <li><a href="#" class="hover:text-orange-500">Điều khoản sử dụng</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-4">Theo dõi chúng tôi</h4>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-600 hover:text-orange-500"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-gray-600 hover:text-orange-500"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-600 hover:text-orange-500"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-600 hover:text-orange-500"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-200 mt-8 pt-8 text-center text-sm text-gray-600">
            <p>&copy; {{ date('Y') }} Traveloka Tour. All rights reserved.</p>
        </div>
    </div>
</footer>

