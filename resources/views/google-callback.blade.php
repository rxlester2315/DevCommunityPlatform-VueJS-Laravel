<!DOCTYPE html>
<html>

    <head>
        <title>Google Authentication</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f5f5f5;
        }

        .message {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
        }

        .success {
            color: #10B981;
        }

        .error {
            color: #EF4444;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4285F4;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
        </style>
    </head>

    <body>
        <div class="message">
            @if(isset($success) && $success)
            <div class="success">
                <h2>✅ Authentication Successful!</h2>
                <p>Closing window...</p>
            </div>
            <script>
            console.log("🔄 Sending user data to Vue app...");

            const userData = @json($user);

            if (window.opener) {
                try {
                    window.opener.postMessage({
                        type: 'GOOGLE_AUTH_SUCCESS',
                        user: userData
                    }, '*');
                    console.log("✅ Message sent via postMessage");
                } catch (error) {
                    console.error("❌ postMessage failed:", error);
                }
            }

            localStorage.setItem('google_auth_success', 'true');
            localStorage.setItem('google_auth_user', JSON.stringify(userData));
            console.log("✅ Data stored in localStorage");

            setTimeout(() => {
                console.log("🔒 Closing popup...");
                window.close();
            }, 1000);
            </script>
            @else
            <div class="error">
                <h2>❌ Authentication Failed</h2>
                <p>{{ $message ?? 'An error occurred during authentication' }}</p>
            </div>
            <script>
            if (window.opener) {
                window.opener.postMessage({
                    type: 'GOOGLE_AUTH_ERROR',
                    message: '{{ $message ?? "Authentication failed" }}'
                }, '*');
            }
            setTimeout(() => {
                window.close();
            }, 3000);
            </script>
            @endif
        </div>
    </body>

</html>