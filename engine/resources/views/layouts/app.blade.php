<!DOCTYPE html>
<html>
<head>
	<meta name="tn" content="{{ csrf_token() }}">
	<title>Tagihan pembayaran</title>
	<link rel="icon" type="image/png" href="{{asset('icon.png')}}"> 
	<link rel="stylesheet" href="{{asset('asset/css/bootstrap/css/bootstrap.css') }}">
	<link rel="stylesheet" href="{{asset('asset/css/fontawesome/css/font-awesome.min.css') }}"> 

	<style> @font-face {
			font-family: "SF Pro Display";
			src: url({{asset('plugin/applefont/SFCompactDisplay-Regular.woff2')}});
		}
		
		body {
			padding-top: 40px;
			font-weight: 300;
			background: black;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			font-family: 'SF Pro Display' !important;
			background-size: cover;
			-o-background-size: cover;
		}
		
		input {
			text-align: center;
			color: black;
			background: #000000;
		}
		
		textarea {
			text-align: center;
			color: black;
			background: #000000;
		}
		
		.container {
			width: @container-desktop  !important;
		} 
		
		.BoxStyle {
			background: #ffffffb8;
			color: black;
			box-shadow: 0 1px 47px rgba(0, 0, 0, 0.64);
			margin-bottom: 50px;
			margin-top: 50px;
			border-radius: 25px;
		}
		
		.CoverBook {
			margin-top: 30px !important; /*box-shadow: 0 1px 20px rgb(140, 140, 140);*/
		}
		
		.ob {
			color: #888c94 !important;
		}
		
		.ulfc {
			list-style: none;
			color: white;
			text-align: left !important;
		}
		
		a {
			color: white; 
		}  
	</style>
	@yield('1')
</head>
<body>  
@yield('2') 
<script src="{{asset('asset/js/jquery2.2.3/jquery.min.js')}}"></script>
<script src="{{asset('asset/js/bootstrap3.3.7/bootstrap.min.js')}}"></script>
<script src="{{asset('plugin/bn/bootstrap-notify.js')}}"></script>
<script>
    function notification() {
        $.notifyClose();
        $.notify({
                message:notificationdata
            },{
                type: 'info',
                placement: {
                    from: "bottom",
                    align: "center"
                }
            }
        );
    } 
    $(document).ready(function () {
 
    });
</script>
@yield('3') 
</body>
</html>
