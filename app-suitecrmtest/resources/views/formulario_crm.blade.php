<div style="margin: 40px auto; width: 500px; font-family: sans-serif;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Usuario: {{ Auth::user()->name }}</h3>
        <form action="{{ route('logout') }}" method="POST"> @csrf <button>Cerrar Sesión</button> </form>
    </div>
    <hr>
    
    <h2>Nuevo Contacto (SuiteCRM API V8)</h2>

    @if(session('success')) 
        <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            ✅ {{ session('success') }}
        </div> 
    @endif
    
    @if(session('error')) 
        <div style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            ❌ {{ session('error') }}
        </div> 
    @endif

    <form method="POST" action="{{ route('enviar.crm') }}" style="display: flex; flex-direction: column; gap: 10px;">
        @csrf
        
        <input type="text" name="first_name" placeholder="Nombre (Ej: Manuel)" required style="padding: 8px;">
        <input type="text" name="last_name" placeholder="Apellido (Ej: Chaparro)" required style="padding: 8px;">
        <input type="text" name="title" placeholder="Cargo (Ej: Desarrollador Docker)" style="padding: 8px;">
        <input type="email" name="email1" placeholder="Email (Ej: manuel@ejemplo.com)" required style="padding: 8px;">
        <input type="text" name="phone_mobile" placeholder="Móvil (Ej: +57300...)" style="padding: 8px;">
        
        <input type="text" name="municipio" placeholder="Municipio (Ej: Soatá)" required style="padding: 8px; border: 2px solid #E44D26;">
        
        <label style="cursor: pointer; padding: 5px;">
            <input type="checkbox" name="hijos" value="1"> 
            <strong>¿Tiene Hijos?</strong>
        </label>

        <textarea name="description" placeholder="Descripción adicional..." rows="3" style="padding: 8px;"></textarea>

        <button type="submit" style="padding: 12px; background: #E44D26; color: white; font-weight: bold; border: none; cursor: pointer; margin-top: 10px;">
            GUARDAR EN CRM
        </button>
    </form>
</div>