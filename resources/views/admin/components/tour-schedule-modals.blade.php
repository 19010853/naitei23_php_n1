
<!-- Tour Schedule Modal -->
<div id="tourScheduleModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>{{ __('common.add_new_schedule') }}</h3>
            <span class="close" onclick="document.getElementById('tourScheduleModal').style.display='none'">&times;</span>
        </div>
        <form id="tourScheduleForm" action="{{ route('admin.tour-schedules.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>{{ __('common.tour') }} <span style="color: red;">*</span></label>
                <select name="tour_id" class="form-control" required>
                    <option value="">{{ __('common.select_tour') }}</option>
                    @foreach($tours as $tour)
                        <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('common.departure_date') }} <span style="color: red;">*</span></label>
                <input type="date" name="start_date" class="form-control" required min="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label>{{ __('common.end_date') }} <span style="color: red;">*</span></label>
                <input type="date" name="end_date" class="form-control" required min="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label>{{ __('common.price') }} ({{ __('common.vnd') }}) <span style="color: red;">*</span></label>
                <input type="number" name="price" class="form-control" required min="0" step="1000" placeholder="{{ __('common.example_price') }}">
            </div>
            <div class="form-group">
                <label>{{ __('common.max_participants') }} <span style="color: red;">*</span></label>
                <input type="number" name="max_participants" class="form-control" required min="1" placeholder="{{ __('common.example_participants') }}">
            </div>
        </form>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('tourScheduleModal').style.display='none'">{{ __('common.cancel') }}</button>
            <button type="submit" form="tourScheduleForm" class="btn btn-primary">{{ __('common.add') }}</button>
        </div>
    </div>
</div>

<!-- Edit Tour Schedule Modal -->
<div id="editTourScheduleModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header modal-header-edit">
            <h3>{{ __('common.edit_schedule') }}</h3>
            <span class="close" onclick="document.getElementById('editTourScheduleModal').style.display='none'">&times;</span>
        </div>
        <form id="editTourScheduleForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="schedule_id" id="edit_schedule_id">
            <div class="form-group">
                <label>{{ __('common.tour') }} <span style="color: red;">*</span></label>
                <select name="tour_id" id="edit_schedule_tour_id" class="form-control" required>
                    <option value="">{{ __('common.select_tour') }}</option>
                    @foreach($tours as $tour)
                        <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('common.departure_date') }} <span style="color: red;">*</span></label>
                <input type="date" name="start_date" id="edit_schedule_start_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('common.end_date') }} <span style="color: red;">*</span></label>
                <input type="date" name="end_date" id="edit_schedule_end_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('common.price') }} ({{ __('common.vnd') }}) <span style="color: red;">*</span></label>
                <input type="number" name="price" id="edit_schedule_price" class="form-control" required min="0" step="1000">
            </div>
            <div class="form-group">
                <label>{{ __('common.max_participants') }} <span style="color: red;">*</span></label>
                <input type="number" name="max_participants" id="edit_schedule_max_participants" class="form-control" required min="1">
            </div>
        </form>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('editTourScheduleModal').style.display='none'">{{ __('common.cancel') }}</button>
            <button type="submit" form="editTourScheduleForm" class="btn btn-primary">{{ __('common.update') }}</button>
        </div>
    </div>
</div>

