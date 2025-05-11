<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-form-label" for="fecha_ini">{{ __('Date init') }}</label>
            <div class="controls">
                <div class="input-prepend input-group">
                    <input type="date" name="fecha_ini" class="form-control" placeholder="{{ __('Enter date') }}" required>
                </div>
                <p class="help-block">Puede digitar o seleccionar la fecha</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-form-label" for="fecha_fin">{{ __('Date finish') }}</label>
            <div class="controls">
                <div class="input-group">
                    <input type="date" name="fecha_fin" class="form-control" placeholder="{{ __('Enter date') }}" required>
                </div>
                <span class="help-block">Puede digitar o seleccionar la fecha</span>
            </div>
        </div>
    </div>
</div>
