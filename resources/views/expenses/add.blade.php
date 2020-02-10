@extends('layouts.expense')

@section('style')
<style>
  .form-control { font-family: 'Merriweather', serif; font-size: 1.3em; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add Expense</div>
                <div class="card-body">
                  <form action="/mail/template" method="POST">
                    @csrf

                    <div class="form-group">
                      <label for="email">Tag:</label>
                      <select class="form-control" id="tag" name="tag">
                        <option>-- select --</option>
                        @foreach($accounts as $key => $account)
                          <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="email">Item Name:</label>
                      <input type="text" class="form-control" id="name" name="name">
                    </div>

                    <div class="form-group">
                      <label for="email">Date:</label>
                      <input type="date" class="form-control" id="name" name="name" value="{{ date("Y-m-d") }}">
                    </div>

                    <div class="form-group">
                      <label for="pwd">Description:</label>
                      <textarea class="form-control" id="code" name="code" ></textarea>
                    </div>

                    <div class="form-group">
                      <label for="email">Price:</label>
                      <input type="number" step="0.01" min="0" max="10" style="text-align: right;" class="form-control" id="price" name="price" value="0">
                    </div>

                    <div class="form-group">
                      <label for="email">Quantity:</label>
                      <input type="number" style="text-align: right;" class="form-control" id="quantity" name="quantity" value="0">
                    </div>

                    <div class="form-group">
                      <label for="email">Summary:</label>
                      <span type="text" id="summary" class="form-control" id="summary" style="text-align: right;">0.00</span>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Save</button>
                    <button type="submit" class="btn btn-warning" style="width: 100%; margin-top: 10px;">Reset</button>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
  $(document).ready(function() {

    $("#price").keyup(function() {
      do_summary();
    });
    $( "#quantity" ).keyup(function() {
      do_summary();
    });

    function do_summary() {
      var price = $("#price").val();
      var quantity = $("#quantity").val();
      console.log(price);
      console.log(quantity);


      var total = parseFloat(price) * parseFloat(quantity);
      $("#summary").empty().prepend(total);
      console.log(total);


    }

  })
</script>
@endsection
