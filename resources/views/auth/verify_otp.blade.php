<form action="{{ route('verify-otp') }}" method="POST">
    @csrf
    <label for="otp">Enter the OTP sent to your phone:</label>
    <input type="text" id="otp" name="otp" required>
    <input type="hidden" name="phone_number" value="{{ $phone_number }}">
    @error('otp')<span>{{ $message }}</span>@enderror
    <button type="submit">Verify OTP</button>
</form>
