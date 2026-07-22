<div class="container" style="text-align: center; margin-top: 200px;">
    



<form action="{{ route('verify-2fa') }}" method="POST">
    @csrf
    <label for="code">Enter Authentication Code:</label>
    <input type="text" name="code" required>
    <button type="submit">Verify</button>
</form>

</div>