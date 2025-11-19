@extends('admin.layouts.app')

@section('page-title', 'Lịch trình Tour')

@section('content')
    <div style="margin-bottom: 20px;">
        <button class="btn btn-primary" onclick="openTourScheduleModal()">
            <i class="fas fa-plus"></i> Thêm lịch trình mới
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-wrapper">
        <div class="table-head">
            <div class="table-title">Lịch trình Tour hiện có</div>
        </div>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">#</th>
                        <th style="min-width: 200px;">Tour</th>
                        <th style="width: 140px; text-align: center;">Ngày khởi hành</th>
                        <th style="width: 140px; text-align: center;">Ngày kết thúc</th>
                        <th style="width: 120px; text-align: right;">Giá</th>
                        <th style="width: 100px; text-align: center;">Số người</th>
                        <th style="width: 100px; text-align: center;">Booking</th>
                        <th style="width: 160px; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $schedule)
                        <tr>
                            <td style="text-align: center;">{{ $schedule->id }}</td>
                            <td>
                                <strong style="display: block; margin-bottom: 4px;">{{ $schedule->tour->name ?? 'N/A' }}</strong>
                                <small style="color: var(--traveloka-muted);">{{ $schedule->tour->location ?? '-' }}</small>
                            </td>
                            <td style="text-align: center;">
                                {{ $schedule->start_date->format('d/m/Y') }}
                            </td>
                            <td style="text-align: center;">
                                {{ $schedule->end_date->format('d/m/Y') }}
                            </td>
                            <td style="text-align: right; font-weight: 600; color: var(--traveloka-orange);">
                                {{ number_format($schedule->price, 0, '.', ',') }} VNĐ
                            </td>
                            <td style="text-align: center;">
                                {{ $schedule->max_participants }} người
                            </td>
                            <td style="text-align: center;">
                                <span class="status-badge status-{{ $schedule->bookings_count > 0 ? 'success' : 'pending' }}">
                                    {{ $schedule->bookings_count }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                    <button class="btn btn-warning btn-sm btn-action" 
                                            onclick="editTourSchedule({{ $schedule->id }}, {{ $schedule->tour_id }}, '{{ $schedule->start_date->format('Y-m-d') }}', '{{ $schedule->end_date->format('Y-m-d') }}', {{ $schedule->price }}, {{ $schedule->max_participants }})"
                                            title="Sửa lịch trình">
                                        <i class="fas fa-edit"></i>
                                        <span>Sửa</span>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-action" 
                                            onclick="deleteTourSchedule({{ $schedule->id }})"
                                            title="Xóa lịch trình">
                                        <i class="fas fa-trash"></i>
                                        <span>Xóa</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">Chưa có lịch trình nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $schedules->links() }}
        </div>
    </div>

    @include('admin.components.tour-schedule-modals')
@endsection

@push('scripts')
<script>
    // Open Tour Schedule Modal
    function openTourScheduleModal() {
        const modal = document.getElementById('tourScheduleModal');
        if (modal) {
            modal.style.display = 'block';
            const form = document.getElementById('tourScheduleForm');
            if (form) {
                form.reset();
            }
        } else {
            console.error('Tour schedule modal not found');
        }
    }

    // Edit Tour Schedule
    function editTourSchedule(id, tourId, startDate, endDate, price, maxParticipants) {
        const modal = document.getElementById('editTourScheduleModal');
        if (modal) {
            modal.style.display = 'block';
            document.getElementById('edit_schedule_id').value = id;
            document.getElementById('edit_schedule_tour_id').value = tourId;
            document.getElementById('edit_schedule_start_date').value = startDate;
            document.getElementById('edit_schedule_end_date').value = endDate;
            document.getElementById('edit_schedule_price').value = price;
            document.getElementById('edit_schedule_max_participants').value = maxParticipants;
            document.getElementById('editTourScheduleForm').action = `/admin/tour-schedules/${id}`;
        }
    }

    // Delete Tour Schedule
    function deleteTourSchedule(id) {
        if (confirm('Bạn có chắc chắn muốn xóa lịch trình này?')) {
            fetch(`/admin/tour-schedules/${id}`, {
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
                    alert(data.message || 'Không thể xóa lịch trình này.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa lịch trình.');
            });
        }
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        const scheduleModal = document.getElementById('tourScheduleModal');
        const editScheduleModal = document.getElementById('editTourScheduleModal');
        if (scheduleModal && event.target == scheduleModal) {
            scheduleModal.style.display = 'none';
        }
        if (editScheduleModal && event.target == editScheduleModal) {
            editScheduleModal.style.display = 'none';
        }
    });
</script>
@endpush

