@extends('layouts.app')
@section('title', 'Edit Device Type')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.device-types.index') }}" class="text-decoration-none text-muted small fw-bold text-uppercase">
        <i class="bi bi-arrow-left me-1"></i> Back to Catalog
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card tg-card border-0">
            <div class="card-body p-5">
                <form action="{{ route('admin.device-types.update', $deviceType) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $deviceType->name) }}" required>
                                <label for="name">Type Name</label>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description', $deviceType->description) }}">
                                <label for="description">Description (optional)</label>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating">
                                <select class="form-select @error('icon') is-invalid @enderror" id="icon" name="icon">
                                    <option value="">Select Icon</option>
                                    @foreach($icons as $icon)
                                        <option value="{{ $icon }}" {{ old('icon', $deviceType->icon) == $icon ? 'selected' : '' }}>{{ ucfirst($icon) }}</option>
                                    @endforeach
                                </select>
                                <label for="icon">Device Icon (optional)</label>
                                @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mt-2 text-muted small d-flex align-items-center gap-2">
                                <span>Preview:</span>
                                <div id="iconPreview" class="d-flex gap-2"></div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-5 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.device-types.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Update Device Type</button>
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
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('icon');
        const preview = document.getElementById('iconPreview');
        
        function updatePreview() {
            if(select.value) {
                preview.innerHTML = `<i data-feather="${select.value}" style="width: 20px;"></i>`;
                feather.replace();
            } else {
                preview.innerHTML = '';
            }
        }
        
        select.addEventListener('change', updatePreview);
        updatePreview();
    });
</script>
@endpush
