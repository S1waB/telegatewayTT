@extends('layouts.app')
@section('title', 'User Management')

@section('content')

{{-- ── Stats bar ── --}}
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center"
             style="width:48px;height:48px;background:#E8F1FA;flex-shrink:0">
          <i class="bi bi-people-fill" style="color:#1A6FBF;font-size:20px"></i>
        </div>
        <div>
          <div class="text-muted" style="font-size:12px">Total users</div>
          <div style="font-size:22px;font-weight:600;color:#0D4A8A">{{ $totalUsers }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center"
             style="width:48px;height:48px;background:#E8F8EF;flex-shrink:0">
          <i class="bi bi-person-check-fill" style="color:#198754;font-size:20px"></i>
        </div>
        <div>
          <div class="text-muted" style="font-size:12px">Active users</div>
          <div style="font-size:22px;font-weight:600;color:#0D4A8A">{{ $activeUsers }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span class="text-muted" style="font-size:12px">Platform activation rate</span>
          <span style="font-size:13px;font-weight:600;color:#1A6FBF">{{ $activePct }}%</span>
        </div>
        <div class="progress" style="height:10px;border-radius:5px">
          <div class="progress-bar" role="progressbar"
               style="width:{{ $activePct }}%;background:#1A6FBF;border-radius:5px"
               aria-valuenow="{{ $activePct }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div style="font-size:11px;color:#888;margin-top:4px">
          {{ $activeUsers }} of {{ $totalUsers }} users currently active
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── Filters bar ── --}}
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label mb-1" style="font-size:12px;color:#666">Search</label>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white"><i class="bi bi-search" style="color:#1A6FBF"></i></span>
          <input type="text" name="search" value="{{ request('search') }}"
                 class="form-control" placeholder="Name, email or phone...">
        </div>
      </div>
      <div class="col-md-2">
        <label class="form-label mb-1" style="font-size:12px;color:#666">Role</label>
        <select name="role" class="form-select form-select-sm">
          <option value="">All roles</option>
          @foreach($roles as $role)
            <option value="{{ $role->name }}" @selected(request('role') === $role->name)>
              {{ ucfirst($role->name) }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label mb-1" style="font-size:12px;color:#666">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">All statuses</option>
          <option value="active"   @selected(request('status') === 'active')>Active</option>
          <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label mb-1" style="font-size:12px;color:#666">Sort by</label>
        <select name="sort" class="form-select form-select-sm">
          <option value="newest"      @selected(request('sort') === 'newest')>Newest first</option>
          <option value="name"        @selected(request('sort') === 'name')>Name A–Z</option>
          <option value="last_active" @selected(request('sort') === 'last_active')>Last active</option>
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-sm btn-primary w-100" style="background:#1A6FBF;border-color:#1A6FBF">
          <i class="bi bi-funnel-fill me-1"></i>Filter
        </button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-x-lg"></i>
        </a>
      </div>
    </form>
  </div>
</div>

{{-- ── Table header row ── --}}
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center py-3" style="border-bottom:1px solid #E8F1FA">
    <span style="font-weight:600;color:#0D4A8A">
      Users
      @if($users->total())
        <span class="badge rounded-pill ms-2" style="background:#E8F1FA;color:#1A6FBF;font-size:11px">{{ $users->total() }}</span>
      @endif
    </span>
    <a href="{{ route('admin.users.create') }}" class="btn btn-sm" style="background:#1A6FBF;color:#fff;border-radius:6px">
      <i class="bi bi-plus-lg me-1"></i>Add user
    </a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle" style="font-size:13px">
        <thead style="background:#F0F6FF">
          <tr>
            <th style="width:44px"></th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Status</th>
            <th>Last active</th>
            <th>Joined</th>
            <th style="width:110px">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
          <tr>
            <td>
              <img src="{{ $user->avatar_url }}"
                   alt="{{ $user->name }}"
                   class="rounded-circle"
                   width="36" height="36"
                   loading="lazy"
                   onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=1A6FBF&color=fff&size=72'">
            </td>
            <td><span style="font-weight:500">{{ $user->name }}</span></td>
            <td class="text-muted">{{ $user->email }}</td>
            <td class="text-muted">{{ $user->phone_number ?? '—' }}</td>
            <td>
              @foreach($user->roles as $role)
                <span class="badge rounded-pill" style="background:#E8F1FA;color:#1A6FBF">{{ $role->name }}</span>
              @endforeach
            </td>
            <td>
              @if($user->is_active)
                <span class="badge bg-success-subtle text-success">Active</span>
              @else
                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
              @endif
            </td>
            <td class="text-muted" style="font-size:12px" title="{{ $user->last_active_at }}">
              {{ $user->last_active_label }}
            </td>
            <td class="text-muted" style="font-size:12px">{{ $user->created_at->format('d M Y') }}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                {{-- View modal trigger --}}
                <button type="button"
                        class="btn btn-sm btn-outline-info p-0 d-flex align-items-center justify-content-center view-user-btn shadow-sm"
                        style="width:34px;height:34px;border-radius:8px"
                        title="View details"
                        data-bs-toggle="modal"
                        data-bs-target="#userDetailModal"
                        data-user="{{ json_encode([
                            'id'           => $user->id,
                            'name'         => $user->name,
                            'email'        => $user->email,
                            'phone'        => $user->phone_number ?? '—',
                            'gender'       => $user->gender ?? '—',
                            'address'      => $user->address ?? '—',
                            'role'         => $user->roles->first()?->name ?? '—',
                            'status'       => $user->is_active ? 'Active' : 'Inactive',
                            'last_active'  => $user->last_active_label,
                            'last_active_full' => $user->last_active_at?->format('d M Y H:i') ?? 'Never',
                            'joined'       => $user->created_at->format('d M Y'),
                            'avatar_url'   => $user->avatar_url,
                            'devices'      => $user->devices()->count(),
                            'commands'     => $user->commands()->count(),
                        ]) }}">
                  <i class="bi bi-eye-fill" style="font-size:16px"></i>
                </button>

                {{-- Edit --}}
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="btn btn-sm btn-outline-primary p-0 d-flex align-items-center justify-content-center shadow-sm"
                   style="width:34px;height:34px;border-radius:8px" title="Edit">
                  <i class="bi bi-pencil-fill" style="font-size:14px"></i>
                </a>

                {{-- Toggle status --}}
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="m-0">
                  @csrf @method('PATCH')
                  <button type="submit"
                          class="btn btn-sm p-0 d-flex align-items-center justify-content-center shadow-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                          style="width:34px;height:34px;border-radius:8px"
                          title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                    <i class="bi bi-{{ $user->is_active ? 'person-x-fill' : 'person-check-fill' }}" style="font-size:16px"></i>
                  </button>
                </form>

                {{-- Delete --}}
                @unless($user->id === auth()->id())
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="m-0"
                      onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger p-0 d-flex align-items-center justify-content-center shadow-sm"
                          style="width:34px;height:34px;border-radius:8px" title="Delete">
                    <i class="bi bi-trash3-fill" style="font-size:15px"></i>
                  </button>
                </form>
                @endunless
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center text-muted py-5">
              <i class="bi bi-people" style="font-size:40px;opacity:.3;display:block;margin-bottom:8px"></i>
              No users found matching your filters.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($users->hasPages())
  <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
    <span class="text-muted" style="font-size:12px">
      Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
    </span>
    {{ $users->links('pagination::bootstrap-5') }}
  </div>
  @endif
</div>

{{-- ── User Detail Modal ── --}}
<div class="modal fade" id="userDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content border-0 shadow">
      <div class="modal-header" style="background:#0D4A8A;border-radius:8px 8px 0 0">
        <h5 class="modal-title text-white" style="font-size:15px">
          <i class="bi bi-person-badge me-2"></i>User details
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        {{-- Avatar + name header --}}
        <div class="d-flex align-items-center gap-3 p-4" style="background:#F0F6FF">
          <img id="modal-avatar" src="" alt="" class="rounded-circle shadow-sm"
               width="72" height="72" style="object-fit:cover;border:3px solid #1A6FBF">
          <div>
            <div id="modal-name" style="font-size:18px;font-weight:600;color:#0D4A8A"></div>
            <div id="modal-role-badge" class="mt-1"></div>
          </div>
          <div class="ms-auto text-end">
            <div id="modal-status-badge"></div>
          </div>
        </div>
        {{-- Details grid --}}
        <div class="p-4">
          <table class="table table-borderless mb-0" style="font-size:13px">
            <tbody>
              <tr>
                <td class="text-muted ps-0" style="width:130px">Email</td>
                <td id="modal-email" class="fw-500"></td>
              </tr>
              <tr>
                <td class="text-muted ps-0">Phone</td>
                <td id="modal-phone"></td>
              </tr>
              <tr>
                <td class="text-muted ps-0">Gender</td>
                <td id="modal-gender" class="text-capitalize"></td>
              </tr>
              <tr>
                <td class="text-muted ps-0">Address</td>
                <td id="modal-address"></td>
              </tr>
              <tr>
                <td class="text-muted ps-0">Last active</td>
                <td id="modal-last-active"></td>
              </tr>
              <tr>
                <td class="text-muted ps-0">Joined</td>
                <td id="modal-joined"></td>
              </tr>
              <tr>
                <td class="text-muted ps-0">Devices assigned</td>
                <td id="modal-devices"></td>
              </tr>
              <tr>
                <td class="text-muted ps-0">Commands sent</td>
                <td id="modal-commands"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-white" style="border-top:1px solid #E8F1FA">
        <a id="modal-edit-btn" href="#" class="btn btn-sm" style="background:#1A6FBF;color:#fff">
          <i class="bi bi-pencil me-1"></i>Edit user
        </a>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.view-user-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const u = JSON.parse(this.dataset.user);

        document.getElementById('modal-avatar').src     = u.avatar_url;
        document.getElementById('modal-avatar').onerror = function() {
            this.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=1A6FBF&color=fff&size=128`;
        };
        document.getElementById('modal-avatar').alt     = u.name;
        document.getElementById('modal-name').textContent = u.name;
        document.getElementById('modal-email').textContent = u.email;
        document.getElementById('modal-phone').textContent = u.phone;
        document.getElementById('modal-gender').textContent = u.gender;
        document.getElementById('modal-address').textContent = u.address;
        document.getElementById('modal-joined').textContent = u.joined;
        document.getElementById('modal-devices').textContent = u.devices + ' device(s)';
        document.getElementById('modal-commands').textContent = u.commands + ' command(s)';
        document.getElementById('modal-last-active').innerHTML =
            `<span title="${u.last_active_full}">${u.last_active}</span>`;

        const roleBadge = document.getElementById('modal-role-badge');
        roleBadge.innerHTML = `<span class="badge rounded-pill" style="background:#E8F1FA;color:#1A6FBF">${u.role}</span>`;

        const statusBadge = document.getElementById('modal-status-badge');
        statusBadge.innerHTML = u.status === 'Active'
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">Inactive</span>';

        document.getElementById('modal-edit-btn').href = `/admin/users/${u.id}/edit`;
    });
});
</script>
@endpush
