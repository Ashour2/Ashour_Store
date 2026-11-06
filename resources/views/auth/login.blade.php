{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}

{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <x-welcome/>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ml-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
 --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400,900' rel='stylesheet' type='text/css'>
    <title>Login</title>
    @if ($logo = logo_url())
        <link rel="icon" href="{{ $logo }}">
    @endif
    <style>
        html,
        body {
            width: 100vw;
            height: 100vh;
            margin: 0;
            padding: 0;
            background: url("https://s3-us-west-2.amazonaws.com/s.cdpn.io/28963/form-bk.jpg") no-repeat center center;
            background-size: cover;
            font-family: "Lato", sans-serif;
        }

        form {
            background: #faf5f3;
            width: 280px;
            margin-top: 50px;
            margin-bottom: 50px;
            position: absolute;
            z-index: 10;
            padding: 60px 60px 80px;
            left: 50%;
            margin-left: -230px;
            opacity: 0.85;
        }

        form h2 {
            text-transform: uppercase;
            text-align: center;
            font-size: 18px;
            color: #2f1f1e;
            letter-spacing: 0.061em;
            margin-top: 60px;
            margin-bottom: 20px;
        }

        form h2.success {
            color: #356e64;
            margin-top: -40px;
            margin-bottom: 0px;
            padding: 0;
        }

        form p.success-dialog {
            margin-top: -150px;
            color: #356e64;
            letter-spacing: 0.02em;
            text-align: center;
            line-height: 1.8em;
            text-transform: uppercase;
        }

        form p.success-dialog a {
            color: #9d775f;
            text-decoration: none;
            transition: 0.8s all ease;
        }

        form p.success-dialog a:hover {
            color: #c08159;
            transition: 0.4s all ease;
        }

        form input {
            width: 100%;
            background: #fff;
            text-align: center;
            margin-bottom: 25px;
            box-shadow: none;
            appearance: none;
            border: none;
            border-top: 1px solid #fff;
            border-left: 1px solid #fff;
            border-right: 1px solid #fff;
            border-bottom: 1px solid #ebd7cf;
            padding-top: 8px;
            padding-bottom: 8px;
            text-transform: uppercase;
            font-size: 11px;
            position: relative;
            z-index: 500;
            letter-spacing: 0.06em;
        }

        form input:focus {
            border: 1px solid #ebd7cf;
            outline: none;
            appearance: none;
        }

        form input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px white inset;
        }

        form .submit {
            padding-top: 12px;
            padding-bottom: 12px;
            border: none;
            text-transform: uppercase;
            font-size: 11px;
            position: relative;
            z-index: 500;
            letter-spacing: 0.06em;
            text-align: center;
            cursor: pointer;
            background: #ec7242;
            color: #fff;
            width: 101%;
            transition: 0.8s all ease;
        }

        form .submit:hover {
            background: #d6673c;
            transform: translateY(1px);
            transition: 0.4s all ease;
        }

        #firstname {
            margin-top: 20px;
        }

        .circle {
            padding: 15px;
            height: 25px;
            width: 25px;
            margin-top: -80px;
            margin-left: 115px;
            position: absolute;
            z-index: 20;
            border-radius: 50%;
            clear: both;
            opacity: 0.8;
        }

        .circle-quill {
            border: 1px solid #ebd7cf;
            background: #fff;
        }

        .circle-paper {
            border: 1px solid #9ac0ba;
            background: #5d978e;
        }

        .submit-under,
        .loader,
        .loader2,
        .circle-paper,
        p.success-dialog,
        h2.success {
            visibility: hidden;
        }

        .submit-under {
            background: #8c6e7a;
            width: 291px;
            height: 45px;
            margin-top: -40px;
            margin-left: -4px;
            position: absolute;
            z-index: -1;
        }

        .loader,
        .loader2 {
            position: absolute;
            z-index: -5;
            margin-top: -45px;
            margin-left: 114px;
        }
    </style>
</head>

<body>

    <form class="sub-form" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="input-contain">
            <div class="circle circle-quill">
                <svg class="quill" x="0px" y="0px" width="25.167px" height="25.167px"
                    viewBox="0 0 25.167 25.167" enable-background="new 0 0 25.167 25.167" xml:space="preserve">
                    <path fill="#4e3332"
                        d="M0,25C3.125,15.625,11.304,0,25,0c-6.421,5.151-9.375,17.188-14.062,17.188s-4.688,0-4.688,0L1.562,25H0z" />
                </svg>
            </div>
            <div class="circle circle-paper">
                <svg class="paper" x="0" y="0" width="25.1" height="25.1" viewBox="0 0 25.1 25.1"
                    enable-background="new 0 0 25.125 25.125" xml:space="preserve">
                    <path fill="#b1dbd3"
                        d="M24 2.1C23.5 2.3 1.2 10.2 0.8 10.3c-0.4 0.1-0.5 0.5 0 0.6 0.5 0.2 5 2 5 2H5.8l3 1.2c0 0 14.2-10.4 14.4-10.6 0.2-0.1 0.4 0.1 0.3 0.3 -0.1 0.2-10.3 11.2-10.3 11.2 0 0 0 0 0 0l-0.6 0.7 0.8 0.4c0 0 6.1 3.3 6.5 3.5 0.4 0.2 0.9 0 1-0.4 0.1-0.6 3.7-16.1 3.8-16.4C24.7 2.3 24.4 2 24 2.1zM8.7 21.2c0 0.3 0.2 0.4 0.4 0.2 0.3-0.3 3.7-3.4 3.7-3.4l-4.2-2.2V21.2z" />
                </svg>
            </div>
            <h2 class="info">Login Now</h2>
            <br>
            <h2 class="success">Success!</h2>
            <input type="text" placeholder="البريد الإلكتروني أو رقم هاتف الصيدلية" name="email" value="{{ old('email') }}" required="required" />
            <input type="password" id="password" name="password" placeholder="Password" required autocomplete="current-password" />
            <div class="allsub">
                <div class="submit">Login</div>
                <div class="submit-under"></div>
            </div><!--allsub-->
            <svg class="loader" x="0px" y="0px" width="55px" height="55px" viewBox="0 0 55 55"
                enable-background="new 0 0 55 55" xml:space="preserve">
                <circle fill="none" stroke-linecap="round" stroke="#B29EAC" stroke-width="2" stroke-miterlimit="10"
                    cx="27.583" cy="27.334" r="26.583" />
            </svg>
            <svg class="loader2" x="0px" y="0px" width="55px" height="55px" viewBox="0 0 55 55"
                enable-background="new 0 0 55 55" xml:space="preserve">
                <circle fill="none" stroke-linecap="round" stroke="#B29EAC" stroke-width="2" stroke-miterlimit="10"
                    cx="27.583" cy="27.334" r="26.583" />
            </svg>
            <p class="success-dialog">
                We’ll be in touch shortly.<br>
                In the meantime, <br>
                check out our <br>
                <a href="#">weekly offerings.</a>
            </p>
        </div><!--input-contain-->
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.15.0/TweenMax.min.js"></script>

    <script>

        $(document).ready(function() {
            tl = new TimelineLite();
            var form = $(".sub-form"),
                sub = $(".submit-under"),
                allsub = $(".allsub"),
                loader = $(".loader circle"),
                loader2 = $(".loader2 circle"),
                loaders = [loader, loader2],
                circP = $(".circle-paper");

            TweenMax.set(sub, {
                opacity: 0.7,
                rotationY: 90
            });
            TweenMax.set($(".submit-under, .loader, .loader2, .circle-paper, p.success-dialog, h2.success"), {
                visibility: "visible"
            });
            TweenMax.set(loader, {
                drawSVG: '100% 100%',
                rotation: -90
            });
            TweenMax.set(circP, {
                scaleY: 0,
                transformOrigin: "50% 50%"
            });
            TweenMax.set([loader2, circP], {
                opacity: 0
            });
            TweenMax.set($(".success, .success-dialog"), {
                opacity: 0
            });

            // $(".submit").on("click", function(e) {
            //     e.preventDefault();
            //     tl.to(sub, 1, {
            //         opacity: 1,
            //         rotationY: 0,
            //         ease: Expo.easeOut
            //     });
            //     tl.add("flip");
            //     tl.to($(".submit"), 0.5, {
            //         rotationX: 90,
            //         ease: Circ.easeOut
            //     }, "flip-=1.5");
            //     tl.to($(".submit"), 0.5, {
            //         opacity: 0
            //     }, "flip-=0.5");
            //     tl.to(sub, 0.25, {
            //         css: {
            //             borderRadius: "50%",
            //             backgroundColor: "#d6c7ca"
            //         },
            //         ease: Circ.easeOut
            //     }, "flip-=0.5");
            //     tl.to(sub, 1.2, {
            //         scaleX: 0.16,
            //         transformOrigin: "50% 50%",
            //         ease: Expo.easeOut
            //     }, "flip-=0.5");
            //     tl.fromTo(loader, 1, {
            //         transformOrigin: "50% 50%",
            //         drawSVG: "50% 50%"
            //     }, {
            //         transformOrigin: "50% 50%",
            //         drawSVG: "100%"
            //     }, "flip+=1");
            //     tl.to(sub, 0.8, {
            //         rotationX: 90,
            //         scaleY: 0
            //     }, "flip+=1.2");
            //     tl.to(loader2, 0.1, {
            //         opacity: 1
            //     }, "flip+=1.8");
            //     tl.to(loader2, 0.5, {
            //         opacity: 1,
            //         transformOrigin: "50% 50%",
            //         scaleX: 0,
            //         rotation: 180
            //     }, "flip+=2");
            //     tl.to(loader2, 0.5, {
            //         opacity: 1,
            //         transformOrigin: "50% 50%",
            //         scaleX: 1,
            //         rotation: 180
            //     }, "flip+=2.5");
            //     tl.add("success");
            //     tl.to($(".circle-quill"), 0.5, {
            //         scaleY: 0,
            //         transformOrigin: "50% 50%",
            //         ease: Circ.easeOut
            //     }, "success");
            //     tl.to(circP, 0.5, {
            //         scaleY: 1,
            //         opacity: 1,
            //         transformOrigin: "50% 50%",
            //         ease: Circ.easeIn
            //     }, "success");
            //     tl.to($(".circle-quill"), 0.5, {
            //         scaleY: 0,
            //         transformOrigin: "50% 50%",
            //         ease: Circ.easeOut
            //     }, "success");
            //     tl.to($("input"), 0.5, {
            //         scaleY: 0
            //     }, "success");
            //     tl.to($(".info"), 0.5, {
            //         scaleY: 0
            //     }, "success");
            //     tl.to($(".success"), 0.5, {
            //         scaleY: 1,
            //         opacity: 1
            //     }, "success+=1");
            //     tl.to($(".success-dialog"), 1, {
            //         opacity: 1
            //     }, "success+=1");
            //     tl.to(loaders, 0.5, {
            //         scaleY: 0,
            //         stroke: "#b1dbd3",
            //         transformOrigin: "50% 50%"
            //     }, "success");
            //     tl.to(form, 0.5, {
            //         // css: {
            //         //     backgroundColor: "#7aada5"
            //         // }
            //     }, "success");

            //     setTimeout(() => {
            //         document.querySelector('.sub-form').submit()
            //     }, 6000);
            // });
            $(".submit").on("click", function(e) {
    e.preventDefault();
    // --- ابدأ الحركة (Animation) ---
    tl.to(sub, 1, {
        opacity: 1,
        rotationY: 0,
        ease: Expo.easeOut
    });
    // ... (باقي الحركات كما هي في الكود الأصلي)
    tl.to($(".success-dialog"), 1, {
        opacity: 1
    }, "success+=1");
    tl.to(loaders, 0.5, {
        scaleY: 0,
        stroke: "#b1dbd3",
        transformOrigin: "50% 50%"
    }, "success");
    tl.to(form, 0.5, {});

    // --- أضف هذا السطر لإرسال النموذج بعد انتهاء الحركة ---
    // 6000 هي المدة الزمنية للحركة (6 ثواني)، يمكنك تقليلها
    setTimeout(() => {
        // قم بإلغاء تعطيل الأزرار أو المدخلات إذا وجدت
        document.querySelector('.sub-form').submit()
    }, 6000);
});
        });
    </script>
</body>

</html>
