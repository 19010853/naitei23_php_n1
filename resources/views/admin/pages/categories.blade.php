@php
    use Illuminate\Support\Str;
@endphp

@extends('admin.layouts.app')

@section('page-title', __('common.tour_categories'))

@section('content')
    <div style="margin-bottom: 20px;">
        <button class="btn btn-primary" onclick="openCategoryModal()">
            <i class="fas fa-plus"></i> {{ __('common.add_new_category') }}
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-wrapper">
        <div class="table-head">
            <div class="table-title">{{ __('common.tour_categories') }}</div>
        </div>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">#</th>
                        <th style="width: 100px; text-align: center;">{{ __('common.image') }}</th>
                        <th style="min-width: 220px;">{{ __('common.category') }}</th>
                        <th style="width: 180px;">{{ __('common.slug') }}</th>
                        <th style="width: 120px; text-align: center;">{{ __('common.tours') }}</th>
                        <th style="width: 160px; text-align: center;">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td style="text-align: center;">{{ $category->id }}</td>
                            <td style="text-align: center;">
                                @if($category->image_url)
                                    <img src="{{ asset($category->image_url) }}" alt="{{ $category->name }}"
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <span style="color: var(--traveloka-muted);">-</span>
                                @endif
                            </td>
                            <td>
                                <strong style="display: block; margin-bottom: 4px;">{{ $category->name }}</strong>
                                <small style="color: var(--traveloka-muted); line-height: 1.4;" data-full-content="{{ $category->description ?? '' }}">{{ Str::limit($category->description ?? '', 80) }}</small>
                            </td>
                            <td>
                                <span style="display: block; overflow: hidden; text-overflow: ellipsis;">{{ $category->slug }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-neutral">{{ $category->tours_count }}</span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                    <button class="btn btn-warning btn-sm btn-action edit-category-btn"
                                            data-category-id="{{ $category->id }}"
                                            data-category-name="{{ $category->name }}"
                                            data-category-slug="{{ $category->slug }}"
                                            data-category-description="{{ $category->description ?? '' }}"
                                            data-category-image-url="{{ $category->image_url ?? '' }}"
                                            title="{{ __('common.edit_category') }}">
                                        <i class="fas fa-edit"></i>
                                        <span>{{ __('common.edit') }}</span>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-action delete-category-btn"
                                            data-category-id="{{ $category->id }}"
                                            title="{{ __('common.delete_category') }}">
                                        <i class="fas fa-trash"></i>
                                        <span>{{ __('common.delete') }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">{{ __('common.no_categories_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $categories->links() }}
        </div>
    </div>

    @include('admin.components.category-modals')
@endsection

@push('scripts')
<script>
    function openCategoryModal() {
        const modal = document.getElementById('categoryModal');
        if (modal) {
            modal.style.display = 'block';
            const form = document.getElementById('categoryForm');
            if (form) {
                form.reset();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.edit-category-btn').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-category-id');
                const name = this.getAttribute('data-category-name');
                const slug = this.getAttribute('data-category-slug');
                const description = this.getAttribute('data-category-description');
                const imageUrl = this.getAttribute('data-category-image-url');

                editCategory(categoryId, name, slug, description, imageUrl);
            });
        });

        document.querySelectorAll('.delete-category-btn').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-category-id');
                deleteCategory(categoryId);
            });
        });
    });

    function editCategory(id, name, slug, description, imageUrl) {
        const modal = document.getElementById('editCategoryModal');
        if (!modal) {
            console.error('Edit category modal not found');
            alert(@json(__('common.error_loading_modal')));
            return;
        }

        modal.style.display = 'block';

        document.getElementById('edit_category_id').value = id || '';
        document.getElementById('edit_category_name').value = name || '';
        document.getElementById('edit_category_slug').value = slug || '';
        document.getElementById('edit_category_description').value = description || '';
        const form = document.getElementById('editCategoryForm');
        if (form) {
            form.action = `/admin/categories/${id}`;
        }

        const img = document.getElementById('edit_category_current_image');
        if (img) {
            if (imageUrl) {
                img.src = `/${imageUrl}`;
                img.style.display = 'block';
            } else {
                img.style.display = 'none';
            }
        }
    }

    function deleteCategory(id) {
        if (!id) {
            return;
        }

        const confirmMessage = @json(__('common.confirm_delete_category'));
        if (confirm(confirmMessage)) {
            fetch(`/admin/categories/${id}`, {
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
                        alert(data.message || @json(__('common.cannot_delete_category_with_tours')));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(@json(__('common.cannot_delete_category_with_tours')));
                });
        }
    }

    document.addEventListener('click', function(event) {
        const createModal = document.getElementById('categoryModal');
        const editModal = document.getElementById('editCategoryModal');
        if (createModal && event.target === createModal) {
            createModal.style.display = 'none';
        }
        if (editModal && event.target === editModal) {
            editModal.style.display = 'none';
        }
    });
</script>
@endpush

