<div class="container" style="text-align: center; margin-top: 200px;">
    
<h3>Scan this QR Code in Google Authenticator</h3>

{!! $QR_Image !!}

<p>enter this secret key manually: <strong>{{ $secret }}</strong></p>

<form action="{{ route('verify-2fa') }}" method="POST">
    @csrf
    <label for="code">Enter Authentication Code:</label>
    <input type="text" name="code" required>
    <button type="submit">Verify</button>
</form>

</div>