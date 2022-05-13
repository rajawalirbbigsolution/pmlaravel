@extends('layouts.app')  
@section('2')
<table style="width: 1000px;" align="center">
	<tr>
		<td>
			<div class="col-xs-12" style="margin-top:10px;">  
				<div class="col-xs-8 col-xs-offset-2 text-center"
				     style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); "> 
					<h4 class="text-center" style="color: #022c69; margin:22px;">Privilages  <small class="pull-right"><a href="{{url('/')}}/logout" style="color: #e0e0e0 !important;">Logout</a></small></h4>  
						<div style="background: #3d3535!important; padding: 10px; border-radius: 5px;">
								<div class="form-group">	<input type="text" placeholder="Nama User" class="form-control" id="inputa"></div>
								<div class="form-group">	<button type="button" class="label label-published" onclick="buat();">Buat</button></div>  
						</div>
				
				          <table class="table  ">
						          <tr style="font-weight: bold;"> 
						          <td>Nama User</td> 
						          <td>Telepon</td>  
								          <td>Password</td>
								          <td>Action</td>
						          </tr>
						
						          @foreach($a as $b)
						          <tr>
								          <td> {{$b->name}}</td> 
								          <td> {{$b->a}}</td>
								          <td> {{$b->b}}</td>
								          <td><button class="label label-published">Edit Privilages </button></td>
						          </tr>  
										  @endforeach
						          {{$a->links()}}
				
				
				          </table>
						<div style="background: #8d8d8d !important; padding: 10px; border-radius: 5px;margin: 10px;">
								<h5>Akses menu</h5>
								<label class="label label-aktivator">Aktivator menu 1 </label>
								<label class="label label-aktivator">Aktivator menu 2 </label>
								<label class="label label-aktivator">Aktivator menu 3 </label>
								<label class="label label-aktivator">Aktivator menu 4 </label>
								<label class="label label-aktivator">Aktivator menu 5 </label> 
						</div>
					
				</div> 
				</div>
			</div> 
		</td>
	</tr>
</table>  
		
@endsection 
@section('3') 
		<script >
            function buat() {
                $.notifyClose();
                notificationdata = 'Loading..';
                notification();
                $.post(
                    '{{action('a1@b')}}',
                    {
                        _token : $('meta[name="tn"]').attr('content'),
	                    a :$('#inputa').val()
                    },
                    function (r){
                        $.notifyClose();
                        location.reload();
                        $('#inputa').val(''); 
                    }
                );
            }
		</script>
@endsection
