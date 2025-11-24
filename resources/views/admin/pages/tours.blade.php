@php
    use Illuminate\Support\Str;
@endphp

@extends('admin.layouts.app')

@section('page-title', 'Danh sách tour')

@section('content')
    <div style="margin-bottom: 20px;">
        <button class="btn btn-primary" onclick="openTourModal()">
            <i class="fas fa-plus"></i> Thêm tour mới
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-wrapper">
        <div class="table-head">
            <div class="table-title">Tour hiện có</div>
        </div>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">#</th>
                        <th style="width: 100px; text-align: center;">Hình ảnh</th>
                        <th style="min-width: 250px;">Tour</th>
                        <th style="width: 180px;">Địa điểm</th>
                        <th style="width: 120px; text-align: center;">Lịch trình</th>
                        <th style="width: 100px; text-align: center;">Đánh giá</th>
                        <th style="width: 160px; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tours as $tour)
                        <tr>
                            <td style="text-align: center;">{{ $tour->id }}</td>
                            <td style="text-align: center;">
                                @if($tour->image_url)
                                    <img src="{{ asset($tour->image_url) }}" alt="{{ $tour->name }}" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <span style="color: var(--traveloka-muted);">-</span>
                                @endif
                            </td>
                            <td>
                                <strong style="display: block; margin-bottom: 4px;">{{ $tour->name }}</strong>
                                <small style="color: var(--traveloka-muted); line-height: 1.4;">{{ Str::limit($tour->description ?? '', 70) }}</small>
                            </td>
                            <td>{{ $tour->location }}</td>
                            <td style="text-align: center;">{{ $tour->schedules_count }} lịch</td>
                            <td style="text-align: center;">
                                <span style="display: inline-flex; align-items: center; gap: 4px; color: var(--traveloka-orange); font-weight: 600;">
                                    <i class="fas fa-star"></i>
                                    {{ number_format((float) ($tour->reviews_avg_rating ?? 0), 1) }}/5
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                    <button class="btn btn-warning btn-sm btn-action" 
                                            onclick="editTour({{ $tour->id }}, '{{ addslashes($tour->name) }}', '{{ addslashes($tour->description ?? '') }}', '{{ addslashes($tour->location) }}', '{{ $tour->image_url ?? '' }}')"
                                            title="Sửa tour">
                                        <i class="fas fa-edit"></i>
                                        <span>Sửa</span>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-action" 
                                            onclick="deleteTour({{ $tour->id }})"
                                            title="Xóa tour">
                                        <i class="fas fa-trash"></i>
                                        <span>Xóa</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">Chưa có tour nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $tours->links() }}
        </div>
    </div>

    @include('admin.components.tour-modals')
@endsection

@push('scripts')
<script>
    // Open Tour Modal
    function openTourModal() {
        const modal = document.getElementById('tourModal');
        if (modal) {
            modal.style.display = 'block';
            const form = document.getElementById('tourForm');
            if (form) {
                form.reset();
            }
        } else {
            console.error('Tour modal not found');
        }
    }

    // Edit Tour
    function editTour(id, name, description, location, imageUrl) {
        const modal = document.getElementById('editTourModal');
        if (modal) {
            modal.style.display = 'block';
            document.getElementById('edit_tour_id').value = id;
            document.getElementById('edit_tour_name').value = name || '';
            document.getElementById('edit_tour_description').value = description || '';
            document.getElementById('edit_tour_location').value = location || '';
            document.getElementById('editTourForm').action = `/admin/tours/${id}`;
            
            const img = document.getElementById('edit_tour_current_image');
            if (imageUrl && imageUrl !== '' && imageUrl !== 'null') {
                img.src = `/${imageUrl}`;
                img.style.display = 'block';
            } else {
                img.style.display = 'none';
            }
        }
    }

    // Delete Tour
    function deleteTour(id) {
        if (confirm('Bạn có chắc chắn muốn xóa tour này?')) {
            fetch(`/admin/tours/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    const data = await response.json();
                    alert(data.message || 'Không thể xóa tour này.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa tour.');
            });
        }
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        const tourModal = document.getElementById('tourModal');
        const editTourModal = document.getElementById('editTourModal');
        if (tourModal && event.target == tourModal) {
            tourModal.style.display = 'none';
        }
        if (editTourModal && event.target == editTourModal) {
            editTourModal.style.display = 'none';
        }
    });
</script>
@endpush
