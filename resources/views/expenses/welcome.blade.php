@extends('layouts.expense')

@section('style')
<style>
  .btn { font-family: 'Merriweather', serif; font-size: 1.3em; }
  .btn-success { background-color: #B2346A; border-color: #B2346A; }
  .btn-success:hover { background-color: #7A1E45; }

  .btn-danger { background-color: #512BA4; border-color: #512BA4; }
  .btn-danger:hover { background-color: #2F1763; }

  .btn-primary { background-color: #259C8F; border-color: #259C8F; }
  .btn-primary:hover { background-color: #165D56; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                  <a href="/fund-wallet" class="btn btn-success" style="width: 100%; padding: 40px; font-weight: bold;">Fund/Wallet</a>
                  <a href="/add-new-expense" class="btn btn-danger" style="width: 100%; padding: 40px; margin-top: 15px; font-weight: bold;">Add New Expense</a>
                  <a href="/view-transactions" class="btn btn-primary" style="width: 100%; padding: 40px; margin-top: 15px; font-weight: bold;">View Transactions</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
