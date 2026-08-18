<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SCIS Kabinet Keong</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .font-serif-heading { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-[#13113C] min-h-screen flex items-center justify-center p-6 relative overflow-hidden selection:bg-[#8AD1C3] selection:text-[#13113C]">
    
    <!-- Ornaments Background -->
    <svg class="absolute top-10 left-10 w-32 h-32 text-[#8AD1C3] opacity-20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 7.4h7.6l-6 4.6 2.3 7.4-6.3-4.8-6.3 4.8 2.3-7.4-6-4.6h7.6z"/></svg>
    <svg class="absolute bottom-10 right-10 w-48 h-48 text-[#E5A87B] opacity-10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2" stroke="currentColor" fill="none" stroke-dasharray="4 4"/></svg>

    <!-- Layered Card Setup -->
    <div class="relative w-full max-w-lg z-10">
        <!-- Shadow Layer -->
        <div class="absolute inset-0 bg-[#8AD1C3] rounded-2xl translate-x-4 translate-y-4"></div>
        
        <!-- Main Card -->
        <div class="relative bg-[#F6F3E7] border-2 border-dashed border-[#8AD1C3] p-10 sm:p-14 rounded-2xl shadow-xl">
            
            <a href="/" class="inline-flex items-center text-sm font-semibold text-[#8AD1C3] hover:text-[#13113C] transition-colors mb-6 uppercase tracking-wider">
                &larr; Back to Home
            </a>

            <div class="mb-10">
                <h1 class="font-serif-heading text-4xl text-[#13113C] font-bold mb-2">Welcome Back.</h1>
                <p class="text-[#13113C]/70">Enter your credentials to manage inventory.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-6">
                    <label for="email" class="block text-sm font-bold text-[#13113C] mb-2 uppercase tracking-wider">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-5 py-4 bg-white border-2 border-[#13113C]/10 rounded-none text-[#13113C] font-medium focus:ring-0 focus:border-[#8AD1C3] outline-none transition-all placeholder:text-gray-400" placeholder="admin@studentcouncil.com">
                    @error('email') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-bold text-[#13113C] mb-2 uppercase tracking-wider">Password</label>
                    <input id="password" type="password" name="password" required class="w-full px-5 py-4 bg-white border-2 border-[#13113C]/10 rounded-none text-[#13113C] font-medium focus:ring-0 focus:border-[#8AD1C3] outline-none transition-all placeholder:text-gray-400" placeholder="••••••••">
                    @error('password') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between mb-10">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-5 h-5 border-2 border-[#13113C]/20 rounded-none text-[#8AD1C3] focus:ring-0 cursor-pointer">
                        <span class="ms-3 text-sm font-medium text-[#13113C]">Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-bold text-[#E5A87B] hover:text-[#13113C] transition-colors">Forgot Password?</a>
                    @endif
                </div>

                <button type="submit" class="w-full py-4 bg-[#13113C] text-[#F6F3E7] font-bold text-lg uppercase tracking-widest hover:bg-[#8AD1C3] hover:text-[#13113C] transition-all duration-300 border-2 border-[#13113C]">
                    Sign In
                </button>
            </form>
            
            <p class="mt-8 text-center text-[#13113C]/70 font-medium">
                Don't have an account? <a href="{{ route('register') }}" class="text-[#8AD1C3] font-bold hover:underline">Register</a>
            </p>
        </div>
    </div>
</body>
</html>