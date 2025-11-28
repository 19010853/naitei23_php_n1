<div id="categoryModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>{{ __('common.add_new_category') }}</h3>
            <span class="close" onclick="document.getElementById('categoryModal').style.display='none'">&times;</span>
        </div>
        <form id="categoryForm" action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>{{ __('common.category_name') }}</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('common.slug') }}</label>
                <input type="text" name="slug" class="form-control" placeholder="{{ __('common.auto_generate_from_name') }}">
            </div>
            <div class="form-group">
                <label>{{ __('common.description') }}</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>{{ __('common.image') }}</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
        </form>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('categoryModal').style.display='none'">{{ __('common.cancel') }}</button>
            <button type="submit" form="categoryForm" class="btn btn-primary">{{ __('common.add') }}</button>
        </div>
    </div>
</div>
<div id="editCategoryModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header modal-header-edit">
            <h3>{{ __('common.edit_category') }}</h3>
            <span class="close" onclick="document.getElementById('editCategoryModal').style.display='none'">&times;</span>
        </div>
        <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_category_id" name="category_id">
            <div class="form-group">
                <label>{{ __('common.category_name') }}</label>
                <input type="text" name="name" id="edit_category_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('common.slug') }}</label>
                <input type="text" name="slug" id="edit_category_slug" class="form-control" placeholder="{{ __('common.auto_generate_from_name') }}">
            </div>
            <div class="form-group">
                <label>{{ __('common.description') }}</label>
                <textarea name="description" id="edit_category_description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>{{ __('common.new_image') }}</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <div style="margin-top: 10px;">
                    <label style="font-size: 13px; color: var(--traveloka-muted, #6b7b93); font-weight: normal;">{{ __('common.current_image') }}:</label><br>
                    <img id="edit_category_current_image" src="" alt="Current image" style="max-width: 200px; max-height: 200px; margin-top: 8px; display: none; border-radius: 8px; border: 1px solid var(--traveloka-border, #e4ecf7);">
                </div>
            </div>
        </form>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('editCategoryModal').style.display='none'">{{ __('common.cancel') }}</button>
            <button type="submit" form="editCategoryForm" class="btn btn-primary">{{ __('common.update') }}</button>
        </div>
    </div>
</div>

