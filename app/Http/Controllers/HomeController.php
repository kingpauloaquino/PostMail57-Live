<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Template;
use App\Account;
use App\Expense;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if($user->type == 2) {
          return view('expenses.welcome');
        }
        else {
          return view('home');
        }
    }

    public function expense()
    {
        $accounts = Account::where("status", 1)->get();

        return view('expenses.add', compact('accounts'));

    }

    public function save_template(Request $request) {

        $temp = new Template();

        $uid = Auth::user()->id;
        $name = $request->name; // template name
        $code = $request->code; // template name
        $active = $request->active != null ? 1 : 0;

        $db = Template::where("user_id", "=", $uid)
            ->where("mail_name", "=", $name)
            ->where("mail_status", "=", 1);

        if( $db->first() != null ) {
            $result = $db->update(
                          array("mail_body" => $input["body"])
                      );

            return array(
              "code" => $result ? 200 : 500,
              "message" => "Updated templates"
            );
        }

        $temp->mail_name = $name;
        $temp->user_id = $uid;
        $temp->mail_body = $code;
        $temp->mail_status = $active;
        $result = $temp->save();

        return array(
          "code" => $result ? 200 : 500,
          "message" => "Added templates"
        );
    }
}
