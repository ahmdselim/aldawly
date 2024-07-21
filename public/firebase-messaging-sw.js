// Scripts for firebase and firebase messaging
importScripts("https://www.gstatic.com/firebasejs/8.2.0/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.2.0/firebase-messaging.js");

// Initialize the Firebase app in the service worker by passing the generated config
const firebaseConfig = {
    apiKey: "AIzaSyD7zPq_lW0w7iTOp1d0cSLmYx8MKR4aTVo",
    authDomain: "ishaar-ab0a4.firebaseapp.com",
    projectId: "ishaar-ab0a4",
    storageBucket: "ishaar-ab0a4.appspot.com",
    messagingSenderId: "823009491412",
    appId: "1:823009491412:web:0522fa544b8f0e6ca2be41",
    measurementId: "G-J9XW6K0BLR",
};

firebase.initializeApp(firebaseConfig);

// Retrieve firebase messaging
const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    console.log("Received background message ", payload);

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
