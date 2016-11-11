<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Contracts\Auth\Guard;
use Auth;
use Redirect;
use App\User;
use Hash;
/**
 *
 */
class AuthController extends Controller
{
  private $auth;
  public function __construct(Guard $auth)
  {
    $this->auth = $auth;
  }
  public function index(){
    if(Auth::check()){
      return redirect('/admin/dashboard');
    }
    return view('auth.login');
  }
  public function getCriarConta(){
    return view('auth.criar-conta');
  }
  public function criarConta(Request $request){
    //Verificando se há algum usuário com o mesmo email
    $user_email = User::query()->where('email', $request->email)->first();

    if(!$user_email){
        $user = new User();
        $user->name = $request->nome;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->remember_token = $request->_token;
        $user->save();

        Auth::login($user);

        return redirect('/admin/dashboard');
    }
    return redirect()->back()->with(['erro'=>'E-mail informado já está cadastrado.','class'=>'danger'])->withInput();;
  }
  public function logar(Request $request){
    $data = $request->only(['email', 'password']);
    if( ($user = User::where('email',$request->email)->first()) != null){
      if (Hash::check($request->password, $user->password)) {
        Auth::login($user);
        return redirect('/admin/dashboard');
      }
        return redirect()->back()->with(['erro' => 'Senha inválida', 'class'=>'danger'])->withInput();
    }else{
      return redirect()->back()->with(['erro' => 'Verifique suas credenciais e tente novamente.', 'class'=>'danger']);
    }
  }

  public function logout(){
    Auth::logout();
    return redirect('/');
  }
}
