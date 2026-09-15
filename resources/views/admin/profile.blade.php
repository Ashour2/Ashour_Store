@extends('admin.master')

@section('css')
<style>
    .prev-img {
        width: 200px;
        height: 200px;
        object-fit: cover;
        border-radius: 50%;
        padding: 5px;
        border: 1px dashed #b8b8b8;
        cursor: pointer;
        transition: all .3s ease
    }
    .prev-img:hover {
        opacity: .8;
    }
    .prev-img-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #06060687;
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(8px);
    display: none;
}

.prev-img-modal img {
    width: 300px;
    height: 300px;
    border-radius: 50%;
    object-fit: cover
}

.pass-wrapper {
    position: relative;
}

.pass-wrapper i {
    position: absolute;
    right: 10px;
    top: 12px
}
</style>
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-1 text-gray-800">{{ __('admin.my_account') }}</h1>
<p class="text-muted mb-4">
    {{ __('admin.my_account_hint') }}
    <span class="badge badge-{{ $admin->role->is_super_admin ? 'danger' : 'secondary' }}">
        <i class="fas fa-user-shield"></i> {{ $admin->role->trans_name }}
    </span>
</p>

<form action="{{ route('admin.profile_data') }}" method="POST">
@csrf
@method('put')

<div class="row">
    <div class="col-md-7">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('msg'))
            <div class="alert alert-success">{{ session('msg') }}</div>
        @endif

        <div class="mb-3">
            <label>{{ __('admin.name') }}</label>
            <input type="text" class="form-control" name="name" value="{{ old('name', $admin->name) }}" required>
        </div>
        <div class="mb-3">
            <label>{{ __('admin.email') }}</label>
            <input type="email" class="form-control" name="email" dir="ltr"
                   value="{{ old('email', $admin->email) }}" required>
            <small class="text-muted">{{ __('admin.email_is_login') }}</small>
        </div>

        <hr>
        <div class="mb-3">
            <label>{{ __('admin.current_password') }} <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="current" name="current_password">
            <small class="text-muted">{{ __('admin.current_password_required_hint') }}</small>
        </div>
        <div class="mb-3">
            <label>
                {{ __('admin.new_password') }}
                <small class="text-muted">({{ __('admin.leave_blank_to_keep') }})</small>
            </label>
            <div class="pass-wrapper">
                <input type="password" disabled id="password" class="form-control new" name="password">
                <i class="fas fa-eye"></i>
            </div>
            <progress style="display: none" max="100" value="0" id="meter"></progress>
            <span class="textbox"></span>
        </div>
        <div class="mb-3">
            <label>{{ __('admin.confirm_password') }}</label>
            <input type="password" disabled class="form-control new" name="password_confirmation">
        </div>

        <button class="btn btn-success"><i class="fas fa-save"></i> {{ __('admin.save') }}</button>
    </div>
</div>
</form>

@endsection

@section('title', 'Dashboard')


@section('scripts')

<script>
    function showImg(e) {

        const file = e.target.files[0]

        if (file) {
            prevImg.src = URL.createObjectURL(file)
        }
    }

    $('.prev-img').click(function() {
        let url = $(this).attr('src')

        $('.prev-img-modal img').attr('src', url)

        $('.prev-img-modal').css('display', 'flex')
    })

    $('.prev-img-modal').click(function() {
        $('.prev-img-modal').hide();
    })

    $('#current').blur(function() {
        $.ajax({
            url: '{{ route("admin.check_password") }}',
            type: 'post',
            data: {
                _token: '{{ csrf_token() }}',
                password: $('#current').val()
            },
            success: function(res) {
                if(res) {
                    $('.new').prop('disabled', false)
                    $('#current').removeClass('is-invalid')
                    $('#current').addClass('is-valid')
                }else {
                    $('.new').prop('disabled', true)
                    $('.new').val('')
                    $('#current').removeClass('is-valid')
                    $('#current').addClass('is-invalid')
                }
            }
        });
    })

    var code = document.getElementById("password");

var strengthbar = document.getElementById("meter");
var display = document.getElementsByClassName("textbox")[0];

code.addEventListener("keyup", function() {
    if(code.value.length > 0) {
        strengthbar.style.display = 'block'
    }else {
        strengthbar.style.display = 'none'
    }
  checkpassword(code.value);
});


function checkpassword(password) {
  var strength = 0;
  if (password.match(/[a-z]+/)) {
    strength += 1;
  }
  if (password.match(/[A-Z]+/)) {
    strength += 1;
  }
  if (password.match(/[0-9]+/)) {
    strength += 1;
  }
  if (password.match(/[$@#&!]+/)) {
    strength += 1;

  }

  if (password.length < 6) {
    display.innerHTML = "minimum number of characters is 6";
  }

  if (password.length > 12) {
    display.innerHTML = "maximum number of characters is 12";
  }

  switch (strength) {
    case 0:
      strengthbar.value = 0;
      break;

    case 1:
      strengthbar.value = 25;
      break;

    case 2:
      strengthbar.value = 50;
      break;

    case 3:
      strengthbar.value = 75;
      break;

    case 4:
      strengthbar.value = 100;
      break;
  }
}
    // $('#current').keyup(function() {
    //     if($(this).val().length > 0) {
    //         $('.new').prop('disabled', false)
    //     }else {
    //         $('.new').prop('disabled', true)
    //         $('.new').val('')
    //     }
    // })

    document.querySelector('.pass-wrapper i').onclick = () => {
        if(code.type == 'password') {
            code.type = 'text'
        }else {
            code.type = 'password'
        }

    }
</script>

@endsection
