<div style="margin: 100px auto; width: 300px; text-align: center; font-family: sans-serif;">
    <h2>Login Local (SQLite)</h2>
    <form method="POST" action="/login">
        @csrf <input type="email" name="email" value="admin@prueba.com" placeholder="Email" style="padding:10px; width:100%; margin-bottom:10px;" required>
        <br>
        <input type="password" name="password" value="123456" placeholder="Password" style="padding:10px; width:100%; margin-bottom:10px;" required>
        <br>
        <button type="submit" style="padding:10px 20px; background: #333; color: white; border: none; cursor: pointer;">Ingresar</button>
    </form>
    
    @if($errors->any())
        <p style="color: red">{{ $errors->first() }}</p>
    @endif
</div>