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
<body class="bg-[#13113C] min-h-screen flex items-center justify-center py-12 px-6 relative overflow-x-hidden selection:bg-[#8AD1C3] selection:text-[#13113C]">
    
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

            <!-- 🌟 TOMBOL GOOGLE LOGIN DI ATAS 🌟 -->
            <div class="mb-6">
                <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center gap-3 bg-white border-2 border-[#13113C]/10 text-[#13113C] px-4 py-4 font-bold text-sm hover:border-[#8AD1C3] hover:text-[#8AD1C3] transition-all duration-300">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Sign In with Google
                </a>
            </div> 

            <!-- GARIS PEMISAH ESTETIK -->
            <div class="flex items-center my-6">
                <div class="flex-grow border-t-2 border-[#13113C]/10"></div>
                <span class="px-4 text-xs font-bold text-[#13113C]/40 uppercase tracking-widest">Atau Manual</span>
                <div class="flex-grow border-t-2 border-[#13113C]/10"></div>
            </div>

            <!-- FORM LOGIN MANUAL -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-6">
                    <label for="email" class="block text-sm font-bold text-[#13113C] mb-2 uppercase tracking-wider">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-5 py-4 bg-white border-2 border-[#13113C]/10 rounded-none text-[#13113C] font-medium focus:ring-0 focus:border-[#8AD1C3] outline-none transition-all placeholder:text-gray-400" placeholder="john@gmail.com">
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
                Don't have an account? <a href="{{ route('register') }}" class="text-[#8AD1C3] font-bold hover:underline decoration-2 underline-offset-4">Register</a>
            </p>
        </div>
    </div>
</body>
</html>