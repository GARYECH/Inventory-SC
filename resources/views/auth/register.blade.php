<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | SCIS Kabinet Keong</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .font-serif-heading { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-[#13113C] min-h-screen flex items-center justify-center p-6 relative overflow-hidden selection:bg-[#E5A87B] selection:text-[#13113C]">
    
    <!-- Layered Card Setup -->
    <div class="relative w-full max-w-lg z-10 my-8">
        <!-- Shadow Layer (Peach for Register) -->
        <div class="absolute inset-0 bg-[#E5A87B] rounded-2xl translate-x-4 translate-y-4"></div>
        
        <!-- Main Card -->
        <div class="relative bg-[#F6F3E7] border-2 border-dashed border-[#E5A87B] p-10 sm:p-14 rounded-2xl shadow-xl">
            
            <a href="/" class="inline-flex items-center text-sm font-semibold text-[#E5A87B] hover:text-[#13113C] transition-colors mb-6 uppercase tracking-wider">
                &larr; Back to Home
            </a>

            <div class="mb-10">
                <h1 class="font-serif-heading text-4xl text-[#13113C] font-bold mb-2">Join Us.</h1>
                <p class="text-[#13113C]/70">Create an account to start borrowing assets.</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-5">
                    <label for="name" class="block text-sm font-bold text-[#13113C] mb-2 uppercase tracking-wider">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full px-5 py-4 bg-white border-2 border-[#13113C]/10 rounded-none text-[#13113C] font-medium focus:ring-0 focus:border-[#E5A87B] outline-none transition-all placeholder:text-gray-400" placeholder="John Doe">
                    @error('name') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="email" class="block text-sm font-bold text-[#13113C] mb-2 uppercase tracking-wider">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full px-5 py-4 bg-white border-2 border-[#13113C]/10 rounded-none text-[#13113C] font-medium focus:ring-0 focus:border-[#E5A87B] outline-none transition-all placeholder:text-gray-400" placeholder="john@studentcouncil.com">
                    @error('email') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-sm font-bold text-[#13113C] mb-2 uppercase tracking-wider">Password</label>
                    <input id="password" type="password" name="password" required class="w-full px-5 py-4 bg-white border-2 border-[#13113C]/10 rounded-none text-[#13113C] font-medium focus:ring-0 focus:border-[#E5A87B] outline-none transition-all placeholder:text-gray-400" placeholder="••••••••">
                    @error('password') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="mb-10">
                    <label for="password_confirmation" class="block text-sm font-bold text-[#13113C] mb-2 uppercase tracking-wider">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full px-5 py-4 bg-white border-2 border-[#13113C]/10 rounded-none text-[#13113C] font-medium focus:ring-0 focus:border-[#E5A87B] outline-none transition-all placeholder:text-gray-400" placeholder="••••••••">
                </div>

                <button type="submit" class="w-full py-4 bg-[#13113C] text-[#F6F3E7] font-bold text-lg uppercase tracking-widest hover:bg-[#E5A87B] hover:text-[#13113C] transition-all duration-300 border-2 border-[#13113C]">
                    Create Account
                </button>
            </form>
            
            <p class="mt-8 text-center text-[#13113C]/70 font-medium">
                Already registered? <a href="{{ route('login') }}" class="text-[#E5A87B] font-bold hover:underline">Log in</a>
            </p>
        </div>
    </div>
</body>
</html>