<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>Eldawly dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">


    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/calendars/fullcalendar.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/bordered-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/semi-dark-layout.css')}}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/app-calendar.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/form-validation.css')}}">
    <!-- END: Page CSS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{(asset('assets/css/style.css'))}}">
    <!-- END: Custom CSS-->

</head>

</head>
<body>

@yield('content')
<script src="{{asset('/app-assets/vendors/js/vendors.min.js')}}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{asset('/app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{asset('/app-assets/js/core/app-menu.js')}}"></script>
<script src="{{asset('/app-assets/js/core/app.js')}}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<script src="{{asset('/app-assets/js/scripts/pages/app-ecommerce-wishlist.js')}}"></script>
<!-- END: Page JS-->

<script>
    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })
</script>
<!-- Include Firebase SDK for JavaScript -->
<script src="https://www.gstatic.com/firebasejs/8.2.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.2.0/firebase-messaging.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

{{--<script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-app-compat.js"></script>--}}
{{--<script src="https://www.gstatic.com/firebasejs/9.14.0/firebase-messaging-compat.js"></script>--}}


<script type="module">
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "2000",
        "hideDuration": "10000",
        "timeOut": "10000",
        "extendedTimeOut": "10000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    const firebaseConfig = {
        apiKey: "AIzaSyD7zPq_lW0w7iTOp1d0cSLmYx8MKR4aTVo",
        authDomain: "ishaar-ab0a4.firebaseapp.com",
        projectId: "ishaar-ab0a4",
        storageBucket: "ishaar-ab0a4.appspot.com",
        messagingSenderId: "823009491412",
        appId: "1:823009491412:web:f11c969d6b1d0fc0a2be41",
    };
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    try {
        //
        const token = await getToken(messaging, { vapidKey: "BDOU99-h67HcA6JeFXHbSNMu7e2yNNu3RzoMj8TM4W88jITfq7ZmPvIM1Iv-4_l2LxQcYwhqby2xGpWwzjfAnG4" });
        console.log(token);

    } catch(err) {
        console.error('Error while retrieving token. ', err);
    }



    function requestPermission() {
        console.log("Requesting permission...");
        Notification.requestPermission().then((permission) => {
            if (permission === "granted") {
                console.log("Notification permission granted.");
            } else if (permission === "denied") {
                toastr.error("Notification permission denied. Please enable it in your browser settings.");
            } else {
                toastr.error("Notification permission dismissed. Please enable it in your browser settings.");

            }
        });
    }
    requestPermission();

    let userInteracted = false;

    function onUserInteraction() {
        userInteracted = true;
        document.removeEventListener('click', onUserInteraction);
        document.removeEventListener('touchstart', onUserInteraction);
    }

    document.addEventListener('click', onUserInteraction);
    document.addEventListener('touchstart', onUserInteraction);


    messaging.onMessage((payload) => {
        const audio = new Audio('{{asset('app-assets/sounds/light-hearted-message-tone.mp3')}}');
        audio.play().catch(err => console.error("Error playing audio:", err));

        const notificationOptions = {
            title : payload.notification.title,
            body: payload.notification.body,
            icon: "https://img.icons8.com/?size=256&id=95109&format=png",
            // image: paye
        }

        toastr.success(notificationOptions.body, notificationOptions.title);

        self.registration.showNotification(notificationOptions.title, notificationOptions);
    });

    // messaging.onBackgroundMessage((payload) => {
    //
    //     const notificationTitle = payload.notification.title;
    //     const notificationOptions = {
    //         body: payload.notification.body,
    //         icon: 'https://img.icons8.com/?size=256&id=95109&format=png'
    //     };
    //
    //     self.registration.showNotification(notificationTitle, notificationOptions);
    // });



    // messaging.onBackgroundMessage(function (payload) {
    //     if (!payload.hasOwnProperty('notification')) {
    //         const notificationTitle = payload.data.title
    //         const notificationOptions = {
    //             body: payload.data.body,
    //             icon: payload.data.icon,
    //             image: payload.data.image
    //         }
    //         self.registration.showNotification(notificationTitle, notificationOptions);
    //         self.addEventListener('notificationclick', function (event) {
    //             const clickedNotification = event.notification
    //             clickedNotification.close();
    //             event.waitUntil(
    //                 clients.openWindow(payload.data.click_action)
    //             )
    //         })
    //     }
    // })


</script>


{{--<script type="module">--}}
{{--    import { initializeApp } from 'https://www.gstatic.com/firebasejs/9.6.10/firebase-app.js';--}}
{{--    import { getMessaging, getToken, onMessage } from 'https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging.js';--}}

{{--    const firebaseConfig = {--}}
{{--        apiKey: "AIzaSyD7zPq_lW0w7iTOp1d0cSLmYx8MKR4aTVo",--}}
{{--        authDomain: "ishaar-ab0a4.firebaseapp.com",--}}
{{--        projectId: "ishaar-ab0a4",--}}
{{--        storageBucket: "ishaar-ab0a4.appspot.com",--}}
{{--        messagingSenderId: "823009491412",--}}
{{--        appId: "1:823009491412:web:f11c969d6b1d0fc0a2be41",--}}
{{--        vapidKey: "BDOU99-h67HcA6JeFXHbSNMu7e2yNNu3RzoMj8TM4W88jITfq7ZmPvIM1Iv-4_l2LxQcYwhqby2xGpWwzjfAnG4" // Correct VAPID key--}}
{{--    };--}}

{{--    const app = initializeApp(firebaseConfig);--}}
{{--    const messaging = getMessaging(app);--}}

{{--    if ('serviceWorker' in navigator) {--}}
{{--        navigator.serviceWorker.register('/firebase-messaging-sw.js')--}}
{{--            .then((registration) => {--}}
{{--                console.log('Service Worker registered with scope:', registration.scope);--}}
{{--                requestPermission();--}}
{{--            })--}}
{{--            .catch((err) => {--}}
{{--                console.error('Service Worker registration failed:', err);--}}
{{--            });--}}
{{--    } else {--}}
{{--        console.warn('Service Workers are not supported by this browser.');--}}
{{--    }--}}

{{--    async function requestPermission() {--}}
{{--        try {--}}
{{--            const token = await getToken(messaging, { vapidKey: firebaseConfig.vapidKey });--}}
{{--            if (token) {--}}
{{--                console.log('FCM Token:', token);--}}
{{--            } else {--}}
{{--                console.log('No registration token available. Request permission to generate one.');--}}
{{--            }--}}
{{--        } catch (err) {--}}
{{--            console.error('Error while retrieving token. ', err);--}}
{{--        }--}}
{{--    }--}}

{{--    onMessage(messaging, (payload) => {--}}
{{--        alert('asd00');--}}
{{--        console.log('Message received. ', payload);--}}
{{--        const notificationTitle = payload.notification.title;--}}
{{--        const notificationOptions = {--}}
{{--            body: payload.notification.body,--}}
{{--            icon: '/firebase-logo.png'--}}
{{--        };--}}

{{--        if (Notification.permission === 'granted') {--}}
{{--            new Notification(notificationTitle, notificationOptions);--}}
{{--        }--}}
{{--    });--}}
{{--</script>--}}
</body>
</html>
