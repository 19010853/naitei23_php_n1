@php
    use Illuminate\Support\Str;
@endphp

@extends('admin.layouts.app')

@section('page-title', __('common.tour_list'))

@section('content')
    <div style="margin-bottom: 20px;">
        <button class="btn btn-primary" onclick="openTourModal()">
            <i class="fas fa-plus"></i> {{ __('common.add_new_tour') }}
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-wrapper">
        <div class="table-head">
            <div class="table-title">{{ __('common.tour_list') }}</div>
        </div>
        <div style="overflow-x: auto;">
            <table class="admin-table admin-table--fixed">
                <colgroup>
                    <col style="width: 60px">
                    <col style="width: 100px">
                    <col style="width: 180px">
                    <col style="width: 220px">
                    <col style="width: 150px">
                    <col style="width: 130px">
                    <col style="width: 110px">
                    <col style="width: 170px">
                </colgroup>
                <thead>
                    <tr>
                        <th style="text-align: center;">#</th>
                        <th style="text-align: center;">{{ __('common.image') }}</th>
                        <th>{{ __('common.category') }}</th>
                        <th>{{ __('common.tour') }}</th>
                        <th>{{ __('common.location') }}</th>
                        <th style="text-align: center;">{{ __('common.schedules') }}</th>
                        <th style="text-align: center;">{{ __('common.rating') }}</th>
                        <th style="text-align: center;">{{ __('common.actions') }}</th>
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
                                <span class="badge badge-neutral">{{ $tour->category?->name ?? __('common.not_assigned') }}</span>
                            </td>
                            <td class="cell-wrap">
                                <strong style="display: block; margin-bottom: 4px;">{{ $tour->name }}</strong>
                                <small style="color: var(--traveloka-muted); line-height: 1.4;" data-full-content="{{ $tour->description ?? '' }}">{{ Str::limit($tour->description ?? '', 60) }}</small>
                            </td>
                            <td>
                                <span class="cell-ellipsis">{{ $tour->location }}</span>
                            </td>
                            <td style="text-align: center;">{{ $tour->schedules_count }} {{ __('common.schedules') }}</td>
                            <td style="text-align: center;">
                                <span style="display: inline-flex; align-items: center; gap: 4px; color: var(--traveloka-orange); font-weight: 600;">
                                    <i class="fas fa-star"></i>
                                    {{ number_format((float) ($tour->reviews_avg_rating ?? 0), 1) }}/5
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                    <button class="btn btn-warning btn-sm btn-action edit-tour-btn" 
                                            data-tour-id="{{ $tour->id }}"
                                            data-tour-name="{{ $tour->name }}"
                                            data-tour-category-id="{{ $tour->category_id }}"
                                            data-tour-description="{{ $tour->description ?? '' }}"
                                            data-tour-location="{{ $tour->location }}"
                                            data-tour-image-url="{{ $tour->image_url ?? '' }}"
                                            title="{{ __('common.edit_tour') }}">
                                        <i class="fas fa-edit"></i>
                                        <span>{{ __('common.edit') }}</span>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-action delete-tour-btn" 
                                            data-tour-id="{{ $tour->id }}"
                                            title="{{ __('common.delete_tour') }}">
                                        <i class="fas fa-trash"></i>
                                        <span>{{ __('common.delete') }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">{{ __('common.no_tours_found') }}</td>
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

    // Edit Tour Button Handler
    document.addEventListener('DOMContentLoaded', function() {
        // Handle edit tour buttons
        document.querySelectorAll('.edit-tour-btn').forEach(button => {
            button.addEventListener('click', function() {
                const tourId = this.getAttribute('data-tour-id');
                const tourName = this.getAttribute('data-tour-name');
                const tourCategoryId = this.getAttribute('data-tour-category-id');
                const tourDescription = this.getAttribute('data-tour-description');
                const tourLocation = this.getAttribute('data-tour-location');
                const tourImageUrl = this.getAttribute('data-tour-image-url');
                
                editTour(tourId, tourName, tourDescription, tourLocation, tourImageUrl, tourCategoryId);
            });
        });

        // Handle delete tour buttons
        document.querySelectorAll('.delete-tour-btn').forEach(button => {
            button.addEventListener('click', function() {
                const tourId = this.getAttribute('data-tour-id');
                deleteTour(tourId);
            });
        });
    });

    // Edit Tour
    function editTour(id, name, description, location, imageUrl, categoryId) {
        const modal = document.getElementById('editTourModal');
        if (!modal) {
            console.error('Edit tour modal not found');
            alert(@json(__('common.error_loading_modal')));
            return;
        }
        
        modal.style.display = 'block';
        
        const tourIdInput = document.getElementById('edit_tour_id');
        const nameInput = document.getElementById('edit_tour_name');
        const descriptionInput = document.getElementById('edit_tour_description');
        const locationInput = document.getElementById('edit_tour_location');
        const form = document.getElementById('editTourForm');
        
        if (!tourIdInput || !nameInput || !descriptionInput || !locationInput || !form) {
            console.error('Required form elements not found');
            return;
        }
        
        tourIdInput.value = id || '';
        nameInput.value = name || '';
        descriptionInput.value = description || '';
        locationInput.value = location || '';
        const categorySelect = document.getElementById('edit_tour_category_id');
        if (categorySelect) {
            categorySelect.value = categoryId || '';
        }
        form.action = `/admin/tours/${id}`;
        
        const img = document.getElementById('edit_tour_current_image');
        if (img) {
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
        if (!id) {
            console.error('Tour ID is missing');
            return;
        }
        
        const confirmMessage = @json(__('common.confirm_delete_tour'));
        if (confirm(confirmMessage)) {
            fetch(`/admin/tours/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok) {
                    window.location.reload();
                } else {
                    const errorMessage = data.message || @json(__('common.cannot_delete_tour_with_schedules'));
                    alert(errorMessage);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(@json(__('common.cannot_delete_tour_with_schedules')));
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
