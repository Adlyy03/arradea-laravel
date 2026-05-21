import { initializeApp } from "firebase/app";
import { getMessaging } from "firebase/messaging";

// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyBRkZOqcrucOIgK05pYiI437-1Oqv2tcmQ",
  authDomain: "arradeaa.firebaseapp.com",
  projectId: "arradeaa",
  storageBucket: "arradeaa.firebasestorage.app",
  messagingSenderId: "659706212200",
  appId: "1:659706212200:web:92e467c5505c87b9a870dd",
  measurementId: "G-CMQNT26ZZQ"
};

const app = initializeApp(firebaseConfig);

export const messaging = getMessaging(app);
