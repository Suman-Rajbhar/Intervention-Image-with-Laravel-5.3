@extends('admin.admin_master')
@section('title','Add A Category')
@section('content')

<div class="row">
                        
                        <div class="col-md-6">
                        @if (Session::has('message'))
                        <div class="alert alert-info alert-dismissable">
                            <i class="fa fa-info"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <b>Alert!</b> {{ Session::get('message') }}.
                        </div>
                        @endif

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                            
                            <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">Add a Category</h3>
                                </div>
                                <!-- form start -->

                                {!! Form::open(array('url'=>'save-a-file', 'method'=>'POST', 'class'=>'', 'files'=>true, 'role'=>'form')) !!}
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">File</label>
                                            {!! Form::file('image[]', array('multiple'=>true))!!}
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">Save a File</button>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>                        
                    </div>
@endsection
