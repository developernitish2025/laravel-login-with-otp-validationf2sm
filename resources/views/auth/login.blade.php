<form action="{{ route('send-otp') }}" method="POST">
    @csrf
    <label for="phone_number">Enter your 10-digit phone number:</label>
    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
    @error('phone_number')<span>{{ $message }}</span>@enderror
    <button type="submit">Send OTP</button>
</form>
