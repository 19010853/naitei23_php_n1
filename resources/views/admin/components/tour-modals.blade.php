<!-- Tour Modal -->
<div id="tourModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>{{ __('common.add_new_tour_modal_title') }}</h3>
            <span class="close" onclick="document.getElementById('tourModal').style.display='none'">&times;</span>
        </div>
        <form id="tourForm" action="{{ route('admin.tours.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>{{ __('common.tour_name') }}</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('common.category') }}</label>
                <select name="category_id" class="form-control" required>
                    <option value="">{{ __('common.select_category') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('common.slug') }}</label>
                <input type="text" name="slug" class="form-control" placeholder="{{ __('common.auto_generate_from_name') }}">
            </div>
            <div class="form-group">
                <label>{{ __('common.description') }}</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>{{ __('common.location') }}</label>
                <input type="text" name="location" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('common.image') }}</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('tourModal').style.display='none'">{{ __('common.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('common.add') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Tour Modal -->
<div id="editTourModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>{{ __('common.edit_tour_modal_title') }}</h3>
            <span class="close" onclick="document.getElementById('editTourModal').style.display='none'">&times;</span>
        </div>
        <form id="editTourForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="tour_id" id="edit_tour_id">
            <div class="form-group">
                <label>{{ __('common.tour_name') }}</label>
                <input type="text" name="name" id="edit_tour_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('common.category') }}</label>
                <select name="category_id" id="edit_tour_category_id" class="form-control" required>
                    <option value="">{{ __('common.select_category') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('common.slug') }}</label>
                <input type="text" name="slug" id="edit_tour_slug" class="form-control" placeholder="{{ __('common.auto_generate_from_name') }}">
            </div>
            <div class="form-group">
                <label>{{ __('common.description') }}</label>
                <textarea name="description" id="edit_tour_description" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>{{ __('common.location') }}</label>
                <input type="text" name="location" id="edit_tour_location" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('common.new_image') }}</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <div style="margin-top: 10px;">
                    <label>{{ __('common.current_image') }}:</label><br>
                    <img id="edit_tour_current_image" src="" alt="Current image" style="max-width: 200px; max-height: 200px; margin-top: 10px; display: none; border-radius: 4px;">
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editTourModal').style.display='none'">{{ __('common.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
            </div>
        </form>
    </div>
</div>
