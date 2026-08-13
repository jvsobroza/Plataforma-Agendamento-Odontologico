<?php

use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\FilialController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PlanoTratamentoController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\ServicoTratamentoController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckDentista;
use App\Http\Middleware\CheckSecretaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'senha' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->tipo == 1) {
            return redirect()->intended('/dentista');
        } else {
            return redirect()->intended('/secretaria');
        }
    }

    return back()->withErrors([
        'email' => 'As credenciais fornecidas não são válidas ou não existem.',
    ])->onlyInput('email');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware([CheckDentista::class])->prefix('dentista')->name('dentista.')->group(function () {
    Route::get('/', function () {
        return view('dentista.index');
    })->name('index');

    Route::resource('agendamentos', AgendamentoController::class);
    Route::resource('pacientes', PacienteController::class);
    Route::resource('filiais', FilialController::class);
    Route::resource('planos-tratamento', PlanoTratamentoController::class);
    Route::resource('servicos', ServicoController::class);
    Route::resource('servicos-tratamento', ServicoTratamentoController::class);
    Route::resource('secretarias', UserController::class);
});

Route::middleware([CheckSecretaria::class])->prefix('secretaria')->name('secretaria.')->group(function () {
    Route::get('/', function () {
        return view('secretaria.index');
    })->name('index');

    Route::resource('agendamentos', AgendamentoController::class);
    Route::resource('pacientes', PacienteController::class)->only(['index', 'show', 'edit', 'update']);
    Route::resource('secretarias', UserController::class)->only(['show', 'edit', 'update']);
});

/*Route::post('/webhook/whatsapp', [PacienteController::class, 'webhook'])
    ->name('webhook.whatsapp');
*/
