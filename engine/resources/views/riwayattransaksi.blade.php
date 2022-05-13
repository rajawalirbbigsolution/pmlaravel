@extends('layouts.app')
@section('2')
	<table style="width: 600px;" align="center">
		<tr>
			<td>
				<div class="col-xs-12" style="margin-top:10px;">
					<div class="col-xs-8 col-xs-offset-2 text-center"  style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); ">
						<div class="form-group text-left" style="margin-top: 10px !important; "> <a href="{{action('b@a')}}"><span class="glyphicon glyphicon-arrow-left" style="color: #8e8e8e;!important;"></span></a></div> 
						@foreach($b as $c)
							<p><a href="{{action('n@b')}}/{{$c->id}}" class="label label-published">{{$c->email}}</a></p>
						@endforeach 
					</div> 
				</div>
				</div> 
			</td>
		</tr>
	</table>
@endsection
@section('3')
@endsection
