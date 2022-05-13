@extends('layouts.Standard')

@section('title')
    @parent
    {{__('standard.e')}}
@stop

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{asset('plugin/FontAwesome/css/font-awesome.min.css')}}"/>
@section('content')
    <!-- Registrasi Gagal -->
    <div class="row col-xs-12">
        <p>&nbsp;</p>

        <div class="container col-xs-12">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="panel" style="border-radius: 6px; box-shadow: 0 1px 47px rgb(0, 0, 0); ">
                        <div class="panel-body">
                            <div class="form-group text-center">
                                <img src="{{asset('img/icon/information3.png')}}" width="150px">
                            </div>
                            <div class="form-group">
                                <h3 style="color: #595959; text-align: center;">{{__('standard.e')}}</h3>
                            </div>
                            <div class="form-group text-center">
                                <a href="{{route('home')}}" class="btn btn-xs btn-purple">{{__('standard.q')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>
    <h1>&nbsp;</h1>

@endsection

