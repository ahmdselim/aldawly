// main.js or your main JavaScript file

import { initializeApp } from 'https://www.gstatic.com/firebasejs/9.6.10/firebase-app.js';
import { getMessaging, getToken, onMessage } from 'https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging.js';

const firebaseConfig = {
    apiKey: "AIzaSyD7zPq_lW0w7iTOp1d0cSLmYx8MKR4aTVo",
    authDomain: "ishaar-ab0a4.firebaseapp.com",
    projectId: "ishaar-ab0a4",
    storageBucket: "ishaar-ab0a4.appspot.com",
    messagingSenderId: "823009491412",
    appId: "1:823009491412:web:f11c969d6b1d0fc0a2be41",
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

navigator.serviceWorker.register('/firebase-messaging-sw.js')
    .then((registration) => {
        messaging.useServiceWorker(registration);

        getToken(messaging, { vapidKey: 'BDOU99-h67HcA6JeFXHbSNMu7e2yNNu3RzoMj8TM4W88jITfq7ZmPvIM1Iv-4_l2LxQcYwhqby2xGpWwzjfAnG4' }).then((currentToken) => {
            if (currentToken) {
                console.log('Token retrieved: ', currentToken);
                // Send the token to your server
            } else {
                console.log('No registration token available. Request permission to generate one.');
            }
        }).catch((err) => {
            console.log('An error occurred while retrieving token. ', err);
        });

        onMessage(messaging, (payload) => {
            console.log('Message received. ', payload);
            // Show notification here
        });
    }).catch((err) => {
    console.log('ServiceWorker registration failed: ', err);
});
