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
</style>

<!-- Tour Schedule Modal -->
<div id="tourScheduleModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Thêm lịch trình tour mới</h3>
            <span class="close" onclick="document.getElementById('tourScheduleModal').style.display='none'">&times;</span>
        </div>
        <form id="tourScheduleForm" action="{{ route('admin.tour-schedules.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Tour <span style="color: red;">*</span></label>
                <select name="tour_id" class="form-control" required>
                    <option value="">Chọn tour</option>
                    @foreach($tours as $tour)
                        <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Ngày khởi hành <span style="color: red;">*</span></label>
                <input type="date" name="start_date" class="form-control" required min="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label>Ngày kết thúc <span style="color: red;">*</span></label>
                <input type="date" name="end_date" class="form-control" required min="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label>Giá (VNĐ) <span style="color: red;">*</span></label>
                <input type="number" name="price" class="form-control" required min="0" step="1000" placeholder="Ví dụ: 5000000">
            </div>
            <div class="form-group">
                <label>Số người tối đa <span style="color: red;">*</span></label>
                <input type="number" name="max_participants" class="form-control" required min="1" placeholder="Ví dụ: 20">
            </div>
            <div class="form-group" style="text-align: right; margin-top: 20px; padding-bottom: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('tourScheduleModal').style.display='none'">Hủy</button>
                <button type="submit" class="btn btn-primary">Thêm</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Tour Schedule Modal -->
<div id="editTourScheduleModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Sửa lịch trình tour</h3>
            <span class="close" onclick="document.getElementById('editTourScheduleModal').style.display='none'">&times;</span>
        </div>
        <form id="editTourScheduleForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="schedule_id" id="edit_schedule_id">
            <div class="form-group">
                <label>Tour <span style="color: red;">*</span></label>
                <select name="tour_id" id="edit_schedule_tour_id" class="form-control" required>
                    <option value="">Chọn tour</option>
                    @foreach($tours as $tour)
                        <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Ngày khởi hành <span style="color: red;">*</span></label>
                <input type="date" name="start_date" id="edit_schedule_start_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Ngày kết thúc <span style="color: red;">*</span></label>
                <input type="date" name="end_date" id="edit_schedule_end_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Giá (VNĐ) <span style="color: red;">*</span></label>
                <input type="number" name="price" id="edit_schedule_price" class="form-control" required min="0" step="1000">
            </div>
            <div class="form-group">
                <label>Số người tối đa <span style="color: red;">*</span></label>
                <input type="number" name="max_participants" id="edit_schedule_max_participants" class="form-control" required min="1">
            </div>
            <div class="form-group" style="text-align: right; margin-top: 20px; padding-bottom: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editTourScheduleModal').style.display='none'">Hủy</button>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

