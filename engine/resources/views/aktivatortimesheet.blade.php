@extends('layouts.app')  
@section('1') 
@endsection
@section('2')
<table style="width: 1200px;" align="center">
	<tr>
		<td>
			<div class="col-xs-12" style="margin-top:10px;">  
				<div class="col-xs-8 col-xs-offset-2 text-center" style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); ">
					<div class="form-group text-left" style="margin-top: 10px !important; "> <a href="{{action('b@a')}}"><span class="glyphicon glyphicon-arrow-left" style="color: #8e8e8e;!important;"></span></a></div>  
					<table class="table table-active" id="btable">
						<tbody>
						<tr>
							<th>Nomor pesanan/struk</th>
							<th>Nama Akun</th>
							<th>Permintaan saldo</th>
							<th>Saldo Sekarang</th>
							<th>Status</th>
							<th>Action </th>
						</tr>
						<tr>
							<td>11232434</td>
							<td>webdaud@gmail.com</td>
							<td>0</td>
							<td>Rp. 30129,-</td>
							<td><span class="glyphicon glyphicon-ok-circle"></span></td>
							<td>
								<div class="btn-group">
									<button class="btn btn-xs  btn-like"><span class="glyphicon glyphicon-remove"></span></button>
									<button class="btn btn-xs  btn-like"><span class="glyphicon glyphicon-ok-circle"></span></button>
								</div>                                                                                                                                                                          								                                                                                                                                         							</td>
						</tr>
						</tbody>
					</table>
				</div> 
			</div> 
		</td>
	</tr>
</table> 
@endsection 
@section('3') 
	<script >
        $(document).ready(function () {   
        }); 
	</script>
@endsection
