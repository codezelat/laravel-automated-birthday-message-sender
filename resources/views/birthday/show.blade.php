<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Happy Birthday {{ $contact->name }}!</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        body { background-color: white; }
        .hidden { display: none; }
        .fade-in { animation: fadeIn 2s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen bg-white text-center p-4">

    <div id="initial-view" class="flex flex-col items-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-8">You have a surprise! 🎁</h1>
        <button id="open-gift-btn" class="bg-red-500 hover:bg-red-600 text-white font-bold py-4 px-8 rounded-full text-xl shadow-lg transform transition hover:scale-105">
            Tap to Open
        </button>
    </div>

    <div id="card-view" class="hidden flex-col items-center w-full max-w-2xl">
        <div class="relative shadow-2xl rounded-lg overflow-hidden mb-8 border-4 border-gray-100">
            <img src="{{ route('birthday.card', $contact->public_token) }}" alt="Birthday Card" class="w-full h-auto">
        </div>

        <a href="{{ route('birthday.download', $contact->public_token) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download Card
        </a>
    </div>

    <script>
        document.getElementById('open-gift-btn').addEventListener('click', function() {
            // Hide initial view
            document.getElementById('initial-view').classList.add('hidden');
            
            // Show card view with fade in
            const cardView = document.getElementById('card-view');
            cardView.classList.remove('hidden');
            cardView.classList.add('fade-in');

            // Fire confetti
            var duration = 5 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

            function random(min, max) {
              return Math.random() * (max - min) + min;
            }

            var interval = setInterval(function() {
              var timeLeft = animationEnd - Date.now();

              if (timeLeft <= 0) {
                return clearInterval(interval);
              }

              var particleCount = 50 * (timeLeft / duration);
              // since particles fall down, start a bit higher than random
              confetti(Object.assign({}, defaults, { particleCount, origin: { x: random(0.1, 0.3), y: Math.random() - 0.2 } }));
              confetti(Object.assign({}, defaults, { particleCount, origin: { x: random(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        });
    </script>
</body>
</html>
