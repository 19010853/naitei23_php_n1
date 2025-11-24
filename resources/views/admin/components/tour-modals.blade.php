<!-- Tour Modal -->
<div id="tourModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Thêm tour mới</h3>
            <span class="close" onclick="document.getElementById('tourModal').style.display='none'">&times;</span>
        </div>
        <form id="tourForm" action="{{ route('admin.tours.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Tên tour</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Slug</label>
                <input type="text" name="slug" class="form-control" placeholder="Tự động tạo từ tên nếu để trống">
            </div>
            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Địa điểm</label>
                <input type="text" name="location" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Hình ảnh</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('tourModal').style.display='none'">Hủy</button>
                <button type="submit" class="btn btn-primary">Thêm</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Tour Modal -->
<div id="editTourModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Sửa tour</h3>
            <span class="close" onclick="document.getElementById('editTourModal').style.display='none'">&times;</span>
        </div>
        <form id="editTourForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="tour_id" id="edit_tour_id">
            <div class="form-group">
                <label>Tên tour</label>
                <input type="text" name="name" id="edit_tour_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Slug</label>
                <input type="text" name="slug" id="edit_tour_slug" class="form-control" placeholder="Tự động tạo từ tên nếu để trống">
            </div>
            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" id="edit_tour_description" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Địa điểm</label>
                <input type="text" name="location" id="edit_tour_location" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Hình ảnh mới</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <div style="margin-top: 10px;">
                    <label>Hình ảnh hiện tại:</label><br>
                    <img id="edit_tour_current_image" src="" alt="Current image" style="max-width: 200px; max-height: 200px; margin-top: 10px; display: none; border-radius: 4px;">
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editTourModal').style.display='none'">Hủy</button>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border: 1px solid #888;
    width: 90%;
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
    padding: 20px;
    background-color: var(--traveloka-primary, #FF6B35);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 8px 8px 0 0;
}

.modal-header h3 {
    margin: 0;
}

.close {
    color: white;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    opacity: 0.7;
}

.form-group {
    margin-bottom: 15px;
    padding: 0 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: var(--traveloka-primary, #FF6B35);
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    margin-left: 8px;
}

.btn-primary {
    background-color: var(--traveloka-primary, #FF6B35);
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-warning {
    background-color: #ffc107;
    color: #000;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
}
</style>

