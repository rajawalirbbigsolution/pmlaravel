@extends('layouts.app')
@section('2')
<table style="width: 600px;" align="center">
	<tr>
		<td>
			<div class="col-xs-12" style="margin-top:10px;"> <?php if(Session::has('InfoBox')): ?>
				<div class="col-xs-8 col-xs-offset-2 text-center alert alert-info alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span style="color: white!important;"
					                                                               aria-hidden="true">&times;</span><span
								class="sr-only">Close</span></button>
					<span class="glyphicon glyphicon-info-sign"></span> <?php echo Session::get('InfoBox') ?>
				</div> <?php endif; ?>
				<div class="col-xs-8 col-xs-offset-2 text-center"
				     style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); ">
 
					<h4 class="text-center" style="color: #022c69; margin:22px;">Permission Management </h4>
					<form method="post" action="{{action('AuthC@Lp')}}" autocomplete="new-password"> {{csrf_field()}}
						<div class="form-group">
							<div class="col-xs-12"><input type="text" name="email" id="email"
							                              placeholder="EMAIL" CLASS="form-control input-lg"
							                              value="{{session('email')}}"
							                              style="border: 1px solid #c7c7c7!important; margin-bottom: 10px;"
							                              required autocomplete="new-password"></div>
						</div>
						<div class="form-group">
							<div class="col-xs-12"><input id="password" type="password" name="password"
							                              class="form-control input-lg"
							                              style="border: 1px solid #c7c7c7!important;"
							                              placeholder="{{__('login.7')}}" required
							                              autocomplete="new-password"></div>
						</div>
						<div class="form-group">
							<div class="col-xs-12 text-right" style="margin-top: 5px;"></div>
						</div>
						<div class="form-group">
							<div class="col-xs-12" style="margin-top: 20px;">
								<div class="form-group">
									 
									<button  id="idlogin" type="submit" style="background:#898d94;color:#fff6b2;border-color:#898d94 !important;" class="btn btn-lg btn-block" style="color: #70478f; border-color: #c7c7c7;  background: white;">LOGIN</button>
									<a  href="{{action('a@a')}}" style="background:#898d94;color:#404040;border-color:#898d94 !important;" class="btn btn-lg btn-block" style="color: #70478f; border-color: #c7c7c7;  background: white;">BUAT AKUN</a>
									{{--<a  href="{{action('a@a')}}" style="background:#898d94;color:#fff6b2;border-color:#898d94 !important;" class="btn btn-lg btn-block" style="color: #70478f; border-color: #c7c7c7;  background: white;">REGISTER</a>--}}
										
			 
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="container col-xs-12">
				<div class="row"></div>
			</div>
			<div class="col-xs-12"><h1>&nbsp;</h1>
				<h1>&nbsp;</h1></div>
		</td>
	</tr>
</table> 
@endsection 
@section('3')
 
@endsection
