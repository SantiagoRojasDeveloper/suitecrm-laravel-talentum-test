<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

// --- LOGIN LOCAL (Sin cambios) ---
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', function (Request $request) {
    $credentials = $request->validate(['email' => 'required', 'password' => 'required']);
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect('/');
    }
    return back()->withErrors(['email' => 'Credenciales incorrectas']);
});
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    return redirect('/login');
})->name('logout');

// --- APP CONECTADA A CRM ---
Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('formulario_crm');
    });

    Route::post('/enviar-crm', function (Request $request) {

        $baseUrl = env('SUITECRM_URL'); // Debe ser: http://127.0.0.1:8200/legacy

        // ---------------------------------------------------------
        // 1. OBTENER TOKEN (CORREGIDO: JSON + Headers V8)
        // ---------------------------------------------------------
        $responseToken = Http::withHeaders([
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->post($baseUrl . '/Api/index.php/access_token', [
            'grant_type' => env('SUITECRM_GRANT_TYPE'),
            'client_id' => env('SUITECRM_CLIENT_ID'),
            'client_secret' => env('SUITECRM_CLIENT_SECRET'),
            'username' => env('SUITECRM_USERNAME'),
            'password' => env('SUITECRM_PASSWORD'),
            'scope' => '',
        ]);

        if ($responseToken->failed()) {
            // Debug: Si falla, mostramos exactamente qué respondió el servidor
            return back()->with('error', 'Error Auth: ' . $responseToken->status() . ' - ' . $responseToken->body());
        }

        $token = $responseToken->json()['access_token'];

        // ---------------------------------------------------------
        // 2. PREPARAR DATOS (Estructura JSON:API)
        // ---------------------------------------------------------
        $crmData = [
            'data' => [
                'type' => 'Contacts',
                'attributes' => [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'title' => $request->title,
                    'email1' => $request->email1,
                    'phone_mobile' => $request->phone_mobile,
                    'description' => $request->description ?? 'Sin descripción',

                    // Campos Personalizados
                    'municipio_c' => $request->municipio,
                    'tiene_hijos_c' => $request->has('hijos'),
                ]
            ]
        ];

        // ---------------------------------------------------------
        // 3. CREAR CONTACTO
        // ---------------------------------------------------------
        // Nota: SuiteCRM es estricto con la URL "/modules/Contacts"
        $responseApi = Http::withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
            ])
            ->post($baseUrl . '/Api/V8/module', $crmData);
        // NOTA: Usé '/Api/V8/module' como me indicaste, 
        // si falla prueba con '/Api/V8/modules/Contacts'

        if ($responseApi->successful()) {
            $nuevoId = $responseApi->json()['data']['id'] ?? 'ID Pendiente';
            return back()->with('success', '¡Contacto Creado Exitosamente! ID: ' . $nuevoId);
        } else {
            return back()->with('error', 'Error Creando Contacto: ' . $responseApi->body());
        }
    })->name('enviar.crm');
});
