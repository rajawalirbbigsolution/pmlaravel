@extends('layouts.app')
@section('1')
@endsection
@section('2')
	<table style="width: 1600px;" align="center">
		<tr>
			<td>
				<div class="col-xs-12" style="margin-top:10px;">
					<div class="col-xs-12 text-center" style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); "> 
						<div class="form-group text-center" style="margin-top: 10px !important; "> <a href="{{action('b@a')}}"><span class="glyphicon glyphicon-home" style="color: #8e8e8e;!important;"></span></a></div>
						<h5>@if(!empty($b->users)) {{$b->users}} @else {{Auth::user()->name}} @endif </h5>
						<h6>Saldo saat ini Rp. @if(!empty($b->e)) {{$b->e}} @else 0  @endif ,-</h6>
						<table class="table table-active" id="btable">
							<tbody>
							<tr>
								<th>Nomor pesanan/struk</th> 
								<th>Saldo sebelum</th>
								<th>Debet</th>
								<th>Kredit</th> 
								<th>Saldo sesudah</th>
								<th>Date</th>
								<th>Keterangan</th> 
							</tr>
							@foreach($a as $b)
							<tr> 
								<td>@if(!empty($b->i)) {{$b->i}} @else - @endif </td>
								
								<td>@if(!empty($b->f)) {{$b->f}},- @else - @endif </td>
								<td>@if(!empty($b->b)) {{$b->b}},- @else - @endif</td>
								<td>@if(!empty($b->c)) {{$b->c}},- @else - @endif</td> 
								<td>@if(!empty($b->g)) {{$b->g}},- @else - @endif </td>
								<td>@if(!empty($b->j)) {{$b->j}} @else - @endif </td>
								<td>@if(!empty($b->h)) {{$b->h}} @else - @endif</td> 
							</tr>
						@endforeach
							@if(count($a)<=0)
								<td>- </td>
								<td>-  </td>
								<td>- </td>
								<td>- </td>
								<td>-  </td>
								<td>-</td>
							@endif
							</tbody>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table> 
@endsection
@section('3')
	<script> 
        $(document).ready(function () { 
        });        
 
	</script>
@endsection
