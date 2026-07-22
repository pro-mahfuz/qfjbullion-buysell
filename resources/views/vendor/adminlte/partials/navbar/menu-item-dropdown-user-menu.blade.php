@php($logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout'))
@php($profile_url = View::getSection('profile_url') ?? config('adminlte.profile_url', 'logout'))

@if (config('adminlte.usermenu_profile_url', false))
    @php($profile_url = Auth::user()->adminlte_profile_url())
@endif

@if (config('adminlte.use_route_url', false))
    @php($profile_url = $profile_url ? route($profile_url) : '')
    @php($logout_url = $logout_url ? route($logout_url) : '')
@else
    @php($profile_url = $profile_url ? url($profile_url) : '')
    @php($logout_url = $logout_url ? url($logout_url) : '')
@endif

<li class="nav-item dropdown user-menu">
    {{-- User menu toggler --}}
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
        @if (config('adminlte.usermenu_image'))
            <img src="{{ Auth::user()->adminlte_image() }}" class="user-image img-circle elevation-2"
                alt="{{ Auth::user()->name }}">
        @endif
        <span @if (config('adminlte.usermenu_image')) class="d-none d-md-inline" @endif>
            {{ Auth::user()->name }}
        </span>
    </a>

    {{-- User menu dropdown --}}
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <li class="user-footer d-flex justify-content-between">
            <a href="#" class="btn btn-default btn-flat w-50 text-center" onclick="openChangePasswordModal()">
                <i class="fa fa-fw fa-key text-lightblue"></i>
                Change Password
            </a>

            <a href="#" class="btn btn-default btn-flat w-50 text-center" onclick="confirmLogout()">
                <i class="fa fa-fw fa-power-off text-red"></i>
                {{ __('adminlte::adminlte.log_out') }}
            </a>

            <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
                @if (config('adminlte.logout_method'))
                    {{ method_field(config('adminlte.logout_method')) }}
                @endif
                {{ csrf_field() }}
            </form>
        </li>
    </ul>

</li>

{{-- Include SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmLogout() {
        Swal.fire({
            title: "Are you sure?",
            text: "You will be logged out from your account.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, Logout!"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }

    function openChangePasswordModal() {
        Swal.fire({
            title: "Change Password",
            html: `<input type="password" id="current-password" class="swal2-input" placeholder="Current Password">
                 <input type="password" id="new-password" class="swal2-input" placeholder="New Password">
                 <input type="password" id="confirm-password" class="swal2-input" placeholder="Confirm New Password">`,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: "Save",
            cancelButtonText: "Cancel",
            preConfirm: () => {
                const currentPassword = document.getElementById("current-password").value;
                const newPassword = document.getElementById("new-password").value;
                const confirmPassword = document.getElementById("confirm-password").value;

                if (!currentPassword || !newPassword || !confirmPassword) {
                    Swal.showValidationMessage("All fields are required!");
                    return false;
                }
                if (newPassword.length < 6) {
                    Swal.showValidationMessage("New password must be at least 6 characters!");
                    return false;
                }
                if (newPassword !== confirmPassword) {
                    Swal.showValidationMessage("Passwords do not match!");
                    return false;
                }

                return fetch("{{ route('admin.users.change.password') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        new_password: newPassword,
                        new_password_confirmation: confirmPassword
                    })
                }).then(response => response.json()).then(data => {
                    if (data.success) {
                        Swal.fire("Success", "Password changed successfully!", "success");
                    } else {
                        Swal.fire("Error", data.message || "Failed to change password", "error");
                    }
                }).catch(() => {
                    Swal.fire("Error", "Something went wrong!", "error");
                });
            }
        });
    }
</script>
