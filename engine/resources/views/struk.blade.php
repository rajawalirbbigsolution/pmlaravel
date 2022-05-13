<html>
<head>
	<meta name="tn" content="{{ csrf_token() }}"> 
	<link rel="stylesheet" href="{{asset('asset/plugin/bs2/css/bootstrap.css')}}">
	<style>
		@font-face {
			font-family: Open Sans Light;
			src: url({{asset('asset/fonts/OpenSans-Light.ttf')}});
		}
		body{
			font-family: 'Open Sans Light', sans-serif!important; 
		}
		table {
			border-collapse: collapse;
			font-size: 10px;
			border: solid;
		}
		table,  td {
			border-top: 1px dashed #d1d1d1;
			padding: 8px;
			border-left: none !important;
			border-right: none !important;
		}
		.dtd{
			background: #fafafa !important; 
			color: #333 !important;
			border-radius: 11px!important;
			margin-left: 2%;
			margin-top: 2%;
		}
		.dtd > tfoot > tr > td{ 
			border-radius: 8px;
			border-left: 1px solid transparent!important;
			border-right: 1px solid transparent!important;
			border-bottom: 1px solid transparent!important;
		}
		th{
			border-right: 1px solid transparent!important;
			border-left: 1px solid transparent!important;
			border-top: 1px solid transparent!important;
		}  
		h1{
			font-size: 100%!important;
		}
		h2{
			font-size: 200%!important;
		}
		h6{
			font-size:100%!important;   
			text-align: center!important;
		}
	</style>
</head>                                                                                                                                       
<body>
<table class="dtd" style="height: 81px; text-align: left;" width="300px" border="none;"> 
	<tbody>
	<tr style="height: 26px; text-align: center; font-weight: bold;text-align: center!important;">
		<th colspan="2">
			<h6>gtc</h6>
		            <h6>Pt. parulian produk presisi</h6>
			            <h6> Jl. Anugrah No. 027 Cilik Riwut Km. 10</h6>
			            <h6>Palangka raya, Indonesia</h6>
		</th>
	</tr> 
	<tr style="height: 26px;">
		<td colspan="2">
			<h1>Kode Pembelian</h1>
			<h2>{{$b->b}}</h2>
		</td>
	</tr>
	<tr style="height: 26px;">
		<td colspan="2">
			<h1>Nama Pembelian</h1>
			<h5>Pengisian saldo sebesar Rp. {{$b->d}},- </h5>
		</td>
	</tr>
	<tr style="height: 26px;">
		<td colspan="2">
			<h1>Nominal transfer </h1>
			<h2>Rp. {{$b->e}},-</h2>
		</td>
	</tr> 
	<tfoot>
	<tr>
		<td colspan="2">
			<h1>Status</h1>
			@if((int)$b->c===2)
			<h2>Memverifikasi</h2>
			@elseif((int)$b->c===3)
			<h2>Gagal</h2>
			@elseif((int)$b->c===1)
			<h2>Berhasil</h2>
			@elseif(empty($b->c))
			<h5>Menunggu dokumen verifikasi</h5>
			@else
			<h2>-</h2>
				@endif
		</td>
	</tr>
	</tfoot>	 
	</tbody> 
</table>
<script src="{{asset('asset/plugin/jquery/jquery-2.2.3.min.js')}}" ></script>
<script src="{{asset('asset/plugin/bs2/js/bootstrap.js')}}" ></script>
</body>
</html> 
