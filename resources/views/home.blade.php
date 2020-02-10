@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add Email Template</div>
                <div class="card-body">
                  <form action="/mail/template" method="POST">
                    @csrf

                    <div class="form-group">
                      <label for="email">Template Name:</label>
                      <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="form-group">
                      <label for="pwd">HTML Code:</label>
                      <textarea class="form-control" id="code" name="code" style="height: 400px;"></textarea>
                    </div>
                    <div class="checkbox">
                      <label><input type="checkbox" name="active">Activate</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
