@extends('layouts.app')
@section('title', 'Send Command')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card tg-card border-0">
            <div class="card-body p-5">
                <form action="{{ auth()->user()->hasRole('admin') ? route('admin.commands.store') : route('operator.commands.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label for="device_id" class="form-label fw-medium">Select Device</label>
                            <select class="form-select select2 @error('device_id') is-invalid @enderror" id="device_id" name="device_id" required>
                                <option value="" disabled selected>Choose a device...</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>{{ $device->name }} ({{ $device->serial_number }}) - {{ ucfirst($device->status) }}</option>
                                @endforeach
                            </select>
                            @error('device_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div class="text-muted small mt-1">Only active devices will process commands immediately.</div>
                        </div>

                        <div class="col-md-12 mt-4">
                            <label for="payload" class="form-label fw-medium">JSON Payload</label>
                            <textarea name="payload" id="payload" class="form-control font-monospace @error('payload') is-invalid @enderror" rows="8" required>{{ old('payload', "{\n  \"action\": \"status\",\n  \"params\": {}\n}") }}</textarea>
                            @error('payload')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="text-muted small mt-2">
                                <strong>Templates:</strong> 
                                <a href="#" class="text-primary text-decoration-none me-2" onclick="setPayload('ping')">Ping</a> |
                                <a href="#" class="text-primary text-decoration-none mx-2" onclick="setPayload('reboot')">Reboot</a> |
                                <a href="#" class="text-primary text-decoration-none mx-2" onclick="setPayload('toggle')">Toggle On/Off</a>
                            </div>
                        </div>

                        <div class="col-md-12 mt-5 d-flex justify-content-end gap-2">
                            <a href="{{ auth()->user()->hasRole('admin') ? route('admin.commands.history') : route('operator.commands.history') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 d-flex align-items-center">
                                <i data-feather="send" class="me-2" style="width: 16px;"></i> Send Command
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setPayload(type) {
        const textarea = document.getElementById('payload');
        let data = {};
        
        if(type === 'ping') {
            data = { action: "ping" };
        } else if(type === 'reboot') {
            data = { action: "reboot", timeout: 30 };
        } else if(type === 'toggle') {
            data = { action: "toggle", state: "on" };
        }
        
        textarea.value = JSON.stringify(data, null, 2);
    }
</script>
@endpush
