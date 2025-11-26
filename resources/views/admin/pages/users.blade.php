@extends('admin.layouts.app')

@section('page-title', __('common.user_management'))

@section('content')
    <div style="margin-bottom: 20px;">
        <button class="btn btn-primary" onclick="openUserModal()">
            <i class="fas fa-plus"></i> {{ __('common.add_new_user') }}
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-wrapper">
        <div class="table-head">
            <div>
                <div class="table-title">{{ __('common.user_list') }}</div>
                <small style="color: var(--traveloka-muted);">
                    {{ __('common.total_accounts', ['count' => $users->total()]) }}
                </small>
            </div>
            <div class="table-actions">
                <form id="filterForm" method="GET" action="{{ route('admin.users') }}" class="search-filter-form">
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input
                            type="search"
                            name="query"
                            id="searchQuery"
                            placeholder="{{ __('common.search_by_name_or_email') }}"
                            class="search-input"
                            value="{{ request('query', '') }}"
                        >
                    </div>
                    <select name="role_id" id="roleFilter" class="role-filter-select">
                        <option value="">{{ __('common.all_roles') }}</option>
                        @foreach($roles ?? [] as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" id="searchBtn" class="btn btn-primary search-btn">
                        <i class="fas fa-search"></i>
                        <span>{{ __('common.search') }}</span>
                    </button>
                </form>
            </div>
        </div>

        <table class="admin-table" id="users-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('common.user_info') }}</th>
                    <th>{{ __('common.role') }}</th>
                    <th>{{ __('common.email_verified') }}</th>
                    <th>{{ __('common.created_date') }}</th>
                    <th style="width: 160px; text-align: center;">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            @include('admin.pages.users-table')
        </table>

        <div class="pagination" id="paginationContainer">
            {{ $users->links() }}
        </div>
    </div>

    @include('admin.components.user-modals')
@endsection

@push('scripts')
<script>
    // Filter and Search Handler
    document.addEventListener('DOMContentLoaded', function() {
        const roleFilter = document.getElementById('roleFilter');
        const searchQuery = document.getElementById('searchQuery');
        const searchBtn = document.getElementById('searchBtn');
        const filterForm = document.getElementById('filterForm');
        
        // Handle role filter change
        if (roleFilter) {
            roleFilter.addEventListener('change', function() {
                loadUsersWithoutRefresh();
            });
        }
        
        // Handle search button click
        if (searchBtn) {
            searchBtn.addEventListener('click', function() {
                loadUsersWithoutRefresh();
            });
        }
        
        // Handle search input Enter key
        if (searchQuery) {
            searchQuery.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    loadUsersWithoutRefresh();
                }
            });
        }
        
        // Load users without page refresh
        function loadUsersWithoutRefresh() {
            const query = searchQuery ? searchQuery.value : '';
            const roleId = roleFilter ? roleFilter.value : '';
            
            // Build URL with query parameters
            const params = new URLSearchParams();
            if (query) params.set('query', query);
            if (roleId) params.set('role_id', roleId);
            
            const url = '{{ route("admin.users") }}' + (params.toString() ? '?' + params.toString() : '');
            
            // Update URL without refresh
            window.history.pushState({}, '', url);
            
            // Show loading state
            const tableBody = document.querySelector('#users-table tbody');
            const paginationContainer = document.getElementById('paginationContainer');
            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Loading...</td></tr>';
            }
            
            // Fetch new data
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.headers.get('content-type')?.includes('application/json')) {
                    return response.json();
                }
                // Fallback to HTML parsing if JSON not available
                return response.text().then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTableBody = doc.querySelector('#users-table tbody');
                    const newPagination = doc.querySelector('#paginationContainer');
                    return {
                        table_html: newTableBody ? newTableBody.innerHTML : '',
                        pagination_html: newPagination ? newPagination.innerHTML : ''
                    };
                });
            })
            .then(data => {
                // Update table body
                if (data.table_html && tableBody) {
                    tableBody.innerHTML = data.table_html;
                }
                
                // Update pagination
                if (data.pagination_html && paginationContainer) {
                    paginationContainer.innerHTML = data.pagination_html;
                }
                
                // Re-initialize edit and delete button handlers
                initializeUserButtons();
                
                // Re-initialize pagination links to use AJAX
                initializePaginationLinks();
                
                // Re-initialize table tooltips
                if (window.AdminUI && window.AdminUI.initTableTooltips) {
                    window.AdminUI.initTableTooltips();
                }
            })
            .catch(error => {
                console.error('Error loading users:', error);
                if (tableBody) {
                    tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: red;">Error loading users. Please refresh the page.</td></tr>';
                }
            });
        }
        
        // Initialize pagination links to use AJAX
        function initializePaginationLinks() {
            document.querySelectorAll('#paginationContainer a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    if (url) {
                        // Update URL
                        window.history.pushState({}, '', url);
                        
                        // Load users
                        const tableBody = document.querySelector('#users-table tbody');
                        const paginationContainer = document.getElementById('paginationContainer');
                        if (tableBody) {
                            tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Loading...</td></tr>';
                        }
                        
                        fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (response.headers.get('content-type')?.includes('application/json')) {
                                return response.json();
                            }
                            return response.text().then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newTableBody = doc.querySelector('#users-table tbody');
                                const newPagination = doc.querySelector('#paginationContainer');
                                return {
                                    table_html: newTableBody ? newTableBody.innerHTML : '',
                                    pagination_html: newPagination ? newPagination.innerHTML : ''
                                };
                            });
                        })
                        .then(data => {
                            if (data.table_html && tableBody) {
                                tableBody.innerHTML = data.table_html;
                            }
                            if (data.pagination_html && paginationContainer) {
                                paginationContainer.innerHTML = data.pagination_html;
                            }
                            initializeUserButtons();
                            initializePaginationLinks();
                            
                            // Re-initialize table tooltips
                            if (window.AdminUI && window.AdminUI.initTableTooltips) {
                                window.AdminUI.initTableTooltips();
                            }
                        })
                        .catch(error => {
                            console.error('Error loading users:', error);
                            window.location.href = url; // Fallback to full page reload
                        });
                    }
                });
            });
        }
        
        // Initialize user buttons (edit and delete)
        function initializeUserButtons() {
            // Remove existing event listeners by cloning
            document.querySelectorAll('.edit-user-btn').forEach(button => {
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
            });
            
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
            });
            
            // Re-attach event listeners
            document.querySelectorAll('.edit-user-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    const userEmail = this.getAttribute('data-user-email');
                    const userRolesJson = this.getAttribute('data-user-roles');
                    
                    let roleIds = [];
                    try {
                        roleIds = JSON.parse(userRolesJson || '[]');
                    } catch (e) {
                        console.error('Error parsing role IDs:', e);
                    }
                    
                    editUser(userId, userName, userEmail, roleIds);
                });
            });

            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    deleteUser(userId);
                });
            });
        }
        
        // Initial setup
        initializeUserButtons();
        initializePaginationLinks();
    });

    // Open User Modal
    function openUserModal() {
        const modal = document.getElementById('userModal');
        if (modal) {
            modal.style.display = 'block';
            const form = document.getElementById('userForm');
            if (form) {
                form.reset();
                // Clear all role checkboxes
                document.querySelectorAll('#userForm input[type="checkbox"][name="role_ids[]"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
        } else {
            console.error('User modal not found');
        }
    }

    // Edit User
    function editUser(id, name, email, roleIds) {
        console.log('editUser called with:', { id, name, email, roleIds });
        const modal = document.getElementById('editUserModal');
        if (!modal) {
            console.error('Edit user modal not found');
            alert(@json(__('common.error_loading_modal')));
            return;
        }
        
        modal.style.display = 'block';
        
        const userIdInput = document.getElementById('edit_user_id');
        const nameInput = document.getElementById('edit_user_name');
        const emailInput = document.getElementById('edit_user_email');
        const form = document.getElementById('editUserForm');
        
        if (!userIdInput || !nameInput || !emailInput || !form) {
            console.error('Required form elements not found');
            return;
        }
        
        userIdInput.value = id || '';
        nameInput.value = name || '';
        emailInput.value = email || '';
        form.action = `/admin/users/${id}`;
        
        // Clear and set role checkboxes
        const roleCheckboxes = document.querySelectorAll('#editUserForm input[type="checkbox"][name="role_ids[]"]');
        roleCheckboxes.forEach(checkbox => {
            const checkboxValue = parseInt(checkbox.value);
            checkbox.checked = Array.isArray(roleIds) && roleIds.includes(checkboxValue);
        });
    }

    // Delete User
    function deleteUser(id) {
        console.log('deleteUser called with id:', id);
        if (!id) {
            console.error('User ID is missing');
            return;
        }
        
        const confirmMessage = @json(__('common.confirm_delete_user'));
        if (confirm(confirmMessage)) {
            fetch(`/admin/users/${id}`, {
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
                    const successMessage = data.message || @json(__('common.user_deleted_successfully'));
                    alert(successMessage);
                    window.location.reload();
                } else {
                    const errorMessage = data.message || @json(__('common.error_deleting_user'));
                    alert(errorMessage);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(@json(__('common.error_deleting_user')));
            });
        }
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        const userModal = document.getElementById('userModal');
        const editUserModal = document.getElementById('editUserModal');
        if (userModal && event.target == userModal) {
            userModal.style.display = 'none';
        }
        if (editUserModal && event.target == editUserModal) {
            editUserModal.style.display = 'none';
        }
    });
</script>
@endpush

