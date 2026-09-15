<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- ... Your existing head content ... -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('cms/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{asset('cms/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('cms/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{asset('cms/plugins/jqvmap/jqvmap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('cms/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('cms/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{asset('cms/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('cms/plugins/summernote/summernote-bs4.min.css')}}">
    <style>
        /* Center the content vertically and horizontally */
        .center-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            /* This makes the container take up the full viewport height */
        }

        .bn {
            width: 350px;
        }

        /* Add a colored border around the container */
        .page-container {
            border: 5px solid #3498db;
            /* You can change the color as per your preference */
            padding: 20px;
            border-radius: 15px;
        }
        .img{

        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Wrap your content in a container with the 'center-container' class -->
    <div class="center-container">
        <img width="250" height="200" src="{{ asset('cms\dist\img\logo.jpg') }}" alt="" class="img-circle img-bordered-sm">
<br>
        <!-- Add the 'page-container' class to create a colored border around your content -->
        <div class="card page-container">

            <div class="card">
                <h1 style="color: green;text-align:center">Select Your Account</h1>
            </div>

            <div class="card-body row">
                <div class="col-md-12">
                    <button type="button" onclick="location.href='#'"
                        class="btn btn-primary btn-block bn">Admin</button>

                    {{-- <button type="button"  class="btn btn-primary btn-block"></button> --}}
                </div>
            </div>
            <div class="card-body row">
                <div class="col-md-12">
                    <button type="button" onclick="location.href='#'"
                        class="btn btn-warning bn">Author</button>
                </div>
            </div>
        </div>
        <!-- Rest of your content -->

        <!-- ... Your existing scripts ... -->
</body>

</html>
